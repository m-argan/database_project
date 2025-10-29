<?php
    // Function definitions for display_table page.


    // Function for formatting table contents:
    function format_result_as_table(mysqli_result $result): void {  
        $output_string = "";
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
        if ($flag == false) { exit(); }
        
        $result = prepare_display_table($conn);
        format_result_as_table($result);
     }
    
