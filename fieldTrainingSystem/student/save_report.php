<?php
include "../includes/session.php";
include "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // جلب البيانات من النموذج
    $student_id = $_SESSION['id'];
    $report_date = $_POST['report_date'];
    $tasks = $_POST['tasks'];
    $skills = $_POST['skills'];
    $difficulties = $_POST['difficulties'];

    // حفظ البيانات في جدول daily_reports
    $stmt = $conn->prepare("INSERT INTO daily_reports (student_id, report_date, tasks, skills, difficulties) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $student_id, $report_date, $tasks, $skills, $difficulties);

    if ($stmt->execute()) {
        header("Location: daily_reports.php?status=success");
    } else {
        echo "خطأ: " . $stmt->error;
    }
    $stmt->close();
}
?>