<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['action'] ?? '') === 'add') {

    $student_id = trim($_POST['student_id'] ?? '');
    $fullname   = trim($_POST['fullname'] ?? '');
    $classroom  = trim($_POST['classroom'] ?? '');

    try {
        if ($student_id === '' || $fullname === '' || $classroom === '') {
            throw new Exception('Vui lòng nhập đầy đủ thông tin!');
        }

        $stmt = $conn->prepare("INSERT INTO students (student_id, fullname, classroom) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $student_id, $fullname, $classroom);
        $stmt->execute();
        $stmt->close();

        $_SESSION['message'] = 'Thêm sinh viên thành công!';
        $_SESSION['message_type'] = 'success';

    } catch (mysqli_sql_exception $e) {
        $_SESSION['message'] = ($e->getCode() === 1062)
            ? 'Mã sinh viên đã tồn tại!'
            : 'Không thể thêm sinh viên!';
        $_SESSION['message_type'] = 'error';
    } catch (Exception $e) {
        $_SESSION['message'] = $e->getMessage();
        $_SESSION['message_type'] = 'error';
    }

    header('Location: index.php', true, 303);
    exit;
}
?>