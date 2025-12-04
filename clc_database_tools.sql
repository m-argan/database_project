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
    INNER JOIN time_blocks ON (time_blocks.time_block_id = slots.time_block_id)
    WHERE tutors.deleted_when = "0000-00-00 00:00:00"';
    IF all_students = false THEN
        SET @query = CONCAT(@query, ' AND tutors.tutor_first_name = ? AND tutors.tutor_last_name = ?');     
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
        SET @query = 'SELECT slots.slot_id, ';  -- Add slot_id

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
        WHERE tutors.deleted_when = "0000-00-00 00:00:00"
        AND slots.deleted_when = "0000-00-00 00:00:00"
        ');

        SET @w = '';
        SET @need_and = false;
        IF all_students = false OR all_subjects = false  OR all_terms = false THEN
                SET @w = 'AND ';
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
                SET @w = CONCAT(@w, ' term_code = ? AND year_term_year = ?');
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
    LEFT JOIN tutor_qualified_subjects ON (tutors.tutor_id = tutor_qualified_subjects.tutor_id)
    WHERE tutors.deleted_when = "0000-00-00 00:00:00"';

    IF all_students = FALSE THEN
        SET @query = CONCAT(@query, ' AND tutors.tutor_first_name = ? AND tutors.tutor_last_name = ?');
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

--View Developed by writing, then asking Gemini AI for improvements
DROP PROCEDURE IF EXISTS calendar_pivot_view;
DELIMITER //

DROP PROCEDURE IF EXISTS calendar_pivot_view;
DELIMITER //

CREATE PROCEDURE calendar_pivot_view(
    IN subject_code CHAR(3),
    IN class_number INT,
    IN all_subjects BOOLEAN,
    IN all_classes BOOLEAN
)
BEGIN
    DECLARE current_month INT;
    DECLARE current_year INT;
    DECLARE current_term VARCHAR(2);
    DECLARE query_statement VARCHAR(4000);

    -- Displays current semester using current_date function
    SET current_month = MONTH(CURRENT_DATE());
    SET current_year = YEAR(CURRENT_DATE());
    IF current_month BETWEEN 2 AND 5 THEN
        SET current_term = 'SP';
    ELSEIF current_month BETWEEN 6 AND 7 THEN
        SET current_term = 'SU';
    ELSEIF current_month BETWEEN 8 AND 12 THEN
        SET current_term = 'FA';
    ELSE
        SET current_term = 'WI';
    END IF;

    -- Create the base query statement
    SET query_statement = '
        WITH NumberedSlots AS (
            SELECT
                ROW_NUMBER() OVER(PARTITION BY time_blocks.week_day_name ORDER BY time_blocks.time_block_start) AS row_num,
                CONCAT(
                    slots.subject_code, " ", slots.class_number, CHAR(10),
                    time_blocks.time_block_start, " - ", time_blocks.time_block_end, CHAR(10),
                    slots.building_name, " ", slots.place_room_number
                ) AS slot_details,
                time_blocks.week_day_name
            FROM slots
            INNER JOIN time_blocks ON slots.time_block_id = time_blocks.time_block_id
            WHERE term_code = ? AND year_term_year = ?';

    -- Conditionally add WHERE clauses for filtering
    IF all_subjects = false THEN
        SET query_statement = CONCAT(query_statement, ' AND subject_code = ?');
        IF all_classes = false THEN
                SET query_statement = CONCAT(query_statement, ' AND class_number = ?');
        END IF;
END IF;

    -- Complete the query with the pivot logic
    SET query_statement = CONCAT(query_statement, '
        )
        SELECT
            MAX(CASE WHEN week_day_name = "Su" THEN slot_details END) AS `Sunday`,
            MAX(CASE WHEN week_day_name = "M" THEN slot_details END) AS `Monday`,
            MAX(CASE WHEN week_day_name = "T" THEN slot_details END) AS `Tuesday`,
            MAX(CASE WHEN week_day_name = "W" THEN slot_details END) AS `Wednesday`,
            MAX(CASE WHEN week_day_name = "TH" THEN slot_details END) AS `Thursday`
        FROM NumberedSlots
        GROUP BY row_num
        ORDER BY row_num;');

    -- Prepare and execute the statement with correct parameter handling
    PREPARE stmt FROM query_statement;

    IF all_subjects = false THEN
        IF all_classes = false THEN
            EXECUTE stmt USING current_term, current_year, subject_code, class_number;
        ELSE
            EXECUTE stmt USING current_term, current_year, subject_code;
        END IF;
    ELSE
        EXECUTE stmt USING current_term, current_year;
    END IF;

    DEALLOCATE PREPARE stmt;
END //

DELIMITER ;

--Grant permissions
GRANT EXECUTE ON PROCEDURE clc_tutoring.calendar_pivot_view TO 'webuser'@'localhost';
GRANT EXECUTE ON PROCEDURE clc_tutoring.tutor_agreement_form_view TO 'webuser'@'localhost';
GRANT EXECUTE ON PROCEDURE clc_tutoring.tutor_schedule_view TO 'webuser'@'localhost';
GRANT EXECUTE ON PROCEDURE clc_tutoring.tutor_history_view TO 'webuser'@'localhost';
GRANT EXECUTE ON PROCEDURE clc_tutoring.deny_classes TO 'webuser'@'localhost';
GRANT EXECUTE ON PROCEDURE clc_tutoring.deny_slots TO 'webuser'@'localhost';
GRANT EXECUTE ON PROCEDURE clc_tutoring.deny_tutors TO 'webuser'@'localhost';

-- Procedure to check if the clc_tutoring_test exists
DELIMITER $$

CREATE PROCEDURE grant_perm_if_exists()
BEGIN
    IF EXISTS (
        SELECT 1 FROM INFORMATION_SCHEMA.SCHEMATA
        WHERE SCHEMA_NAME = 'clc_tutoring_test'
    ) THEN
        GRANT EXECUTE ON PROCEDURE clc_tutoring_test.deny_classes
            TO 'webuser'@'localhost';
        GRANT EXECUTE ON PROCEDURE clc_tutoring_test.deny_slots TO 'webuser'@'localhost';
        GRANT EXECUTE ON PROCEDURE clc_tutoring_test.deny_tutors TO 'webuser'@'localhost';
    END IF;
END$$

DELIMITER ;

CALL grant_perm_if_exists();
