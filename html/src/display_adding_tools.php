<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once "display_table_tools.php";

/**
 * Display add form for a table
 */
function display_adding_forms($conn) {
    $table = $_GET['tablename'] ?? '';
    $fields = get_fields($conn);
    $aut_inc_array = get_aut_inc_keys($conn, $table, $fields);

    // Load previous POST values (for preserving time conversions, etc.)
    $values = $_SESSION['form_values'] ?? [];

    ?>
    <form action="" method="POST">
        <?php foreach ($fields as $field): ?>
            <?php if (!in_array($field->name, $aut_inc_array)): ?>
                <p>
                    <?= htmlspecialchars($field->name) ?>:
                    <input type="text" name="<?= htmlspecialchars($field->name) ?>"
                        value="<?= htmlspecialchars($values[$field->name] ?? '') ?>" />
                </p>
            <?php endif; ?>
        <?php endforeach; ?>
        <p><input type="submit" name="submit" value="submit" /></p>
    </form>
    <?php
    unset($_SESSION['form_values']);
}

/**
 * Get table fields
 */
function get_fields($conn) {
    $result = prepare_display_table($conn);
    $fields = [];
    while ($field = $result->fetch_field()) $fields[] = $field;
    return $fields;
}

/**
 * Handle new data insert
 */
function input_new_data($conn): bool {
    $table = $_GET['tablename'] ?? '';
    $fields = get_fields($conn);
    $aut_inc_array = get_aut_inc_keys($conn, $table, $fields);

    // Check required fields
    foreach ($fields as $field) {
        if (!in_array($field->name, $aut_inc_array) && empty($_POST[$field->name])) {
            echo "<p style='color:red;'>You did not fill in all fields.</p>";
            $_SESSION['form_values'] = $_POST;
            return false;
        }
    }

    // Handle NO button
    if (isset($_POST['no'])) {
        $_SESSION['form_values'] = $_POST;
        return false;
    }

    // Handle time_blocks special case
    if ($table === 'time_blocks') {
        $ok = test_time_blocks($_POST);
        if (!$ok) {
            $_SESSION['form_values'] = $_POST;
            return false;
        }
    }

    // Insert
    insert_into_table($conn, $table, $_POST);
    echo "<p style='color:green;'>Record added successfully.</p>";
    return true;
}

/**
 * Convert and validate time_blocks
 */
function test_time_blocks($data) {
    if (!isset($data['time_block_start'], $data['time_block_end'])) return true;

    $start = strtotime(convert_12h_to_24h($data['time_block_start']));
    $end   = strtotime(convert_12h_to_24h($data['time_block_end']));

    // Store converted times in $_POST for form redisplay
    $_POST['time_block_start'] = date("H:i", $start);
    $_POST['time_block_end']   = date("H:i", $end);

    $min = strtotime("07:30");
    $max = strtotime("21:00");

    // Out-of-hours confirmation
    if ($start < $min || $end > $max) {
        if (!isset($_POST['yes'])) {
            are_you_sure_time($data);
            return false;
        }
    }

    return true;
}

/**
 * Show confirmation form for out-of-hours times
 */
function are_you_sure_time($data) {
    echo "<p>Time is outside normal hours. Are you sure?</p>";
    echo '<form method="POST">';
    foreach ($data as $k => $v) {
        echo '<input type="hidden" name="'.htmlspecialchars($k).'" value="'.htmlspecialchars($v).'">';
    }
    echo '<input type="submit" name="yes" value="Yes">';
    echo '<input type="submit" name="no" value="No">';
    echo '</form>';
}

/**
 * Insert data into table
 */
function insert_into_table($conn, $table, $data) {
    unset($data['submit'], $data['yes'], $data['no'], $data['tablename']);

    $columns = array_keys($data);

    // Check foreign keys
    $foreign_keys = [];
    $fk_sql = "SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
               FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
               WHERE TABLE_SCHEMA = DATABASE()
                 AND TABLE_NAME = '$table'
                 AND REFERENCED_TABLE_NAME IS NOT NULL";
    $fk_result = $conn->query($fk_sql);
    while ($fk = $fk_result->fetch_assoc()) {
        $foreign_keys[$fk['COLUMN_NAME']] = [
            'ref_table' => $fk['REFERENCED_TABLE_NAME'],
            'ref_column' => $fk['REFERENCED_COLUMN_NAME']
        ];
    }

    foreach ($foreign_keys as $col => $ref) {
        if (isset($data[$col])) {
            $val = $data[$col];
            $check = $conn->prepare("SELECT 1 FROM `{$ref['ref_table']}` WHERE `{$ref['ref_column']}` = ?");
            $check->bind_param('s', $val);
            $check->execute();
            $check->store_result();
            if ($check->num_rows == 0) {
                throw new Exception("Value '$val' for column '$col' does not exist in '{$ref['ref_table']}'");
            }
            $check->close();
        }
    }

    $placeholders = implode(', ', array_fill(0, count($columns), '?'));
    $col_list = implode(', ', array_map(fn($c) => "`$c`", $columns));

    $sql = "INSERT INTO `$table` ($col_list) VALUES ($placeholders)";
    $stmt = $conn->prepare($sql);
    $types = str_repeat('s', count($columns));
    $values = array_values($data);
    $stmt->bind_param($types, ...$values);
    $stmt->execute();
    $stmt->close();
}

/**
 * Get auto-increment columns
 */
function get_aut_inc_keys($conn, $table_name, $fields) {
    $stmt = $conn->prepare("SELECT COLUMN_NAME
                            FROM INFORMATION_SCHEMA.COLUMNS
                            WHERE TABLE_SCHEMA = DATABASE()
                              AND TABLE_NAME = ?
                              AND EXTRA LIKE '%auto_increment%';");
    if (!$stmt) die("Prepare failed: " . $conn->error);

    $stmt->bind_param("s", $table_name);
    $stmt->execute();
    $result = $stmt->get_result();

    $auto_inc_columns = [];
    while ($row = $result->fetch_assoc()) $auto_inc_columns[] = $row['COLUMN_NAME'];

    foreach ($fields as $field) {
        if ($field->name === "deleted_when") $auto_inc_columns[] = $field->name;
    }

    $stmt->close();
    return $auto_inc_columns;
}

/**
 * Convert 12h time string to 24h format
 */
function convert_12h_to_24h($t) {
    $t = trim($t);
    $t = preg_replace('/\s+/', ' ', $t);
    $parsed = strtotime($t);
    if ($parsed === false) throw new Exception("Invalid time format: '$t'");
    return date("H:i", $parsed);
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
