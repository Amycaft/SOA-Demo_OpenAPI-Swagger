<?php
$result = $conn->query("SELECT * FROM students ORDER BY id DESC");
?>

<div class="card">
    <h3>Danh Sách Sinh Viên</h3>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Mã SV</th>
                    <th>Họ và Tên</th>
                    <th>Lớp</th>
                    <th>Thao tác</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php $stt = 1; ?>

                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $stt++ ?></td>

                            <td>
                                <strong>
                                    <?= htmlspecialchars($row['student_id']) ?>
                                </strong>
                            </td>

                            <td><?= htmlspecialchars($row['fullname']) ?></td>
                            <td><?= htmlspecialchars($row['classroom']) ?></td>

                            <td>
                                <div class="table-actions">

                                    <!-- sửa -->
                                    <a href="index.php?edit=<?= (int)$row['id'] ?>#student-form"
                                       class="btn-edit">
                                        Sửa
                                    </a>

                                    <!-- xóa -->
                                    <form action="index.php"
                                          method="POST"
                                          onsubmit="return confirm('Bạn có chắc muốn xóa sinh viên này?');">

                                        <input type="hidden"
                                               name="action"
                                               value="delete">

                                        <input type="hidden"
                                               name="id"
                                               value="<?= (int)$row['id'] ?>">

                                        <input type="hidden"
                                               name="csrf_token"
                                               value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                                        <button type="submit" class="btn-delete">
                                            Xóa
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="5"
                            style="text-align: center; color: var(--text-muted);">
                            Chưa có dữ liệu sinh viên.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
