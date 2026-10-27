<?php
$isEdit = !empty($editStudent);
?>

<div class="card" id="student-form">
    <h3>
        <?= $isEdit ? 'Cập Nhật Sinh Viên' : 'Thêm Sinh Viên Mới' ?>
    </h3>

    <form action="index.php" method="POST" class="student-form">

        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <input type="hidden" name="action"
               value="<?= $isEdit ? 'update' : 'add' ?>">

        <?php if ($isEdit): ?>
            <input type="hidden" name="id"
                   value="<?= (int)$editStudent['id'] ?>">
        <?php endif; ?>

        <div class="form-row">

            <div class="form-group">
                <label>Mã SV:</label>
                <input type="text" name="student_id"
                       maxlength="20"
                       value="<?= htmlspecialchars($editStudent['student_id'] ?? '') ?>"
                       required>
            </div>

            <div class="form-group">
                <label>Họ và Tên:</label>
                <input type="text" name="fullname"
                       maxlength="100"
                       value="<?= htmlspecialchars($editStudent['fullname'] ?? '') ?>"
                       required>
            </div>

            <div class="form-group">
                <label>Lớp:</label>
                <input type="text" name="classroom"
                       maxlength="50"
                       value="<?= htmlspecialchars($editStudent['classroom'] ?? '') ?>"
                       required>
            </div>

        </div>

        <button type="submit" class="btn-submit">
            <?= $isEdit ? 'Lưu Thay Đổi' : 'Thêm Sinh Viên' ?>
        </button>

        <?php if ($isEdit): ?>
            <a href="index.php" class="btn-cancel">Hủy chỉnh sửa</a>
        <?php endif; ?>

    </form>
</div>
