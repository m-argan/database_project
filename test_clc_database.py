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
    
    TEST_DB_NAME = "clc_tutoring"
    TEST_MAIN_FILE = "clc_database_test_main.sql"

    @classmethod
    def getDefaultPassword(cls):
        return cls.config['default']['mysqli_default_pw']


    @classmethod
    def runMariaDBTerminalCommandAsDefaultUser(cls, command: list):
        DB_EXEC_COMMAND = ["mariadb", f"-p{cls.getDefaultPassword()}", "-e"]
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
    def testDeleteTimeBlock(self):
        # can't delete time block
        self.performDelete("time_blocks","time_id",1)

    @unittest.expectedFailure
    def testDeleteSubjectCode(self):
        # can't delete subject code
        self.performDelete("classes","subject_code",0)

    @unittest.expectedFailure
    def testDeleteWeekDayName(self):
        # can't delete week day
        self.performDelete("week_days","week_day_name",0)

    @unittest.expectedFailure
    def testDeleteTerms(self):
        # can't delete delete terms
        self.performDelete("terms","term_code","FA")

    @unittest.expectedFailure
    def testDeleteYearID(self):
        # can't delete subject code
        self.performDelete("year_term","term_id",1)

    @unittest.expectedFailure
    def testDeleteSubjectCodes(self):
        # can't delete subject code
        self.performDelete("subjects","subject_code","MAT")

    def testTermsCount(self):
        num_in_test_data = 4
        count_query = "SELECT COUNT(term_code) FROM terms;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data)

    def testBuildingsCount(self):
        num_in_test_data = 4
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
        num_in_test_data = 4
        count_query = "SELECT COUNT(subject_code) FROM subjects;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data)

def testYearTermsCount(self):
        num_in_test_data = 5
        count_query = "SELECT COUNT(term_id) FROM year_terms;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data) 

def testYearClassesCount(self):
        num_in_test_data = 4
        count_query = "SELECT COUNT(subject_code) FROM classes;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data) 

def testTutorsCount(self):
        num_in_test_data = 1
        count_query = "SELECT COUNT(tutor_id) FROM tutors;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data) 

def testSlotsCount(self):
        num_in_test_data = 1
        count_query = "SELECT COUNT(building_name) FROM slots;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data) 

def testTimeBlocksCount(self):
        num_in_test_data = 1
        count_query = "SELECT COUNT(time_id) FROM time_blocks;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data)

def testTutorAgreedClassesCount(self):
        num_in_test_data = 1
        count_query = "SELECT COUNT(tutor_id) FROM tutor_agreed_classes;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data) 

def testTutorQualifiedSubjectsCount(self):
        num_in_test_data = 1
        count_query = "SELECT COUNT(tutor_id) FROM tutor_qualified_subjects;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data) 

def testSlotTermsCount(self):
        num_in_test_data = 1
        count_query = "SELECT COUNT(term_id) FROM slot_terms;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data) 

def testSlotTimesCount(self):
        num_in_test_data = 1
        count_query = "SELECT COUNT(slot_id) FROM slot_times;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data) 

def testSlotTutorsCount(self):
        num_in_test_data = 1
        count_query = "SELECT COUNT(slot_id) FROM slot_tutors;"
        self.cur.execute(count_query)
        (num_expected,) = self.cur.fetchone()
        self.assertEqual(num_expected, num_in_test_data) 

def testTutorAvailibilitiesCount(self):
        num_in_test_data = 1
        count_query = "SELECT COUNT(tutor_id) FROM tutor_availibities;"
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
def testRoomNumSmall(self):
    add_large_room_num_query = "INSERT INTO slots(building_name, subject_code,class_number,room_number) VALUES (Olin, MAT, 123,6014)"
    self.cur.execute(add_large_room_num_query)
    
    # query return tests

    # tables exist tests

    # constraints / CHECKS are followed


    # ----- END ACTUAL TEST METHODS -------------------------------------------

if __name__ == "__main__":
    unittest.main()
