<?php
    // "Main" page for display_database, AKA index.php; calls the functions to render the page.
    
    require_once __DIR__ . "/setup_tools.php";
    require_once __DIR__ . "/display_database_tools.php";
    
    // Set up and render page
    error_checking();
    $conn = config();

    if(!isset($_GET['role_admin']) && !isset($_GET['role_student']))
    {render_login($conn);}
    elseif(isset($_GET['role_admin']))
        {
            render_homepage($conn);
        }
    elseif(isset($_GET['role_student']))
    {
        echo "hi student";
    }
    //render_homepage($conn);

    // Close connection
    $conn->close();
?>