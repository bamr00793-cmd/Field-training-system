<?php 
include "../includes/session.php"; 
include "../includes/header.php"; 
include "../includes/sidebar.php"; 
include "../config/db.php"; // تأكد من تضمين اتصال قاعدة البيانات

// معالجة حفظ التقرير عند الإرسال
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_SESSION['id']; // تأكد أن session يحتوي على user_id
    $report_date = $_POST['report_date'];
    $tasks = $_POST['tasks'];
    $skills = $_POST['skills'];
    $difficulties = $_POST['difficulties'];

    // 1. حفظ بيانات التقرير
    $stmt = $conn->prepare("INSERT INTO daily_reports (student_id, report_date, tasks, skills, difficulties) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $student_id, $report_date, $tasks, $skills, $difficulties);
    
    if ($stmt->execute()) {
        $report_id = $stmt->insert_id; // الحصول على رقم التقرير الجديد

        // 2. معالجة رفع الملف (إذا تم اختيار ملف)
        if (!empty($_FILES['attachment']['name'])) {
            $fileName = time() . '_' . basename($_FILES['attachment']['name']);
            $target = "../uploads/" . $fileName;

            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target)) {
                $conn->query("INSERT INTO attachments (report_id, file_name) VALUES ('$report_id', '$fileName')");
            }
        }
        echo "<script>alert('تم حفظ التقرير بنجاح'); window.location.href='daily_reports.php';</script>";
    }
}
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white"><h4>إضافة تقرير يومي جديد</h4></div>
        <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>التاريخ:</label>
                    <input type="date" name="report_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>المهام:</label>
                    <textarea name="tasks" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label>المهارات:</label>
                    <textarea name="skills" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label>الصعوبات:</label>
                    <textarea name="difficulties" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label>إرفاق ملف (كشف توقيع، صور):</label>
                    <input type="file" name="attachment" class="form-control">
                </div>
                <button type="submit" class="btn btn-success">حفظ التقرير</button>
                <a href="daily_reports.php" class="btn btn-secondary">إلغاء</a>
            </form>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>