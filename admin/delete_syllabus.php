<?php
require_once '../config/database.php';
require_once 'includes/auth_check.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // First get the file path
    $query = "SELECT file_path FROM syllabus WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $syllabus = $result->fetch_assoc();
    
    if ($syllabus) {
        // Delete the file
        $file_path = "../" . $syllabus['file_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Delete from database
        $query = "DELETE FROM syllabus WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Syllabus deleted successfully.";
        } else {
            $_SESSION['error'] = "Error deleting syllabus.";
        }
    }
}

// Redirect back to manage syllabus page
header("Location: manage_syllabus.php");
exit(); 