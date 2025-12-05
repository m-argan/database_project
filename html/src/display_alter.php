<?php
require_once __DIR__ . "/setup_tools.php";
require_once __DIR__ . "/display_database_tools.php";
require_once __DIR__ . "/display_table_tools.php";
require_once __DIR__ . "/alter_tools.php";

error_checking();
$conn = config();

if (isset($_POST['submit_btn'])) {

    // Call perform_alter() and capture error message directly
    $error_message = perform_alter($conn);

} else {
    $error_message = ""; // default if not submitted
}

?>

<?php
if (!empty($error_message)): ?>
    <p style="color:red;"><?= htmlspecialchars($error_message); ?></p>
<?php endif; ?>


<?php  
// Now display the form or table normally:
$result = get_result($conn);
alt($conn);

$conn->close();
