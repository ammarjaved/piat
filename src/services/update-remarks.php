<?php
// Start output buffering first to prevent header issues
ob_start();
session_start();
include 'connection.php';
require_once __DIR__ . '/access.php';

// Clear any output that might have been generated
ob_end_clean();

// Block view-only accounts from editing remarks
if (is_viewer()) {
    header('Content-Type: application/json');
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied: view-only account.']);
    exit;
}

// Set header for JSON response
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && $_POST['id'] !== '') {
    try {
        $id = intval($_POST['id']); // Convert to integer
        $remarks = isset($_POST['remarks']) ? $_POST['remarks'] : '';
 
        // Validate ID
        if ($id <= 0) {
            throw new Exception('Invalid ID provided');
        }
        
        $stmt = $pdo->prepare('UPDATE public.ad_service_qr SET remark = :remark WHERE id = :id');
        $stmt->bindParam(':remark', $remarks, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            // Return success response
            echo json_encode([
                'success' => true,
                'message' => 'Update Successfully',
                'id' => $id,
                'remark' => $remarks
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Update Failed'
            ]);
        }
        
    } catch (PDOException $e) {
        // Return error response
        echo json_encode([
            'success' => false,
            'message' => 'Request Failed: ' . $e->getMessage()
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    
    $pdo = null;
    exit();
} else {
    // Invalid request
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request - ID is required'
    ]);
    exit();
}
?>