<?php

session_start();
require_once 'db.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include 'add_student.php';
include 'update_student.php';
include 'delete_student.php';

include 'header.php';

if (isset($_SESSION['message'])) {
    $type = $_SESSION['message_type'] ?? 'success';

    echo '<div class="alert ' .
        ($type === 'error' ? 'error' : 'success') . '">' .
        htmlspecialchars($_SESSION['message']) .
        '</div>';

    unset($_SESSION['message'], $_SESSION['message_type']);
}

include 'form.php';
include 'table.php';
?>

    </div>
    <script>
        document.querySelectorAll('.alert').forEach(function (el) {
            setTimeout(function () {
                el.style.transition = 'opacity 0.5s';
                el.style.opacity = '0';
                setTimeout(function () { el.remove(); }, 500);
            }, 30000);
        });
    </script>
</body>
</html>
