<?php
$allowedOrigins = [
    'http://localhost:8081',
    'http://127.0.0.1:8081',
    'http://localhost:5500'
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins, true)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Vary: Origin");
}
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, Accept");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../db.php';

function respond($status, $data)
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function readInput()
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (!is_array($data)) {
        respond(400, ['error' => 'JSON khong hop le']);
    }

    return $data;
}

function validateStudent($data)
{
    $student_id = trim($data['student_id'] ?? '');
    $fullname = trim($data['fullname'] ?? '');
    $classroom = trim($data['classroom'] ?? '');

    if ($student_id === '' || $fullname === '' || $classroom === '') {
        respond(422, ['error' => 'Vui long nhap day du thong tin']);
    }

    return [$student_id, $fullname, $classroom];
}

function handleDatabaseError($e)
{
    if ((int)$e->getCode() === 1062) {
        respond(409, ['error' => 'Ma sinh vien da ton tai']);
    }

    error_log($e->getMessage());
    respond(500, ['error' => 'Loi co so du lieu']);
}

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

try {
    // GET: Lay danh sach sinh vien
    if ($method === 'GET' && $id === null) {
        $result = $conn->query(
            'SELECT id, student_id, fullname, classroom FROM students ORDER BY id'
        );

        respond(200, ['data' => $result->fetch_all(MYSQLI_ASSOC)]);
    }

    // GET: Lay mot sinh vien
    if ($method === 'GET' && $id !== null) {
        if ($id <= 0) {
            respond(400, ['error' => 'ID khong hop le']);
        }

        $stmt = $conn->prepare(
            'SELECT id, student_id, fullname, classroom FROM students WHERE id = ?'
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $student = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$student) {
            respond(404, ['error' => 'Khong tim thay sinh vien']);
        }

        respond(200, ['data' => $student]);
    }

    // POST: Them sinh vien
    if ($method === 'POST') {
        $data = readInput();
        [$student_id, $fullname, $classroom] = validateStudent($data);

        $stmt = $conn->prepare(
            'INSERT INTO students (student_id, fullname, classroom) VALUES (?, ?, ?)'
        );
        $stmt->bind_param('sss', $student_id, $fullname, $classroom);
        $stmt->execute();
        $newId = $conn->insert_id;
        $stmt->close();

        respond(201, [
            'message' => 'Them sinh vien thanh cong',
            'data' => [
                'id' => $newId,
                'student_id' => $student_id,
                'fullname' => $fullname,
                'classroom' => $classroom
            ]
        ]);
    }

    // PUT: Cap nhat sinh vien
    if ($method === 'PUT' && $id !== null) {
        if ($id <= 0) {
            respond(400, ['error' => 'ID khong hop le']);
        }

        $data = readInput();
        [$student_id, $fullname, $classroom] = validateStudent($data);

        $check = $conn->prepare('SELECT id FROM students WHERE id = ?');
        $check->bind_param('i', $id);
        $check->execute();
        $exists = $check->get_result()->fetch_assoc();
        $check->close();

        if (!$exists) {
            respond(404, ['error' => 'Khong tim thay sinh vien']);
        }

        $stmt = $conn->prepare(
            'UPDATE students SET student_id = ?, fullname = ?, classroom = ? WHERE id = ?'
        );
        $stmt->bind_param('sssi', $student_id, $fullname, $classroom, $id);
        $stmt->execute();
        $stmt->close();

        respond(200, [
            'message' => 'Cap nhat sinh vien thanh cong',
            'data' => [
                'id' => $id,
                'student_id' => $student_id,
                'fullname' => $fullname,
                'classroom' => $classroom
            ]
        ]);
    }

    // DELETE: Xoa sinh vien
    if ($method === 'DELETE' && $id !== null) {
        if ($id <= 0) {
            respond(400, ['error' => 'ID khong hop le']);
        }

        $stmt = $conn->prepare('DELETE FROM students WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $deleted = $stmt->affected_rows;
        $stmt->close();

        if ($deleted === 0) {
            respond(404, ['error' => 'Khong tim thay sinh vien']);
        }

        respond(200, ['message' => 'Xoa sinh vien thanh cong']);
    }

    header('Allow: GET, POST, PUT, DELETE');
    respond(405, ['error' => 'Method hoac endpoint khong duoc ho tro']);

} catch (mysqli_sql_exception $e) {
    handleDatabaseError($e);
} catch (Throwable $e) {
    error_log($e->getMessage());
    respond(500, ['error' => 'Loi may chu']);
}
