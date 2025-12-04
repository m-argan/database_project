<?php
require_once __DIR__ . '/display_views_tools.php';

start_view_capture();
?>
<h2>Select a Subject or Class:</h2>
        <form method="POST">
                <p>Subject code(e.g. HIS, MAT): <input type="text" name="subject" /></p>
                <p>Class (e.g. 110, 330): <input type="integer" name="class" /></p>
                <p><input type="submit" value="See Details"/></p>
        </form>
<?php
        require_once __DIR__ . "/setup_tools.php";
        require_once __DIR__ . "/display_table_tools.php";
        error_checking();
        $conn = config();
        
        if(isset($_POST["submit"])){
                echo "?>>";
        }
        
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
        $query = "CALL calendar_pivot_view('$subject', '$class', '$allsubjects', '$allclasses')";
        $result = $conn->query($query);
        format_result_as_calendar($result);

        if (isset($result) && $result instanceof mysqli_result) {
            $result->free();
            mysqli_next_result($conn);
        }
        
        echo(var_dump($_POST));
        if(isset($_POST["role_student"])){
            finish_view_capture_and_render($conn, true, false);
        }
        else{
            finish_view_capture_and_render($conn, false, false);
        }

        $conn->close();
?>
