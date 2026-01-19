<?php
class Analytics {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getRoomOccupancyRate() {
        return $this->pdo->query("
            SELECT 
                COUNT(CASE WHEN status = 'booked' THEN 1 END) * 100.0 / COUNT(*) as occupancy_rate
            FROM rooms
        ")->fetchColumn();
    }
    
    public function getRevenueByPeriod($start_date, $end_date) {
        $stmt = $this->pdo->prepare("
            SELECT SUM(total_price) as revenue
            FROM bookings
            WHERE check_in BETWEEN ? AND ?
        ");
        $stmt->execute([$start_date, $end_date]);
        return $stmt->fetchColumn();
    }
}
?>
