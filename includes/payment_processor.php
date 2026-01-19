<?php
class PaymentProcessor {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function processPayment($booking_id, $amount, $payment_method) {
        try {
            $this->pdo->beginTransaction();
            
            $stmt = $this->pdo->prepare("
                INSERT INTO payments (booking_id, amount, payment_method, status)
                VALUES (?, ?, ?, 'completed')
            ");
            $stmt->execute([$booking_id, $amount, $payment_method]);
            
            $this->updateBookingStatus($booking_id, 'paid');
            
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
    
    private function updateBookingStatus($booking_id, $status) {
        $stmt = $this->pdo->prepare("
            UPDATE bookings 
            SET payment_status = ? 
            WHERE id = ?
        ");
        return $stmt->execute([$status, $booking_id]);
    }
}
?>
