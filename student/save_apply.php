<?php
include "../includes/session.php";
include "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // التأكد من وجود بيانات الطالب في الجلسة
    if (!isset($_SESSION['id'])) {
        die("خطأ: يجب تسجيل الدخول.");
    }

    $student_id = $_SESSION['id'];
    $org_id = $conn->real_escape_string($_POST['organization_id']);
    $start = $conn->real_escape_string($_POST['start_date']);
    $end = $conn->real_escape_string($_POST['end_date']);

    // إدخال الطلب
    $sql = "INSERT INTO training_requests (student_id, organization_id, start_date, end_date, status) 
            VALUES ('$student_id', '$org_id', '$start', '$end', 'Pending')";

    if ($conn->query($sql)) {
        // إعادة التوجيه لصفحة الـ dashboard مع رسالة نجاح
        header("Location: dashboard.php?status=success");
        exit();
    } else {
        echo "حدث خطأ أثناء حفظ الطلب: " . $conn->error;
    }
} else {
    header("Location: apply.php");
}
?>