<?php
include "../includes/session.php";
include "../config/db.php";

// التأكد من تسجيل الدخول
if ($_SESSION['role'] != 0) { header("Location: ../index.php"); exit(); }

// جلب رقم التقرير من الرابط
$report_id = $_GET['report_id'] ?? null;
if (!$report_id) { die("خطأ: يجب تحديد التقرير أولاً."); }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['attachment'])) {
    $target_dir = "../uploads/"; // المجلد الموجود في مشروعك
    $file_name = time() . "_" . basename($_FILES["attachment"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
        // إضافة مسار الملف لقاعدة البيانات
        $sql = "INSERT INTO attachments (report_id, file_name, file_path) VALUES ('$report_id', ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $file_name, $target_file);
        $stmt->execute();
        echo "<div class='alert alert-success'>تم رفع الملف بنجاح!</div>";
    }
}
?>

<div class="container mt-4">
    <h3>رفع مرفقات للتقرير رقم: <?php echo $report_id; ?></h3>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label>اختر الملف (كشف توقيع أو صور أنشطة):</label>
            <input type="file" name="attachment" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">رفع الملف</button>
        <a href="daily_reports.php" class="btn btn-secondary">عودة</a>
    </form>
</div>