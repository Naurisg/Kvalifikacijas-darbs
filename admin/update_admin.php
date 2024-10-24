<?php
header('Content-Type: application/json'); 

try {
    $db = new PDO('sqlite:admin_signup.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = 1;


    $email = $_POST['Email'] ?? null;
    $name = $_POST['Name'] ?? null;
    $oldPassword = $_POST['OldPassword'] ?? null;
    $newPassword = $_POST['NewPassword'] ?? null;


    if (empty($email) || empty($name)) {
        echo json_encode(["success" => false, "message" => "Email and name are required."]);
        exit();
    }

    
    $stmt = $db->prepare("SELECT password FROM admin_signup WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

 
    if ($admin && password_verify($oldPassword, $admin['password'])) {

        $sql = "UPDATE admin_signup SET email = :email, name = :name";
        
    
        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $sql .= ", password = :password";
        }
        $sql .= " WHERE id = :id";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':name', $name);
        if (!empty($newPassword)) {
            $stmt->bindParam(':password', $hashedPassword);
        }
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Information updated successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Update failed."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Old password is incorrect."]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $e->getMessage()]);
}
?>
