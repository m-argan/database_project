<?php
    // "Main" page for display_database, AKA index.php; calls the functions to render the page.
    
    require_once __DIR__ . "/setup_tools.php";
    require_once __DIR__ . "/display_database_tools.php";
    
    error_checking();
    $conn = config();
    render_header_sidebar_footer($conn); // Need a homepage

    $conn->close();
?>