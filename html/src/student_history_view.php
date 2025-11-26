<?php
require_once __DIR__ . '/display_views_tools.php';
start_view_capture();
?>

<h2>Select a Student:</h2>
        <form action="student_history_view.php" method="GET">
                <p>First name: <input type="text" name="firstname" /></p>
                <p>Last name: <input type="text" name="lastname" /></p>
                <p><input type="submit" value="See Details"/></p>
        </form>

<?php
        require_once __DIR__ . "/setup_tools.php";
        require_once __DIR__ . "/display_table_tools.php";
        error_checking();
        $conn = config();

        if (isset($_GET['firstname']) && isset($_GET['lastname']))
        {
                $first = htmlspecialchars($_GET['firstname']);
                $last = htmlspecialchars($_GET['lastname']);
                $allstudents = 0;
        }
        else
        {
                $first = NULL;
                $last = NULL;
                $allstudents = 1;
        }
        $query = "CALL tutor_history_view('$first', '$last', '$allstudents')";
        $result = $conn->query($query);
        view_edits($conn, $result, 2);

        if (isset($result) && $result instanceof mysqli_result) {
            $result->free();
            mysqli_next_result($conn);
        }

        finish_view_capture_and_render($conn, false);

        $conn->close();
?>
