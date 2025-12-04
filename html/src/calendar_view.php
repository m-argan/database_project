<?php
require_once __DIR__ . '/display_views_tools.php';

        start_view_capture();
        require_once __DIR__ . "/setup_tools.php";
        require_once __DIR__ . "/display_table_tools.php";
        error_checking();
        $conn = config();

        $query = "CALL calendar_pivot_view()";
        $result = $conn->query($query);
        format_result_as_calendar($result);

        //echo(var_dump($_POST));
         if (isset($result) && $result instanceof mysqli_result) {
            $result->free();
            mysqli_next_result($conn);
        }

        echo(var_dump($_POST));
        if(isset($_POST["role_admin"])){
                finish_view_capture_and_render($conn, false, false);
        }
        elseif(isset($_POST["role_student"])){
                echo "hi student";
                finish_view_capture_and_render_student($conn, true, false);
        }

        $conn->close();
?>
