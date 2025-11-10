<?php
use PHPUnit\Framework\TestCase;

require_once 'setup_tools.php'; 
require_once 'display_adding_tools.php';

class DatabaseFunctionsTest extends TestCase
{
    private $conn;

    protected function setUp(): void {
        $this->conn = config();
    }

    protected function tearDown(): void
    {
        $this->conn->close();
    }

    /** @test */
    public function testGetAutoIncrementKeys()
    {
        $keys = get_aut_inc_keys($this->conn, "slots");
        $this->assertContains("slot_id", $keys);
    }

    /** @test */
    public function testInsertIntoTableValidForeignKey()
    {
        $data = [
            "subject_code" => "CHE",
            "class_number" => 110,
            "class_name" => "Intro to Chemistry"
        ];

        insert_into_table($this->conn, "classes", $data);

        $result = $this->conn->query("SELECT * FROM classes WHERE subject_code='CHE' AND class_number = '110'");
        $this->assertEquals(1, $result->num_rows);
    }

    /** @test */
    public function testInsertIntoTableInvalidForeignKey()
    {
        $this->expectExceptionMessage("does not exist");

        $data = [
            "tutor_id" => "123456",
            "subject_code" => "CSC" 
        ];

        insert_into_table($this->conn, "tutor_qualified_subjects", $data);
    }

    /** @test */
    public function testDisplayAddingFormsOutputsForm()
    {
        $_GET['tablename'] = "slots";
        $_POST = []; // no submission

        ob_start();
        display_adding_forms($this->conn);
        $output = ob_get_clean();

        $this->assertStringContainsString("<form", $output);
        $this->assertStringContainsString("subject_code", $output);
    }

    /** @test */
    public function testDisplayAddingFormsSubmissionIncomplete()
    {
        $_GET['tablename'] = "buildings";
        $_POST = ["submit" => "Add record"]; 

        ob_start();
        display_adding_forms($this->conn);
        $output = ob_get_clean();

        $this->assertStringContainsString("You did not fill in all fields", $output);
    }

    /** @test */
    public function testDisplayAddingFormsSubmissionSuccess()
    {
        $_GET['tablename'] = "buildings";
        $_POST = ["submit" => "Add record", "building_name" => "JVAC"];

        ob_start();
        display_adding_forms($this->conn);
        $output = ob_get_clean();

        $this->assertStringContainsString("Record added successfully", $output);

        $result = $this->conn->query("SELECT * FROM buildings WHERE building_name = 'JVAC'");
        $this->assertEquals(1, $result->num_rows);
    }
}
