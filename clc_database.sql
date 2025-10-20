DROP DATABASE IF EXISTS clc_tutoring;

CREATE DATABASE clc_tutoring;

USE clc_tutoring;

CREATE TABLE slots
(
    slot_id           INT unsigned NOT NULL AUTO_INCREMENT,
    building_name     VARCHAR(7),
    subject_code      INT,
    class_number      INT,
    tutor_id          INT,
    room_number       INT,
    slot_year         INT NOT NULL,
    slot_term         VARCHAR(3) NOT NULL
    FOREIGN KEY       (building_name) references buildings(building_name),
    FOREIGN KEY       (subject_code, class_number) references classes(subject_code, class_number),
    FOREIGN KEY       (tutor_id) references tutors(tutor_id),
    PRIMARY KEY       (slot_id)
);
CREATE TABLE times
(
    time_id           INT unsigned NOT NULL AUTO_INCREMENT,
    time_start_time   VARCHAR(32) NOT NULL,
    time_end_time     VARCHAR(32) NOT NULL,
    day_name          VARCHAR(8) NOT NULL,
    FOREIGN KEY       (day_name) references days(day_name) ON DELETE RESTRICT,
    PRIMARY KEY       (time_id)
);


CREATE TABLE schedules
(
    slot_id         INT unsigned NOT NULL,
    time_id         INT unsigned NOT NULL,
    FOREIGN KEY    (slot_id) references slots(slot_id),
    FOREIGN KEY    (time_id) references times(time_id) ON DELETE RESTRICT,
    PRIMARY KEY    (slot_id, time_id)
);

CREATE TABLE classes
(
    subject_code     VARCHAR(3) NOT NULL,
    class_number     INT unsigned NOT NULL,
    class_name       VARCHAR(32) NOT NULL,
    FOREIGN KEY      (subject_code) references subjects(subject_code) ON DELETE RESTRICT,
    PRIMARY KEY      (subject_code, class_number)
);

CREATE TABLE subjects
(
    subject_code     VARCHAR(3) NOT NULL,
    subject_name     VARCHAR(32) NOT NULL,
    PRIMARY KEY      (subject_code)
);

CREATE TABLE days
(
    day_name        VARCHAR(1) NOT NULL,
    PRIMARY KEY     (day_name)
);

CREATE TABLE buildings
(
    building_name   VARCHAR(7),
    PRIMARY KEY     (building_name)
);

CREATE TABLE tutors(
    PRIMARY KEY     (tutor_id);                 --Still needs deny rule implemented
    tutor_id            INT NOT NULL,
    tutor_first_name    VARCHAR(30) NOT NULL,
    tutor_last_name     VARCHAR(30) NOT NULL,
    tutor_email         VARCHAR(80) NOT NULL,
);

CREATE TABLE tutor_course_agreements(
    tutor_id        INT NOT NULL,
    subject_code    INT NOT NULL,
    class_number    INT NOT NULL,
    PRIMARY KEY     (tutor_id, subject_code, class_number),
    FOREIGN KEY     (tutor_id) REFERENCES tutors(tutor_id),
    FOREIGN KEY     (subject_code,class_number) REFERENCES classes(subject_code,class_number)
);

CREATE TABLE tutor_availibility(
    tutor_id        INT NOT NULL,
    time_id        INT NOT NULL,
    PRIMARY KEY     (tutor_id, time_id),
    FOREIGN KEY     (tutor_id) REFERENCES tutors(tutor_id),
    FOREIGN KEY     (time_id) REFERENCES times(time_id)
);

CREATE TABLE tutor_qualified_subjects(
    tutor_id        INT NOT NULL,
    subject_code    CHAR(3) NOT NULL,
    PRIMARY KEY     (tutor_id, subject_code),
    FOREIGN KEY     (tutor_id) REFERENCES tutors,
    FOREIGN KEY     (subject_code) REFERENCES subjects(subject_code)
);





