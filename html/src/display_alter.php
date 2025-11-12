<?php
include_once "setup_tools.php";
include_once "display_database_tools.php";
include_once "display_table_tools.php";
include_once "alter_tools.php";

error_checking();
$conn = config();


if (isset($_POST['submit_btn'])) {
    perform_alter($conn);
    display_session_errors();
} else {
    $result = get_result($conn);
    alt($conn);
}

$conn->close();
?>


