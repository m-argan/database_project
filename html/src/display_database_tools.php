<?php
    // Function definitions for display_database, AKA index.php.
    
    require_once __DIR__ . "/display_table_tools.php";


    // Function for listing links to the tables of the database, displayed in sidebar.
    function list_tables($conn) {
        $dblist = "SHOW TABLES";
        $result = $conn->query($dblist);

        echo "<ul>";
        while ($tablename = $result->fetch_array()) {
           $url = "display_table.php?tablename=".$tablename[0];
            echo '<br><a href="'.$url.'">' . $tablename[0] . '</a><br>';

        }
        echo "</ul>";
    }

    // Function for displaying the links to the views, displayed in header.
    function display_form() { ?>
        <table style="border-style: none">
                <tr style="border-style: none">
                        <td style="border-style: none"><a href="student_history_view.php">View Student</a></td>
                        <td style="border-style: none"><a href="student_schedule_view.php">View Student Schedule</a></td>
                        <td style="border-style: none"><a href="full_schedule_view.php">View Full Schedule</a></td>
                        <td style="border-style: none"><a href="student_agreement_view.php">View Student Agreement Forms</a></td>
                        <td style="border-style: none"><a href="calendar_view.php">View Current Tutoring Calendar</a></td>
                </tr>
        </table>
    <?php
    }


    // Function for rendering the HTML structure for the header of the page.
    function render_header() { ?>
        <div class="header">
            <div class="headertitle">
                <h1 style="color:red;"> CLC Database <?php echo "<a href='index.php'>Back to login</a>";?> </h1>
            </div>
            <div class="topnav">
                <?php
                // Displays the views
                display_form();
            ?></div>
        </div>
    <?php }

    // Function for rendering the HTML structure for the sidebar of the page.
    function render_sidebar($conn) { ?>
        <div class="sidenav"><?php
            // List the tables of the database
            list_tables($conn);
        ?>
        </div>
   <?php 
    }

    // Function for rendering the HTML structure for the footer of the page. 
    function render_footer() { ?>
    <footer>
            <br>
            <p>CSC 362: Database Systems Fall 2025</p>
            <p>Developed by Hannah Morrison, Stella Green, Madeleine Arganbright, Jenna Nicodemus</p>
    </footer>
  <?php 
    }

    function render_login($conn, $display_error)
    {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <link rel="stylesheet" href="nav.css">
            <div class="header">
                <div class="headertitle">
                    <h1>Login</h1>
                </div>
            <h1> STUDENTS-> Click the button below to view the schedule as a guest: <h1>
                <form method="POST">    
                <input type="submit" name="role_student" value="Student View" />
            <h4> ADMIN-> Enter your password to view the schedule builder: <h4>
                <input type="text" name="admin_password" placeholder='Enter password'/>
                <input type="submit" name="role_admin" value="Login" />
                </form>
            </div>

       
        <?php

        if($display_error == True)
        {
            //echo "Incorrect password, please try again";
            ?> <p><small><small> Incorrect password, please try again.</small></small></p> <?php
            //echo "<p <small>style='color:red;'>Incorrect password, please try again.<small></p>";
            $display_error = False;
        }

       // if jess:
       // render_homepage($conn);
    }


    // Homepage rendering function. Calls function in display_table_tools.php.
    function render_homepage($conn)
    {
        render_display_table_page($conn, True);
    }

    // Backwards-compatible wrapper function, expected by tests.
    // !! WRITTEN BY COPILOT !!
    function render_display_database_page($conn) {
        // Show the homepage/content using the renderer
        if (function_exists('render_display_table_page')) {
            render_display_table_page($conn, True);
            return;
        }
        // Fallback: attempt to render header/sidebar/footer
        render_header_sidebar_footer($conn);
    }

    // Function that renders the header, sidebar, and footer all together.
    function render_header_sidebar_footer($conn) { ?>
        <div class="header">
            <div class="headertitle">
                <h1>CLC Database</h1>
            </div>

            <div class="topnav">
                <?php
                // Displays the views
                 display_form();
            ?></div>
        </div>

        <div class="sidenav"><?php
            // List the tables of the database
            list_tables($conn);
        ?>
        </div>

        <footer>
            <br>
            <p>CSC 362: Database Systems Fall 2025</p>
            <p>Developed by Hannah Morrison, Stella Green, Madeleine Arganbright, Jenna Nicodemus</p>
        </footer>

<?php    }

?>

