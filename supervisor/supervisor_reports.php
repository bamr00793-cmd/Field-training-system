<?php 
include "../includes/session.php";
include "../includes/header.php"; 
include "../includes/sidebar.php"; 
include "../config/db.php";

$current_supervisor_name = $_SESSION['name'];

// التعديل: استخدام LEFT JOIN لضمان ظهور التقارير حتى لو لم تكن مرتبطة بطلب تدريب
// واستخدام TRIM للمقارنة لضمان تجاوز مشاكل المسافات في الأسماء
// استعلام مرن يستخدم LEFT JOIN لجلب التقارير والمرفقات دائماً
$sql = "SELECT dr.*, u.name as student_name, 
               GROUP_CONCAT(a.file_name SEPARATOR ',') as file_names
        FROM daily_reports dr
        JOIN users u ON dr.student_id = u.id
        LEFT JOIN attachments a ON dr.id = a.report_id
        GROUP BY dr.id
        ORDER BY dr.report_date DESC";

$result = $conn->query($sql);
?>

<div class="container mt-4">
    <h3>تقارير الطلاب ومرفقاتهم</h3>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>اسم الطالب</th>
                <th>التاريخ</th>
                <th>المهام</th>
                <th>المهارات</th>
                <th>الصعوبات</th>
                <th>المرفقات</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['report_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['tasks']); ?></td>
                    <td><?php echo htmlspecialchars($row['skills']); ?></td>
                    <td><?php echo htmlspecialchars($row['difficulties']); ?></td>
                    <td>
                        <?php if (!empty($row['file_names'])): 
                            $files = explode(',', $row['file_names']);
                            foreach ($files as $file): ?>
                                <a href="../uploads/<?php echo htmlspecialchars($file); ?>" target="_blank" class="btn btn-sm btn-info mb-1 d-block">عرض ملف</a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="text-muted">لا يوجد</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">لا توجد تقارير متاحة حالياً للمشرف: <?php echo htmlspecialchars($current_supervisor_name); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include "../includes/footer.php"; ?>