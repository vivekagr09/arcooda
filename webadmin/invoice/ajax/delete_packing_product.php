<?PHP 
ECHO $_POST['invoice_id'];
ECHO $_POST['product_id'];
  ECHO "SKJDKSJKSJDS";

header('Content-Type: application/json');

// Include database configuration
require_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/includes/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/includes/database.inc.php';

$response = ['success' => false, 'message' => '', 'debug' => []];

try {
    // Ensure request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method");
    }

    // Check if required parameters are present
    if (!isset($_POST['invoice_id']) || !isset($_POST['product_id'])) {
        throw new Exception("Missing parameters");
    }

    // Get invoice and product ID, ensuring they are integers
    $invoice_id = intval($_POST['invoice_id']);
    $product_id = intval($_POST['product_id']);

    if ($invoice_id <= 0 || $product_id <= 0) {
        throw new Exception("Invalid ID values");
    }

    // Initialize database
    global $DB;
    if (!isset($DB)) {
        throw new Exception("Database object not initialized");
    }

    // Get PDO connection
    $pdo = $DB->getPDO();
    if (!$pdo) {
        throw new Exception("Failed to get database connection");
    }

    // Debugging: check database selection
    $stmt = $pdo->query("SELECT DATABASE()");
    $response['debug']['current_db'] = $stmt->fetchColumn();

    // Verify record exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM packing_information WHERE invoice_id = ? AND product_id = ?");
    $stmt->execute([$invoice_id, $product_id]);
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        throw new Exception("Record not found");
    }

    // Delete record
    $stmt = $pdo->prepare("DELETE FROM packing_information WHERE invoice_id = ? AND product_id = ?");
    $stmt->execute([$invoice_id, $product_id]);

    $affected = $stmt->rowCount();
    $response['debug']['affected_rows'] = $affected;

    if ($affected > 0) {
        $response['success'] = true;
        $response['message'] = "Deleted successfully";
    } else {
        throw new Exception("Delete failed - no rows affected");
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log("Delete Error: " . $e->getMessage());
}

echo json_encode($response);


?>
