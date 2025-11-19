CREATE TABLE subjects
(
    subject_code     CHAR(3) NOT NULL,
    subject_name     VARCHAR(32) NOT NULL,
    PRIMARY KEY      (subject_code)
);


CREATE TABLE week_days
(
    week_day_name   VARCHAR(8) NOT NULL, 
    PRIMARY KEY     (week_day_name)
);

CREATE TABLE buildings
(
    building_name   VARCHAR(7) NOT NULL,
    PRIMARY KEY     (building_name)
);

CREATE TABLE places
(
    building_name   VARCHAR(7) NOT NULL,
    place_room_number     INT unsigned NOT NULL,
    FOREIGN KEY     (building_name) references buildings(building_name),
    PRIMARY KEY     (building_name, place_room_number)
);

CREATE TABLE classes
(
    deleted_when     TIMESTAMP DEFAULT 0,
    subject_code     CHAR(3) NOT NULL,
    class_number     INT unsigned NOT NULL,
    class_name       VARCHAR(32) NOT NULL,
    FOREIGN KEY      (subject_code) references subjects(subject_code) ON DELETE RESTRICT,
    PRIMARY KEY      (subject_code, class_number),
    CONSTRAINT correct_code_num CHECK (class_number BETWEEN 100 AND 500)
);

CREATE TABLE tutors(
    deleted_when        TIMESTAMP DEFAULT 0,
    tutor_id            INT NOT NULL,
    tutor_first_name    VARCHAR(30) NOT NULL,
    tutor_last_name     VARCHAR(30) NOT NULL,
    tutor_email         VARCHAR(80) NOT NULL,
    PRIMARY KEY         (tutor_id)          
);

CREATE TABLE tutor_agreed_classes(
    tutor_id        INT NOT NULL,
    subject_code    CHAR(3) NOT NULL,
    class_number    INT unsigned NOT NULL,
    PRIMARY KEY     (tutor_id, subject_code, class_number), 
    FOREIGN KEY     (tutor_id) REFERENCES tutors(tutor_id), /* ON DELETE TRIGGER DENY */
    FOREIGN KEY     (subject_code,class_number) REFERENCES classes(subject_code,class_number) /* ON DELETE TRIGGER DENY */
);

CREATE TABLE tutor_qualified_subjects(
    tutor_id        INT NOT NULL,
    subject_code    CHAR(3) NOT NULL,
    PRIMARY KEY     (tutor_id, subject_code),
    FOREIGN KEY     (tutor_id) REFERENCES tutors(tutor_id), /* ON DELETE TRIGGER DENY */
    FOREIGN KEY     (subject_code) REFERENCES subjects(subject_code) ON DELETE RESTRICT
);

CREATE TABLE terms(
    term_code       CHAR(2) NOT NULL, 
    PRIMARY KEY     (term_code)
);

CREATE TABLE year_terms(
    term_code       CHAR(2) NOT NULL, 
    year_term_year       INT NOT NULL,
    PRIMARY KEY     (term_code, year_term_year),
    FOREIGN KEY     (term_code) REFERENCES terms(term_code) ON DELETE RESTRICT
);


CREATE TABLE time_blocks
(
    time_block_id           INT unsigned NOT NULL AUTO_INCREMENT,
    time_block_start        VARCHAR(32) NOT NULL,
    time_block_end          VARCHAR(32) NOT NULL,
    week_day_name     VARCHAR(8) NOT NULL,
    term_code          VARCHAR(2),
    year_term_year      INT unsigned NOT NULL,
    FOREIGN KEY       (week_day_name) references week_days(week_day_name) ON DELETE RESTRICT,
    PRIMARY KEY       (time_block_id)
);

CREATE TABLE tutor_availibilities(
    tutor_id        INT NOT NULL,
    time_block_id         INT unsigned NOT NULL,
    PRIMARY KEY     (tutor_id, time_block_id),
    FOREIGN KEY     (tutor_id) REFERENCES tutors(tutor_id), /* ON DELETE TRIGGER DENY */
    FOREIGN KEY     (time_block_id) REFERENCES time_blocks(time_block_id) ON DELETE RESTRICT
);

CREATE TABLE slots
(
    deleted_when      TIMESTAMP DEFAULT 0,
    slot_id           INT unsigned NOT NULL AUTO_INCREMENT,
    time_block_id     INT unsigned NOT NULL,
    building_name     VARCHAR(7) NOT NULL DEFAULT "NA",
    place_room_number       INT unsigned NOT NULL,
    subject_code      CHAR(3) NOT NULL,
    class_number      INT unsigned NOT NULL,
    tutor_id          INT NOT NULL,
    FOREIGN KEY (building_name, place_room_number) REFERENCES places(building_name, place_room_number),
    FOREIGN KEY (subject_code, class_number) REFERENCES classes(subject_code, class_number),
    FOREIGN KEY (time_block_id) REFERENCES time_blocks(time_block_id),
    FOREIGN KEY (tutor_id) REFERENCES tutors(tutor_id),
    PRIMARY KEY (slot_id),
    CONSTRAINT correct_room_num CHECK (place_room_number BETWEEN 0 AND 600)
);

DELIMITER //

CREATE TRIGGER prevent_slot_overlap
BEFORE INSERT ON slots
FOR EACH ROW
BEGIN
    DECLARE overlap_count INT DEFAULT 0;

    SELECT COUNT(s.slot_id)
    INTO overlap_count
    FROM slots AS s
    JOIN time_blocks AS tb_existing
        ON s.time_block_id = tb_existing.time_block_id
    JOIN time_blocks AS tb_new
        ON tb_new.time_block_id = NEW.time_block_id
    WHERE
        s.deleted_when = 0
        AND s.building_name = NEW.building_name
        AND s.place_room_number = NEW.place_room_number

        AND tb_existing.week_day_name = tb_new.week_day_name
        AND tb_existing.term_code = tb_new.term_code
        AND tb_existing.year_term_year = tb_new.year_term_year

        AND STR_TO_DATE(tb_new.time_block_start, '%H:%i')
              < STR_TO_DATE(tb_existing.time_block_end, '%H:%i')
        AND STR_TO_DATE(tb_new.time_block_end, '%H:%i')
              > STR_TO_DATE(tb_existing.time_block_start, '%H:%i');

    IF overlap_count > 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Time slot overlaps an existing slot in this room.';
    END IF;

END //

DELIMITER ;


DELIMITER //

CREATE TRIGGER tutor_qualifications
BEFORE INSERT ON tutor_agreed_classes
FOR EACH ROW
BEGIN
    DECLARE qualified INT DEFAULT 0;

    SELECT COUNT(*)
    INTO qualified
    FROM tutor_qualified_subjects
    WHERE tutor_id = NEW.tutor_id
      AND TRIM(subject_code) = TRIM(NEW.subject_code);

    IF qualified = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Tutor is unqualified.';
    END IF;

END //

DELIMITER ;


DELIMITER //

CREATE TRIGGER prevent_tutor_time_overlap
BEFORE INSERT ON slots
FOR EACH ROW
BEGIN
    DECLARE overlap_count INT DEFAULT 0;

    SELECT COUNT(s.slot_id)
    INTO overlap_count
    FROM slots AS s
    JOIN time_blocks AS tb_existing
        ON s.time_block_id = tb_existing.time_block_id
    JOIN time_blocks AS tb_new
        ON tb_new.time_block_id = NEW.time_block_id
    WHERE
        s.deleted_when = 0
        AND s.tutor_id = NEW.tutor_id

        -- same weekday / term / year
        AND tb_existing.week_day_name = tb_new.week_day_name
        AND tb_existing.term_code = tb_new.term_code
        AND tb_existing.year_term_year = tb_new.year_term_year

        -- time overlap condition
        AND STR_TO_DATE(tb_new.time_block_start, '%H:%i')
              < STR_TO_DATE(tb_existing.time_block_end, '%H:%i')
        AND STR_TO_DATE(tb_new.time_block_end, '%H:%i')
              > STR_TO_DATE(tb_existing.time_block_start, '%H:%i');

    IF overlap_count > 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Tutor already has a conflicting time slot.';
    END IF;

END //

DELIMITER ;

DELIMITER //

CREATE TRIGGER prevent_tutor_time_overlap_update
BEFORE UPDATE ON slots
FOR EACH ROW
BEGIN
    DECLARE overlap_count INT DEFAULT 0;

    SELECT COUNT(s.slot_id)
    INTO overlap_count
    FROM slots AS s
    JOIN time_blocks AS tb_existing
        ON s.time_block_id = tb_existing.time_block_id
    JOIN time_blocks AS tb_new
        ON tb_new.time_block_id = NEW.time_block_id
    WHERE
        s.deleted_when = 0
        
        -- Same tutor (ignore the current row being updated)
        AND s.tutor_id = NEW.tutor_id
        AND s.slot_id <> OLD.slot_id

        -- Same weekday / term / year
        AND tb_existing.week_day_name = tb_new.week_day_name
        AND tb_existing.term_code = tb_new.term_code
        AND tb_existing.year_term_year = tb_new.year_term_year

        -- Time overlap logic
        AND STR_TO_DATE(tb_new.time_block_start, '%H:%i')
              < STR_TO_DATE(tb_existing.time_block_end, '%H:%i')
        AND STR_TO_DATE(tb_new.time_block_end, '%H:%i')
              > STR_TO_DATE(tb_existing.time_block_start, '%H:%i');

    IF overlap_count > 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Tutor update creates a time conflict.';
    END IF;

END //

DELIMITER ;
