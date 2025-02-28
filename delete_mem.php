<?php
include 'db.php';

try {
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        throw new Exception("ID is required");
    }

    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    if ($id === false) {
        throw new Exception("Invalid ID");
    }

    // Prepare and execute statement
    $stmt = $conn->prepare("DELETE FROM members WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception("No member found with ID: $id");
    }

    echo json_encode(["message" => "Member deleted successfully"]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?>
