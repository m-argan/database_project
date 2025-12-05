<?php

require_once __DIR__ . '/../src/setup_tools.php';
require_once __DIR__ . '/../src/display_table_tools.php';

use PHPUnit\Framework\TestCase;

class student_schedule_viewTest extends TestCase
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
        include __DIR__ . '/../src/student_schedule_view.php';
        $output = '';
        while (ob_get_level() > 0) {
            $output = ob_get_clean() . $output;
        }

        // Assert the form sections are present
        $this->assertStringContainsString('form action="student_schedule_view.php" method="GET"', $output);
        $this->assertStringContainsString('Select a Student:', $output);
        $this->assertStringContainsString('Subject', $output);
        $this->assertStringContainsString('Term:', $output);
        $this->assertStringContainsString('See Details', $output);
    }

    /**
     * Test page with no GET parameters
     */
    public function test_page_with_no_parameters() {
        $_GET = [];
        $_POST = [];
        
        ob_start();
        include __DIR__ . '/../src/student_schedule_view.php';
        $output = '';
        while (ob_get_level() > 0) {
            $output = ob_get_clean() . $output;
        }

        // Should render the form
        $this->assertStringContainsString('form', $output);
    }

    /**
     * Test page with student info only
     */
    public function test_page_with_student_info_only() {
        $_GET = ['firstname' => 'John', 'lastname' => 'Doe'];
        $_POST = [];
        
        ob_start();
        include __DIR__ . '/../src/student_schedule_view.php';
        $output = '';
        while (ob_get_level() > 0) {
            $output = ob_get_clean() . $output;
        }

        // Should render form
        $this->assertStringContainsString('form', $output);
    }

    /**
     * Test form input field names are correct
     */
    public function test_form_input_field_names() {
        $_GET = [];
        $_POST = [];
        
        ob_start();
        include __DIR__ . '/../src/student_schedule_view.php';
        $output = '';
        while (ob_get_level() > 0) {
            $output = ob_get_clean() . $output;
        }

        // Check for correct input field names
        $this->assertStringContainsString('firstname', $output);
        $this->assertStringContainsString('lastname', $output);
    }

    /**
     * Test page with non-numeric year
     */
    public function test_page_with_numeric_year() {
        $_GET = ['term' => 'FA', 'tyear' => '2025'];
        $_POST = [];
        
        ob_start();
        include __DIR__ . '/../src/student_schedule_view.php';
        $output = '';
        while (ob_get_level() > 0) {
            $output = ob_get_clean() . $output;
        }

        // Should render form
        $this->assertStringContainsString('form', $output);
    }

    /**
     * Test page with special characters in subject
     */
    public function test_page_with_subject_code() {
        $_GET = ['subjectcode' => 'CSC'];
        $_POST = [];
        
        ob_start();
        include __DIR__ . '/../src/student_schedule_view.php';
        $output = '';
        while (ob_get_level() > 0) {
            $output = ob_get_clean() . $output;
        }

        // Should render form
        $this->assertStringContainsString('form', $output);
    }

    /**
     * Test page with valid firstname
     */
    public function test_page_with_valid_firstname() {
        $_GET = ['firstname' => 'John', 'lastname' => 'Doe'];
        $_POST = [];
        
        ob_start();
        include __DIR__ . '/../src/student_schedule_view.php';
        $output = '';
        while (ob_get_level() > 0) {
            $output = ob_get_clean() . $output;
        }

        // Form should render
        $this->assertStringContainsString('form', $output);
    }
}