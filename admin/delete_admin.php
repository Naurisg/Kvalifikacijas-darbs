<?php
header('Content-Type: application/json');
try {
    $db = new PDO('sqlite:../Datubazes/admin_signup.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = $_POST['id'] ?? null;
    
    if (!$id) {
        throw new Exception("ID nav norādīts");
    }

    $stmt = $db->prepare("DELETE FROM admin_signup WHERE id = :id");
    $stmt->execute([':id' => $id]);

    echo json_encode(["success" => true, "message" => "Administrators veiksmīgi dzēsts"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
