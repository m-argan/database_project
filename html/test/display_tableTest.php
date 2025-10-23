<?php

include "html/src/display_table.php";

use PHPUnit\Framework\TestCase;

class display_tableTest extends TestCase
{

    public function test_format_result_as_table() {
        $config = parse_ini_file('../../../mysql.ini');
        $dbname = 'clc_tutoring';
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
        format_result_as_table($result);

        ob_start();
        include "html/src/display_table.php";
        $html_code = ob_get_clean();

        while ($field = $result->fetch_field()) {
            $this->assertStringContainsString($field->name, $html_code);
        }

        $i = 0;
        while ($row = $result->fetch_row()) {
            $this->assertStringContainsString($row[$i], $html_code);
            $i++;
        }

        $conn->close();
    }

}