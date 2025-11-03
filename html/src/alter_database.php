<!-- function to alter contents of any table-->

<?php
function alter_db(&$table, &$setElem, &$setValue, &$IdElem, &$IdValue)
{
    $sel_tbl = file_get_contents("UPDATE " + $table + "SET " + $setElem + " = " + $setValue + " WHERE " + $IdElem + " = ?;");
    $up_stmt = $conn->prepare($sel_tbl);
    $up_stmt->bind_param($IdValue);
    $up_stmt->execute();
}

?>