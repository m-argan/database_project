<!-- Database or PHP needs a way to tell the current semester for this page to work, not yet implemented -->

<h2>Select a Subject or Class:</h2>
        <form action="full_schedule_view.php" method="GET">
                <p>Subject code(e.g. HIS, MAT): <input type="text" name="subject" /></p>
                <p>Class (e.g. 110, 330): <input type="integer" name="class" /></p>
                <p><input type="submit" value="See Details"/></p>
        </form>

<?php
        require_once __DIR__ . "/setup_tools.php";
        require_once __DIR__ . "/display_table_tools.php";
        error_checking();
        $conn = config();

        if (isset($_GET['subject']) && !empty($_GET['subject']))
        {
                $subject = htmlspecialchars($_GET['subject']);
                $allsubjects = 0;
                if (isset($_GET['class']) && !empty($_GET['class']))
                {
                        $class = (int)htmlspecialchars($_GET['class']);
                        $allclasses = 0;
                }
                else
                {
                        $class = 0;
                        $allclasses = 1;
                }
        }
        else
        {
                $subject = '';
                $class = 0;
                $allsubjects = 1;
                $allclasses = 1;
        }
        $semester = 'Fa';
        $year = 2025;
        $query = "CALL tutor_schedule_view(false, true, NULL, NULL, '$subject', '$class', '$semester', '$year', true, '$allsubjects', '$allclasses', false)";
        $result = $conn->query($query);
        view_edits($conn, $result, 3);

        $conn->close();
?>
