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


