<?php

// Outdated draft -- need to refactor using expectOutputString()

use PHPUnit\Framework\TestCase;

class indexTest extends TestCase
{

    public function test_HTML_shows_header() {
        ob_start();
        include "html/src/index.php";
        $html_code = ob_get_clean();
        $this->assertStringContainsString('<h1>CLC Database</h1>', $html_code);
    }

    public function test_HTML_lists_all_tables() {
        ob_start();
        include "html/src/index.php";
        $html_code = ob_get_clean();

        $this->assertStringContainsString('buildings', $html_code);
        $this->assertStringContainsString('classes', $html_code);
        $this->assertStringContainsString('slot_terms', $html_code);
        $this->assertStringContainsString('slot_times', $html_code);
        $this->assertStringContainsString('slot_tutors', $html_code);
        $this->assertStringContainsString('slots', $html_code);
        $this->assertStringContainsString('subjects', $html_code);
        $this->assertStringContainsString('terms', $html_code);
        $this->assertStringContainsString('time_blocks', $html_code);
        $this->assertStringContainsString('tutor_agreed_classes', $html_code);
        $this->assertStringContainsString('tutor_availabilities', $html_code);
        $this->assertStringContainsString('tutor_qualified_subjects', $html_code);
        $this->assertStringContainsString('tutors', $html_code);
        $this->assertStringContainsString('week_days', $html_code);
        $this->assertStringContainsString('year_terms', $html_code);
    }

    public function test_HTML_display_button() {
        ob_start();
        include "html/src/index.php";
        $html_code = ob_get_clean();
        $this->assertStringContainsString('<form action="display_table.php" method="GET">
                                           <p>Table name: <input type="text" name="tablename" /></p>
                                           <p><input type="submit" value="See Details"/></p>
                                           </form>', $html_code);
    }

    
}