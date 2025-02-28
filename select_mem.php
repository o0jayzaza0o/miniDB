<?php
include 'db.php';

try {
    // Prepare and execute statement
    $stmt = $conn->prepare("SELECT * FROM members ORDER BY id DESC");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $members = [];
    
    while ($row = $result->fetch_assoc()) {
        $members[] = $row;
    }

    echo json_encode($members);

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
