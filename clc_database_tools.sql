-- DENY trigger for Classes. Activates whenever a record from Classes is deleted.
DROP TRIGGER IF EXISTS deny_classes;
DELIMITER //
CREATE TRIGGER deny_classes
BEFORE DELETE ON classes
FOR EACH ROW
BEGIN
    /* Kill deletion attempt */
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Delete failed. Must soft delete.';
END;
//
DELIMITER ;

-- DENY procedure for Classes.
DROP PROCEDURE IF EXISTS deny_classes;
DELIMITER //
CREATE PROCEDURE deny_classes(
    IN locate_subject CHAR(3),
    IN locate_class INT
)
BEGIN
    UPDATE classes
    SET deleted_when = CURRENT_TIME()
    WHERE subject_code = locate_subject AND class_number = locate_class;
END;
//
DELIMITER ;



-- DENY trigger for Slots. Activates whenever a record from Slots is deleted. 
DROP TRIGGER IF EXISTS deny_slots;
DELIMITER //
CREATE TRIGGER deny_slots
BEFORE DELETE ON slots
FOR EACH ROW
BEGIN
    /* Kill deletion attempt */
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Delete failed. Must soft delete.';
END;
//
DELIMITER ;

-- DENY procedure for Slots.
DROP PROCEDURE IF EXISTS deny_slots;
DELIMITER //
CREATE PROCEDURE deny_slots(
    IN locate_slot INT
)
BEGIN
    UPDATE slots
    SET deleted_when = CURRENT_TIME()
    WHERE slot_id = locate_slot;
END;
//
DELIMITER ;



-- DENY trigger for Tutors. Activates whenever a record from Tutors is deleted.
DROP TRIGGER IF EXISTS deny_tutors;
DELIMITER //
CREATE TRIGGER deny_tutors
BEFORE DELETE ON tutors
FOR EACH ROW
BEGIN
    /* Kill deletion attempt */
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Delete failed. Must soft delete.';
END;
//
DELIMITER ;

-- DENY procedure for Tutors.
DROP PROCEDURE IF EXISTS deny_tutors;
DELIMITER //
CREATE PROCEDURE deny_tutors(
    IN locate_tutor INT
)
BEGIN
    UPDATE tutors
    SET deleted_when = CURRENT_TIME()
    WHERE tutor_id = locate_tutor;
END;
//
DELIMITER ;

--Creates procedure which shows tutor name, id, email, and what classes they taught
--May need to remove semester column in future
DROP PROCEDURE IF EXISTS tutor_history_view;

DELIMITER //
CREATE PROCEDURE tutor_history_view(
    IN first_name VARCHAR(30),
    IN last_name VARCHAR(30),
    IN all_students BOOLEAN
)
BEGIN
    SET @query =
    'SELECT CONCAT(tutor_first_name, " ", tutor_last_name) AS `Name`,
    tutors.tutor_id AS `Student ID`,
    tutor_email AS `Email`,
    GROUP_CONCAT(DISTINCT CONCAT(year_terms.term_code, " ", year_terms.term_year) SEPARATOR "," ) AS `Semesters Taught`,
    GROUP_CONCAT(DISTINCT CONCAT(slots.subject_code, " ", slots.class_number) SEPARATOR "," ) AS `Classes Taught`
    FROM tutors
    INNER JOIN slot_tutors ON (slot_tutors.tutor_id = tutors.tutor_id)
    INNER JOIN slots ON (slots.slot_id = slot_tutors.slot_id)
    INNER JOIN slot_terms on (slot_terms.slot_id = slots.slot_id)
    INNER JOIN year_terms ON (year_terms.term_id = slot_terms.term_id)
    INNER JOIN tutor_agreed_classes ON (tutor_agreed_classes.tutor_id = tutors.tutor_id)';
    IF all_students = false THEN
        SET @query = CONCAT(@query, ' WHERE tutors.tutor_first_name = ? AND tutors.tutor_last_name = ?');
    END IF;
    SET @query = CONCAT(@query, ' GROUP BY tutors.tutor_id;');

    PREPARE stmt from @query;
    IF all_students = FALSE THEN
        EXECUTE stmt USING first_name, last_name;
ELSE
        EXECUTE stmt;
END IF;
    DEALLOCATE PREPARE stmt;
END //

DELIMITER ;


--Creates a view which shows tutor name, the class(es) they teach, and each time they teach it
--Sorting can be done by tutors, subjects, or both
DROP PROCEDURE IF EXISTS tutor_schedule_view;

DELIMITER //
CREATE PROCEDURE tutor_schedule_view(
        IN first_name VARCHAR(30),
        IN last_name VARCHAR(30),
        IN subject_code CHAR(3),
        IN all_students BOOLEAN,
        IN all_subjects BOOLEAN
)
BEGIN
        SET @query =
        'SELECT CONCAT(tutor_first_name, " ", tutor_last_name) AS `Name`,
        CONCAT(slots.subject_code, " ", slots.class_number) AS `Class`,
        CONCAT(time_blocks.time_start, " - ", time_blocks.time_end, " ", time_blocks.week_day_name) AS `Time Offered`
        FROM tutors
        INNER JOIN slot_tutors ON (tutors.tutor_id = slot_tutors.tutor_id)
        INNER JOIN slots ON (slots.slot_id = slot_tutors.slot_id)
        INNER JOIN slot_times ON (slots.slot_id = slot_times.slot_id)
        INNER JOIN time_blocks ON (time_blocks.time_id = slot_times.slot_id)
        ';

        SET @w = '';

        IF all_students = false THEN
                SET @w = ' WHERE tutors.tutor_first_name = ? AND tutors.tutor_last_name = ?';
                IF all_subjects = false THEN
                        SET @w = CONCAT(@w, ' AND slots.subject_code = ?');
                END IF;
        ELSEIF all_subjects = false THEN
                SET @w = ' WHERE slots.subject_code = ?';
        END IF;

        SET @query = CONCAT(@query, @w);

        PREPARE stmt FROM @query;
        IF all_students = false AND all_students = false THEN
                EXECUTE stmt USING first_name, last_name, subject_code;
        ELSEIF all_students = false THEN
                EXECUTE stmt USING first_name, last_name;
        ELSEIF all_subjects = false THEN
                EXECUTE stmt USING subject_code;
        ELSE
                EXECUTE stmt;
        END IF;
        DEALLOCATE PREPARE stmt;
END //

DELIMITER ;


--Creates a view which shows room building and number, tutoring time and day, class subject and number
--Sorting done by subject or class
--Procedure also takes semester as input, but this should not be selectable by students (use current semester)
DROP PROCEDURE IF EXISTS full_schedule_view;

DELIMITER //
CREATE PROCEDURE full_schedule_view(
    IN subject_code CHAR(3),
    IN class_number INT,
    IN semester CHAR(2),
    IN s_year INT,
    IN all_subjects BOOLEAN,
    IN all_classes BOOLEAN
)
BEGIN
    SET @query =
    'SELECT CONCAT(slots.subject_code, " ", slots.class_number) AS `Class`,
    CONCAT(slots.building_name, " ", slots.room_number) AS `Room`,
    CONCAT(time_blocks.time_start, " ", time_blocks.time_end, " ", time_blocks.week_day_name) AS `Time Offered`
    FROM slots
    INNER JOIN slot_times ON (slots.slot_id = slot_times.slot_id)
    INNER JOIN time_blocks ON (time_blocks.time_id = slot_times.slot_id)
    ';
    IF all_subjects = false THEN
        SET @w = CONCAT(@w, 'AND slots.subject_code = ?');
        IF all_classes = false THEN
            SET @w = CONCAT(@w, 'AND slots.class_number = ?');
        END IF;
    END IF;

    SET @query = CONCAT(@query, @w, ';');

    PREPARE stmt FROM @query;

    IF all_subjects = false AND all_classes = false THEN
        EXECUTE stmt USING semester, s_year, subject_code, class_number;
    ELSEIF all_subjects = false AND all_classes = true THEN
        EXECUTE stmt USING semester, s_year, subject_code;
    ELSE
        EXECUTE stmt USING semester, s_year;
    END IF;
END //

DELIMITER ;

--Grant permissions
GRANT EXECUTE ON PROCEDURE full_schedule_view TO 'webuser'@'localhost';
GRANT EXECUTE ON PROCEDURE tutor_schedule_view TO 'webuser'@'localhost';
GRANT EXECUTE ON PROCEDURE tutor_history_view TO 'webuser'@'localhost';
GRANT EXECUTE ON PROCEDURE clc_tutoring.deny_classes TO 'webuser'@'localhost';
GRANT EXECUTE ON PROCEDURE clc_tutoring.deny_slots TO 'webuser'@'localhost';
GRANT EXECUTE ON PROCEDURE clc_tutoring.deny_tutors TO 'webuser'@'localhost';
