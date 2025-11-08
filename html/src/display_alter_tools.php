<?php
 function display_alter_forms($conn) { 
    $result = prepare_display_table($conn);
    ?>
    <form action="display_alter_tools.php" method="POST">
        <?php
    $row = $result->fetch_row();
    $i = 0;

    while ($field = $result->fetch_field()) {
        echo $field->name;?>: <?php
        ?> <input type="text" name="value" value = <?php echo $row[$i];?>/> <?php
        $i++;
    ?>
        
        
        
    <?php                  }
    ?>
    <p><input type="submit" value="Submit"/></p>
        </form> 
    <?php
    }?>