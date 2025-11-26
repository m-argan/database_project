import unittest     # for the test runner in Python's standard library.
import mariadb      # for connecting to MariaDB, submitting queries
import subprocess   # for invoking the MariaDB interpreter
import configparser # for reading .ini files
import os.path      # for constructing file paths
from pathlib import Path

class TestCLCDatabase(unittest.TestCase):
    
    TEST_DB_NAME = "clc_tutoring"
    TEST_MAIN_FILE = "clc_database_tools.sql"

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
    def setUpClass(cls):
        '''
        This is run once, before all tests (not once per test).
        '''
        # Construct the absolute path to the .ini file. 
        # NOTE: You will need to change this section to make this module find your .ini file.
        
        script_dir = Path(os.path.abspath(os.curdir))
        parent_of_script_dir = script_dir.parent.parent
        config_path = os.path.join(parent_of_script_dir, "mysqli.ini")
        # print("path"+config_path)

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
    
    @unittest.expectedFailure
    def testMinimum(self):
        print("hi")

