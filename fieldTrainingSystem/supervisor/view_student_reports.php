<?php 
session_start();
include "../config/db.php";

// 1. التحقق من صلاحية الدخول للمشرف الأكاديمي فقط (Role = 1)
if (!isset($_SESSION['id']) || $_SESSION['role'] != 1) {
    header("Location: ../auth/login.php");
    exit();
}

// 2. التأكد من إرسال رقم الطالب المراد عرض تقاريره
if (!isset($_GET['student_id']) || empty($_GET['student_id'])) {
    header("Location: dashboard.php");
    exit();
}

$student_id = intval($_GET['student_id']);

// 3. جلب اسم الطالب الأساسي من جدول المستخدمين
$student_query = $conn->query("SELECT name FROM users WHERE id = '$student_id' AND role = 3");
if (!$student_query || $student_query->num_rows == 0) {
    die("<div style='text-align:center; margin-top:50px; font-family:sans-serif;' dir='rtl'><h2>عذراً، لم يتم العثور على هذا الطالب في النظام.</h2><a href='dashboard.php'>العودة للوحة التحكم</a></div>");
}
$student = $student_query->fetch_assoc();

// 4. استعلام جلب كافة التقارير اليومية المرتبطة بالطالب من الأحدث للأقدم
$sql_reports = "SELECT * FROM daily_reports WHERE student_id = '$student_id' ORDER BY report_date DESC";
$result_reports = $conn->query($sql_reports);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التقارير اليومية للطالب - <?php echo htmlspecialchars($student['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .table-responsive { background-color: #fff; border-radius: 8px; padding: 15px; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
        .report-text { white-space: pre-line; font-size: 0.95rem; line-height: 1.6; color: #333; }
    </style>
</head>
<body>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-12">
            
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <div>
                    <h3 class="fw-bold text-primary"><i class="fas fa-book-open me-2"></i> سجل التقارير التدريبية اليومية</h3>
                    <p class="text-muted mb-0">متابعة الأنشطة الميدانية للطالب: <strong><?php echo htmlspecialchars($student['name']); ?></strong></p>
                </div>
                <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-right me-1"></i> العودة للوحة التحكم</a>
            </div>

            <div class="table-responsive">
                <?php if ($result_reports && $result_reports->num_rows > 0): ?>
                    <table class="table table-bordered table-striped table-hover align-middle">
                        <thead class="table-dark text-center">
                            <tr>
                                <th style="width: 12%;">التاريخ</th>
                                <th style="width: 33%;">المهام والأنشطة المنجزة (Tasks)</th>
                                <th style="width: 28%;">المهارات المكتسبة (Skills)</th>
                                <th style="width: 27%;">الصعوبات والتحديات (Difficulties)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($report = $result_reports->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center fw-bold text-secondary">
                                        <i class="far fa-calendar-alt text-primary me-1"></i>
                                        <?php echo htmlspecialchars($report['report_date']); ?>
                                    </td>
                                    
                                    <td class="report-text"><?php echo htmlspecialchars($report['tasks']); ?></td>
                                    
                                    <td class="report-text text-success fw-semibold"><?php echo htmlspecialchars($report['skills']); ?></td>
                                    
                                    <td class="report-text text-danger">
                                        <?php echo $report['difficulties'] ? htmlspecialchars($report['difficulties']) : '<span class="text-muted italic">لا يوجد صعوبات</span>'; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-warning text-center p-4 my-3">
                        <h4 class="alert-heading fw-bold"><i class="fas fa-exclamation-circle me-2"></i> لا يوجد تقارير مرصودة</h4>
                        <p class="mb-0">هذا الطالب لم يقم برفع أي تقارير يومية في النظام حتى هذه اللحظة.</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>