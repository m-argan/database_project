<?php
    // "Main" page for display_table; calls the functions to render the page.
    
    require_once __DIR__ . "/setup_tools.php";
    require_once __DIR__ . "/display_table_tools.php";
    // require_once __DIR__ . "/display_database_tools.php";
    // include_once "alter_database.php";

    session_start();

    error_checking();
    $conn = config();
    render_display_table_page($conn, False);

    // Close connection
    $conn->close();
    
    session_destroy();
?>