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

         if (isset($result) && $result instanceof mysqli_result) {
            $result->free();
            mysqli_next_result($conn);
        }

        finish_view_capture_and_render($conn, false);

        $conn->close();
?>
