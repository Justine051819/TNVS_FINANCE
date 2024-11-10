<?php
// submit_reason.php

// Check if data was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reject_id']) && isset($_POST['reason'])) {
    $servername = '127.0.0.1:3308';
    $usernameDB = 'root';
    $passwordDB = '';
    $dbname = 'db';

    $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get data from the form
    $rejectId = intval($_POST['reject_id']);
    $reason = $conn->real_escape_string($_POST['reason']);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Step 1: Insert data into the `rr` table
        $insert_sql = "INSERT INTO rr (id, account_name, requested_department, amount, reason_rejected, expense_categories, description, document)
                       SELECT id, account_name, requested_department, amount, '$reason', expense_categories, description, document 
                       FROM br WHERE id = '$rejectId'";

        if ($conn->query($insert_sql) === TRUE) {
            // Step 2: Delete data from the `br` table
            $delete_sql = "DELETE FROM br WHERE id = '$rejectId'";
            if ($conn->query($delete_sql) === TRUE) {
                $conn->commit();
                echo json_encode(["success" => true]);
            } else {
                throw new Exception("Error deleting record from br table: " . $conn->error);
            }
        } else {
            throw new Exception("Error inserting record into rr table: " . $conn->error);
        }
    } catch (Exception $e) {
        // Rollback if any error occurs
        $conn->rollback();
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    } finally {
        $conn->close();
    }
}
?>
