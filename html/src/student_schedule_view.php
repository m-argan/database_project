<!-- Incomplete - encountering issues with display_table_tools.php. Stored procedure should work otherwise -->

<h2>Select a Student:</h2>
        <form action="display_table.php" method="GET">
                <p>First name: <input type="text" name="firstname" /></p>
                <p>Last name: <input type="text" name="lastname" /></p>
	</form>
<h2>Select a Subject:</h2>
        <form action="display_table.php" method="GET">
                <p>Subject code: <input type="text" name="subjectcode" /></p>
                <p><input type="submit" value="See Details"/></p>
				<input type="hidden" name="tablename" value="<?php echo htmlspecialchars($_GET['tablename']); ?>">
        </form>

<?php
        include_once "setup_tools.php";
        include_once "display_table_tools.php";
        error_checking();
        $conn = config();

        if (isset($_GET['firstname']) && isset($_GET['lastname']))
        {
                $first = $_GET['firstname'];
                $last = $_GET['lastname'];
                $allstudents = false;
        }
        else
        {
                $first = '';
                $last = '';
	  	$allstudents = true;
	}
	if (isset($_GET['subjectcode']))
        {
                $subject = $_GET['subjectcode'];
                $allsubjects = 0;
        }
        else
        {
                $subject = '';
                $allsubjects = 1;
        }
        $query = "CALL tutor_schedule_view('$first', '$last', '$subject', '$allstudents', '$allsubjects')";
        $result = $conn->query($query);
        format_result_as_table($result);
        //Note: this does not handle wrong input

        $conn->close();
?>
