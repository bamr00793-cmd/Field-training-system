<?php
session_start();
include "../config/db.php";

// 1. التحقق الصارم من صلاحية الدخول للمؤسسات التدريبية فقط (Role = 2)
if (!isset($_SESSION['id']) || $_SESSION['role'] != 2) {
    header("Location: ../auth/login.php");
    exit();
}

// الحصول على اسم المؤسسة الحالية من الجلسة
$org_name = $_SESSION['name']; 

/* 2. جلب الـ id الصحيح للمؤسسة من جدول organizations بناءً على الاسم المشترك */
$organization_id = 0;
$org_check = $conn->query("SELECT id FROM organizations WHERE organization_name = '$org_name'");

if ($org_check && $org_check->num_rows > 0) {
    $org_data = $org_check->fetch_assoc();
    $organization_id = $org_data['id']; 
} else {
    // كود احتياطي في حال لم يجد الاسم
    $organization_id = $_SESSION['id'];
}

// 3. جلب قائمة الطلاب المقبولين (Approved) والذين يتدربون حالياً في هذه المؤسسة
$sql_students = "SELECT tr.student_id, u.name AS student_name, u.email AS student_email, u.phone AS student_phone
                 FROM training_requests tr
                 JOIN users u ON tr.student_id = u.id
                 WHERE tr.organization_id = '$organization_id' AND tr.status = 'Approved'";
$result_students = $conn->query($sql_students);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم المؤسسة التدريبية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-brand { font-weight: bold; font-size: 1.4rem; }
        .card { border: none; border-radius: 12px; }
        .table align-middle th { font-weight: 600; }
        .badge-status { padding: 8px 12px; border-radius: 30px; font-size: 0.85rem; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-building text-info me-2"></i> بوابة المؤسسات التدريبية</a>
            <div class="d-flex align-items-center">
                <span class="navbar-text text-white me-3 d-none d-md-inline">
                    <i class="fas fa-user-circle"></i> مرحباً: <strong><?php echo htmlspecialchars($org_name); ?></strong>
                </span>
                <a href="../auth/logout.php" class="btn btn-danger btn-sm"><i class="fas fa-sign-out-alt"></i> خروج</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        
        <div class="card p-4 bg-white shadow-sm mb-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="text-secondary fw-bold">لوحة التحكم والمتابعة الميدانية</h2>
                    <p class="text-muted mb-0">من هنا يمكنك متابعة أداء الطلاب المتدربين في مؤسستكم، وتعبئة نماذج التقييم الدورية والنهائية لإرسالها للمشرف الأكاديمي.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="badge bg-primary p-3 fs-6 rounded-3">حساب مؤسسة معتمد</span>
                </div>
            </div>
        </div>

        <div class="card shadow-sm bg-white">
            <div class="card-header bg-gradient bg-secondary text-white p-3">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i> الطلاب المتدربون حالياً في المؤسسة</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">اسم الطالب</th>
                                <th>البريد الإلكتروني</th>
                                <th>رقم الجوال</th>
                                <th>حالة التقييم الحالي</th>
                                <th class="text-center">الإجراء الإداري</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result_students && $result_students->num_rows > 0): ?>
                                <?php while ($row = $result_students->fetch_assoc()): ?>
                                    <?php 
                                    $student_id = $row['student_id'];
                                    
                                    // فحص ذكي: هل قامت المؤسسة بتقييم هذا الطالب مسبقاً؟
                                    $check_eval = $conn->query("SELECT id, recommendation FROM evaluations WHERE student_id = $student_id");
                                    $is_evaluated = false;
                                    
                                    if ($check_eval && $check_eval->num_rows > 0) {
                                        $eval_row = $check_eval->fetch_assoc();
                                        if (!empty($eval_row['recommendation'])) {
                                            $is_evaluated = true;
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark"><?php echo htmlspecialchars($row['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['student_email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['student_phone'] ? $row['student_phone'] : 'غير مدخل'); ?></td>
                                        <td>
                                            <?php if ($is_evaluated): ?>
                                                <span class="badge bg-success badge-status text-white"><i class="fas fa-check-circle"></i> تم إرسال التقييم</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning badge-status text-dark"><i class="fas fa-exclamation-triangle"></i> بانتظار التقييم</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center pe-4">
                                            <?php if ($is_evaluated): ?>
                                                <a href="view_evaluation.php?student_id=<?php echo $student_id; ?>" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-eye"></i> عرض التقييم المرسل
                                                </a>
                                            <?php else: ?>
                                                <a href="evaluate_student.php?student_id=<?php echo $student_id; ?>" class="btn btn-success btn-sm shadow-sm">
                                                    <i class="fas fa-pen-to-square"></i> تقييم الطالب الآن
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="fas fa-folder-open fa-3x mb-3 text-secondary"></i>
                                        <p class="mb-0">لا يوجد طلاب متدربون ومقبولون في مؤسستكم حالياً.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <footer class="text-center py-4 text-muted mt-auto">
        <p class="small">نظام إدارة التدريب الميداني &copy; 2026 - مشروع تخرج نظم المعلومات الإدارية (MIS)</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>