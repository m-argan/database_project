<?php
    // "Main" page for display_database, AKA index.php; calls the functions to render the page.
    
    require_once __DIR__ . "/setup_tools.php";
    require_once __DIR__ . "/display_database_tools.php";
    
    // Set up and render page
    error_checking();
    $conn = config();

    if(!isset($_POST['role_admin']) && !isset($_POST['role_student']))
    {render_login($conn, False);}
    // echo(var_dump($_GET));
    elseif(isset($_POST['role_admin']))
        {
            if($_POST['admin_password'] == 'p@ss4CLCDB')
            render_homepage($conn);
            elseif($_POST['admin_password'] != 'p@ss4CLCDB')
            {
                render_login($conn, True);
            }
        }
    elseif(isset($_POST['role_student']))
    {
        //echo "hi student";
        
        // if (session_status() === PHP_SESSION_NONE) {
        //     session_start();
        //     $_SESSION["role"] = "student";
        // }
        include 'calendar_view.php';

    }
    //render_homepage($conn);
    //echo(var_dump($_POST));
    // Close connection

    try{
        $conn->close();
    }
    catch(Exception $e) {
        //
    }
?>