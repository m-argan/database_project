<?php //Code below written by user then run through chatgpt to make more efficient.
 function display_adding_forms($conn) { 
    $table = $_GET['tablename'] ?? '';
    $result = prepare_display_table($conn);
    $aut_inc_array = get_aut_inc_keys($conn, $table);

    $fields = [];
    while ($field = $result->fetch_field()) {
        $fields[] = $field;
    }

    if (isset($_POST['submit'])) {
        $incomplete = false;
        foreach ($fields as $field) {
            if (!in_array($field->name, $aut_inc_array) && empty($_POST[$field->name])) {
                $incomplete = true;
                break;
            }
        }

        if ($incomplete) {
            echo "<p style='color:red;'>You did not fill in all fields.</p>";
        } else {
            insert_into_table($conn, $table, $_POST);
            echo "<p style='color:green;'>Record added successfully.</p>";
           
        }
    }

    ?>
    <form action="" method="POST">
        <?php foreach ($fields as $field): ?>
            <?php if (!in_array($field->name, $aut_inc_array)): ?>
                <p>
                    <?= htmlspecialchars($field->name) ?>:
                    <input type="text" name="<?= htmlspecialchars($field->name) ?>" />
                </p>
            <?php endif; ?>
        <?php endforeach; ?>
        <p><input type="submit" name="submit" value="Add record" /></p>
    </form>
    <?php
}

function insert_into_table($conn, $table, $data) {
    unset($data['submit']); 

    $columns = array_keys($data);

     $foreign_keys = [];
    $fk_sql = "
        SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = '$table'
          AND REFERENCED_TABLE_NAME IS NOT NULL
    ";
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
                //die("Error: Value '$val' for column '$col' does not exist in '{$ref['ref_table']}'");
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

 function get_aut_inc_keys($conn, $table_name)
    {
    $sql = "SELECT COLUMN_NAME
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND EXTRA LIKE '%auto_increment%';";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $table_name);
    $stmt->execute();
    $result = $stmt->get_result();

    $auto_inc_columns = [];
    while ($row = $result->fetch_assoc()) {
        $auto_inc_columns[] = $row['COLUMN_NAME'];
    }

    $stmt->close();

    return $auto_inc_columns;
    }

?>