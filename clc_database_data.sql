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

INSERT INTO year_terms(term_code, year_term_year) VALUES
("FA", 2024),
("SP", 2024),
("WI", 2025),
("SU", 2025),
("FA", 2025);

INSERT INTO classes(subject_code, class_number, class_name) VALUES
("MAT", 330, "Example Math Class"),
("CSC", 332, "Example CSC Class"),
("CSC", 101, "Example CSC Class 2"),
("ARS", 250, "Example Art Class"),
("ENS", 110, "Example ENS Class");

INSERT INTO places(building_name, place_room_number) VALUES
("Olin", 111),
("Young", 111),
("Crounse", 111),
("Grant", 101);

INSERT INTO tutors(tutor_id, tutor_first_name, tutor_last_name, tutor_email) VALUES
(380932, "Hannah", "Morrison", "hannah.morrison@centre.edu");

INSERT INTO time_blocks(time_block_start, time_block_end, week_day_name,term_code,year_term_year) VALUES
("7:00", "8:00", "Su", "FA", 2025);

INSERT INTO tutor_agreed_classes(tutor_id,subject_code, class_number) VALUES
(380932, "MAT", 330);

INSERT INTO tutor_qualified_subjects(tutor_id, subject_code) VALUES
(380932, "MAT");

INSERT INTO tutor_availibilities(tutor_id, time_block_id) VALUES
(380932,1);

INSERT INTO slots(time_block_id, building_name, place_room_number,subject_code,class_number,tutor_id) VALUES
(1,"Crounse", 111,"MAT", "330", 380932);






