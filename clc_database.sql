DROP DATABASE IF EXISTS clc_tutoring;

CREATE DATABASE clc_tutoring;

USE clc_tutoring;

CREATE TABLE subjects
(
    subject_code     VARCHAR(3) NOT NULL,
    subject_name     VARCHAR(32) NOT NULL,
    PRIMARY KEY      (subject_code)
);

CREATE TABLE week_days
(
    week_day_name        VARCHAR(1) NOT NULL,
    PRIMARY KEY     (week_day_name)
);

CREATE TABLE buildings
(
    building_name   VARCHAR(7),
    PRIMARY KEY     (building_name)
);

CREATE TABLE classes
(
    subject_code     VARCHAR(3) NOT NULL,
    class_number     INT unsigned NOT NULL,
    class_name       VARCHAR(32) NOT NULL,
    FOREIGN KEY      (subject_code) references subjects(subject_code) ON DELETE RESTRICT,
    PRIMARY KEY      (subject_code, class_number)
);

CREATE TABLE slots
(
    slot_id           INT unsigned NOT NULL AUTO_INCREMENT,
    building_name     VARCHAR(7),
    subject_code      VARCHAR(3) NOT NULL,
    class_number      INT unsigned NOT NULL,
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
    week_day_name          VARCHAR(8) NOT NULL,
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
    PRIMARY KEY     (tutor_id)             
);

CREATE TABLE tutor_agreed_classes(
    tutor_id        INT NOT NULL,
    subject_code    INT NOT NULL,
    class_number    INT NOT NULL,
    PRIMARY KEY     (tutor_id, subject_code, class_number),
    FOREIGN KEY     (tutor_id) REFERENCES tutors(tutor_id),
    FOREIGN KEY     (subject_code,class_number) REFERENCES classes(subject_code,class_number)
);

CREATE TABLE tutor_availibilities(
    tutor_id        INT NOT NULL,
    time_id        INT NOT NULL,
    PRIMARY KEY     (tutor_id, time_id),
    FOREIGN KEY     (tutor_id) REFERENCES tutors(tutor_id),
    FOREIGN KEY     (time_id) REFERENCES time_blocks(time_id) ON DELETE RESTRICT
);

CREATE TABLE tutor_qualified_subjects(
    tutor_id        INT NOT NULL,
    subject_code    CHAR(3) NOT NULL,
    PRIMARY KEY     (tutor_id, subject_code),
    FOREIGN KEY     (tutor_id) REFERENCES tutors,
    FOREIGN KEY     (subject_code) REFERENCES subjects(subject_code) ON DELETE RESTRICT
);

CREATE TABLE terms(
    term_code       char(2) NOT NULL,
    PRIMARY KEY     (term_code)
);

CREATE TABLE year_terms(
    term_id         INT NOT NULL,
    term_code       CHAR(3) NOT NULL,
    term_year       INT NOT NULL,
    PRIMARY KEY     (term_id),
    FOREIGN KEY     (term_code) REFERENCES terms ON DELETE RESTRICT
);


CREATE TABLE slot_terms(
    slot_id         INT NOT NULL,
    term_id         CHAR(6) NOT NULL,
    PRIMARY KEY     (slot_id, term_id),
    FOREIGN KEY     (slot_id) REFERENCES slots,
    FOREIGN KEY     (term_id) REFERENCES year_terms(term_id) ON DELETE RESTRICT
);

CREATE TABLE slot_tutors(
    slot_id         INT NOT NULL,
    tutor_id        INT NOT NULL,
    PRIMARY KEY     (tutor_id, slot_id),
    FOREIGN KEY     (tutor_id) REFERENCES tutors,
    FOREIGN KEY     (slot_id) REFERENCES slots(slot_id)
);

INSERT INTO terms(term_code) VALUES
("FA"),
("SP"),
("WI"),
("SU");

INSERT INTO buildings(building_name) VALUES
("Olin"),
("Young"),
("Crounse"),
("Grant");

INSERT INTO week_days(week_day_name) VALUES
("M"),
("T"),
("W"),
("TH"),
("F"),
("S"),
("Su");

INSERT INTO subjects(subject_code, subject_name) VALUES
("MAT", "Mathematics"),
("ENS", "Environmental Science"),
("ARS", "Studio Art"),
("CSC", "Computer Science");

INSERT INTO year_terms(term_code, term_year) VALUES
("FA", 2024),
("SP", 2024),
("WI", 2025),
("SU", 2025),
("FA", 2025);

INSERT INTO classes(subject_code, class_number, class_name) VALUES
("MAT", 330),
("CSC", 332),
("ARS", 250),
("ENS", 110);

INSERT INTO tutors(tutor_id, tutor_first_name, tutor_last_name, tutor_email) VALUES
(380932, "Hannah", "Morrison", "hannah.morrison@centre.edu");

INSERT INTO SLOTS(building_name, subject_code,class_number,room_number) VALUES
("Crounse", "MAT", "330", 215);

INSERT INTO time_blocks(time_start, time_end, week_day_name) VALUES
("7:00", "8:00", "Su");

INSERT INTO tutor_agreed_classes(tutor_id,subject_code, class_number) VALUES
(380932, "MAT", 330);

INSERT INTO tutor_qualified_subjects(tutor_id, subject_code) VALUES
(380932, "MAT");

INSERT INTO slot_terms(slot_id,term_id) VALUES 
(1,2);

INSERT INTO slot_times(slot_id, time_id) VALUES
(1,1);

INSERT INTO slot_tutors(slot_id, tutor_id) VALUES
(1,380932);

INSERT INTO tutor_availibilities(tutor_id, time_id) VALUES
(380932,1);







