<?php
include 'db.php';

try {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception("ID is required");
    }

    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($id === false) {
        throw new Exception("Invalid ID");
    }

    // Prepare and execute statement
    $stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $member = $result->fetch_assoc();

    if (!$member) {
        throw new Exception("No member found with ID: $id");
    }

    echo json_encode($member);

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
