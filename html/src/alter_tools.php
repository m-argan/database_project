<?php
include_once "display_table_tools.php";
 
function select_from_db($result, $index, $conn)
{
    ?>
   <form method="POST">
   <?php

    $result->data_seek($index);
    $row = $result->fetch_assoc();

    echo $result->field_count . " field(s) in results.<br>";

    foreach ($row as $field_name => $value) {
        // echo "Field " . htmlspecialchars($field_name) . ": " . htmlspecialchars($value) . "<br>";
        echo htmlspecialchars($field_name) . ": ";
        ?>
           <input type="text" name="value<?php echo $i; ?>[]" value="<?php echo htmlspecialchars($value) ?>"/>
           <?php
    }

}


function get_result($conn)
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        echo "Table: " . htmlspecialchars($_GET['tablename'] ). "<br>";
 
        $query = "SELECT * FROM ".htmlspecialchars($_GET['tablename']).";";
             $stmt = $conn->prepare($query);
              if (!$stmt) {
                 echo "Couldn't prepare statement!";
                 echo exit;
             }
     
             $stmt->execute();
             $result = $stmt->get_result();
    return $result;
    }
}

function check_boxes($result, $conn)
{
    $count = 0;
    for ($i=0; $i < $result->num_rows; $i++) {      // Loop through records; check if $_POST
        $row = $result->fetch_row();                // has a corresponding entry.
        $name = "checkbox" . "$i";                  
        if (array_key_exists($name, $_POST)) {
            $count++;
            if($count > 1)
            {
                return -1;
                // display_alter_forms($conn);
            }
            else{$index = $i;}
        }
    }
    return $index;
}

function alt($conn)
{
    $result = get_result($conn);
    $res = check_boxes($result, $conn);
    if($res>= 0)
    {
        select_from_db($result, $res, $conn);
    }
    else echo "Please only select one entry to edit at a time";
}
    
?>