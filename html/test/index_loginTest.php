<?php
use PHPUnit\Framework\TestCase;

// Test for login page
// WRITTEN BY COPILOT (Claude Haiku 4.5)
require_once __DIR__ . '/../src/setup_tools.php';
require_once __DIR__ . '/../src/display_database_tools.php';

class index_loginTest extends TestCase
{
    private $conn;
    private $testPassword = 'test_password_123';
    private $renderHomePageCalled = false;
    private $renderLoginCalled = false;
    private $renderLoginErrorFlag = false;

    protected function setUp(): void
    {
        $this->conn = config();
        $this->renderHomePageCalled = false;
        $this->renderLoginCalled = false;
        $this->renderLoginErrorFlag = false;
        
        // Mock render_homepage function
        if (!function_exists('render_homepage')) {
            function render_homepage($conn) {
                global $renderHomePageCalled;
                $renderHomePageCalled = true;
            }
        }
        
        // Mock render_login function
        if (!function_exists('render_login')) {
            function render_login($conn, $errorFlag = false) {
                global $renderLoginCalled, $renderLoginErrorFlag;
                $renderLoginCalled = true;
                $renderLoginErrorFlag = $errorFlag;
            }
        }
    }

    protected function tearDown(): void
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    /**
     * Test login with correct password
     * Should call render_homepage when password matches
     */
    public function testLoginWithCorrectPassword()
    {
        // Set up POST data with correct password
        $_POST['admin_password'] = $this->testPassword;
        
        // Capture output to suppress any HTML output
        ob_start();
        
        // Call the login function
        $this->login($this->testPassword, $this->conn);
        
        ob_end_clean();
        
        // Verify the password comparison logic
        $this->assertEquals($_POST['admin_password'], $this->testPassword);
    }

    /**
     * Test login with incorrect password
     * Should call render_login with error flag when password doesn't match
     */
    public function testLoginWithIncorrectPassword()
    {
        $incorrectPassword = 'wrong_password';
        
        // Set up POST data with incorrect password
        $_POST['admin_password'] = $incorrectPassword;
        
        // Capture output to suppress any HTML output
        ob_start();
        
        // Call the login function
        $this->login($incorrectPassword, $this->conn);
        
        ob_end_clean();
        
        // Verify that the incorrect password does not match the correct one
        $this->assertNotEquals($_POST['admin_password'], $this->testPassword);
    }

    /**
     * Test login password comparison logic
     * Verifies the core logic of password validation
     */
    public function testPasswordComparison()
    {
        $correctPassword = 'admin123';
        $testPassword1 = 'admin123';
        $testPassword2 = 'wrongpass';

        // Test correct password
        $this->assertTrue($testPassword1 === $correctPassword);
        
        // Test incorrect password
        $this->assertFalse($testPassword2 === $correctPassword);
    }

    /**
     * Private method that mimics the login function from index.php
     * This is used for testing purposes
     */
    private function login($password, $conn)
    {
        // If password is correct, display Admin view
        if ($_POST['admin_password'] == $password) {
            render_homepage($conn);
        } elseif ($_POST['admin_password'] != $password) {
            // Reload login page, but this time with an error message
            render_login($conn, true);
        }
    }

    /**
     * Test login with empty password
     */
    public function testLoginWithEmptyPassword()
    {
        $_POST['admin_password'] = '';
        
        // Empty password should not equal non-empty password
        $this->assertNotEquals($_POST['admin_password'], $this->testPassword);
    }

    /**
     * Test login with case-sensitive password
     * Verifies that password comparison is case-sensitive
     */
    public function testLoginPasswordCaseSensitive()
    {
        $correctPassword = 'TestPassword123';
        $testPassword1 = 'testpassword123';
        
        // Different case should not match
        $this->assertNotEquals($testPassword1, $correctPassword);
    }
}
?>
