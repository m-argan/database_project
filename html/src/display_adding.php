<?php


    require_once __DIR__ . "/setup_tools.php";
    require_once __DIR__ . "/display_database_tools.php";
    require_once __DIR__ . "/display_table.php";
    require_once __DIR__ . "/display_adding_tools.php";
    error_checking();
    $conn = config();
    display_adding_forms($conn);
    $conn->close();

     ?>

    

