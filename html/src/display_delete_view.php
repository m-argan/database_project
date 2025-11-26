<?php
// ADDITIONS FROM COPILOT MARKED
require_once __DIR__ . "/display_views_tools.php";
require_once __DIR__ . "/setup_tools.php";
require_once __DIR__ . "/display_database_tools.php";
require_once __DIR__ . "/display_table_tools.php";
require_once __DIR__ . "/delete_tools.php";

error_checking();
$conn = config();

start_view_capture();   // ADDITION FROM COPILOT

if (isset($_POST['delete_from_view'])) {
    perform_alter_view($conn);
    display_session_errors();
}
else {
    $result = get_result($conn);
    alt($conn);
}

// COPILOT CHANGES BEGIN
if (isset($result) && $result instanceof mysqli_result) {
    $result->free();
    mysqli_next_result($conn);
}

finish_view_capture_and_render($conn, false);
// COPILOT CHANGES END

$conn->close();
?>
