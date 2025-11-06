 <?php
    require_once "display_table_tools.php";
    
 function display_adding_forms($conn) { 
    $result = prepare_display_table($conn);
    ?>
    <form action="display_adding_tools.php" method="POST">
        <?php
    while ($field = $result->fetch_field()) {?>
        
        <p> <?php echo $field->name ?>: <input type="text" name="tablename" /></p>
        
    <?php                  }
    ?>
    <p><input type="submit" value="Submit"/></p>
        </form> 
     <!-- <h2>Add information table:</h2>
    <form action="display_adding_tools.php" method="GET">
        <p>Table name: <input type="text" name="tablename" /></p>
        <p><input type="submit" value="See Details"/></p>
    </form> -->
    <?php
    }?>