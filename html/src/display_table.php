<?php
    // "Main" page for display_table; calls the functions to render the page.
    
    require_once __DIR__ . "/setup_tools.php";
    require_once __DIR__ . "/display_table_tools.php";

    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
    session_start();
    }

    // Setup and render page
    error_checking();
    $conn = config();
    render_display_table_page($conn, False);

    // Close connection
    $conn->close();
    
    // Close session
    session_destroy();
?>