<?php
require_once __DIR__ . "/setup_tools.php";
require_once __DIR__ . "/display_database_tools.php";
require_once __DIR__ . "/display_table_tools.php";
require_once __DIR__ . "/alter_tools.php";

$conn = config();
$error_message = null;

// Was the form submitted?
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_btn'])) {

    // Safely check for tablename
    if (!isset($_POST['tablename'])) {
        $error_message = "Error: Missing table name.";
    } else {
        $error_message = perform_alter($conn, false); // doExit=false
    }
}

// Now display the form (always)
?>

<!DOCTYPE html>
<html>
<body>

<?php if (!empty($error_message)): ?>
    <p style="color:red;"><?= htmlspecialchars($error_message); ?></p>
<?php endif; ?>

<?php
// ALWAYS display the form after processing
$result = get_result($conn);
alt($conn);
?>

</body>
</html>

<?php $conn->close(); ?>
