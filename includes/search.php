<?php
require_once('config.php');

$search = $_GET['query'] ?? '';
$stmt = $pdo->prepare("
    SELECT * FROM rooms 
    WHERE room_number LIKE :search 
    OR description LIKE :search
");
$stmt->execute(['search' => "%$search%"]);
$results = $stmt->fetchAll();

echo json_encode([
    'results' => $results,
    'count' => count($results)
]);
?>
