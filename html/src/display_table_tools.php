<?php
    include "delete_tools.php";
    include "alter_tools.php";

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
                    <!-- Update row -->    
                    <td><b>Update?</b></td>
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


     // Function for rendering the webpage; called in display_table.php:
     function render_display_table_page($conn) {
        $flag = filter_user_input($conn);
        if ($flag == false) { exit(); }     // Exit if invalid input; could be dangerous.

        $result = prepare_display_table($conn);

        if (array_key_exists('delbtn', $_POST)) {
            delete_records($result, $conn);

            header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
            exit();
        }
        
        format_result_as_table_del($result);
        display_session_del_errors();

        if (array_key_exists('alter_btn', $_POST)) {
            // $table = htmlspecialchars($_GET['tablename']);
            alt($conn);

            // header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
            // exit();
        }

     }
    
