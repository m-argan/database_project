<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/setup_tools.php'; 
require_once __DIR__ . '/../src/display_adding_tools.php';
require_once __DIR__ . '/../src/display_table_tools.php';
#include_once 'html/scr/display_table.php';

class add_toolsTest extends TestCase
{
    private $conn;
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
    //Tests returned list of autoincremented keys is accurate
    public function testGetAutoIncrementKeys()
    {
        // First, get the fields from the slots table
        $query = "SELECT * FROM slots LIMIT 1";
        $result = $this->conn->prepare($query);
        $result->execute();
        $result = $result->get_result();
        
        $fields = [];
        while ($field = $result->fetch_field()) {
            $fields[] = $field;
        }
        
        // Now call get_aut_inc_keys with all three required arguments
        $keys = get_aut_inc_keys($this->conn, "slots", $fields);
        $this->assertContains("slot_id", $keys);
    }

    /** @test */
    //Tests that a valid input is accurately inserted
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
    //Tests that the proper error message appears and a record with an improper
    //foreign key is not inserted
    public function testInsertIntoTableInvalidForeignKey()
    {
        $data = [
            "tutor_id" => 123456,
            "subject_code" => "ENS"
        ];
   
        ob_start();
        insert_into_table($this->conn, "tutor_qualified_subjects", $data);
        $output = ob_get_clean();

       
        $this->assertStringContainsString("Error: Value '123456' for column 'tutor_id' does not exist in 'tutors'", $output);
    }

    /** @test */
    //Tests that proper error message shows and that
    //an unqualified tutor record can't be created
    public function testUnqualifiedTutor()
    {
        $data = [
            "tutor_id" => 380932,
            "subject_code" => "ENS",
            "class_number" => 110
        ];
   
        ob_start();
        insert_into_table($this->conn, "tutor_agreed_classes", $data);
        $output = ob_get_clean();

       
        $this->assertStringContainsString("Error: Tutor is unqualified.", $output);
    }

    /** @test */
    //Tests that forms are properly displayed
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
    //Tests that error message pops up when submit is hit with empty fields
    public function testDisplayAddingFormsSubmissionIncomplete()
    {
        $_GET['tablename'] = "buildings";
        $_POST = ["submit" => "Add record"]; 

        ob_start();
        input_new_data($this->conn);
        $output = ob_get_clean();

        $this->assertStringContainsString("You did not fill in all fields", $output);
    }

    /** @test */
    //Tests that success message is displayed and the insert was executed successfully.
    public function testDisplayAddingFormsSubmissionSuccess()
    {
        $_GET['tablename'] = "buildings";
        $_POST = ["submit" => "Add record", "building_name" => "ABCD"];

        ob_start();
        try {
            input_new_data($this->conn);
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }
        $output = ob_get_clean();

        $this->assertStringContainsString("Record added successfully", $output);

        $result = $this->conn->query("SELECT * FROM buildings WHERE building_name = 'ABCD'");
        $this->assertEquals(1, $result->num_rows);
    }

    /** @test */
    //Tests that error message shows when timeslots overlap for same tutor
    public function testTutorOverlap()
    {
        $data = [
            "time_block_id" => 1,
            "building_name" => "Young",
            "place_room_number" => 111,
            "subject_code" => "MAT",
            "class_number" => 332,
            "tutor_id" => 380932
        ];
   
        ob_start();
        insert_into_table($this->conn, "slots", $data);
        $output = ob_get_clean();

       
        $this->assertStringContainsString("Error: Tutor already has a conflicting time slot.", $output);
    }

    /** @test */
    //Tests that error message shows when timeslots overlap for same room
    public function testRoomTimeOverlap()
    {
        $data = [
            "time_block_id" => 1,
            "building_name" => "Crounse",
            "place_room_number" => 101,
            "subject_code" => "ENS",
            "class_number" => 110,
            "tutor_id" => 1
        ];
   
        ob_start();
        insert_into_table($this->conn, "slots", $data);
        $output = ob_get_clean();

       
        $this->assertStringContainsString("Error: Time slot overlaps an existing slot in this room.", $output);
    }
     
}
