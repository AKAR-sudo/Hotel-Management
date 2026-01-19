<?php
session_start();
require_once('config.php');

class RoomService {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function requestService($room_id, $service_type) {
        $stmt = $this->pdo->prepare("
            INSERT INTO room_services (room_id, service_type, requested_at, status)
            VALUES (?, ?, NOW(), 'pending')
        ");
        return $stmt->execute([$room_id, $service_type]);
    }
    
    public function getActiveRequests() {
        return $this->pdo->query("
            SELECT * FROM room_services 
            WHERE status = 'pending'
            ORDER BY requested_at DESC
        ")->fetchAll();
    }
}
?>
