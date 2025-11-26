<?php
    // Functions that facilitate the deletion of records on display_table.php.
    // Note these functions will never run unless the user input has passed validity check first.


     // Function that queries database to soft delete a record from classes, slots, or tutors.
    function soft_delete($row, $conn) {
        // Use __DIR__ to construct a reliable path from current file location
        $base_path = dirname(dirname(__DIR__)) . '/prepared_statements/';
        
        if (htmlspecialchars( $_GET['tablename'] ) == 'classes') {
            $del_stmt = file_get_contents($base_path . "soft_delete_classes.sql");
            $del_stmt = $conn->prepare($del_stmt);
            $del_stmt->bind_param('ss', $row[1], $row[2]);
        } 
        
        else if (htmlspecialchars( $_GET['tablename'] ) == 'slots') {
            $del_stmt = file_get_contents($base_path . "soft_delete_slots.sql");
            $del_stmt = $conn->prepare($del_stmt);
            $del_stmt->bind_param('i', $row[1]);
        } 
        
        else if (htmlspecialchars( $_GET['tablename'] ) == 'tutors') {
            $del_stmt = file_get_contents($base_path . "soft_delete_tutors.sql");
            $del_stmt = $conn->prepare($del_stmt);
            $del_stmt->bind_param('s', $row[1]);
        }

        if (isset($del_stmt) && $del_stmt !== false) {
            $del_stmt->execute();
        }

    }
    

    // Function to help build a DELETE query on the fly, using the row selected by the checkbox.
    function build_delete_statement($result, $conn) {
        $tablename = htmlspecialchars( $_GET['tablename'] );

        $del_stmt = "DELETE FROM " . $tablename . " WHERE ";    // Begin statement

        // Add parameters to WHERE clause. Since it could be any table, add all fields/values.
        for ($i=0; $i < $result->field_count; $i++) {
            if ($i > 0) {
                $del_stmt = $del_stmt . " AND ";
            }
            $del_stmt = $del_stmt . $result->fetch_fields()[$i]->name . " = ? ";
        }
        
        $del_stmt = $del_stmt . ";";    // Final semicolon

        return $del_stmt;
    }


    // Function to help build the field string which is the first parameter in the
    // bind_param() method.
    function build_field_str($del_stmt, $result) {
        $field_str = '';
        for ($i=0; $i < $result->field_count; $i++) {
            
            $field_type = $result->fetch_fields()[$i]->type;    // Fetch a field and its type
            
            // Determine type and append to field string
            switch ($field_type) {
                case MYSQLI_TYPE_DECIMAL:
                case MYSQLI_TYPE_FLOAT:
                case MYSQLI_TYPE_DOUBLE:
                    $field_str = $field_str . 'd';
                    break;

                case MYSQLI_TYPE_BIT:
                case MYSQLI_TYPE_TINY:
                case MYSQLI_TYPE_SHORT:
                case MYSQLI_TYPE_LONG:
                case MYSQLI_TYPE_LONGLONG:
                case MYSQLI_TYPE_INT24:
                case MYSQLI_TYPE_YEAR:
                case MYSQLI_TYPE_ENUM:
                    $field_str = $field_str . 'i'; 
                    break;
                
                case MYSQLI_TYPE_TIMESTAMP:
                case MYSQLI_TYPE_DATE:
                case MYSQLI_TYPE_TIME:
                case MYSQLI_TYPE_DATETIME:
                case MYSQLI_TYPE_VAR_STRING:
                case MYSQLI_TYPE_STRING:
                case MYSQLI_TYPE_CHAR:
                    $field_str = $field_str . 's';
                    break;  

                default:
                    $field_str = $field_str . 's';
                }
        }

        return $field_str;

    }


    // Function for deleting records based on checked checkboxes.
    function delete_records($result, $conn) {
        $del_stmt = build_delete_statement($result, $conn); // Build DELETE statement skeleton using
                                                            // function that looks at table structure.
        $del_stmt = $conn->prepare($del_stmt);

        for ($i=0; $i < $result->num_rows; $i++) {      // Loop through records; check if $_POST
            $row = $result->fetch_row();                // has a corresponding entry.
            $name = "checkbox" . "$i";                  

            if (array_key_exists($name, $_POST)) {
                $field_str = build_field_str($del_stmt, $result);   // Build field string
                $del_stmt->bind_param($field_str, ...$row);         // Bind parameters using unpacking

                // Try-catch, since DELETE may fail for a number of reasons.
                try {
                    $del_stmt->execute();
                } catch (mysqli_sql_exception $error) {
                    // If delete fails due to soft delete requirement, call soft_delete().
                    if ($error->getMessage() === 'Delete failed. Must soft delete.') {
                        soft_delete($row, $conn);
                    }
                    // Foreign key constraint failure
                    else if (str_contains($error->getMessage(), 'Cannot delete or update a parent row: a foreign key constraint fails')) {
                        $_SESSION['fk_delete'] = true;  // Log into $_SESSION so this error
                                                        // is retained across states and can display.
                    }
                    else {
                        $_SESSION['other_delete_error'] = true;
                    }
                } 

            }
        }

    }


    // Display error messages relating to deletion, if any. Called at the end of the page.
    function display_session_del_errors(){

        if (array_key_exists('fk_delete', $_SESSION) && $_SESSION['fk_delete'] == true) {
            echo "<p style='color:red;'>Delete failed due to foreign key constraint.</p>";
            unset($_SESSION['fk_delete']);
        }
        if (array_key_exists('other_delete_error', $_SESSION) && $_SESSION['other_delete_error'] == true) {
            echo "<p style='color:red;'>Delete failed due to an unknown error.</p>";
            unset($_SESSION['other_delete_error']); 
        }

    }


?>