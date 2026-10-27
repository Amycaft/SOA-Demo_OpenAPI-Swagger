<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['action'] ?? '') === 'delete') {

    $id = (int)($_POST['id'] ?? 0);

    try {
        if ($id <= 0) {
            throw new Exception('Mã sinh viên không hợp lệ!');
        }

        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['message'] = 'Xóa sinh viên thành công!';
        $_SESSION['message_type'] = 'success';

    } catch (Exception $e) {
        error_log($e->getMessage());
        $_SESSION['message'] = 'Không thể xóa sinh viên!';
        $_SESSION['message_type'] = 'error';
    }

    header('Location: index.php', true, 303);
    exit;
}
?>