<?php
    // For error checking
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);


    // Config
    $config = parse_ini_file('../../../mysql.ini');
    if ($config === false) {
        $config = parse_ini_file('../mysql.ini');
    }    $dbname = 'clc_tutoring';
    $conn = new mysqli(
        $config['mysqli.default_host'],
        $config['mysqli.default_user'],
        $config['mysqli.default_pw'],
        $dbname);
    // Check errors in connection
     if ($conn->connect_errno) {
        echo "Error: Failed to make a MySQL connection, here is why: ". "<br>";
        echo "Errno: " . $conn->connect_errno . "\n";
        echo "Error: " . $conn->connect_error . "\n";
        exit; // Quit this PHP script if the connection fails
    }


    // Function for formatting
    function format_result_as_table(mysqli_result $result): void {  ?>
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


    // Function for whitelisting possibilities for valid tables
    function filter_user_input($conn) {
        $result = $conn->query("SHOW TABLES;");
        $is_a_table = false;
        while ($tablename = $result->fetch_array()) {
            if ($tablename[0] === htmlspecialchars( $_GET['tablename'] )) {
                $is_a_table = true;
                break;
            }
        }
        if ($is_a_table === false) {
            echo 'No table of that name found.';
            exit();
        }
    }

    
    // Function to prepare statement to display table
    function prepare_display_table($conn) {
        $query = "SELECT * FROM " . htmlspecialchars( $_GET['tablename'] ) . ";";
        $query = $conn->prepare($query);
        $query->execute();
        $result = $query->get_result();

        return $result;
    }
    

    // Run webpage
    filter_user_input($conn);
    $result = prepare_display_table($conn);
    format_result_as_table($result);


    // Close connection
    $conn->close();
?>