<?php
use PHPUnit\Framework\TestCase;

require_once 'html/src/setup_tools.php'; 
include_once 'html/src/display_adding_tools.php';
include_once 'html/src/display_table_tools.php';
#include_once 'html/scr/display_table.php';

class add_toolsTest extends TestCase
{
    // private $conn;
    // protected function setUp(): void {
    //     $this->conn = config();
    // }

    // protected function tearDown(): void {
    //     if ($this->conn) {
    //         $this->conn->close();
    //     }
    // }
    protected function setUp(): void
{
    $this->conn = config();
    $this->conn->begin_transaction(); // Start a transaction
}

protected function tearDown(): void
{
    if ($this->conn) {
        $this->conn->rollback(); // Undo any inserts, updates, deletes
        $this->conn->close();
    }
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
            "subject_code" => "CSC",
            "class_number" => 118,
            "class_name" => "Intro to Chemistry"
        ];

        insert_into_table($this->conn, "classes", $data);

        $result = $this->conn->query("SELECT * FROM classes WHERE subject_code='CSC' AND class_number = '118'");
        $this->assertEquals(1, $result->num_rows);
    }

    /** @test */
    public function testInsertIntoTableInvalidForeignKey()
    {
        $this->expectException(Exception::class);
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
        $_POST = ["submit" => "Add record", "building_name" => "ABCD"];

        ob_start();
        display_adding_forms($this->conn);
        $output = ob_get_clean();

        $this->assertStringContainsString("Record added successfully", $output);

        $result = $this->conn->query("SELECT * FROM buildings WHERE building_name = 'ABCD'");
        $this->assertEquals(1, $result->num_rows);
    }
}
