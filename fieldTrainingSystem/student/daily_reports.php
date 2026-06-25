<?php 
include "../includes/session.php"; 
include "../includes/header.php"; 
include "../includes/sidebar.php"; 
include "../config/db.php";

$student_id = $_SESSION['id'];

// استعلام لجلب التقارير اليومية للطالب
$sql = "SELECT * FROM daily_reports WHERE student_id = '$student_id' ORDER BY report_date DESC";
$result = $conn->query($sql);
?>

<style>
    .custom-student-content {
        margin-right: 280px !important; /* يدفع المحتوى لتبدأ بعد الـ Sidebar تماماً */
        padding: 20px;
        min-height: 100vh;
        background-color: #f4f6f9; /* للحفاظ على خلفية متناسقة */
    }
    /* متجاوب مع الشاشات الصغيرة والهواتف */
    @media (max-width: 768px) {
        .custom-student-content {
            margin-right: 0 !important;
            padding: 10px;
        }
    }
</style>

<div class="custom-student-content">
    <div class="container-fluid mt-2">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="fw-bold">تقاريري اليومية</h3>
            <a href="add_report.php" class="btn btn-success">إضافة تقرير جديد</a>
        </div>

        <table class="table table-bordered mt-3">
            <thead class="table-dark">
                <tr>
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
                        <td><?php echo htmlspecialchars($row['report_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['tasks']); ?></td>
                        <td><?php echo htmlspecialchars($row['skills']); ?></td>
                        <td><?php echo htmlspecialchars($row['difficulties']); ?></td>
                        <td>
                            <?php
                            // جلب المرفقات المرتبطة بهذا التقرير
                            $rep_id = $row['id'];
                            $att_sql = "SELECT * FROM attachments WHERE report_id = '$rep_id'";
                            $att_result = $conn->query($att_sql);
                            
                            if ($att_result && $att_result->num_rows > 0):
                                while($att = $att_result->fetch_assoc()): ?>
                                    <a href="../uploads/<?php echo htmlspecialchars($att['file_name']); ?>" target="_blank" class="btn btn-sm btn-info mb-1 d-block">
                                        عرض: <?php echo substr($att['file_name'], 11); // إخفاء الطابع الزمني للاسم ?>
                                    </a>
                                <?php endwhile;
                            else:
                                echo "<span class='text-muted small'>لا توجد ملفات</span><br>";
                            endif;
                            ?>
                            <a href="upload_attachment.php?report_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary mt-1">
                                + رفع ملف
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">لا توجد تقارير حالياً.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "../includes/footer.php"; ?>