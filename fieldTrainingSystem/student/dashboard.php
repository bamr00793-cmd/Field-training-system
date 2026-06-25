<?php 
include "../includes/session.php"; 
include "../includes/header.php"; 
include "../includes/sidebar.php"; 
include "../config/db.php";
?>

<div class="card mt-4">
    <div class="card-header">أهلاً بك يا <?php echo $_SESSION['name']; ?></div>
    <div class="card-body">
        <h3>لوحة تحكم الطالب</h3>
        <p>مرحباً بك في نظام التدريب الميداني.</p>
    </div>
</div>

<!-- بطاقة التقارير اليومية الجديدة -->
<div class="card mt-4 shadow-sm border-info">
    <div class="card-header bg-info text-white">التقارير اليومية</div>
    <div class="card-body">
        <p>يمكنك إدارة تقاريرك اليومية الخاصة بالتدريب الميداني هنا.</p>
        <a href="daily_reports.php" class="btn btn-primary">إضافة تقرير جديد</a>
        <a href="daily_reports.php" class="btn btn-outline-info">عرض تقاريري السابقة</a>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-primary text-white">طلباتي السابقة</div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>المؤسسة</th>
                    <th>تاريخ البدء</th>
                    <th>تاريخ الانتهاء</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // استخدام ID الطالب من الجلسة
                $student_id = $_SESSION['id'];

                $sql = "SELECT tr.*, o.organization_name 
                        FROM training_requests tr 
                        JOIN organizations o ON tr.organization_id = o.id 
                        WHERE tr.student_id = '$student_id'";
                
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['organization_name']; ?></td>
                            <td><?php echo $row['start_date']; ?></td>
                            <td><?php echo $row['end_date']; ?></td>
                            <td>
                                <?php 
                                if($row['status'] == 'Approved') {
                                    echo '<span class="badge bg-success">تم القبول</span>';
                                } else {
                                    echo '<span class="badge bg-warning text-dark">قيد الانتظار</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endwhile;
                } else {
                    echo "<tr><td colspan='4' class='text-center'>لا توجد طلبات تدريب حالياً.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "../includes/footer.php"; ?>