<!-- Incomplete - encountering issues with display_table_tools.php. Stored procedure should work otherwise -->
<!-- Database or PHP needs a way to tell the current semester for this page to work, not yet implemented -->

<h2>Select a Subject:</h2>
        <form action="display_table.php" method="GET">
                <p>Subject code(e.g. HIS, MAT): <input type="text" name="subject" /></p>
        </form>
<h2>Select a Class:</h2>
        <form action="display_table.php" method="GET">
                <p>Class (e.g. 110, 330): <input type="integer" name="class" /></p>
                <p><input type="submit" value="See Details"/></p>
        </form>

<?php
        include_once "setup_tools.php";
        include_once "display_view_tools.php";
        error_checking();
        $conn = config();

        if (isset($_GET['subject']))
        {
                $subject = $_GET['subject'];
		$allsubjects = false;
		if (isset($_GET['class']))
		{
			$class = (int)$_GET['class'];
			$allclasses = false;
		}
		else
		{
			$class = 0;
			$allclasses = true;
		}
	}
        else
        {
		$subject = '';
		$class = 0;
		$allsubjects = true;
		$allclasses = true;
	}
	$semester = 'Fa';
	$year = 2025;
        $query = "CALL full_schedule_view('$subject', '$class', '$semester', '$year', '$allsubjects', '$allclasses')";
        $result = $conn->query($query);
        format_result_as_table($result);
        //Note: this does not handle wrong input

        $conn->close();
?>
