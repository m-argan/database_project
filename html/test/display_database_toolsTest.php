<?php

require_once "html/src/setup_tools.php";
include_once "html/src/display_database_tools.php";

use PHPUnit\Framework\TestCase;

class display_database_toolsTest extends TestCase
{

    // Test #1: list_tables().
    public function test_list_tables() {
        $conn = config();
        $dblist = "SHOW TABLES";            // Show list of tables
        $result = $conn->query($dblist);

        ob_start();                         // Begin buffer
        list_tables($conn);                 // Run function
        $html_code = ob_get_clean();        // Capture HTML output

        $result = $conn->query("SHOW TABLES");    // Reset pointer in result object
        // Check all table names are displayed
        while ($tablename = $result->fetch_array()) {
            $this->assertStringContainsString($tablename[0], $html_code);
        }

        $conn->close();

    }

    
    // Test #2: display_form().
    public function test_display_form() {
        $conn = config();

        ob_start();                             // Begin buffer
        render_display_database_page($conn);    // Run function
        $html_code = ob_get_clean();            // Capture HTML output

        // Check the lines of the form are displayed
        $this->assertStringContainsString('<form action="display_table.php" method="GET">', $html_code);
        $this->assertStringContainsString('<p>Table name: <input type="text" name="tablename" /></p>', $html_code);
        $this->assertStringContainsString('<p><input type="submit" value="See Details"/></p>', $html_code);
        $this->assertStringContainsString('</form>', $html_code);

    
        $conn->close();
    }


    // Test #3: render_display_database_page().
    public function test_render_display_database_page() {
        $conn = config();

        ob_start();                             // Begin buffer
        render_display_database_page($conn);    // Run function
        $html_code = ob_get_clean();            // Capture HTML output

        // Assert page renders html tag, title, and header
        $this->assertStringContainsString('<html>', $html_code);
        $this->assertStringContainsString('<title>CLC Database</title>', $html_code);
        $this->assertStringContainsString('<h1>CLC Database</h1>', $html_code);
        $this->assertStringContainsString('</html>', $html_code);

        $conn->close();
    }

}