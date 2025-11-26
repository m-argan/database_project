<?php
// ADDITIONS FROM COPILOT MARKED
require_once __DIR__ . '/display_views_tools.php';
start_view_capture();
?>

<h2>View a Student:</h2>
        <form action="student_agreement_view.php" method="GET">
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

        // COPILOT ADDITIONS/CHANGES BEGINS NOW
                $query = "CALL tutor_agreement_form_view('$first', '$last', '$allstudents')";
                $result = null;
                try {
                        $result = $conn->query($query);
                        if ($result instanceof mysqli_result) {
                                view_edits($conn, $result, 4);
                        }
                } catch (mysqli_sql_exception $ex) {
                        // Show a helpful message in the page content instead of fatal error
                        echo '<p><b>Database error:</b> ' . htmlspecialchars($ex->getMessage()) . '</p>';
                        // Attempt to advance any pending results so subsequent rendering can proceed
                        if ($conn) {
                                @mysqli_next_result($conn);
                        }
                }

                if (isset($result) && $result instanceof mysqli_result) {
                        $result->free();
                        // advance connection results after freeing stored-proc result
                        mysqli_next_result($conn);
                }

        finish_view_capture_and_render($conn, false);

        // COPILOT END
        
        $conn->close();
?>
