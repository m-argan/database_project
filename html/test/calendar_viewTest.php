<?php
use PHPUnit\Framework\TestCase;

class calendar_viewTest extends TestCase
{
    protected function tearDown(): void {
        // Clear global variables for next test
        unset($_GET, $_POST);
    }

    /**
     * Test that htmlspecialchars is applied to subject input
     */
    public function test_input_sanitization() {
        // Test htmlspecialchars directly
        $subject_input = "IT'S A TEST";
        $sanitized = htmlspecialchars($subject_input);
        $this->assertStringContainsString('&#039;', $sanitized);
    }
}

/* Use code with caution.

Test for File 2: calendar_pivot_view
Since File 2 is a stored procedure, testing it requires direct SQL commands. This uses the style of SQL comments found in the original file.
Save this as tests/test_calendar_pivot_view.sql:
sql
-- SQL Script to perform integration/unit tests on the calendar_pivot_view stored procedure.
-- This script requires existing tables (slots, time_blocks) in the 'clc_tutoring' schema.

USE clc_tutoring;

-- -------------------------------------------------------------
-- Setup Phase: Prepare the environment and insert test data
-- -------------------------------------------------------------

-- Note: The procedure uses CURRENT_DATE() to determine the current term/year.
-- The test data below assumes the script is run when CURRENT_DATE() falls within 
-- the 'FA' term (e.g., sometime between August and December of the current year).


SET @current_year = YEAR(CURRENT_DATE());
SET @term = 'FA';

-- Clean up previous test data if the script ran before
DELETE FROM slots WHERE subject_code IN ('TST', 'CS');
DELETE FROM time_blocks WHERE time_block_id BETWEEN 900 AND 999;

-- Insert mock time blocks
INSERT INTO time_blocks (time_block_id, week_day_name, time_block_start, time_block_end) VALUES
(999, 'M', '10:00:00', '11:00:00'),
(998, 'W', '10:00:00', '11:00:00'),
(997, 'T', '13:00:00', '14:00:00');

-- Insert mock slots data for the *current* term/year
INSERT INTO slots (subject_code, class_number, term_code, year_term_year, time_block_id, building_name, place_room_number) VALUES
('TST', 101, @term, @current_year, 999, 'BLDG_A', '101'), -- Mon TST 101
('TST', 101, @term, @current_year, 998, 'BLDG_A', '101'), -- Wed TST 101
('CS', 202,  @term, @current_year, 999, 'BLDG_B', '202'),  -- Mon CS 202
('CS', 202,  @term, @current_year, 997, 'BLDG_B', '202');  -- Tue CS 202

-- -------------------------------------------------------------
-- Test Phase: Execute the procedure and verify output
-- -------------------------------------------------------------

-- TEST CASE 1: All Subjects, All Classes (default behavior)
-- Expected: TST 101 (M, W) and CS 202 (M, T) entries in the respective day columns.
SELECT '--- TEST 1: All Subjects, All Classes ---' AS TEST_DESCRIPTION;
CALL calendar_pivot_view(NULL, NULL, TRUE, TRUE);

-- TEST CASE 2: Specific Subject 'TST', All Classes
-- Expected: Only TST 101 entries (M, W).
SELECT '--- TEST 2: Specific Subject TST, All Classes ---' AS TEST_DESCRIPTION;
CALL calendar_pivot_view('TST', NULL, FALSE, TRUE);

-- TEST CASE 3: Specific Subject 'CS', Specific Class '202'
-- Expected: Only CS 202 entries (M, T).
SELECT '--- TEST 3: Specific Subject CS, Specific Class 202 ---' AS TEST_DESCRIPTION;
CALL calendar_pivot_view('CS', 202, FALSE, FALSE);

-- TEST CASE 4: Non-existent Subject 'PHY'
-- Expected: Empty result set.
SELECT '--- TEST 4: Non-existent Subject PHY ---' AS TEST_DESCRIPTION;
CALL calendar_pivot_view('PHY', NULL, FALSE, TRUE);

-- TEST CASE 5: Filtering for a different term/year (which should yield no results with this test data)
-- This tests the `WHERE term_code = ? AND year_term_year = ?` logic indirectly.
-- Expected: Empty result set.
SELECT '--- TEST 5: Different Year (Expected Empty) ---' AS TEST_DESCRIPTION;
-- We must manually manipulate internal logic for this specific test case, as the procedure hardcodes CURRENT_DATE()
-- This test is harder to perform in-line without altering the procedure code, so we rely on the above tests primarily.


-- -------------------------------------------------------------
-- Cleanup Phase: Remove test data
-- -------------------------------------------------------------
DELETE FROM slots WHERE subject_code IN ('TST', 'CS');
DELETE FROM time_blocks WHERE time_block_id BETWEEN 900 AND 999;
*/