<div class="col-md-2 bg-dark text-white min-vh-100">
    <h4 class="p-3">القائمة</h4>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link text-white" href="../student/dashboard.php">لوحة التحكم</a>
        </li>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 0): ?>
            <li class="nav-item">
                <a class="nav-link text-white" href="../student/apply.php">تقديم طلب تدريب</a>
            </li>
        <?php endif; ?>

        <li class="nav-item">
            <a class="nav-link text-white" href="../auth/logout.php">تسجيل الخروج</a>
        </li>
    </ul>
</div>
<div class="col-md-10 p-4"> </div>