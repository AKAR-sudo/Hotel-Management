<?php
class Notifications {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function sendNotification($user_id, $message) {
        $stmt = $this->pdo->prepare("
            INSERT INTO notifications (user_id, message, created_at) 
            VALUES (?, ?, NOW())
        ");
        return $stmt->execute([$user_id, $message]);
    }
    
    public function getUnread($user_id) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM notifications 
            WHERE user_id = ? AND read_at IS NULL 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }
}
?>
