<?php
include 'db.php';

try {
    // Validate required fields
    $required_fields = ['name', 'email', 'phone', 'membership_type'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("$field is required");
        }
    }

    // Sanitize and validate input
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
    $stmt = $conn->prepare("INSERT INTO members (name, email, phone, membership_type) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssss", $name, $email, $phone, $membership_type);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    echo json_encode(["message" => "Member added successfully", "id" => $conn->insert_id]);

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
