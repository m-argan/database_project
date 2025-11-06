<?php


    include_once "setup_tools.php";
    include_once "display_database_tools.php";
    include_once "display_table.php";
    include_once "display_adding_tools.php";
    error_checking();
    $conn = config();
    display_adding_forms($conn);
    $conn->close();

     ?>

    

