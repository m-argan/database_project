<?php
require_once __DIR__ . "/setup_tools.php";
require_once __DIR__ . "/display_database_tools.php";
require_once __DIR__ . "/display_table_tools.php";
require_once __DIR__ . "/delete_tools.php";

error_checking();
$conn = config();

if (isset($_POST['delete_from_view'])) {
    perform_alter_view($conn);
    display_session_errors();
}
else {
    $result = get_result($conn);
    alt($conn);
}

$conn->close();
?>
