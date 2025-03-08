<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$host = 'localhost';
$dbname = 'task_manager';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'get_tasks_by_date_range':
            $start_date = $_GET['start_date'] ?? date('Y-m-d');
            $end_date = $_GET['end_date'] ?? date('Y-m-d');
            
            $stmt = $pdo->prepare("SELECT * FROM tasks 
                WHERE task_date BETWEEN :start_date AND :end_date 
                ORDER BY task_date, start_time");
            $stmt->execute([':start_date' => $start_date, ':end_date' => $end_date]);
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($tasks);
            break;

        case 'get_tasks':
            $stmt = $pdo->query("SELECT * FROM tasks ORDER BY task_date DESC");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case 'save_task':
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (empty($data['title']) || empty($data['task_date'])) {
                throw new Exception('Missing required fields');
            }

            if (isset($data['id']) && $data['id']) {
                $stmt = $pdo->prepare("UPDATE tasks SET 
                    title = :title, 
                    description = :description, 
                    task_date = :task_date, 
                    start_time = :start_time, 
                    end_time = :end_time, 
                    priority = :priority,
                    category = :category 
                    WHERE id = :id");

                $params = [
                    ':id' => $data['id'],
                    ':title' => $data['title'],
                    ':description' => $data['description'] ?? '',
                    ':task_date' => $data['task_date'],
                    ':start_time' => $data['start_time'] ?? null,
                    ':end_time' => $data['end_time'] ?? null,
                    ':priority' => $data['priority'] ?? 'medium',
                    ':category' => $data['category'] ?? 'Uncategorized'
                ];
            } else {
                $stmt = $pdo->prepare("INSERT INTO tasks 
                    (title, description, task_date, start_time, end_time, priority, category) 
                    VALUES (:title, :description, :task_date, :start_time, :end_time, :priority, :category)");

                $params = [
                    ':title' => $data['title'],
                    ':description' => $data['description'] ?? '',
                    ':task_date' => $data['task_date'],
                    ':start_time' => $data['start_time'] ?? null,
                    ':end_time' => $data['end_time'] ?? null,
                    ':priority' => $data['priority'] ?? 'medium',
                    ':category' => $data['category'] ?? 'Uncategorized'
                ];
            }

            $stmt->execute($params);
            $taskId = $pdo->lastInsertId();
            echo json_encode(['status' => 'success', 'id' => $taskId]);
            break;

        case 'delete_task':
            $taskId = $_GET['id'] ?? null;
            if (!$taskId) {
                throw new Exception('Missing task ID');
            }

            $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
            $stmt->execute([':id' => $taskId]);
            echo json_encode(['status' => 'success']);
            break;

        default:
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}