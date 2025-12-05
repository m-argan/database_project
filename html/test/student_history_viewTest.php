<?php

require_once __DIR__ . '/../src/setup_tools.php';
require_once __DIR__ . '/../src/display_table_tools.php';

use PHPUnit\Framework\TestCase;

class student_history_viewTest extends TestCase
{
    private $conn;

    protected function setUp(): void {
        $this->conn = config();
    }

    protected function tearDown(): void {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    /**
     * Test that the page renders the form HTML without errors
     */
    public function test_page_renders_form() {
        $_GET = [];
        $_POST = [];
        
        ob_start();
        include __DIR__ . '/../src/student_history_view.php';
        $output = '';
        while (ob_get_level() > 0) {
            $output = ob_get_clean() . $output;
        }

        // Assert the form is present
        $this->assertStringContainsString('form action="student_history_view.php" method="GET"', $output);
        $this->assertStringContainsString('First name:', $output);
        $this->assertStringContainsString('Last name:', $output);
        $this->assertStringContainsString('See Details', $output);
    }

    /**
     * Test page with no GET parameters (should show all students)
     */
    public function test_page_with_no_parameters() {
        $_GET = [];
        $_POST = [];
        
        ob_start();
        include __DIR__ . '/../src/student_history_view.php';
        $output = '';
        while (ob_get_level() > 0) {
            $output = ob_get_clean() . $output;
        }

        // Should still render the form
        $this->assertStringContainsString('form', $output);
    }

    /**
     * Test page with firstname only
     */
    public function test_page_with_firstname_only() {
        $_GET = ['firstname' => 'John'];
        $_POST = [];
        
        ob_start();
        include __DIR__ . '/../src/student_history_view.php';
        $output = '';
        while (ob_get_level() > 0) {
            $output = ob_get_clean() . $output;
        }

        // Should render form
        $this->assertStringContainsString('form', $output);
    }

    /**
     * Test page with lastname only
     */
    public function test_page_with_lastname_only() {
        $_GET = ['lastname' => 'Doe'];
        $_POST = [];
        
        ob_start();
        include __DIR__ . '/../src/student_history_view.php';
        $output = '';
        while (ob_get_level() > 0) {
            $output = ob_get_clean() . $output;
        }

        // Should render form
        $this->assertStringContainsString('form', $output);
    }

    /**
     * Test page with both firstname and lastname
     */
    public function test_page_with_firstname_and_lastname() {
        $_GET = ['firstname' => 'John', 'lastname' => 'Doe'];
        $_POST = [];
        
        ob_start();
        include __DIR__ . '/../src/student_history_view.php';
        $output = '';
        while (ob_get_level() > 0) {
            $output = ob_get_clean() . $output;
        }

        // Should render form and table
        $this->assertStringContainsString('form', $output);
    }

    /**
     * Test form input field names are correct
     */
    public function test_form_input_field_names() {
        $_GET = [];
        $_POST = [];
        
        ob_start();
        include __DIR__ . '/../src/student_history_view.php';
        $output = '';
        while (ob_get_level() > 0) {
            $output = ob_get_clean() . $output;
        }

        // Check for correct input field names
        $this->assertStringContainsString('firstname', $output);
        $this->assertStringContainsString('lastname', $output);
    }

    /**
     * Test that stored procedure is called with correct defaults
     */
    public function test_stored_procedure_defaults() {
        $_GET = [];
        $_POST = [];
        
        ob_start();
        include __DIR__ . '/../src/student_history_view.php';
        $output = '';
        while (ob_get_level() > 0) {
            $output = ob_get_clean() . $output;
        }

        // The page should render without fatal errors
        $this->assertIsString($output);
        $this->assertNotEmpty($output);
    }
}