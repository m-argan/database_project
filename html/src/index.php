<?php
    // "Main" page for display_database, AKA index.php; calls the functions to render the page.
    
    require_once __DIR__ . "/setup_tools.php";
    require_once __DIR__ . "/display_database_tools.php";

    // get password
    $password = file_get_contents('../../../password.txt');
    $password = trim($password);
    
    function login($password){
        // If password is correct, display Admin view
        if($_POST['admin_password'] == $password)
        {
            render_homepage($conn);
        }
        elseif($_POST['admin_password'] != $password)
        {
            // Reload login page, but this time with an error message
            render_login($conn, True);
        }
    }
    // Set up and render page
    error_checking();
    $conn = config();

    // If roles have not been set, default to login page
    if(!isset($_POST['role_admin']) && !isset($_POST['role_student']))
    {render_login($conn, False);}

    elseif(isset($_POST['role_admin']))
        {
            login($password);
        }

    elseif(isset($_POST['role_student']))
    {
        include 'calendar_view.php'; 
    }
    
    // Close connection -> commented out because calendary_view already closes the connection

    // try{
    //     $conn->close();
    // }
    // catch(Exception $e) {
    //     echo 'Message: ' .$e->getMessage();
    // }
?>