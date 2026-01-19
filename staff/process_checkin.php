<?php
session_start();
require_once('../includes/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    
    try {
        // Update booking status
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'checked_in' WHERE id = ?");
        $stmt->execute([$booking_id]);
        
        // Update room status using correct join conditions
        $stmt = $pdo->prepare("
            UPDATE rooms r 
            JOIN bookings b ON r.id = b.room_id 
            SET r.status = 'occupied' 
            WHERE b.id = ?
        ");
        $stmt->execute([$booking_id]);
        
        header('Location: manage_checkins.php?success=1');
        exit();
    } catch (PDOException $e) {
        header('Location: manage_checkins.php?error=1');
        exit();
    }
} else {
    header('Location: manage_checkins.php');
    exit();
}
