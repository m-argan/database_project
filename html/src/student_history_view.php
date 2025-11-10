<!-- Incomplete - encountering issues with display_table_tools.php. Stored procedure should work otherwise -->

<h2>Select a Student:</h2>
        <form action="display_table.php" method="GET">
                <p>First name: <input type="text" name="firstname" /></p>
                <p>Last name: <input type="text" name="lastname" /></p>
                <p><input type="submit" value="See Details"/></p>
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
	$query = "CALL tutor_history_view('$first', '$last', '$allstudents')";
	$result = $conn->query($query);
	format_result_as_table($result);
	//Note: this does not handle wrong input

	$conn->close();
?>

