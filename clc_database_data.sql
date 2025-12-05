INSERT INTO terms(term_code) VALUES
("FA"),
("SP"),
("WI"),
("SU");

INSERT INTO buildings(building_name) VALUES
("Olin"),
("Young"),
("Crounse"),
("Grant"),
("JVAC");

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
("DSC", "Data Science"),
("CSC", "Computer Science"),
("ARH", "ART HISTORY"),
("PSY", "Psychology");

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
("ARS", 250, "Example ARS Class"),
("ENS", 110, "Example ENS Class"),
("ARH", 305, "Example ARH Class"),
("PSY", 235, "Example PSY Class");

INSERT INTO places(building_name, place_room_number) VALUES
("Olin", 111),
("Young", 111),
("Crounse", 111),
("Grant", 101),
("Crounse",101),
("JVAC", 201);

INSERT INTO tutors(tutor_id, tutor_first_name, tutor_last_name, tutor_email) VALUES
(380932, "Hannah", "Morrison", "hannah.morrison@centre.edu"),
(380012, "Stella", "Green", "stella.green@centre.edu"),    
(000001, "William", "Bailey", "william.bailey@centre.edu"),
(123543, "Jenna", "Nicodemus", "jenna.nicodemus@centre.edu"),
(654321, "Madeleine", "Arganbright", "m.arganbright@centre.edu");

INSERT INTO time_blocks(time_block_start, time_block_end, week_day_name,term_code,year_term_year) VALUES
("7:30", "8:00", "Su", "FA", 2025),
("7:00", "8:30", "Su", "SP", 2025),
("7:30", "8:30", "Su", "SP", 2025),
("7:00", "8:00", "M", "FA", 2025),
("7:30", "8:00", "M", "FA", 2025),
("7:45", "8:45", "T", "FA", 2025),
("8:00", "9:00", "T", "FA", 2025),
("7:30", "8:00", "W", "FA", 2025),
("7:30", "8:00", "W", "FA", 2025),
("8:00", "9:00", "Th", "FA", 2025);


INSERT INTO tutor_qualified_subjects(tutor_id, subject_code) VALUES
(380932, "MAT"),
(380012, "CSC"),
(000001, "ARS"),
(000001, "ENS"),
(123543, "ARH"),
(654321, "PSY");

INSERT INTO tutor_agreed_classes(tutor_id,subject_code, class_number) VALUES
(380932, "MAT", 330),
(380012, "CSC", 332),
(000001, "ARS", 250),
(000001, "ENS", 110),
(123543, "ARH", 305),
(654321, "PSY", 235);



INSERT INTO tutor_availabilities(tutor_id, time_block_id) VALUES
(380932,1),
(380932,4),
(380932,6),
(380932,8),
(380012,4),
(380012,8),
(000001,2),
(000001,5),
(000001,10),
(123543, 3),
(654321, 9),
(123543, 2),
(654321, 6);

INSERT INTO slots(time_block_id, building_name, place_room_number,subject_code,class_number,tutor_id) VALUES
(1,"Crounse", 101,"MAT", "330", 380932),
(4,"Crounse", 101,"MAT", "330", 380932),
(6,"Crounse", 101,"MAT", "330", 380932),
(8,"Olin", 111,"CSC", "332", 380012),
(4,"Olin", 111,"CSC", "101", 380012),
(2,"JVAC", 201,"ARS", "250", 000001),
(10,"Young", 111,"ENS", "110", 000001),
(3,"GRANT", 101,"ARH", "305", 123543),
(9,"GRANT", 101,"PSY", "235", 654321);






