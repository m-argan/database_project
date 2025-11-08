<?php

include_once "setup_tools.php";
include_once "display_database_tools.php";
include_once "display_table_tools.php";
// include_once "display_table.php";
include_once "alter_tools.php";
error_checking();
$conn = config();
display_alter_forms($conn);
alter_db($conn);
$conn->close();

 ?>



