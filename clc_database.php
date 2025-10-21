<?php
    //For error checking
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);


    // Config
    $config = parse_ini_file('../mysql.ini');
    $dbname = 'clc_tutoring';
    $conn = new mysqli(
        $config['mysqli.default_host'],
        $config['mysqli.default_user'],
        $config['mysqli.default_pw'],
        $dbname);
    // Error checking
     if ($conn->connect_errno) {
        echo "Error: Failed to make a MySQL connection, here is why: ". "<br>";
        echo "Errno: " . $conn->connect_errno . "\n";
        echo "Error: " . $conn->connect_error . "\n";
        exit; // Quit this PHP script if the connection fails
    }


    //Formating function
    function format_result_as_table(mysqli_result $result): void {
        ?>

        <h1>Table</h1>
        <table style="width:100%">

        <tr>
        <?php
            while ($field = $result->fetch_field()) {
                echo "<th> $field->name </th>";
            }
        ?>
        </tr>

        <?php
            while ($row = $result->fetch_row()) {
                echo "<tr>";
                for ($i = 0; $i < count($row); $i++) {
                echo "<td> $row[$i] </td>";
                }
                echo "</tr>";
            }
        ?>

        </table>

        <?php

    }


    // Call function
    $sel_tbl = file_get_contents('select_clc_database.sql');         
    $result = $conn->query($sel_tbl);   
    format_result_as_table($result);    


    // Close connection
    $conn->close();
?>