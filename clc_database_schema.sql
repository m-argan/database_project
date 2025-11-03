CREATE TABLE subjects
(
    subject_code     CHAR(3) NOT NULL,
    subject_name     VARCHAR(32) NOT NULL,
    PRIMARY KEY      (subject_code)
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

CREATE TABLE week_days
(
    week_day_name   VARCHAR(8) NOT NULL, 
    PRIMARY KEY     (week_day_name)
);

CREATE TABLE buildings
(
    building_name   VARCHAR(7),
    PRIMARY KEY     (building_name)
);

CREATE TABLE slots
(
    deleted_when      TIMESTAMP DEFAULT 0,
    slot_id           INT unsigned NOT NULL AUTO_INCREMENT,
    building_name     VARCHAR(7) DEFAULT "NA", /* Default value */
    subject_code      CHAR(3) NOT NULL,
    class_number      INT unsigned NOT NULL,
    room_number       INT,
    FOREIGN KEY       (building_name) references buildings(building_name) ON DELETE SET DEFAULT, 
    FOREIGN KEY       (subject_code, class_number) references classes(subject_code, class_number), /* ON DELETE TRIGGER DENY */
    PRIMARY KEY       (slot_id),
    CONSTRAINT correct_room_num CHECK (room_number BETWEEN 0 AND 600)
);

CREATE TABLE time_blocks
(
    time_id           INT unsigned NOT NULL AUTO_INCREMENT,
    time_start        VARCHAR(32) NOT NULL,
    time_end          VARCHAR(32) NOT NULL,
    week_day_name     VARCHAR(8) NOT NULL,
    FOREIGN KEY       (week_day_name) references week_days(week_day_name) ON DELETE RESTRICT,
    PRIMARY KEY       (time_id)
);


CREATE TABLE slot_times
(
    slot_id         INT unsigned NOT NULL,
    time_id         INT unsigned NOT NULL,
    FOREIGN KEY     (slot_id) references slots(slot_id), /* ON DELETE TRIGGER DENY */
    FOREIGN KEY     (time_id) references time_blocks(time_id) ON DELETE RESTRICT,
    PRIMARY KEY     (slot_id, time_id)
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
    term_id         INT NOT NULL,
    term_code       CHAR(2) NOT NULL, 
    term_year       INT NOT NULL,
    PRIMARY KEY     (term_id),
    FOREIGN KEY     (term_code) REFERENCES terms(term_code) ON DELETE RESTRICT
);


CREATE TABLE slot_terms(
    slot_id         INT unsigned NOT NULL,
    term_id         INT NOT NULL,
    PRIMARY KEY     (slot_id, term_id),
    FOREIGN KEY     (slot_id) REFERENCES slots(slot_id), /* ON DELETE TRIGGER DENY */
    FOREIGN KEY     (term_id) REFERENCES year_terms(term_id) ON DELETE RESTRICT
);

CREATE TABLE slot_tutors(
    slot_id         INT unsigned NOT NULL,
    tutor_id        INT NOT NULL,
    PRIMARY KEY     (tutor_id, slot_id),
    FOREIGN KEY     (tutor_id) REFERENCES tutors(tutor_id), /* ON DELETE TRIGGER DENY */
    FOREIGN KEY     (slot_id) REFERENCES slots(slot_id)     /* ON DELETE TRIGGER DENY */
);

CREATE TABLE tutor_availibilities(
    tutor_id        INT NOT NULL,
    time_id         INT unsigned NOT NULL,
    PRIMARY KEY     (tutor_id, time_id),
    FOREIGN KEY     (tutor_id) REFERENCES tutors(tutor_id), /* ON DELETE TRIGGER DENY */
    FOREIGN KEY     (time_id) REFERENCES time_blocks(time_id) ON DELETE RESTRICT
);
