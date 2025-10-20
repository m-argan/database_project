DROP DATABASE IF EXISTS clc_tutoring;

CREATE DATABASE clc_tutoring;

USE clc_tutoring;

CREATE TABLE slots
(
    slot_id           INT unsigned NOT NULL AUTO_INCREMENT,
    building_name     VARCHAR(7),
    class_id          INT,
    tutor_id          INT,
    room_number       INT,
    slot_year         INT NOT NULL,
    slot_term         VARCHAR(3) NOT NULL
    FOREIGN KEY       (building_name) references buildings(building_name),
    FOREIGN KEY       (class_id) references classes(class_id),
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
    class_id         INT unsigned NOT NULL AUTO_INCREMENT,
    subject_code     VARCHAR(3) NOT NULL,
    class_number     INT unsigned NOT NULL,
    class_name       VARCHAR(32) NOT NULL,
    FOREIGN KEY      (subject_code) references subjects(subject_code) ON DELETE RESTRICT,
    PRIMARY KEY      (class_id)
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





