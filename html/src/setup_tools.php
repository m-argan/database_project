<?php
    // Critical setup functions; error checking and connection configuration.

    // Sets up PHP error checking -- currently called on most pages:
    function error_checking() {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    // Sets up configuration to log into database:
    function config() {
        // Find .ini file
        $config = parse_ini_file('../../../mysqli.ini');
        if ($config === false) {    // If previous path did not work, use this path:
            $config = parse_ini_file('../mysqli.ini');
        }

        // Make connection
        $dbname = 'clc_tutoring';
        $conn = new mysqli(
            $config['mysqli.default_host'],
            $config['mysqli.default_user'],
            $config['mysqli.default_pw'],
            $dbname);

        // Check for errors in connection
        if ($conn->connect_errno) {
            echo "Error: Failed to make a MySQL connection, here is why: ". "<br>";
            echo "Errno: " . $conn->connect_errno . "\n";
            echo "Error: " . $conn->connect_error . "\n";
            exit; // Quit this PHP script if the connection fails
        }

        return $conn;   // Return connection for the sake of passing it to functions
    }