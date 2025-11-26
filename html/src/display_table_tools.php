<?php
    require_once __DIR__ . "/delete_tools.php";
    require_once __DIR__ . "/alter_tools.php";
    require_once __DIR__ . "/display_adding_tools.php";
    require_once __DIR__ . "/display_database_tools.php";
   # include "display_adding.php";
    // Function definitions for display_table page.
  

    // Function for formatting table contents:
    function format_result_as_table(mysqli_result $result): void {  
        ?>
        
        <table style="width:100%">
            <thead>
                <tr>
                    <?php
                        // Header rows
                        while ($field = $result->fetch_field()) {
                            echo "<td><b>$field->name</b></td>";
                        }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Data rows
                    while ($row = $result->fetch_row()) {
                        echo "<tr>";
                        for ($i = 0; $i < count($row); $i++) {
                        echo "<td>$row[$i]</td>";
                        }
                        echo "</tr>";
                    }
            ?>
            </tbody>
        </table>
    <?php 
    }


    // Function for formatting table contents -- WITH DELETE CHECKBOXES
    function format_result_as_table_del(mysqli_result $result): void {  
        ?>
        <form method="POST">
        <table style="width:100%">
            <thead>
                <tr>    
                    <!-- Delete row -->    
                    <td><b>Delete?</b></td>
                    <?php
                        // Header rows
                        while ($field = $result->fetch_field()) {
                            echo "<td><b>$field->name</b></td>";
                        }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Data rows
                    $i = 0;
                    while ($row = $result->fetch_row()) { 
                        //Begin row
                        echo "<tr>";

                        // Checkbox
                        $name = "checkbox" . "$i"; ?>
                        <td><input type="checkbox" name="<?= $name ?>" value="<?= $i ?>"
                        
                        /></td>

                        <?php
                        // Data in row
                        for ($j = 0; $j < count($row); $j++) {
                            echo "<td>$row[$j]</td>";
                        }
                        
                        echo "</tr>";
                        // End row

                        $i++;
                    }
            ?>
            </tbody>
        </table>

        <!-- submit button input -->
        <p><input type="submit" name="delbtn" value="Delete Selected Records" /></p>
        <p><input type="submit" name="alter_btn" value="Alter Selected Records" /></p>
        <p><input type="submit" name="add_btn" value="Add Records" /></p>

        </form>
    <?php 
    }


    // Function to prepare statement to display table:
    function prepare_display_table($conn) {
        $query = "SELECT * FROM " . htmlspecialchars( $_GET['tablename'] ) . ";";
        $query = $conn->prepare($query);
        $query->execute();
        $result = $query->get_result();

        return $result;
    }


    // Function for whitelisting possibilities for valid tables:
    function filter_user_input($conn) {
        $result = $conn->query("SHOW TABLES;");

        // Check whether user-inputted string is a valid table name in the database
        while ($tablename = $result->fetch_array()) {
            if ($tablename[0] === htmlspecialchars( $_GET['tablename'] )) {
                $is_a_table = true;
                return true;
            }
        }
        
        // If value not found, display nothing and exit
        echo 'No table of that name found.';
        return false;
        
    }
function render_display_table($conn) {
    $flag = filter_user_input($conn);
    if (!$flag) { exit(); }

    if (isset($_POST['delbtn'])) {
        $result = prepare_display_table($conn);
        delete_records($result, $conn);

        // COPILOT ADDITION
        if (php_sapi_name() !== 'cli' && isset($_SERVER['REQUEST_URI'])) {
            if (php_sapi_name() !== 'cli' && isset($_SERVER['REQUEST_URI'])) {
                header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
                exit();
            }
        }
    }

    if (isset($_POST['submit']) || isset($_POST['yes']) || isset($_POST['no'])) {
        $inserted = input_new_data($conn);

        if ($inserted || isset($_POST['no'])) {
            // Redirect after insert or cancel (PRG)
            // COPILOT ADDITION
            if (php_sapi_name() !== 'cli' && isset($_SERVER['REQUEST_URI'])) {
                header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
                exit();
            }
        }
        
    }

    $result = prepare_display_table($conn);

    if (isset($_POST['alter_btn'])) {
        alt($conn);
        // Redirect after alter (PRG)
       // header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
        exit();
    }

    format_result_as_table_del($result);
    display_session_del_errors();

    if (isset($_POST['add_btn'])) {
        display_adding_forms($conn);
    }
}

     // Function for rendering the table display:
    //  function render_display_table($conn) {
    
    //     $flag = filter_user_input($conn);
    //     if ($flag == false) { exit(); }     // Exit if invalid input; could be dangerous.

    //     $result = prepare_display_table($conn);

    //     if (array_key_exists('delbtn', $_POST)) {
    //         delete_records($result, $conn);

    //         header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    //         exit();
    //     }
        
    //     format_result_as_table_del($result);
    //     display_session_del_errors();

    //     if (array_key_exists('alter_btn', $_POST)) {
    //         // $table = htmlspecialchars($_GET['tablename']);
    //         alt($conn);

    //         // header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    //         // exit();
    //     }
    //     if(array_key_exists('add_btn', $_POST))
    //     {
    //         display_adding_forms($conn);
            
    //     }
    //     if(array_key_exists('submit', $_POST))
    //     {
            
    //         input_new_data($conn);
            
    //        // header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    //     exit(); 
    //     }   
        
    //     // if(array_key_exists('yes', $_POST))
    //     // {
    //     //     if($incomplete == false)
    //     //     {
    //     //         yes_set($conn);}
    //     //     // header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    //     //  exit(); 
    //     // }  

    //  }

    function view_edits($conn, $result, $view)
    {
        format_result_as_table_del($result);

        if (array_key_exists('delbtn', $_POST)) {
             if ($view == 1) { // view 1 = slots
                $result->data_seek(0);
                $all_rows = $result->fetch_all(MYSQLI_ASSOC); 
                // Loop through the table row indexes
                foreach ($all_rows as $i => $row) {
                    $checkbox_name = "checkbox$i";
                    if (isset($_POST[$checkbox_name])) {
                        // Use slot_id from the row for soft delete
                        $slot_id = $row['slot_id'];
                        $result->free(); // free the SP result set
mysqli_next_result($conn); 
                        $stmt = $conn->prepare("UPDATE slots SET deleted_when = NOW() WHERE slot_id = ?");
                        $stmt->bind_param("i", $slot_id);
                        $stmt->execute();
                        $stmt->close();
                    }
    }

            }
            if($view == 3)
            {
                $result->data_seek(0);
                $all_rows = $result->fetch_all(MYSQLI_ASSOC); 
                // Loop through the table row indexes
                foreach ($all_rows as $i => $row) {
                    $checkbox_name = "checkbox$i";
                    if (isset($_POST[$checkbox_name])) {
                        // Use slot_id from the row for soft delete
                        $slot_id = $row['slot_id'];
                        $result->free(); // free the SP result set
mysqli_next_result($conn); 
                        $stmt = $conn->prepare("UPDATE slots SET deleted_when = NOW() WHERE slot_id = ?");
                        $stmt->bind_param("i", $slot_id);
                        $stmt->execute();
                        $stmt->close();
                    }
    }

            }

            if($view == 2)
            {
                $result->data_seek(0);
                $all_rows = $result->fetch_all(MYSQLI_ASSOC); 
                // Loop through the table row indexes
                foreach ($all_rows as $i => $row) {
                    $checkbox_name = "checkbox$i";
                    if (isset($_POST[$checkbox_name])) {
                       
                        $tutor_id = $row['Student ID'];
                        $result->free(); // free the SP result set
                        mysqli_next_result($conn); 
                        $stmt = $conn->prepare("UPDATE tutors SET deleted_when = NOW() WHERE tutor_id = ?");
                        $stmt->bind_param("i", $tutor_id);
                        $stmt->execute();
                        $stmt->close();
                    }
    }

            }

            if($view == 4)
            {
                $result->data_seek(0);
                $all_rows = $result->fetch_all(MYSQLI_ASSOC); 
                // Loop through the table row indexes
                foreach ($all_rows as $i => $row) {
                    $checkbox_name = "checkbox$i";
                    if (isset($_POST[$checkbox_name])) {
                       
                        $tutor_id = $row['Student ID'];
                        $result->free(); // free the SP result set
                        mysqli_next_result($conn); 
                        $stmt = $conn->prepare("UPDATE tutors SET deleted_when = NOW() WHERE tutor_id = ?");
                        $stmt->bind_param("i", $tutor_id);
                        $stmt->execute();
                        $stmt->close();
                    }
    }

            }

            // COPILOT ADDITION
            if (php_sapi_name() !== 'cli' && isset($_SERVER['REQUEST_URI'])) {
                header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
                exit();
            }
        }

            //display_session_del_errors();

        if (array_key_exists('alter_btn', $_POST))
        {
                // $table = htmlspecialchars($_GET['tablename']);
                alt_views($conn, $result, $view);
        }
            // header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
            // exit();

        if(array_key_exists('add_btn', $_POST))
        {
            display_adding_forms_view($conn, $view);

        }


        /*if(array_key_exists('submit', $_POST))
        {
            input_new_data($conn);
            header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
            exit();
        }*/
    }
    // Function for rendering the webpage altogether; called in display_table.php.
    // if $is_init is true, landing page content is displayed. Otherwise, table content
    // is displayed

    // Minor tweaks from Copilot marked
    function render_display_table_page($conn, $is_init = false, $content = '') { ?>
        <!DOCTYPE html>
        <html>
        <head>
          <link rel="stylesheet" href="nav.css">
            <title>CLC Database</title>
        </head>
        <body> 
            <?php
            render_header();
            ?>

            <div class="main"><?php
                render_sidebar($conn); ?>
                <div class="page-content"><?php
                if (!empty($content)) {     // ADDITION FROM COPILOT
                    echo $content;          // ADDITION FROM COPILOT
                } else if ($is_init == True) {    // ADDITION FROM COPILOT ?>
                    <p>Welcome to the Tutoring Database!</p>
                    <p>Select a table name or view to get started</p><?php
                } else {
                    render_display_table($conn);
                }
            ?></div>
            </div>

            <?php
            render_footer();
            ?>

        </body>
        </html>
<?php
    }
    
