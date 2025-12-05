<?php
require_once __DIR__ . "/display_table_tools.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function select_from_db($result, $index, $conn, $row)
{
    ?>
   <form method="POST" action="display_alter.php">
    <!-- This is so that tablename carries over from the first form after it is cleared -->
   <input type="hidden" name="tablename" value="<?php echo htmlspecialchars($_GET['tablename']); ?>">

   <?php

    // echo $result->field_count . " field(s) in results.<br>";

    foreach ($row as $field_name => $value):
        // Prevent "deleted_when" from being edited
        if(htmlspecialchars($field_name) != 'deleted_when')
        {
            echo htmlspecialchars($field_name) . ": ";
            ?>
                <!-- Turns the values of each field into text boxes which are autofilled with existing db data -->
            <input type="text" name="<?php echo htmlspecialchars($field_name); ?>" value="<?php echo htmlspecialchars($value) ?>"/><br>

            <?php 
        }
    endforeach; 
    
    $pk_cols = get_primary_keys($conn, $_GET['tablename']);
    foreach ($pk_cols as $pk) {
        ?>
        <!-- Form also stores PK of selected row to be altered later -->
        <input type="hidden" name="orig_<?php echo $pk ?>" value="<?php echo htmlspecialchars($row[$pk]); ?>">
        <?php
    }
    
    ?><p><input type="submit" name="submit_btn" value="Submit"></p>
    </form>
    <?php
    

}

// Helper function to add primary keys of every table into the $keys array (written by ChatGPT)
function get_primary_keys($conn, $table) {
    $sql = "
        SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = ?
          AND CONSTRAINT_NAME = 'PRIMARY'
        ORDER BY ORDINAL_POSITION";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $table);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $keys = [];
    while ($row = $result->fetch_assoc()) {
        $keys[] = $row['COLUMN_NAME'];
    }
    return $keys;
}

// had to add $doExit for the tests to work
function perform_alter($conn, $doExit = true)
{
    // Gets tablename from the form
    $table = $_POST['tablename'];
    // Gets primary key list from helper function
    $pk_cols = get_primary_keys($conn, $table);
    $where_parts = [];
    $where_params = [];
    $where_types = '';

    // Populating the lists with current data (pulled from form)
    foreach ($pk_cols as $pk) {
        $where_parts[] = "$pk = ?";
        $where_params[] = $_POST["orig_$pk"];
        $where_types .= 's';
    }
    // Forms select statement, binds parameters to the statement
    $sql = "SELECT * FROM ". $table. " WHERE " . implode(" AND ", $where_parts);
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($where_types, ...$where_params);
    $stmt->execute();
    // Query returns "old" data (from past form rather than newly submitted)
    $oldRow = $stmt->get_result()->fetch_assoc();

    if (!$oldRow) return; // Safety check

    // Detect changed fields
    $updates = [];
    $update_params = [];
    $update_types = '';

    // Now pulls values from $_POST (newly submitted form) to compare against old values and determined what to change
    foreach ($_POST as $field => $newValue) {
        if (str_starts_with($field, "orig_") || $field === 'tablename' || $field === 'submit_btn') continue;

        // Only process fields that exist in the database row
        // !! EDIT BY COPILOT !!
        if (!array_key_exists($field, $oldRow)) continue;

        if ($oldRow[$field] !== $newValue) {
            $updates[] = "$field = ?";
            $update_params[] = $newValue;
            $update_types .= 's';
        }
    }

    // If empty (no changes) do nothing
    if (empty($updates)) {
        if ($doExit) {
           //
    header("Location: display_table.php?tablename=" . urlencode($table));
            exit;
        }
        return true;
    }

    // Constructs update statement
    $sql = "UPDATE ".$table." SET " . implode(", ", $updates) .
           " WHERE " . implode(" AND ", $where_parts);

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($update_types . $where_types, ...$update_params, ...$where_params);

    // If user attempts to change primary key(s), throws exception 
    try {
        $stmt->execute();
    } catch (mysqli_sql_exception $error) {
        echo "<p style='color:red;'>This value cannot be edited!.</p>";
        //$_SESSION['pk_error_msg'] = "Cannot alter primary keys";
        
    }
    
    // Redirects to make updates display on webpage
    if(isset($_SESSION['pk_error_msg']))
    {
        display_session_errors();
    }
    else if ($doExit) {
        header("Location: display_table.php?tablename=" . urlencode($table));
        exit;
    }
    return true;
}


// Helper function to get all results after a table is selected
function get_result($conn)
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // echo "Table: " . htmlspecialchars($_GET['tablename'] ). "<br>";
 
        $query = "SELECT * FROM ".htmlspecialchars($_GET['tablename']).";";
             $stmt = $conn->prepare($query);
              if (!$stmt) {
                 echo "Couldn't prepare statement!";
                 echo exit;
             }
     
             $stmt->execute();
             $result = $stmt->get_result();
    return $result;
    }
}

function display_session_errors(){

    if (array_key_exists('pk_error_msg', $_SESSION) && $_SESSION['pk_error_msg'] == true) {
        echo "<p style='color:red;'>This value cannot be edited!.</p>";
        unset($_SESSION['pk_error_msg']);
    }

}

// Function to determine which checkbox was selected (initially was going to use index but switched to pk)
// Returns -1 if more than one checkbox has been selected, and -2 if no checkbox has been selected
function check_boxes($result, $conn)
{
    $count = 0;
    for ($i=0; $i < $result->num_rows; $i++) {     
        $row = $result->fetch_row();              
        $name = "checkbox" . "$i";                 
        if (array_key_exists($name, $_POST)) {
            $count++;
            if($count > 1)
            {
                return -1;
            }
            else{$index = $i;}
        }
    }
    if($count === 0)
    {
        return -2;
    }
    return $index;
}

function alt($conn)
{
    $result = get_result($conn);
    $res = check_boxes($result, $conn);
    if($res>= 0)
    {
        $result->data_seek($res);
        $row = $result->fetch_assoc();
        if(array_key_exists('deleted_when', $row))
        {
            if($row['deleted_when'] == '0000-00-00 00:00:00')
            {
                select_from_db($result, $res, $conn, $row);
            }
            else
            {
                echo "Cannot alter entry which has already been deleted";
            }
        }
        else
        {
            select_from_db($result, $res, $conn, $row);
        }
    }
    else if($res === -2)
    {
        echo "Please select an entry to edit";
    }
    else echo "Please only select one entry to edit at a time";
}

//Helper function which allows views to be altered
//Edited version of alt()
function alt_views($conn, $result, $view)
{
        $res = check_boxes($result, $conn);
        if($res>= 0)
    {
        $result->data_seek($res);
        $row = $result->fetch_assoc();
        if(array_key_exists('deleted_when', $row))
        {
            if($row['deleted_when'] == '0000-00-00 00:00:00')
            {
                select_view_from_db($result, $res, $conn, $row, $view);
            }
            else
            {
                echo "Cannot alter entry which has already been deleted";
            }
        }
        else
        {
            select_view_from_db($result, $res, $conn, $row, $view);
        }
    }
    else if($res === -2)
    {
        echo "Please select an entry to edit";
    }
    else echo "Please only select one entry to edit at a time";
}

//Function which creates alter table for views - edited from select_from_db
//to-do: add pks/fks to some array, which can be accessed later by a query
//create a new perform_alter function which alters all necessary tables in view
function select_view_from_db($result, $index, $conn, $row, $view)
{
?>
<form method="POST" action="display_alter_view.php">
<?php
        //Used for perform_alter_view
        if ($view == 1)
        {
                ?>
                <input type="hidden" name="view" value=1>
                <?php
        }
        if ($view == 2)
        {
                ?>
                <input type="hidden" name="view" value=2>
                <?php
        }
        if ($view == 3)
        {
                ?>
                <input type="hidden" name="view" value=3>
                <?php
        }
        //Prevents this view from being altered
        //Little user friendliness is lost by preventing this alter function, as all but student name is retained in the agreed/qualified tables   
        if ($view == 4)
        {
                echo "Please alter Tutor Agreed Classes and Tutor Qualified Subjects in their respective tables";
        }
        else
        {
        foreach ($row as $field_name => $value):
                //if statements handle concat - name, class, etc
                if (htmlspecialchars($field_name) == 'Name')
                {
                        $tokens = explode(" ", htmlspecialchars($value));
                        $first = $tokens[0];
                        $last = $tokens[1];
                        echo "First Name: ";
                        ?>
                        <input type="text" name="first" value="<?php echo htmlspecialchars($first) ?>"/><br>
                        <?php
                        echo "Last Name: ";
                        ?>
                        <input type="text" name="last" value="<?php echo htmlspecialchars($last) ?>"/><br>
                        <?php
                }
                else if (htmlspecialchars($field_name) == 'Student ID')
                {
                        echo "Student ID: ";
                                ?>
                                <input type="number" name="tutorid" value="<?php echo htmlspecialchars($value)?>" /><br>
                                <input type="hidden" name="old_tutorid" value="<?php echo htmlspecialchars($value)?>"/>
                        <?php
                }
                 else if (htmlspecialchars($field_name) == 'Email')
                {
                        echo "Email: ";
                                ?>
                                <input type="text" name="email" value="<?php echo htmlspecialchars($value)?>" /><br>
                                <input type="hidden" name="old_email" value="<?php echo htmlspecialchars($value)?>"/>
                        <?php
                 }
                else if (htmlspecialchars($field_name) == 'Location')
                {
                        $tokens = explode(" ", htmlspecialchars($value));
                        $building = $tokens[0];
                        $room = $tokens[1];
                        echo "Building: ";
                        ?>
                        <input type="text" name="building" value="<?php echo htmlspecialchars($building) ?>"/><br>
                        <input type="hidden" name="old_building" value="<?php echo htmlspecialchars($building)?>"/>
                        <?php
                        echo "Room: ";
                        ?>
                        <input type="number" name="room" value="<?php echo htmlspecialchars($room) ?>"/><br>
                        <input type="hidden" name="old_room" value="<?php echo htmlspecialchars($room)?>"/>
                        <?php
                }
                else if (htmlspecialchars($field_name) == 'Class' || htmlspecialchars($field_name) == 'Classes Taught')
                {
                        $tokens = explode(" ", htmlspecialchars($value));
                        $class = $tokens[0];
                        $number = $tokens[1];
                        echo "Subject Code: ";
                        ?>
                        <input type="text" name="subject" value="<?php echo htmlspecialchars($class) ?>"/><br>
                        <input type="hidden" name="old_subject" value="<?php echo htmlspecialchars($class)?>"/>
                        <?php
                        echo "Class Code: ";
                        ?>
                        <input type="number" name="class" value="<?php echo htmlspecialchars($number) ?>"/><br>
                        <input type="hidden" name="old_class" value="<?php echo htmlspecialchars($number)?>"/>
                        <?php
                }
                else if (htmlspecialchars($field_name) == 'Time Tutored')
                {
                        $tokens = explode(" ", htmlspecialchars($value));
                        $start = $tokens[0];
                        $end = $tokens[2];
                        $day = $tokens[3];
                        echo "Time Start: ";
                        ?>
                        <input type="text" name="start" value="<?php echo htmlspecialchars($start) ?>"/><br>
                        <input type="hidden" name="old_start" value="<?php echo htmlspecialchars($start)?>"/>
                        <?php
                        echo "Time End: ";
                        ?>
                        <input type="text" name="end" value="<?php echo htmlspecialchars($end) ?>"/><br>
                        <input type="hidden" name="old_end" value="<?php echo htmlspecialchars($end)?>"/>
                        <?php
                        echo "Day: ";
                        ?>
                        <input type="text" name="week" value="<?php echo htmlspecialchars($day) ?>"/><br>
                        <input type="hidden" name="old_week" value="<?php echo htmlspecialchars($day)?>"/>
                        <?php
                }
                else if (htmlspecialchars($field_name) == 'Semester Taught')
                {
                        $tokens = explode(" ", htmlspecialchars($value));
                        $semester = $tokens[0];
                        $year = $tokens[1];
                        if ($view == 2)
                        {
                                echo "Semester: ";
                                echo htmlspecialchars($semester);
                                echo "<br>";
                                echo "Year: ";
                                echo htmlspecialchars($year);
                                echo "<br>";
                        }
                        else
                        {
                                echo "Semester: ";
                                ?>
                                <input type="text" name="semester" value="<?php echo htmlspecialchars($semester) ?>"/><br>
                                <input type="hidden" name="old_semester" value="<?php echo htmlspecialchars($semester)?>"/>
                                <?php
                                echo "Year: ";
                                ?>
                                <input type="number" name="year" value="<?php echo htmlspecialchars($year) ?>"/><br>
                                <input type="hidden" name="old_year" value="<?php echo htmlspecialchars($year)?>"/>
<?php

                        }
                }
        endforeach;

    ?><p><input type="submit" name="submit_btn" value="Submit"></p>
    </form>
<?php
        }
}

//Function which alters views using a transaction
//Created by writing function, then asking Gemini AI for improvements to make code functional
function perform_alter_view($conn)
{
        $view = $_POST['view'];
        if ($view == 1)
        {
                // Begin a transaction to ensure all or no changes are committed
                $conn->begin_transaction();

                try {
                // --- 1. Retrieve the old and new time_block_id values ---

                // Prepare statement to get old time_block_id
                $old_time_sql = "SELECT time_block_id FROM time_blocks WHERE time_block_start = ? AND time_block_end = ? AND week_day_name = ? AND term_code = ? AND year_term_year = ?";
                $old_time_stmt = $conn->prepare($old_time_sql);
                $old_time_stmt->bind_param("sssss", $_POST['old_start'], $_POST['old_end'], $_POST['old_week'], $_POST['old_semester'], $_POST['old_year']);
                $old_time_stmt->execute();
                $old_time_result = $old_time_stmt->get_result();
                $old_time_row = $old_time_result->fetch_assoc();
                $old_time_id = $old_time_row ? $old_time_row['time_block_id'] : null;

                // Prepare statement to get new time_block_id
                $new_time_sql = "SELECT time_block_id FROM time_blocks WHERE time_block_start = ? AND time_block_end = ? AND week_day_name = ? AND term_code = ? AND year_term_year = ?";
                $new_time_stmt = $conn->prepare($new_time_sql);
                $new_time_stmt->bind_param("sssss", $_POST['start'], $_POST['end'], $_POST['week'], $_POST['semester'], $_POST['year']);
                $new_time_stmt->execute();
                $new_time_result = $new_time_stmt->get_result();
                $new_time_row = $new_time_result->fetch_assoc();
                $new_time_id = $new_time_row ? $new_time_row['time_block_id'] : null;

                if (!$old_time_id || !$new_time_id) {
                        throw new Exception("Error: One or more time blocks not found. Please add this time to the time_blocks table first");      
                }

                // --- 2. Update the 'tutors' table ---

                $tutor_sql = "UPDATE tutors SET tutor_first_name = ?, tutor_last_name = ? WHERE tutor_id = ?";
                $tutor_stmt = $conn->prepare($tutor_sql);
                $tutor_stmt->bind_param("ssi", $_POST['first'], $_POST['last'], $_POST['old_tutorid']);
                $tutor_stmt->execute();

                // --- 3. Update the 'slots' table ---

                $slots_sql = "UPDATE slots SET class_number = ?, subject_code = ?, time_block_id = ?, tutor_id = ? WHERE tutor_id = ? AND subject_code = ? AND class_number = ? AND time_block_id = ?";
                $slots_stmt = $conn->prepare($slots_sql);
                $slots_stmt->bind_param("isiiisii", $_POST['class'], $_POST['subject'], $new_time_id, $_POST['tutorid'], $_POST['old_tutorid'], $_POST['old_subject'], $_POST['old_class'], $old_time_id);
                $slots_stmt->execute();
                // Commit if all updates were successful
                $conn->commit();
                echo "Record updated successfully.";

                } catch (Exception $e) {
                // Rollback the transaction on error
                $conn->rollback();
                echo "Error: " . $e->getMessage();
                }
        }
        elseif ($view == 2)
        {
                $conn->begin_transaction();
                try {
                // --- 1. Update the 'tutors' table ---

                $tutor_sql = "UPDATE tutors SET tutor_first_name = ?, tutor_last_name = ?, tutor_email = ? WHERE tutor_id = ?";
                $tutor_stmt = $conn->prepare($tutor_sql);
                $tutor_stmt->bind_param("sssi", $_POST['first'], $_POST['last'], $_POST['email'], $_POST['old_tutorid']);
                $tutor_stmt->execute();

                // --- 2. Update the 'slots' table ---

                $slots_sql = "UPDATE slots SET class_number = ?, subject_code = ?, tutor_id = ? WHERE tutor_id = ? AND subject_code = ? AND class_number = ?";
                $slots_stmt = $conn->prepare($slots_sql);
                $slots_stmt->bind_param("isiiis", $_POST['class'], $_POST['subject'], $_POST['tutorid'], $_POST['old_tutorid'], $_POST['old_subject'], $_POST['old_class']);
                $slots_stmt->execute();
                // Commit if all updates were successful
                $conn->commit();
                echo "Record updated successfully.";

                } catch (Exception $e) {
                // Rollback the transaction on error
                $conn->rollback();
                echo "Error: " . $e->getMessage();
                }
        }
        elseif ($view == 3)
        {
                $conn->begin_transaction();
                try {
                        // --- 1. Retrieve the old time_block_id ---
                        $old_time_sql = "SELECT time_block_id FROM time_blocks WHERE time_block_start = ? AND time_block_end = ? AND week_day_name = ? AND term_code = ? AND year_term_year = ?";
                        $old_time_stmt = $conn->prepare($old_time_sql);
                        $old_time_stmt->bind_param("sssss", $_POST['old_start'], $_POST['old_end'], $_POST['old_week'], $_POST['old_semester'], $_POST['old_year']);
                        $old_time_stmt->execute();
                        $old_time_result = $old_time_stmt->get_result();
                        $old_time_row = $old_time_result->fetch_assoc();
                        $old_time_id = $old_time_row ? $old_time_row['time_block_id'] : null;

                        // --- 2. Retrieve or create the new time_block_id ---
                        $new_time_sql = "SELECT time_block_id FROM time_blocks WHERE time_block_start = ? AND time_block_end = ? AND week_day_name = ? AND term_code = ? AND year_term_year = ?";
                        $new_time_stmt = $conn->prepare($new_time_sql);
                        $new_time_stmt->bind_param("sssss", $_POST['start'], $_POST['end'], $_POST['week'], $_POST['semester'], $_POST['year']);   
                        $new_time_stmt->execute();
                        $new_time_result = $new_time_stmt->get_result();
                        $new_time_row = $new_time_result->fetch_assoc();
                        $new_time_id = $new_time_row ? $new_time_row['time_block_id'] : null;

                        if (!$new_time_id) {
                                // If the new time block does not exist, insert it
                                $insert_time_sql = "INSERT INTO time_blocks (time_block_start, time_block_end, week_day_name, term_code, year_term_year) VALUES (?, ?, ?, ?, ?)";
                                $insert_time_stmt = $conn->prepare($insert_time_sql);
                                $insert_time_stmt->bind_param("sssss", $_POST['start'], $_POST['end'], $_POST['week'], $_POST['semester'], $_POST['year']);
                                $insert_time_stmt->execute();
                                $new_time_id = $conn->insert_id; // Get the ID of the newly inserted row
                        }

                        if (!$old_time_id) {
                                throw new Exception("Error: Original time block could not be found.");
                        }

                        $new_building = trim($_POST['building']);
                        $new_room = trim($_POST['room']);

                        $place_sql = "SELECT COUNT(*) FROM places WHERE building_name = ? AND place_room_number = ?";
                        $place_stmt = $conn->prepare($place_sql);
                        $place_stmt->bind_param("si", $new_building, $new_room);
                        $place_stmt->execute();
                        $place_result = $place_stmt->get_result();
                        $place_count = $place_result->fetch_array()[0];

                        if ($place_count == 0) {
                                // Creates new place if new place did not exist
                                $insert_place_sql = "INSERT INTO places (building_name, place_room_number) VALUES (?, ?)";
                                $insert_place_stmt = $conn->prepare($insert_place_sql);
                                $insert_place_stmt->bind_param("si", $new_building, $new_room);
                                $insert_place_stmt->execute();
                        }

                        // --- 2. Build the UPDATE query dynamically ---
                        $update_parts = [];
                        $bind_params = [];
                        $type_string = "";

                        // Check for changes and build the update parts
                        if ($_POST['class'] !== $_POST['old_class']) {
                                $update_parts[] = "class_number = ?";
                                $bind_params[] = $_POST['class'];
                                $type_string .= "i";
                        }
                        if ($_POST['subject'] !== $_POST['old_subject']) {
                                $update_parts[] = "subject_code = ?";
                                $bind_params[] = $_POST['subject'];
                                $type_string .= "s";
                        }
                        if ($new_time_id !== $old_time_id) {
                                $update_parts[] = "time_block_id = ?";
                                $bind_params[] = $new_time_id;
                                $type_string .= "i";
                        }
                        if ($_POST['room'] !== $_POST['old_room']) {
                                $update_parts[] = "place_room_number = ?";
                                $bind_params[] = $_POST['room'];
                                $type_string .= "i";
                        }
                        if ($_POST['building'] !== $_POST['old_building']) {
                                $update_parts[] = "building_name = ?";
                                $bind_params[] = $_POST['building'];
                                $type_string .= "s";
                        }

                        // If no fields have changed, just commit and exit
                        if (empty($update_parts)) {
                                $conn->commit();
                                echo "No changes were made.";
                                return;
                        }

                        // --- 3. Append WHERE clause parameters ---
                        $where_params = [$_POST['old_room'], $_POST['old_building'], $_POST['old_subject'], $_POST['old_class'], $old_time_id];    
                        $where_types = "isssi";

                        $bind_params = array_merge($bind_params, $where_params);
                        $type_string .= $where_types;

                        // --- 4. Prepare and execute the dynamic UPDATE query ---
                        $slots_sql = "UPDATE slots SET " . implode(", ", $update_parts) . " WHERE place_room_number = ? AND building_name = ? AND subject_code = ? AND class_number = ? AND time_block_id = ?";
                        $slots_stmt = $conn->prepare($slots_sql);
                        $slots_stmt->bind_param($type_string, ...$bind_params);
                        $slots_stmt->execute();

                        $conn->commit();
                        echo "Record updated successfully.";

                } catch (Exception $e) {
                $conn->rollback();
                echo "Error: " . $e->getMessage();
                }
        }
}

?>
