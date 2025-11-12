<?php
include_once "display_table_tools.php";

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
        $_SESSION['pk_error_msg'] = "Cannot alter primary keys";
        
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
    
?>