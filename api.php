    <?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Content-Type: application/json");

    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'get_tasks_by_date_range':
            $start_date = $_GET['start_date'] ?? null;
            $end_date = $_GET['end_date'] ?? null;
        
            // If only one day is selected
            if (!$end_date) {
                $end_date = $start_date;
            }
        
            if ($start_date && $end_date) {
                $stmt = $pdo->prepare("SELECT * FROM tasks WHERE task_date BETWEEN :start_date AND :end_date ORDER BY start_time");
                $stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
                $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($tasks);
            } else {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid date range']);
            }
            break;

 

        case 'get_tasks':
            getTasks();
            break;
        case 'save_task':
            saveTask();
            break;
        case 'delete_task':
            deleteTask();
            break;
        default:
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }

    function getTasks() {
        // Database connection details
        $host = 'localhost';
        $dbname = 'task_manager';
        $username = 'root';
        $password = '';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->query("SELECT * FROM tasks");
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($tasks);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    function saveTask() {
        // Retrieve JSON data
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        // Database connection details
        $host = 'localhost';
        $dbname = 'task_manager';
        $username = 'root';
        $password = '';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (isset($data['id']) && $data['id']) {
                $stmt = $pdo->prepare("UPDATE tasks SET 
                    title = ?, 
                    description = ?, 
                    task_date = ?, 
                    start_time = ?, 
                    end_time = ?, 
                    priority = ?,
                    category = ? 
                    WHERE id = ?");
                $stmt->execute([
                    $data['title'], 
                    $data['description'],
                    $data['task_date'],
                    $data['start_time'],
                    $data['end_time'],
                    $data['priority'],
                    $data['category'], // New category field
                    $data['id']
                ]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO tasks 
                    (title, description, task_date, start_time, end_time, priority, category) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $data['title'], 
                    $data['description'],
                    $data['task_date'],
                    $data['start_time'],
                    $data['end_time'],
                    $data['priority'],
                    $data['category'] // New category field
                ]);
            }

            echo json_encode(['status' => 'success']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    function deleteTask() {
        // Retrieve JSON data
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        // Database connection details
        $host = 'localhost';
        $dbname = 'task_manager';
        $username = 'root';
        $password = '';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
            $stmt->execute([$data['id']]);

            echo json_encode(['status' => 'success']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }