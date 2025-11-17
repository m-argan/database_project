<?php
    // Function definitions for display_database, AKA (currently) index.php.


    // Function for listing tables of database:
    function list_tables($conn) {
        $dblist = "SHOW TABLES";
        $result = $conn->query($dblist);

        echo "<ul>";
        while ($tablename = $result->fetch_array()) {
            echo "<li> $tablename[0] </li>";
            // echo "hi";
            echo ?><a href=""><<?php$tablename[0]?></a>;<?php
        }
        echo "</ul>";

    }

    // Function for displaying form at the bottom of the page:
    function display_form() { ?>
    <h2>View a table:</h2>
    <form action="display_table.php" method="GET">
        <p>Table name: <input type="text" name="tablename" /></p>
        <p><input type="submit" value="See Details"/></p>
    </form>
    <!-- ADDED BY HANNAH -->
     <!-- <h2>Add to table:</h2>
    <form action="display_adding.php" method="GET">
        <p>Table name: <input type="text" name="tablename" /></p>
        <p><input type="submit" value="See Details"/></p>
    </form> -->
    <!-- ADDED BY STELLA -->
        <table style="border-style: none">
                <tr style="border-style: none">
                        <td style="border-style: none"><a href="student_history_view.php">View Student</a>&nbsp;</td>
                        <td style="border-style: none"><a href="student_schedule_view.php">View Student Schedule</a>&nbsp;</td>
                        <td style="border-style: none"><a href="full_schedule_view.php">View Full Schedule</a>&nbsp;</td>
                        <!-- <td style="border-style: none"></td> -->
                </tr>
        </table>
    <?php
    }


    // Function for rendering the page:
    function render_display_database_page($conn) { ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>CLC Database</title>
        </head>
        <body>
            <h1>CLC Database</h1>

            <?php
                // List the tables of the database
                list_tables($conn);

                // Allow a user to specify a table to view
                display_form();
            ?>
        </body>
        </html>
   <?php }
?>
