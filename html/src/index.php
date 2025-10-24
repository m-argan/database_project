<?php
    // INDEX --- DISPLAY CLC_TUTORING CONTENTS
    
    
    // For error checking
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

     // Config
    $config = parse_ini_file('../../../mysql.ini');
    if ($config === false) {
        $config = parse_ini_file('../mysql.ini');
    }
    $dbname = 'clc_tutoring';
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

    // Function for listing tables
    function list_tables($conn) {
        $dblist = "SHOW TABLES";
        $result = $conn->query($dblist);
        echo "<ul>";
        while ($tablename = $result->fetch_array()) {
            echo "<li> $tablename[0] </li>";
        }
        echo "</ul>";
    }

    // Function for displaying form
    function display_form() { ?>
    <h2>View a table:</h2>
    <form action="display_table.php" method="GET">
        <p>Table name: <input type="text" name="tablename" /></p>
        <p><input type="submit" value="See Details"/></p>
    </form>
    <?php
    }


?>

<!--- BEGIN WEBPAGE FORMATTING --->

<!DOCTYPE html>
<html>
<head>
    <title>CLC Database</title>
</head>
<body>
    <h1>CLC Database</h1>

    <?php
        // List the tables of the database
        list_tables($conn);
    ?>

    <!--- Allow a user to specify a table to view --->
   <?php display_form(); ?>

</body>
</html>

<?php
    $conn->close();
?>