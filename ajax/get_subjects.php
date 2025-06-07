<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_GET['branch']) || !isset($_GET['semester'])) {
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

try {
    $query = "SELECT DISTINCT subject_name, subject_code 
              FROM previous_papers 
              WHERE branch = :branch AND semester = :semester 
              ORDER BY subject_name";
              
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':branch', $_GET['branch']);
    $stmt->bindParam(':semester', $_GET['semester']);
    $stmt->execute();
    
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($subjects);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 