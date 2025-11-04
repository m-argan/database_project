<?php

include "html/src/index.php";
include "html/src/display_table.php";

use PHPUnit\Framework\TestCase;

class display_tableTest extends TestCase
{

    public function test_format_result_as_table() {
        // $config = parse_ini_file('../mysql.ini');
        $config = parse_ini_file('../mysqli.ini');
        // $dbname = 'clc_tutoring';
        $dbname = 'clc_tutoring_test';
        $conn = new mysqli(
            $config['mysqli.default_host'],
            $config['mysqli.default_user'],
            $config['mysqli.default_pw'],
            $dbname);
        // Check errors in connection
        if ($conn->connect_errno) {
            echo "Error: Failed to make a MySQL connection, here is why: ". "<br>";
            echo "Errno: " . $conn->connect_errno . "\n";
            echo "Error: " . $conn->connect_error . "\n";
            exit; // Quit this PHP script if the connection fails   
        }
        
        $result = $conn->query("SELECT * FROM classes");
        // $_GET = [];
        $_GET['tablename'] = "classes";
        // $_GET[]
        // $GLOBALS[]
        $this->expectOutputString(format_result_as_table($result));

        while ($field = $result->fetch_field()) {
           echo $field->name;
        }

        $i = 0;
        while ($row = $result->fetch_row()) {
            echo $row[i];
        }

        $conn->close();
    }

}