<!-- function to alter contents of any table-->

<?php
function alter_db($conn, $table, $setElem, $setValue, $IdElem, $IdValue)
{
    include_once "setup_tools.php";
    error_checking();
    $sel_tbl = "UPDATE ".$table." SET ".$setElem." = ? WHERE ".$IdElem." = ".$IdValue.";";
    #echo $sel_tbl;
    $up_stmt = $conn->prepare($sel_tbl);
    $up_stmt->bind_param("s",$setValue);
    $up_stmt->execute();
}

?>