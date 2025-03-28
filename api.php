<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CORS and Content-Type Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Database Configuration
$host = 'localhost';
$dbname = 'task_manager';
$username = 'root';
$password = '';

// Logging Function
function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, 'task_manager_error.log');
}

try {
    // Database Connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Handle OPTIONS request for CORS preflight
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    // Get the requested action
    $action = $_GET['action'] ?? '';

    switch ($action) {
        // Get Tasks Action
        case 'get_tasks':
            $currentDateTime = date('Y-m-d H:i:s');
        
            $stmt = $pdo->prepare("SELECT * FROM tasks 
                WHERE CONCAT(task_date, ' ', start_time) > :currentDateTime 
                AND (completed IS NULL OR completed != 'Yes')
                ORDER BY task_date, start_time");
        
            $stmt->execute([':currentDateTime' => $currentDateTime]);
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Ensure all tasks have a notificationMessage
            foreach ($tasks as &$task) {
                $task['notificationMessage'] = $task['notificationMessage'] ?? '';
            }
            
            echo json_encode($tasks);
            break;

        // Save Task Action
        case 'save_task':
            // Get JSON input
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
        
            // Add debug logging
            logError('Received Task Data: ' . print_r($data, true));
        
            // Validate required fields
            if (empty($data['title']) || empty($data['task_date'])) {
                throw new Exception('Missing required fields: title or task_date');
            }
        
            // Prepare SQL statement
            $stmt = $pdo->prepare("INSERT INTO tasks 
            (title, description, task_date, start_time, end_time, 
            priority, category, notificationMessage, completed, rescheduleTimer) 
            VALUES 
            (:title, :description, :task_date, :start_time, :end_time, 
            :priority, :category, :notificationMessage, 'No', 2)");
            // Prepare parameters with default values
            $params = [
                ':title' => $data['title'],
                ':description' => $data['description'] ?? '',
                ':task_date' => $data['task_date'],
                ':start_time' => $data['start_time'] ?? null,
                ':end_time' => $data['end_time'] ?? null,
                ':priority' => $data['priority'] ?? 'medium',
                ':category' => $data['category'] ?? 'Uncategorized',
                ':notificationMessage' => $data['notificationMessage'] ?? 'Do the job' // Match JS default
            ];
            // Debug logging of params
            logError('SQL Params: ' . print_r($params, true));
        
            // Execute the statement
            try {
                $stmt->execute($params);
                $taskId = $pdo->lastInsertId();
        
                // Log successful task creation with more details
                logError("Task created successfully. ID: $taskId, Notification message: " . $params[':notificationMessage']);
        
                echo json_encode([
                    'status' => 'success', 
                    'id' => $taskId,
                    'notificationMessage' => $params[':notificationMessage']
                ]);
            } catch (PDOException $e) {
                // Log database insertion error
                logError('Task Insertion Error: ' . $e->getMessage());
                
                http_response_code(500);
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'Failed to save task: ' . $e->getMessage()
                ]);
            
            }    
            break;
            
            case 'update_notification':
                try {
                    $data = json_decode(file_get_contents('php://input'), true);
                    
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new Exception('Invalid JSON input');
                    }
            
                    if (!isset($data['id']) || !is_numeric($data['id'])) {
                        throw new Exception('Invalid task ID');
                    }
            
                    if (!isset($data['notificationMessage'])) {
                        throw new Exception('Missing notification message');
                    }
            
                    $stmt = $pdo->prepare("UPDATE tasks SET notificationMessage = ? WHERE id = ?");
                    $stmt->execute([
                        $data['notificationMessage'],
                        $data['id']
                    ]);
            
                    // Verify update
                    if ($stmt->rowCount() === 0) {
                        throw new Exception('No rows affected - task may not exist');
                    }
            
                    echo json_encode([
                        'status' => 'success', 
                        'message' => 'Notification updated',
                        'updatedId' => $data['id']
                    ]);
                    
                } catch (Exception $e) {
                    http_response_code(400);
                    echo json_encode([
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ]);

                    logError('UPDATE NOTIFICATION DATA: ' . print_r($data, true));
logError('SQL QUERY: UPDATE tasks SET notificationMessage = "' . $data['notificationMessage'] . '" WHERE id = ' . $data['id']);
                }
                break;

        // Delete Task Action
        case 'delete_task':
            $taskId = $_GET['id'] ?? null;
            if (!$taskId) {
                throw new Exception('Missing task ID');
            }

            $stmt = $pdo->prepare("UPDATE tasks SET completed = 'Yes' WHERE id = :id");
            
            try {
                $stmt->execute([':id' => $taskId]);
                logError("Task $taskId marked as completed");
                echo json_encode(['status' => 'success']);
            } catch (Exception $e) {
                logError('Task Deletion Error: ' . $e->getMessage());
                
                http_response_code(500);
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'Failed to delete task: ' . $e->getMessage()
                ]);
            }
            break;

        // Get Tasks by Date Range
        case 'get_tasks_by_date_range':
            $start_date = $_GET['start_date'] ?? date('Y-m-d');
            $end_date = $_GET['end_date'] ?? date('Y-m-d');
            
            $stmt = $pdo->prepare("SELECT * FROM tasks 
                WHERE task_date BETWEEN :start_date AND :end_date 
                AND (completed IS NULL OR completed != 'Yes')
                ORDER BY task_date, start_time");
            
            try {
                $stmt->execute([
                    ':start_date' => $start_date, 
                    ':end_date' => $end_date
                ]);
                $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($tasks);
            } catch (Exception $e) {
                logError('Date Range Fetch Error: ' . $e->getMessage());
                
                http_response_code(500);
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'Failed to fetch tasks: ' . $e->getMessage()
                ]);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode([
                'status' => 'error', 
                'message' => 'Invalid action'
            ]);
    }

} catch (Exception $e) {
    // Global exception handler
    logError('Global Exception: ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage()
    ]);
}