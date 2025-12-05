<?php
use PHPUnit\Framework\TestCase;

class ViewEditsTest extends TestCase
{
    private $conn;

    protected function setUp(): void
    {
        $this->conn = new mysqli("localhost", "root", "", "test_db");
        if ($this->conn->connect_error) {
            $this->fail("Connection failed: " . $this->conn->connect_error);
        }

        // Reset tables
        $this->conn->query("DROP TABLE IF EXISTS slots");
        $this->conn->query("DROP TABLE IF EXISTS tutors");

        $this->conn->query("
            CREATE TABLE slots (
                slot_id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50),
                deleted_when DATETIME NULL
            )
        ");

        $this->conn->query("
            CREATE TABLE tutors (
                tutor_id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50),
                deleted_when DATETIME NULL
            )
        ");

        // Insert sample rows
        $this->conn->query("INSERT INTO slots (name) VALUES ('Slot A'), ('Slot B'), ('Slot C')");
        $this->conn->query("INSERT INTO tutors (name) VALUES ('Tutor A'), ('Tutor B'), ('Tutor C')");
    }

    protected function tearDown(): void
    {
        $this->conn->close();
    }

    /** @test */
    public function it_soft_deletes_slot_when_checkbox_selected()
    {
        $_POST = ['delbtn' => true, 'checkbox1' => true]; // select Slot B
        $result = $this->conn->query("SELECT slot_id, name FROM slots");

        view_edits($this->conn, $result, 1);

        $res = $this->conn->query("SELECT * FROM slots WHERE slot_id = 2");
        $row = $res->fetch_assoc();

        $this->assertNotNull($row['deleted_when'], "Slot B should be soft deleted");
    }

    /** @test */
    public function it_soft_deletes_tutor_when_checkbox_selected()
    {
        $_POST = ['delbtn' => true, 'checkbox2' => true]; // select Tutor C
        $result = $this->conn->query("SELECT tutor_id AS `Student ID`, name FROM tutors");

        view_edits($this->conn, $result, 2);

        $res = $this->conn->query("SELECT * FROM tutors WHERE tutor_id = 3");
        $row = $res->fetch_assoc();

        $this->assertNotNull($row['deleted_when'], "Tutor C should be soft deleted");
    }

    /** @test */
    public function it_calls_alt_views_when_alter_btn_pressed()
    {
        $_POST = ['alter_btn' => true];
        $result = $this->conn->query("SELECT slot_id, name FROM slots");

        // Expect no exception, just call alt_views
        $this->expectOutputRegex('/Alter view called/');
        view_edits($this->conn, $result, 3);
    }

    /** @test */
    public function it_calls_display_add_forms_when_add_btn_pressed()
    {
        $_POST = ['add_btn' => true];
        $result = $this->conn->query("SELECT tutor_id AS `Student ID`, name FROM tutors");

        $this->expectOutputRegex('/Display adding forms/');
        view_edits($this->conn, $result, 4);
    }
}
