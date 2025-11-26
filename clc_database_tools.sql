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

--Creates procedure which shows tutor name, id, email, what classes they taught, and which semester they taught it    
DROP PROCEDURE IF EXISTS tutor_history_view;

DELIMITER //
CREATE PROCEDURE tutor_history_view
(
    IN first_name VARCHAR(30),
    IN last_name VARCHAR(30),
    IN all_students BOOLEAN
)
BEGIN
    SET @query =
    'SELECT DISTINCT CONCAT(tutor_first_name, " ", tutor_last_name) AS `Name`,
    tutors.tutor_id AS `Student ID`,
    tutor_email AS `Email`,
    CONCAT(slots.subject_code, " ", slots.class_number) AS `Classes Taught`,
    CONCAT(term_code, " ", year_term_year) AS `Semester Taught`
    FROM tutors
    INNER JOIN slots ON (tutors.tutor_id = slots.tutor_id)
    INNER JOIN time_blocks ON (time_blocks.time_block_id = slots.time_block_id)';
    IF all_students = false THEN
        SET @query = CONCAT(@query, ' WHERE tutors.tutor_first_name = ? AND tutors.tutor_last_name = ?');     
    END IF;
    SET @query = CONCAT(@query, ';');
    PREPARE stmt from @query;
    IF all_students = FALSE THEN
        EXECUTE stmt USING first_name, last_name;  
    ELSE
            EXECUTE stmt;
    END IF;
    DEALLOCATE PREPARE stmt;
END //

DELIMITER ;

--Creates a view which shows tutor name, room, the class(es) taught, the times they were taught, and the semester they were taught
--Sorting can be done by tutor, subject, class, or semester
--View updated to work both for tutor_schedule (Chisley) and full_schedule (students)
--Chisley view shows student name, but not room, student view shows room, but not tutor (all done via php pages)
DROP PROCEDURE IF EXISTS tutor_schedule_view;

DELIMITER //
CREATE PROCEDURE tutor_schedule_view(
        IN show_students BOOLEAN,
        IN show_rooms BOOLEAN,
        IN first_name VARCHAR(30),
        IN last_name VARCHAR(30),
        IN subject_code CHAR(3),
        IN class_number INT,
        IN term VARCHAR(2),
        IN t_year INT,
        IN all_students BOOLEAN,
        IN all_subjects BOOLEAN,
        IN all_classes BOOLEAN,
        IN all_terms BOOLEAN
)
BEGIN
        SET @query = 'SELECT ';
        IF show_students = true THEN
                SET @query = CONCAT(@query, 'CONCAT(tutor_first_name, " ", tutor_last_name) AS `Name`,');
        END IF;
        IF show_rooms = true THEN
                SET @query = CONCAT (@query, 'CONCAT(building_name, " ", place_room_number) AS `Location`,');
        END IF;
        SET @query = CONCAT(@query, '
        CONCAT(slots.subject_code, " ", slots.class_number) AS `Class`,
        CONCAT(time_block_start, " - ", time_block_end, " ", time_blocks.week_day_name) AS `Time Tutored`,
        CONCAT(term_code, " ", year_term_year) AS `Semester Taught`
        FROM tutors
        INNER JOIN slots ON (tutors.tutor_id = slots.tutor_id)
        INNER JOIN time_blocks ON (time_blocks.time_block_id = slots.time_block_id)
        ');

        SET @w = '';
        SET @need_and = false;
        IF all_students = false OR all_subjects = false  OR all_terms = false THEN
                SET @w = 'WHERE ';
        END IF;
        IF all_students = false THEN
                SET @w = CONCAT(@w, ' tutors.tutor_first_name = ? AND tutors.tutor_last_name = ? ');
                SET @need_and = true;
        END IF;
        IF all_subjects = false THEN
                IF @need_and = true THEN
                        SET @w = CONCAT(@w, ' AND ');      
                END IF;
                SET @w = CONCAT(@w, ' slots.subject_code = ? ');
                SET @need_and = true;
        END IF;
        IF all_classes = false THEN
                IF @need_and = true THEN
                        SET @w = CONCAT(@w, ' AND ');      
                END IF;
                SET @w = CONCAT(@w, ' slots.class_number = ?');
                SET @need_and = true;
        END IF;
        IF all_terms = false THEN
                IF @need_and = true THEN
                        SET @w = CONCAT(@w, ' AND ');      
                END IF;
                SET @w = CONCAT(@w, 'term_code = ? AND year_term_year = ?');
        END IF;

        SET @query = CONCAT(@query, @w, ';');

        PREPARE stmt FROM @query;

        IF all_students = false AND all_subjects = false AND all_classes = false AND all_terms = false THEN
            EXECUTE stmt USING first_name, last_name, subject_code, class_number, term, t_year;
        ELSEIF all_students = false AND all_subjects = false AND all_classes = false THEN
            EXECUTE stmt USING first_name, last_name, subject_code, class_number;
        ELSEIF all_students = false AND all_subjects = false AND all_terms = false THEN
            EXECUTE stmt USING first_name, last_name, subject_code, term, t_year;
        ELSEIF all_students = false AND all_subjects = false THEN
            EXECUTE stmt USING first_name, last_name, subject_code;
        ELSEIF all_students = false AND all_terms = false THEN
            EXECUTE stmt USING first_name, last_name, term, t_year;
        ELSEIF all_subjects = false AND all_classes = false AND all_terms = false THEN
            EXECUTE stmt USING subject_code, class_number, term, t_year;
        ELSEIF all_subjects = false AND all_classes = false THEN
            EXECUTE stmt USING subject_code, class_number; 
        ELSEIF all_subjects = false AND all_terms = false THEN
            EXECUTE stmt USING subject_code, term, t_year; 
        ELSEIF all_students = false THEN
            EXECUTE stmt USING first_name, last_name;      
        ELSEIF all_subjects = false THEN
            EXECUTE stmt USING subject_code;
        ELSEIF all_terms = false THEN
            EXECUTE stmt USING term, t_year;
        ELSE
            EXECUTE stmt;
        END IF;

        DEALLOCATE PREPARE stmt;
END //

DELIMITER ;

DROP PROCEDURE IF EXISTS tutor_agreement_form_view;

DELIMITER //
CREATE PROCEDURE tutor_agreement_form_view(
    IN first_name VARCHAR(30),
    IN last_name VARCHAR(30),
    IN all_students BOOLEAN
)
BEGIN
    SET @query =
    'SELECT CONCAT(tutors.tutor_first_name, " ", tutors.tutor_last_name) AS `Name`,
    tutors.tutor_id AS `Student ID`,
    GROUP_CONCAT(DISTINCT CONCAT(tutor_agreed_classes.subject_code, " ", tutor_agreed_classes.class_number)) AS `Tutor Agreed Classes`,
    GROUP_CONCAT(DISTINCT tutor_qualified_subjects.subject_code) AS `Tutor Qualified Subjects`
    FROM tutors
    LEFT JOIN tutor_agreed_classes ON (tutors.tutor_id = tutor_agreed_classes.tutor_id)
    LEFT JOIN tutor_qualified_subjects ON (tutors.tutor_id = tutor_qualified_subjects.tutor_id)';

    IF all_students = FALSE THEN
        SET @query = CONCAT(@query, ' WHERE tutors.tutor_first_name = ? AND tutors.tutor_last_name = ?');
    END IF;

    SET @query = CONCAT(@query, ' GROUP BY tutors.tutor_id;');

    PREPARE stmt FROM @query;

    IF all_students = FALSE THEN
        EXECUTE stmt USING first_name, last_name;
    ELSE
        EXECUTE stmt;
    END IF;

    DEALLOCATE PREPARE stmt;
END //

DELIMITER ;

--Grant permissions
GRANT EXECUTE ON PROCEDURE tutor_agreement_form_view TO 'webuser'@'localhost';
GRANT EXECUTE ON PROCEDURE tutor_schedule_view TO 'webuser'@'localhost';
GRANT EXECUTE ON PROCEDURE tutor_history_view TO 'webuser'@'localhost';
GRANT EXECUTE ON PROCEDURE clc_tutoring.deny_classes TO 'webuser'@'localhost';
GRANT EXECUTE ON PROCEDURE clc_tutoring.deny_slots TO 'webuser'@'localhost';
GRANT EXECUTE ON PROCEDURE clc_tutoring.deny_tutors TO 'webuser'@'localhost';

--Permissions for testing
GRANT EXECUTE ON PROCEDURE clc_tutoring_test.deny_classes TO 'webuser'@'localhost';
GRANT EXECUTE ON PROCEDURE clc_tutoring_test.deny_slots TO 'webuser'@'localhost';
GRANT EXECUTE ON PROCEDURE clc_tutoring_test.tutors TO 'webuser'@'localhost';
