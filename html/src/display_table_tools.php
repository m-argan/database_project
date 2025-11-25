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
                    <td><b>Alter?</b></td>
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

        header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
        exit();
    }

    if (isset($_POST['submit']) || isset($_POST['yes']) || isset($_POST['no'])) {
        $inserted = input_new_data($conn);

        if ($inserted || isset($_POST['no'])) {
            // Redirect after insert or cancel (PRG)
            header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
            exit();
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

        /*if (array_key_exists('delbtn', $_POST)) {
            delete_records_view($result, $conn, $view);

            header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
            exit();
            }*/

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
    function render_display_table_page($conn, $is_init) { ?>
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
                if($is_init == True){?>
                    <p>Welcome to the Tutoring Database!</p>
                    <p>Select a table name or view to get started</p><?php
                }
                else{
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
    
function display_adding_forms_view($conn, $view)
{
        if ($view == 1 || $view == 2 || $view == 3)
        {
                echo "Please add new slots through the slot table. This ensures that all values are filled out";
        }
        else
        {
?>
<form method="POST" action="display_add_view.php">
<p>Student ID: <input type="number" name="tutorid" /></p>
                <p>New Agreed Subject Code: <input type="text" name="agreed_subject" /></p>
                <p>New Agreed Class Code: <input type="number" name="agreed_class" /></p>
<p><input type="submit" name="submit_btn" value="submit"></p>
                <?php
        }
}

function perform_add_view($conn)
{
        // Get user input and sanitize.
    $tutorid = htmlspecialchars($_POST['tutorid']);
    $agreed_subject = htmlspecialchars($_POST['agreed_subject']);
    $agreed_class = htmlspecialchars($_POST['agreed_class']);

    // Check if the subject and class exist in the 'classes' table.
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM classes WHERE subject_code = ? AND class_number = ?");
    $check_stmt->bind_param("si", $agreed_subject, $agreed_class);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['COUNT(*)'] == 0) {
        // The class does not exist, so insert it into the parent table first.
        $add_class_stmt = $conn->prepare("INSERT INTO classes (subject_code, class_number, class_name) VALUES (?, ?, '')");
        $add_class_stmt->bind_param("si", $agreed_subject, $agreed_class);
        $add_class_stmt->execute();
        $add_class_stmt->close();
    }
    $check_stmt->close();

    // Now, insert the record into the 'tutor_agreed_classes' child table.
    $add_stmt = $conn->prepare("INSERT INTO tutor_agreed_classes (tutor_id, subject_code, class_number) VALUES (?, ?, ?)");
    $add_stmt->bind_param("isi", $tutorid, $agreed_subject, $agreed_class);
    $add_stmt->execute();
    $add_stmt->close();
}

?>