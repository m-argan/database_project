<?php
    // "Main" page for display_database, AKA index.php; calls the functions to render the page.
    
    require_once __DIR__ . "/setup_tools.php";
    require_once __DIR__ . "/display_database_tools.php";

    // get password
    $password = file_get_contents('../../../password.txt');
    $password = trim($password);
    
    echo(var_dump($_POST));
    // Set up and render page
    error_checking();
    $conn = config();

    if(!isset($_POST['role_admin']) && !isset($_POST['role_student']))
    {render_login($conn, False);}
    // echo(var_dump($_GET));
    elseif(isset($_POST['role_admin']))
        {
            if($_POST['admin_password'] == $password)
            {
                render_homepage($conn);
            }
            elseif($_POST['admin_password'] != $password)
            {
                render_login($conn, True);
            }
        }
    elseif(isset($_POST['role_student']))
    {
        // if (session_status() === PHP_SESSION_NONE) {
        //     session_start();
        //     $_SESSION["role"] = "student";
        // }
        ?>
        <h2>Select a Subject or Class:</h2>
        <form method="POST">
                <p>Subject code(e.g. HIS, MAT): <input type="text" name="subject" /></p>
                <p>Class (e.g. 110, 330): <input type="integer" name="class" /></p>
                <p><input type="submit" value="See Details"/></p>
        </form> <?php
        include 'calendar_view.php'; 

    }
    
    // Close connection

    // try{
    //     $conn->close();
    // }
    // catch(Exception $e) {
    //     echo 'Message: ' .$e->getMessage();
    // }
?>