<?php

require_once "html/src/setup_tools.php";
include_once "html/src/display_table_tools.php";

use PHPUnit\Framework\TestCase;

class display_table_toolsTest extends TestCase
{

    // Test #1: format_result_as_table().
    public function test_format_result_as_table() {
        $conn = config();
        $result = $conn->query("SELECT * FROM classes");
        
        $_GET['tablename'] = "classes";     // Set $_GET

        ob_start();                         // Begin buffer
        format_result_as_table($result);    // Run function
        $html_code = ob_get_clean();        // Capture HTML output

        $result = $conn->query("SELECT * FROM classes");    // Reset pointer in result object

        // Check all field names are displayed
        while ($field = $result->fetch_field()) {
            $this->assertStringContainsString($field->name, $html_code);
        }
       
        // Check all rows are displayed
        while ($row = $result->fetch_row()) {
            for ($i = 0; $i < count($row); $i++) {
              $this->assertStringContainsString($row[$i], $html_code);
            }
        }

        $conn->close();
    }


    // Test #2: prepare_display_table().
    public function test_prepare_display_table() {
        $conn = config();

        // Prepare a statement
        $query = "SELECT * FROM " . htmlspecialchars( "classes" ) . ";";
        $query = $conn->prepare($query);
        $query->execute();
        $result = $query->get_result();

        // assertEquals
        $this->assertEquals($result, prepare_display_table($conn),
                            "Result was not the same.");

        $conn->close();
    }


    // Test #3: filter_user_input() using good input.
    public function test_filter_user_input() {
        $conn = config();
        $_GET['tablename'] = 'classes';     // Set $_GET -- good input

        ob_start();                         // Begin buffer
        $flag = filter_user_input($conn);   // Run function
        $html_code = ob_get_clean();        // Capture HTML output (should be empty here)

        // For good input, assert flag is set correctly and did not exit early
        $this->assertStringNotContainsString('No table of that name found.', $html_code);
        $this->assertEquals(true, $flag);

        $conn->close();
    }

    
    // Test #4: filter_user_input() using bad input.
    public function test_bad_filter_user_input() {
        $conn = config();
        $_GET['tablename'] = 'asdfghjkl;';  // Set $_GET -- bad input

        ob_start();                         // Begin buffer
        $flag = filter_user_input($conn);   // Run function
        $html_code = ob_get_clean();        // Capture HTML output

        // For bad input, assert flag is set correctly and did exit early
        $this->assertStringContainsString('No table of that name found.', $html_code);
        $this->assertEquals(false, $flag);

        $conn->close();
    }
}