'''
Example set of test cases for testing SQL code using Python's unittest, and the mariadb connector.

Look for the NOTE tags below for things you should know.
'''
import unittest     # for the test runner in Python's standard library.
import mariadb      # for connecting to MariaDB, submitting queries
import subprocess   # for invoking the MariaDB interpreter
import configparser # for reading .ini files
import os.path      # for constructing file paths

class TestInstrumentRentals(unittest.TestCase):
    
    TEST_DB_NAME = "clc_tutoring"
    TEST_MAIN_FILE = "clc_database_main.sql"

    @classmethod
    def getDefaultPassword(cls):
        return cls.config["default"]["mysqli.default_pw"]


    @classmethod
    def runMariaDBTerminalCommandAsDefaultUser(cls, command: list):
        DB_EXEC_COMMAND = ["mariadb", f"-p{cls.getDefaultPassword()}", "-e"]
        return subprocess.run(DB_EXEC_COMMAND + command, check=True)
    

    @classmethod
    def setUpClass(cls):
        '''
        This is run once, before all tests (not once per test).
        '''
        # Construct the absolute path to the .ini file. 
        # NOTE: You will need to change this section to make this module find your .ini file.
        this_file_dir = os.path.dirname(__file__)
        one_dir_up = os.path.join("/", *this_file_dir.split(os.sep)[:-1])
        ini_path = os.path.join(one_dir_up, "home_volume", "mysqli.ini")

        # read the config parser
        cls.config = configparser.ConfigParser()
        cls.config.read(ini_path)

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
    
    # write tests here
    print("worked!")
    # ----- END ACTUAL TEST METHODS -------------------------------------------

if __name__ == "__main__":
    unittest.main()