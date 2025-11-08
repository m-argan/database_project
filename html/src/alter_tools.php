<?php
 function display_alter_forms($conn) 
 { 
    $result = prepare_display_table($conn);
    ?>
    <form action="alter_tools.php" method="POST">
        <?php
    $row = $result->fetch_row();
    $i = 0;

    while ($field = $result->fetch_field()) {
        echo $field->name;?>: <?php
        ?> <input type="text" name="value" value = "<?php echo $row[$i]?>"/> <?php
        $i++;
    }
    ?>
    <form action="display_alter.php" method="GET">
    <p><input type="submit" value="Submit"/></p>
        </form> 
    <?php
}
    
function alter_db($conn)
{
    echo "hello";
    $setElem = 'tutor_first_name';
    $IdElem = 'tutor_id';
    $IdValue = '380932';
    $setValue = 'Madeleine';
    include_once "setup_tools.php";
    error_checking();
    $sel_tbl = "UPDATE ".htmlspecialchars($_GET['tablename'] )." SET ".$setElem." = ? WHERE ".$IdElem." = ".$IdValue.";";
    // echo $sel_tbl;
    $up_stmt = $conn->prepare($sel_tbl);
    $up_stmt->bind_param("s",$setValue);
    $up_stmt->execute();
}

// function alter_db($conn, $table, $setElem, $setValue, $IdElem, $IdValue)
// {
//     include_once "setup_tools.php";
//     error_checking();
//     $sel_tbl = "UPDATE ".$table." SET ".$setElem." = ? WHERE ".$IdElem." = ".$IdValue.";";
//     #echo $sel_tbl;
//     $up_stmt = $conn->prepare($sel_tbl);
//     $up_stmt->bind_param("s",$setValue);
//     $up_stmt->execute();
// }
    
    
?>