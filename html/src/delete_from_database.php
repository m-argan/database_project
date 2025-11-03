<?php

// Delete records function
function delete_records($result, $conn) {
    $del_stmt = file_get_contents("delete.sql");
    $del_stmt = $conn->prepare($del_stmt);

    $qryres = $result->fetch_all();
    $n_rows = $result->num_rows;

    for ($i=0; $i<$n_rows; $i++) {      // Loop through records, check if $_POST has a corresponding entry
        $id = $qryres[$i][2];
        $name = "checkbox" . "$id";

        if (array_key_exists($name, $_POST)) {
            $del_stmt->bind_param('i', $id);
            $del_stmt->execute();
        }
    }

}

?>