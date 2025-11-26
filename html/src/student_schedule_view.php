<?php
// ADDITIONS FROM COPILOT MARKED
require_once __DIR__ . '/display_views_tools.php';
start_view_capture();
?>

<!-- Incomplete - errors occur when all fields aren't filled in on form -->

<h2>Select a Student:</h2>
        <form action="student_schedule_view.php" method="GET">
                <p>First name: <input type="text" name="firstname" /></p>
                <p>Last name: <input type="text" name="lastname" /></p>

<h2>Select a Subject or Class:</h2>

                <p>Subject code: <input type="text" name="subjectcode" /></p>
                <p>Class code: <input type="number" name="classnumber" /></p>

<h2>Select a Term:</h2>

                <p>Term(e.g. SP, FA): <input type="text" name="term" /></p>
                <p>Year(e.g. 2021): <input type="number" name="tyear" /></p>
                <p><input type="submit" value="See Details"/></p>
        </form>

<?php
        require_once __DIR__ . "/setup_tools.php";
        require_once __DIR__ . "/display_table_tools.php";
        error_checking();
        $conn = config();

        if (isset($_GET['firstname']) && isset($_GET['lastname']) && !empty($_GET['firstname']) && !empty($_GET['lastname']))
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
        if (isset($_GET['subjectcode']) && !empty($_GET['subjectcode']))
        {
                $subject = htmlspecialchars($_GET['subjectcode']);
                $allsubjects = 0;
                if (isset($_GET['classnumber']) && !empty($_GET['classnumber']))
                {
                        $classnumber = htmlspecialchars($_GET['classnumber']);
                        $allclasses = 0;
                }
                else
                {
                        $classnumber = 0;
                        $allclasses = 1;
                }
        }
        else
        {
                $subject = NULL;
                $classnumber = 0;
                $allsubjects = 1;
                $allclasses = 1;
        }
        if (isset($_GET['term']) && isset($_GET['tyear']) && !empty($_GET['term']) && !empty($_GET['tyear']))
        {
                $term = htmlspecialchars($_GET['term']);
                $tyear = htmlspecialchars($_GET['tyear']);
                $allterms = 0;
        }
        else
        {
                $term = NULL;
                $tyear = 0;
                $allterms = 1;
        }
        $query = "CALL tutor_schedule_view(1, 0, '$first', '$last', '$subject', '$classnumber', '$term', '$tyear', '$allstudents', '$allsubjects', '$allclasses', '$allterms')";
        $result = $conn->query($query);
        view_edits($conn, $result, 1);

        // COPILOT CHANGES BEGIN NOW
        if (isset($result) && $result instanceof mysqli_result) {
            $result->free();
            mysqli_next_result($conn);
        }

        finish_view_capture_and_render($conn, false);
        // COPOILOT CHANGES END

        $conn->close();
?>
