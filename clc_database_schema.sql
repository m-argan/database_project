CREATE TABLE subjects
(
    subject_code     VARCHAR(3) NOT NULL,
    subject_name     VARCHAR(32) NOT NULL,
    PRIMARY KEY      (subject_code)
);

CREATE TABLE classes
(
    subject_code     VARCHAR(3) NOT NULL,
    class_number     INT unsigned NOT NULL,
    class_name       VARCHAR(32) NOT NULL,
    FOREIGN KEY      (subject_code) references subjects(subject_code) ON DELETE RESTRICT,
    PRIMARY KEY      (subject_code, class_number)
);

CREATE TABLE week_days
(
    week_day_name   VARCHAR(8) NOT NULL,  /*made this VARCHAR(8)*/
    PRIMARY KEY     (week_day_name)
);

CREATE TABLE buildings
(
    building_name   VARCHAR(7),
    PRIMARY KEY     (building_name)
);

CREATE TABLE slots
(
    slot_id           INT unsigned NOT NULL AUTO_INCREMENT,
    building_name     VARCHAR(7),
    subject_code      VARCHAR(3) NOT NULL,/*made this varchar(3)*/
    class_number      INT unsigned NOT NULL, /*made this unsigned*/
    room_number       INT,
    FOREIGN KEY       (building_name) references buildings(building_name),
    FOREIGN KEY       (subject_code, class_number) references classes(subject_code, class_number),
    PRIMARY KEY       (slot_id)
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
    FOREIGN KEY     (slot_id) references slots(slot_id),
    FOREIGN KEY     (time_id) references time_blocks(time_id) ON DELETE RESTRICT,
    PRIMARY KEY     (slot_id, time_id)
);

CREATE TABLE tutors(
    tutor_id            INT NOT NULL,
    tutor_first_name    VARCHAR(30) NOT NULL,
    tutor_last_name     VARCHAR(30) NOT NULL,
    tutor_email         VARCHAR(80) NOT NULL,
    PRIMARY KEY         (tutor_id)  /*Still needs deny rule implemented*/         
);

CREATE TABLE tutor_agreed_classes(
    tutor_id        INT NOT NULL,
    subject_code    VARCHAR(3) NOT NULL, /* changed to VARCHAR(3)*/
    class_number    INT unsigned NOT NULL, /*added unsigned*/
    PRIMARY KEY     (tutor_id, subject_code, class_number),
    FOREIGN KEY     (tutor_id) REFERENCES tutors(tutor_id),
    FOREIGN KEY     (subject_code,class_number) REFERENCES classes(subject_code,class_number)
);

CREATE TABLE tutor_qualified_subjects(
    tutor_id        INT NOT NULL,
    subject_code    VARCHAR(3) NOT NULL, /*changed to VARCHAR instead of CHAR*/
    PRIMARY KEY     (tutor_id, subject_code),
    FOREIGN KEY     (tutor_id) REFERENCES tutors(tutor_id),
    FOREIGN KEY     (subject_code) REFERENCES subjects(subject_code) ON DELETE RESTRICT
);

CREATE TABLE terms(
    term_code       CHAR(2) NOT NULL, 
    PRIMARY KEY     (term_code)
);

CREATE TABLE year_terms(
    term_id         INT NOT NULL,
    term_code       CHAR(2) NOT NULL, /*changed to char(2)*/
    term_year       INT NOT NULL,
    PRIMARY KEY     (term_id),
    FOREIGN KEY     (term_code) REFERENCES terms(term_code) ON DELETE RESTRICT
);


CREATE TABLE slot_terms(
    slot_id         INT unsigned NOT NULL, /*changed to unsigned*/
    term_id         INT NOT NULL, /*changed to int not null*/
    PRIMARY KEY     (slot_id, term_id),
    FOREIGN KEY     (slot_id) REFERENCES slots(slot_id),
    FOREIGN KEY     (term_id) REFERENCES year_terms(term_id) ON DELETE RESTRICT
);

CREATE TABLE slot_tutors(
    slot_id         INT unsigned NOT NULL, /*changed to unsigned*/
    tutor_id        INT NOT NULL,
    PRIMARY KEY     (tutor_id, slot_id),
    FOREIGN KEY     (tutor_id) REFERENCES tutors(tutor_id),
    FOREIGN KEY     (slot_id) REFERENCES slots(slot_id)
);

CREATE TABLE tutor_availibilities(
    tutor_id        INT NOT NULL,
    time_id         INT unsigned NOT NULL, /*added unsigned*/
    PRIMARY KEY     (tutor_id, time_id),
    FOREIGN KEY     (tutor_id) REFERENCES tutors(tutor_id),
    FOREIGN KEY     (time_id) REFERENCES time_blocks(time_id) ON DELETE RESTRICT
);
