<?php
session_start();
include "../config/db.php";

// 1. التحقق من صلاحية الدخول للمشرف الأكاديمي فقط (Role = 1)
if (!isset($_SESSION['id']) || $_SESSION['role'] != 1) {
    header("Location: ../auth/login.php");
    exit();
}

$supervisor_name = $_SESSION['name'];

// معالجة طلب الأجاكس (AJAX) لجلب التقارير اليومية للطالب دون إعادة تحميل الصفحة
if (isset($_GET['fetch_reports'])) {
    $st_id = intval($_GET['fetch_reports']);
    $output = '';
    
    $reports_q = $conn->query("SELECT * FROM daily_reports WHERE student_id = '$st_id' ORDER BY report_date DESC");
    
    if ($reports_q && $reports_q->num_rows > 0) {
        $output .= '<div class="table-responsive"><table class="table table-bordered table-striped text-wrap align-middle" dir="rtl">';
        $output .= '<thead class="table-dark text-center"><tr><th>التاريخ</th><th>المهام (Tasks)</th><th>المهارات (Skills)</th><th>الصعوبات (Difficulties)</th></tr></thead><tbody>';
        while ($rep = $reports_q->fetch_assoc()) {
            $diff_text = $rep['difficulties'] ? htmlspecialchars($rep['difficulties']) : '<span class="text-muted">لا يوجد</span>';
            $output .= '<tr>
                <td class="text-center fw-bold text-secondary" style="min-width:100px;">' . htmlspecialchars($rep['report_date']) . '</td>
                <td style="white-space: pre-line;">' . htmlspecialchars($rep['tasks']) . '</td>
                <td class="text-success fw-semibold" style="white-space: pre-line;">' . htmlspecialchars($rep['skills']) . '</td>
                <td class="text-danger" style="white-space: pre-line;">' . $diff_text . '</td>
            </tr>';
        }
        $output .= '</tbody></table></div>';
    } else {
        $output .= '<div class="alert alert-warning text-center mb-0">⚠️ هذا الطالب لم يقم برفع أي تقارير يومية في النظام حتى الآن.</div>';
    }
    echo $output;
    exit(); // إنهاء السكريبت هنا لمنع تحميل باقي الـ HTML عند طلب الأجاكس
}

// 2. جلب الإحصائيات الحيوية للوحة التحكم (Dashboard Statistics)
$count_approved = $conn->query("SELECT COUNT(*) AS total FROM training_requests WHERE status = 'Approved'");
$total_students = $count_approved->fetch_assoc()['total'];

$count_evaluated = $conn->query("SELECT COUNT(*) AS total FROM evaluations");
$total_evaluated = $count_evaluated->fetch_assoc()['total'];

$total_pending_eval = $total_students - $total_evaluated;


// 3. الاستعلام الشامل المعدل لجلب حقول نقاط القوة والضعف لتحديث الكشف فورا
$sql_summary = "SELECT 
                    tr.student_id, 
                    u.name AS student_name, 
                    u.email AS student_email, 
                    u.phone AS student_phone,
                    org.organization_name,
                    ev.final_grade,
                    ev.strengths,
                    ev.weaknesses,
                    ev.recommendation
                FROM training_requests tr
                JOIN users u ON tr.student_id = u.id
                JOIN organizations org ON tr.organization_id = org.id
                LEFT JOIN evaluations ev ON tr.student_id = ev.student_id
                WHERE tr.status = 'Approved'
                ORDER BY u.name ASC";

$result_summary = $conn->query($sql_summary);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم المشرف الأكاديمي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-brand { font-weight: bold; font-size: 1.4rem; }
        .card-stats { border: none; border-radius: 15px; transition: transform 0.2s; }
        .card-stats:hover { transform: translateY(-5px); }
        .main-card { border: none; border-radius: 12px; }
        .table th { font-weight: 600; background-color: #f8f9fa; }
        .grade-badge { font-size: 1rem; padding: 6px 12px; border-radius: 20px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-graduation-cap me-2"></i> بوابة المشرف الأكاديمي</a>
            <div class="d-flex align-items-center">
                <span class="navbar-text text-white me-3 d-none d-md-inline">
                    <i class="fas fa-user-tie"></i> المشرف: <strong><?php echo htmlspecialchars($supervisor_name); ?></strong>
                </span>
                <a href="../auth/logout.php" class="btn btn-light btn-sm text-primary fw-bold"><i class="fas fa-sign-out-alt"></i> خروج</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        
        <div class="mb-4">
            <h2 class="text-dark fw-bold">نظام المتابعة والإشراف الأكاديمي</h2>
            <p class="text-muted">مرحباً بك يا دكتور. يمكنك هنا متابعة أداء طلاب التدريب الميداني، ومراجعة التقييمات والدرجات المرسلة من المؤسسات الشريكة.</p>
        </div>

        <div class="row g-3 mb-5">
            <div class="col-md-4">
                <div class="card card-stats bg-white shadow-sm p-3 border-start border-primary border-5">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-1">إجمالي الطلاب المتدربين</h6>
                            <h3 class="fw-bold text-primary mb-0"><?php echo $total_students; ?></h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-stats bg-white shadow-sm p-3 border-start border-success border-5">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-1">طلاب تم تقييمهم من المؤسسة</h6>
                            <h3 class="fw-bold text-success mb-0"><?php echo $total_evaluated; ?></h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                            <i class="fas fa-check-double fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-stats bg-white shadow-sm p-3 border-start border-warning border-5">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-1">طلاب بانتظار تقييم المؤسسة</h6>
                            <h3 class="fw-bold text-warning mb-0"><?php echo $total_pending_eval; ?></h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card main-card shadow-sm bg-white">
            <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i> كشف متابعة تقييمات التدريب الميداني</h5>
                <span class="badge bg-primary px-3 py-2">تحديث فوري 2026</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead>
                            <tr>
                                <th class="ps-4">اسم الطالب</th>
                                <th>المؤسسة التدريبية</th>
                                <th>رقم التواصل</th>
                                <th class="text-center">درجة المؤسسة (100)</th>
                                <th>التوصية والملحوظات</th>
                                <th class="text-center">الإجراءات والتحكم</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result_summary && $result_summary->num_rows > 0): ?>
                                <?php while ($row = $result_summary->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['student_name']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($row['student_email']); ?></small>
                                        </td>
                                        
                                        <td>
                                            <span class="badge bg-light text-dark border p-2"><i class="fas fa-building text-secondary me-1"></i> <?php echo htmlspecialchars($row['organization_name']); ?></span>
                                        </td>
                                        
                                        <td><?php echo htmlspecialchars($row['student_phone'] ? $row['student_phone'] : 'غير مدخل'); ?></td>
                                        
                                        <td class="text-center">
                                            <?php if ($row['final_grade'] !== null): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success fw-bold grade-badge">
                                                    <?php echo $row['final_grade']; ?> / 100
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary fw-bold grade-badge">
                                                    لم تُرسل بعد
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td>
                                            <div class="text-truncate" style="max-width: 250px;" title="<?php echo htmlspecialchars($row['recommendation']); ?>">
                                                <?php echo $row['recommendation'] ? htmlspecialchars($row['recommendation']) : '<em class="text-muted">لا يوجد ملاحظات</em>'; ?>
                                            </div>
                                        </td>
                                        
                                        <td class="text-center pe-4">
                                            <div class="d-flex gap-2 justify-content-center">
                                                <a href="assign_university_grade.php?student_id=<?php echo $row['student_id']; ?>" class="btn btn-warning btn-sm shadow-sm fw-bold text-dark">
                                                    <i class="fas fa-marker me-1"></i> رصد الدرجة
                                                </a>

                                                <?php if ($row['final_grade'] !== null): ?>
                                                    <a href="view_company_evaluation.php?student_id=<?php echo $row['student_id']; ?>" class="btn btn-primary btn-sm shadow-sm">
                                                        <i class="fas fa-eye"></i> التقييم
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-outline-secondary btn-sm" disabled>
                                                        <i class="fas fa-ban"></i> قيد التدريب
                                                    </button>
                                                <?php endif; ?>

                                                <button type="button" class="btn btn-success btn-sm btn-report" data-id="<?php echo $row['student_id']; ?>" data-name="<?php echo htmlspecialchars($row['student_name']); ?>">
                                                    <i class="fas fa-calendar-alt"></i> تقاريره اليومية
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="fas fa-graduation-cap fa-3x mb-3 text-secondary"></i>
                                        <p class="mb-0">لا يوجد طلاب مسجلين ومقبولين في التدريب الميداني حالياً.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="reportsModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="reportsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold" id="reportsModalLabel"><i class="fas fa-book-open me-2"></i> سجل التقارير اليومية للطالب: <span id="modalStudentName" class="text-warning"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="reportsModalBody">
                    <div class="text-center my-4">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">إغلاق النافذة</button>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center py-4 text-muted bg-white border-top mt-auto">
        <p class="small mb-0">نظام إدارة التدريب الميداني &copy; 2026 - مشروع تخرج نظم المعلومات الإدارية (MIS)</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    $(document).ready(function(){
        $('.btn-report').on('click', function(){
            var studentId = $(this).attr('data-id');
            var studentName = $(this).attr('data-name');
            
            $('#modalStudentName').text(studentName);
            $('#reportsModalBody').html('<div class="text-center my-5"><div class="spinner-border text-success" role="status"><span class="visually-hidden">جاري جلب التقارير...</span></div></div>');
            
            var myModal = new bootstrap.Modal(document.getElementById('reportsModal'));
            myModal.show();
            
            $.ajax({
                url: 'dashboard.php',
                type: 'GET',
                data: { fetch_reports: studentId },
                success: function(response) {
                    $('#reportsModalBody').html(response);
                },
                error: function() {
                    $('#reportsModalBody').html('<div class="alert alert-danger text-center">❌ حدث خطأ غير متوقع أثناء الاتصال بالسيرفر.</div>');
                }
            });
        });
    });
    </script>
</body>
</html>