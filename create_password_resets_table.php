<?php
// This script creates the password_resets table in the SQLite database.
// Run this script once to set up the table.

try {
    $db = new SQLite3('Datubazes/client_signup.db');
    $query = "CREATE TABLE IF NOT EXISTS password_resets (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT NOT NULL,
        token TEXT NOT NULL,
        expires_at DATETIME NOT NULL
    )";
    $db->exec($query);
    echo "Table 'password_resets' created successfully.";
} catch (Exception $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
