<?php
require_once __DIR__ . "/setup_tools.php";
require_once __DIR__ . "/display_database_tools.php";
require_once __DIR__ . "/display_table_tools.php";
require_once __DIR__ . "/alter_tools.php";

error_checking();
$conn = config();

if (isset($_POST['submit_btn'])) {
    perform_alter($conn);
    if (!empty($error_message)): ?>
    <p style="color:red;"><?php echo $error_message; ?></p>
    <?php endif; 
    //display_session_errors();
} else {
    $result = get_result($conn);
    alt($conn);
}

$conn->close();
?>


