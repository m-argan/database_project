<?php
include_once "display_table_tools.php";
 function display_alter_forms($conn) 
 { 
    $result = prepare_display_table($conn);
    ?>
    <form method="POST">
    <?php
    $row = $result->fetch_row();
    $i = 0;

    while ($field = $result->fetch_field()) {
        echo $field->name . ": ";
    ?>
        <input type="text" name="value<?php echo $i; ?>" value="<?php echo $row[$i]; ?>"/>
    <?php
        $i++;
    }
    ?>
    <p><input type="submit" name="alter_btn" value="Submit"/></p>
    </form>

    <?php
}
    
function alter_db($conn)
{
    $setElem = 'tutor_first_name';
    $IdElem = 'tutor_id';
    $IdValue = '380932';
    $setValue = 'Madeleine';
    include_once "setup_tools.php";
    error_checking();
    $sel_tbl = "UPDATE ".htmlspecialchars($_GET['tablename'] )." SET ".$setElem." = ? WHERE ".$IdElem." = ".$IdValue.";";
    $up_stmt = $conn->prepare($sel_tbl);
    $up_stmt->bind_param("s",$setValue);
    $up_stmt->execute();
}

function alt($conn)
{
    display_alter_forms($conn);

    if (array_key_exists('alter_btn', $_POST)) {
        alter_db($conn);
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
        exit();
    }
}
    
?>