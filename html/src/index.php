<?php
    // "Main" page for display_database, AKA index.php; calls the functions to render the page.
    
    require_once __DIR__ . "/setup_tools.php";
    require_once __DIR__ . "/display_database_tools.php";
    
    // Set up and render page
    error_checking();
    $conn = config();
    render_homepage($conn);

    // Close connection
    $conn->close();
?>