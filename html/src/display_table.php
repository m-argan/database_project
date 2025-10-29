<?php
    // "Main" page for display_table; calls the functions to render the page.
    
    include_once "setup_tools.php";
    include_once "display_table_tools.php";

    error_checking();
    $conn = config();
    render_display_table_page($conn);

    // Close connection
    $conn->close();
?>