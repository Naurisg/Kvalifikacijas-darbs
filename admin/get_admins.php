<?php
header('Content-Type: application/json');

try {

    $db = new PDO('sqlite:admin_signup.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT id, email, name, accept_privacy_policy, approved, created_at FROM admin_signup";
    $stmt = $db->query($sql);
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "admins" => $admins]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Failed to fetch data: " . $e->getMessage()]);
}
?>
