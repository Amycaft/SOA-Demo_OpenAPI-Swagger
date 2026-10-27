<?php
$editStudent = null;

if (isset($_GET['edit']) && ctype_digit($_GET['edit'])) {
    $id = (int) $_GET['edit'];

    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $editStudent = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['action'] ?? '') === 'update') {

    $id = (int)($_POST['id'] ?? 0);
    $student_id = trim($_POST['student_id'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $classroom = trim($_POST['classroom'] ?? '');

    try {
        if ($id <= 0 || $student_id === '' ||$fullname === '' || $classroom === '') {
            throw new Exception('Thông tin cập nhật không hợp lệ!');
        }

        $stmt = $conn->prepare(
            "UPDATE students
             SET student_id = ?, fullname = ?, classroom = ?
             WHERE id = ?"
        );

        $stmt->bind_param("sssi", $student_id, $fullname, $classroom, $id);

        $stmt->execute();
        $stmt->close();

        $_SESSION['message'] = 'Cập nhật sinh viên thành công!';
        $_SESSION['message_type'] = 'success';

    } catch (Exception $e) {
        error_log($e->getMessage());
        $_SESSION['message'] = 'Không thể cập nhật sinh viên!';
        $_SESSION['message_type'] = 'error';
    }

    header('Location: index.php', true, 303);
    exit;
}
?>
