<?php

include "html/src/index.php";

use PHPUnit\Framework\TestCase;

class indexTest extends TestCase
{

    public function test_HTML_output() {
        ob_start();
        $html_code = ob_get_clean();
         $this->assertStringContainsString('<h1>CLC Database</h1>', $html_code);
    }

}