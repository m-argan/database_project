'''
Example set of test cases for testing SQL code using Python's unittest, and the mariadb connector.

Look for the NOTE tags below for things you should know.
'''
import unittest     # for the test runner in Python's standard library.
import mariadb      # for connecting to MariaDB, submitting queries
import subprocess   # for invoking the MariaDB interpreter
import configparser # for reading .ini files
import os.path      # for constructing file paths

class TestCLCDatabase(unittest.TestCase):
    
    TEST_DB_NAME = "clc_tutoring_test"
    TEST_MAIN_FILE = "clc_database_test_main.sql"

    @classmethod
    def getDefaultPassword(cls):
        return cls.config['default']['mysqli.default_pw']
    @classmethod
    def getDefaultUser(cls):
        return cls.config['default']['mysqli.default_user']

    @classmethod
    def runMariaDBTerminalCommandAsDefaultUser(cls, command: list):
        DB_EXEC_COMMAND = ["mariadb",f"-u{cls.getDefaultUser()}", f"-p{cls.getDefaultPassword()}", "-e"]
        return subprocess.run(DB_EXEC_COMMAND + command, check=True)

    @classmethod
    def performDelete(self,dir,field,index):
        delete_query = "DELETE FROM "+dir+" WHERE "+field+" = ?;"
        self.cur.execute(delete_query, (index,))

    @classmethod
    def setUpClass(cls):
        '''
        This is run once, before all tests (not once per test).
        '''
        # Construct the absolute path to the .ini file. 
        # NOTE: You will need to change this section to make this module find your .ini file.
        script_dir = os.path.dirname(os.path.abspath(__file__))
        parent_of_script_dir = os.path.dirname(script_dir)
        config_path = os.path.join(parent_of_script_dir, "mysqli.ini")
        print(config_path)

        # read the config parser
        cls.config = configparser.ConfigParser()
        cls.config.read(config_path)

        # Run the test main file to build the test database. This relies on the config read above.
        cls.runMariaDBTerminalCommandAsDefaultUser([f"SOURCE {cls.TEST_MAIN_FILE};"])

        # Create a connection and cursor object for use by test methods.
        cls.conn = mariadb.connect(
            password=cls.getDefaultPassword(),
            host="localhost",
            database=cls.TEST_DB_NAME
        )
        cls.cur = cls.conn.cursor()

        return super().setUpClass() # just in case, call the default constructor too.
    
        
    @classmethod
    def tearDownClass(cls):
        '''
        This is run once, after all tests (not once per test).
        '''
        cls.cur.close()
        cls.conn.close()
        cls.runMariaDBTerminalCommandAsDefaultUser([f"DROP DATABASE IF EXISTS {cls.TEST_DB_NAME};"])
        return super().tearDownClass()
    
    # ----- START ACTUAL TEST METHODS -----------------------------------------
    # Everything above has been for setting up and tearing down the teset environment.
    # NOTE: Test method name must start with "test".
    # NOTE: Test class names should start with "test".
    # NOTE: Test module name should start with "test".
    
    # deletion rule tests

    @unittest.expectedFailure
    def testDeleteSubjectCode(self):
        # can't delete subject code
        self.performDelete("classes","subject_code",0)
    
    @unittest.expectedFailure
    def testDeleteSubjectCodes(self):
        # can't delete subject code
        self.performDelete("subjects","subject_code","MAT")

    @unittest.expectedFailure
    def testDeleteTerms(self):
        # can't delete delete terms
        self.performDelete("terms","term_code","FA")

    @unittest.expectedFailure
    def testDeleteTimeBlock(self):
        # can't delete time block
        self.performDelete("time_blocks","time_id",1)

    @unittest.expectedFailure
    def testDeleteWeekDayName(self):
        # can't delete week day
        self.performDelete("week_days","week_day_name",0)

    @unittest.expectedFailure
    def testDeleteYearID(self):
        # can't delete year id
        self.performDelete("year_term","term_id",1)

    def testTermsCount(self):
        num_in_test_data = 4
        count_query = "SELECT COUNT(term_code) FROM terms;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data)

    def testBuildingsCount(self):
        num_in_test_data = 5
        count_query = "SELECT COUNT(building_name) FROM buildings;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data)

    def testWeekDaysCount(self):
        num_in_test_data = 7
        count_query = "SELECT COUNT(week_day_name) FROM week_days;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data)

    def testSubjectsCount(self):
        num_in_test_data = 7
        count_query = "SELECT COUNT(subject_code) FROM subjects;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data)

    def testYearTermsCount(self):
        num_in_test_data = 5
        count_query = "SELECT COUNT(term_code) FROM year_terms;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data) 

    def testClassesCount(self):
        num_in_test_data = 7
        count_query = "SELECT COUNT(subject_code) FROM classes;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data) 

    def testTutorsCount(self):
        num_in_test_data = 5
        count_query = "SELECT COUNT(tutor_id) FROM tutors;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data) 

    def testSlotsCount(self):
        num_in_test_data = 9
        count_query = "SELECT COUNT(building_name) FROM slots;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data) 

    def testTimeBlocksCount(self):
        num_in_test_data = 10
        count_query = "SELECT COUNT(time_block_id) FROM time_blocks;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data)

    def testTutorAgreedClassesCount(self):
        num_in_test_data = 6
        count_query = "SELECT COUNT(tutor_id) FROM tutor_agreed_classes;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data) 

    def testTutorQualifiedSubjectsCount(self):
        num_in_test_data = 6
        count_query = "SELECT COUNT(tutor_id) FROM tutor_qualified_subjects;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data) 

    def testSlotTermsCount(self):
        num_in_test_data = 4
        count_query = "SELECT COUNT(term_code) FROM terms;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data) 

    def testSlotTimesCount(self):
        num_in_test_data = 10
        count_query = "SELECT COUNT(time_block_id) FROM time_blocks;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data) 

    def testTutorAvailabilitiesCount(self):
        num_in_test_data = 12
        count_query = "SELECT COUNT(tutor_id) FROM tutor_availabilities;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data) 

    @unittest.expectedFailure
    def testClassNumConstraintLarge(self):
        add_class_query_too_large = "INSERT INTO classes(subject_code, class_number,class_name) VALUES (MAT, 555, Example Math Class)"
        #add_class_query_too_small = "INSERT INTO classes(subject_code, class_number,class_name) VALUES (MAT, 12, Example Math Class)"
        self.cur.execute(add_class_query_too_large)
        #self.cur.execute(add_class_query_too_small)

    @unittest.expectedFailure
    def testClassNumConstraintSmall(self):
        #add_class_query_too_large = "INSERT INTO classes(subject_code, class_number,class_name) VALUES (MAT, 555, Example Math Class)"
        add_class_query_too_small = "INSERT INTO classes(subject_code, class_number,class_name) VALUES (MAT, 12, Example Math Class)"
        #self.cur.execute(add_class_query_too_large)
        self.cur.execute(add_class_query_too_small)

    @unittest.expectedFailure
    def testRoomNumSmall(self):
        add_small_room_num_query = "INSERT INTO slots(building_name, subject_code,class_number,room_number) VALUES (Olin, MAT, 123,-1)"
        self.cur.execute(add_small_room_num_query)

    @unittest.expectedFailure
    def testRoomNumLarge(self):
        add_large_room_num_query = "INSERT INTO slots(building_name, subject_code,class_number,room_number) VALUES (Olin, MAT, 123,6014)"
        self.cur.execute(add_large_room_num_query)

    @unittest.expectedFailure
    def testSameSlotRoom(self):
        overlapping_query = "INSERT INTO slots(time_block_id, building_name, place_room_number, subject_code, class_number, tutor_id) VALUES (1,Crounse, 101, CSC, 332, 380932)"
        self.cur.execute(overlapping_query)

    @unittest.expectedFailure
    def testOverlappingSlot(self):
        overlapping_query = "INSERT INTO slots(time_block_id, building_name, place_room_number, subject_code, class_number, tutor_id) VALUES (3,Crounse, 101, CSC, 332, 380932)"
        overlapping_query2 = "INSERT INTO slots(time_block_id, building_name, place_room_number, subject_code, class_number, tutor_id) VALUES (1,Crounse, 101, CSC, 332, 380932)"
        self.cur.execute(overlapping_query2)
    


    @unittest.expectedFailure
    def test_prevent_slot_overlap():
        conn = get_connection()
        cursor = conn.cursor()

        # Insert a time block and slot
        cursor.execute("INSERT INTO time_blocks (time_block_id, week_day_name, term_code, year_term_year, time_block_start, time_block_end) VALUES (1,'Monday','FALL','2025','10:00','11:00')")
        cursor.execute("INSERT INTO slots (slot_id, time_block_id, building_name, place_room_number, tutor_id, subject_code, class_number, deleted_when) VALUES (1,1,'Main','101',1,'MATH','101',0)")
        conn.commit()

        # Try to insert overlapping slot in same room
        cursor.execute("INSERT INTO time_blocks VALUES (2,'Monday','FALL','2025','10:30','11:30')")
        with pytest.raises(Error) as e:
            cursor.execute("INSERT INTO slots (slot_id, time_block_id, building_name, place_room_number, tutor_id, subject_code, class_number, deleted_when) VALUES (2,2,'Main','101',2,'MATH','102',0)")
            conn.commit()
        assert "Time slot overlaps" in str(e.value)

        cursor.close()
        conn.close()

    @unittest.expectedFailure
    def test_tutor_qualifications_agreed_classes():
        conn = get_connection()
        cursor = conn.cursor()

        # Tutor qualified in MATH
        cursor.execute("INSERT INTO tutor_qualified_subjects (tutor_id, subject_code) VALUES (1,'MATH')")
        conn.commit()

        # Valid insert
        cursor.execute("INSERT INTO tutor_agreed_classes (tutor_id, subject_code) VALUES (1,'MATH')")
        conn.commit()

        # Invalid insert (unqualified subject)
        with pytest.raises(Error) as e:
            cursor.execute("INSERT INTO tutor_agreed_classes (tutor_id, subject_code) VALUES (1,'PHYS')")
            conn.commit()
        assert "Tutor is unqualified" in str(e.value)

        cursor.close()
        conn.close()

    @unittest.expectedFailure
    def test_tutor_qualifications_slots():
        conn = get_connection()
        cursor = conn.cursor()

        cursor.execute("INSERT INTO tutor_qualified_subjects (tutor_id, subject_code) VALUES (1,'MATH')")
        cursor.execute("INSERT INTO time_blocks VALUES (1,'Monday','FALL','2025','09:00','10:00')")
        conn.commit()

        # Valid slot
        cursor.execute("INSERT INTO slots (slot_id, time_block_id, building_name, place_room_number, tutor_id, subject_code, class_number, deleted_when) VALUES (1,1,'Main','101',1,'MATH','101',0)")
        conn.commit()

        # Invalid slot (unqualified subject)
        with pytest.raises(Error) as e:
            cursor.execute("INSERT INTO slots (slot_id, time_block_id, building_name, place_room_number, tutor_id, subject_code, class_number, deleted_when) VALUES (2,1,'Main','101',1,'PHYS','102',0)")
            conn.commit()
        assert "Tutor is unqualified" in str(e.value)

        cursor.close()
        conn.close()

    @unittest.expectedFailure
    def test_prevent_tutor_time_overlap():
        conn = get_connection()
        cursor = conn.cursor()

        cursor.execute("INSERT INTO time_blocks VALUES (1,'Monday','FALL','2025','09:00','10:00')")
        cursor.execute("INSERT INTO time_blocks VALUES (2,'Monday','FALL','2025','09:30','10:30')")
        cursor.execute("INSERT INTO tutor_qualified_subjects VALUES (1,'MATH')")
        conn.commit()

        cursor.execute("INSERT INTO slots VALUES (1,1,'Main','101',1,'MATH','101',0)")
        conn.commit()

        with pytest.raises(Error) as e:
            cursor.execute("INSERT INTO slots VALUES (2,2,'Main','102',1,'MATH','102',0)")
            conn.commit()
        assert "conflicting time slot" in str(e.value)

        cursor.close()
        conn.close()

    @unittest.expectedFailure
    def test_prevent_tutor_unagreed_time():
        conn = get_connection()
        cursor = conn.cursor()

        cursor.execute("INSERT INTO time_blocks VALUES (1,'Monday','FALL','2025','09:00','10:00')")
        cursor.execute("INSERT INTO tutor_availabilities VALUES (1,1)")
        cursor.execute("INSERT INTO tutor_qualified_subjects VALUES (1,'MATH')")
        conn.commit()

        # Valid slot
        cursor.execute("INSERT INTO slots VALUES (1,1,'Main','101',1,'MATH','101',0)")
        conn.commit()

        # Invalid slot (no availability)
        cursor.execute("INSERT INTO time_blocks VALUES (2,'Monday','FALL','2025','10:00','11:00')")
        with pytest.raises(Error) as e:
            cursor.execute("INSERT INTO slots VALUES (2,2,'Main','101',1,'MATH','102',0)")
            conn.commit()
        assert "Tutor has not agreed" in str(e.value)

        cursor.close()
        conn.close()

    @unittest.expectedFailure
    def test_check_valid_class_for_slot():
        conn = get_connection()
        cursor = conn.cursor()

        cursor.execute("INSERT INTO classes VALUES ('MATH','101')")
        cursor.execute("INSERT INTO time_blocks VALUES (1,'Monday','FALL','2025','09:00','10:00')")
        cursor.execute("INSERT INTO tutor_qualified_subjects VALUES (1,'MATH')")
        conn.commit()

        # Valid slot
        cursor.execute("INSERT INTO slots VALUES (1,1,'Main','101',1,'MATH','101',0)")
        conn.commit()

        # Invalid slot (class not exists)
        with pytest.raises(Error) as e:
            cursor.execute("INSERT INTO slots VALUES (2,1,'Main','101',1,'MATH','999',0)")
            conn.commit()
        assert "Invalid class" in str(e.value)

        cursor.close()
        conn.close()

    # ----- END ACTUAL TEST METHODS -------------------------------------------

if __name__ == "__main__":
    unittest.main()