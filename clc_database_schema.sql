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

CREATE TABLE tutor_availabilities(
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

