<?php
include 'db.php';

try {
    // Validate required fields
    $required_fields = ['id', 'name', 'email', 'phone', 'membership_type'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("$field is required");
        }
    }

    // Sanitize and validate input
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    if ($id === false) {
        throw new Exception("Invalid ID");
    }

    $name = sanitize($conn, $_POST['name']);
    $email = sanitize($conn, $_POST['email']);
    $phone = sanitize($conn, $_POST['phone']);
    $membership_type = sanitize($conn, $_POST['membership_type']);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format");
    }

    // Validate membership type
    $valid_types = ['Standard', 'Premium', 'VIP'];
    if (!in_array($membership_type, $valid_types)) {
        throw new Exception("Invalid membership type");
    }

    // Prepare and execute statement
    $stmt = $conn->prepare("UPDATE members SET name=?, email=?, phone=?, membership_type=? WHERE id=?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssi", $name, $email, $phone, $membership_type, $id);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception("No member found with ID: $id");
    }

    echo json_encode(["message" => "Member updated successfully"]);

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
