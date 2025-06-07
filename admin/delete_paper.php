<?php
require_once '../config/database.php';
require_once 'includes/auth_check.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // First get the file path
    $query = "SELECT file_path FROM previous_papers WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $paper = $result->fetch_assoc();
    
    if ($paper) {
        // Delete the file
        $file_path = "../" . $paper['file_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Delete from database
        $query = "DELETE FROM previous_papers WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Paper deleted successfully.";
        } else {
            $_SESSION['error'] = "Error deleting paper.";
        }
    }
}

// Redirect back to manage papers page
header("Location: manage_papers.php");
exit(); 