<?php
session_start();
require_once('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_id = $_POST['room_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("INSERT INTO bookings (room_id, user_id, check_in, check_out) VALUES (?, ?, ?, ?)");
        $stmt->execute([$room_id, $_SESSION['user_id'], $check_in, $check_out]);
        
        $stmt = $pdo->prepare("UPDATE rooms SET status = 'booked' WHERE id = ?");
        $stmt->execute([$room_id]);
        
        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
