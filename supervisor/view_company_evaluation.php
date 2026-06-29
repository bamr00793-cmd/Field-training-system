<?php 
session_start(); // التأكد من بدء الجلسة
include "../config/db.php";

// 1. التحقق من صلاحية الدخول للمشرف الأكاديمي فقط (Role = 1)
if (!isset($_SESSION['id']) || $_SESSION['role'] != 1) {
    header("Location: ../auth/login.php");
    exit();
}

// 2. التأكد من إرسال رقم الطالب المراد عرض تقييمه
if (!isset($_GET['student_id']) || empty($_GET['student_id'])) {
    header("Location: dashboard.php");
    exit();
}

$student_id = intval($_GET['student_id']);

// 3. استعلام دقيق متوافق 100% مع مخطط قاعدة البيانات (ERD) المعتمد لديك
$sql_report = "SELECT 
                    u.name AS student_name, 
                    u.email AS student_email, 
                    u.phone AS student_phone,
                    org.organization_name,
                    ev.final_grade AS org_grade,
                    ev.strengths,
                    ev.weaknesses,
                    ev.recommendation
               FROM users u
               LEFT JOIN training_requests tr ON u.id = tr.student_id AND tr.status = 'Approved'
               LEFT JOIN organizations org ON tr.organization_id = org.id
               LEFT JOIN evaluations ev ON u.id = ev.student_id
               WHERE u.id = '$student_id'";

$result_report = $conn->query($sql_report);

if (!$result_report || $result_report->num_rows == 0) {
    die("<div style='text-align:center; margin-top:50px; font-family:sans-serif;' dir='rtl'><h2>عذراً، لم يتم العثور على بيانات هذا الطالب في النظام.</h2><a href='dashboard.php'>العودة للوحة التحكم</a></div>");
}

$report = $result_report->fetch_assoc();

// 4. معالجة حقل الـ ENUM للتوصية وتحويله إلى نصوص عربية أنيقة تليق بالتقرير الرسمي
$recommendation_text = 'لم يتم تحديد التوصية بعد.';
if (!empty($report['recommendation'])) {
    switch ($report['recommendation']) {
        case 'Excellent': $recommendation_text = 'توصية ممتازة: الطالب مؤهل تماماً لسوق العمل ونوصي بتوظيفه فوراً.'; break;
        case 'Good':      $recommendation_text = 'توصية جيدة: أداء الطالب جيد جداً ويحتاج لبعض الممارسة العملية الإضافية.'; break;
        case 'Average':   $recommendation_text = 'توصية متوسطة: أداء الطالب مقبول وبحاجة لتطوير مهاراته البرمجية بشكل أكبر.'; break;
        case 'Weak':      $recommendation_text = 'توصية ضعيفة: الطالب بحاجة لإعادة التدريب والتركيز على الأساسيات.'; break;
        default:          $recommendation_text = htmlspecialchars($report['recommendation']); break;
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير تقييم التدريب الميداني - <?php echo htmlspecialchars($report['student_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .info-label { font-weight: bold; color: #495057; }
        .eval-box { min-height: 80px; line-height: 1.7; background-color: #fff; }
        @media print {
            .no-print { display: none !important; }
            body { background-color: #fff; font-size: 14px; }
            .card { border: 1px solid #000 !important; box-shadow: none !important; }
            .bg-light { background-color: #f8f9fa !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .text-success { color: #198754 !important; }
        }
    </style>
</head>
<body>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white text-center p-4">
                    <h3 class="fw-bold mb-1"><i class="fas fa-file-signature"></i> تقرير تقييم التدريب الميداني الرسمي</h3>
                    <p class="mb-0 opacity-75">مستند رسمي صادر عن المؤسسة التدريبية وموجه لجامعة الأقصى</p>
                </div>
                
                <div class="card-body p-4">
                    
                    <h5 class="text-primary border-bottom pb-2 mb-3 fw-bold"><i class="fas fa-user-graduate me-1"></i> أولاً: البيانات الأساسية للتدريب</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <p class="mb-2"><span class="info-label">اسم الطالب المتدرب:</span> <?php echo htmlspecialchars($report['student_name']); ?></p>
                            <p class="mb-2"><span class="info-label">البريد الإلكتروني:</span> <?php echo htmlspecialchars($report['student_email']); ?></p>
                            <p class="mb-2"><span class="info-label">رقم جوال التواصل:</span> <?php echo htmlspecialchars($report['student_phone'] ? $report['student_phone'] : 'غير مدخل'); ?></p>
                        </div>
                        <div class="col-md-6 border-start ps-md-4">
                            <p class="mb-2"><span class="info-label">جهة التدريب الميداني:</span> <strong class="text-success"><?php echo htmlspecialchars($report['organization_name'] ? $report['organization_name'] : 'لم تحدد بعد'); ?></strong></p>
                            <p class="mb-2"><span class="info-label">حالة الاعتماد بالنظام:</span> 
                                <?php if ($report['org_grade'] !== null): ?>
                                    <span class="badge bg-success">تم رصد التقييم وإرساله</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">قيد التدريب / لم يرصد بعد</span>
                                <?php endif; ?>
                            </p>
                            <p class="mb-2"><span class="info-label">تاريخ إصدار التقرير:</span> <?php echo htmlspecialchars(date('Y-m-d')); ?></p>
                        </div>
                    </div>

                    <h5 class="text-primary border-bottom pb-2 mb-3 fw-bold"><i class="fas fa-chart-bar me-1"></i> ثانياً: النتيجة الإجمالية للمؤسسة</h5>
                    <div class="p-4 bg-light rounded text-center mb-4 border">
                        <h6 class="text-secondary mb-2">الدرجة الممنوحة من مشرف الموقع بالمؤسسة</h6>
                        <?php if ($report['org_grade'] !== null): ?>
                            <span class="display-4 fw-bold text-success"><?php echo htmlspecialchars(number_format($report['org_grade'], 1)); ?></span>
                            <span class="fs-4 text-muted">/ 100</span>
                        <?php else: ?>
                            <span class="fs-4 fw-bold text-danger"><i class="fas fa-exclamation-triangle"></i> لم تُرصد درجة اختبارية بعد</span>
                        <?php endif; ?>
                    </div>

                    <h5 class="text-primary border-bottom pb-2 mb-3 fw-bold"><i class="fas fa-paste me-1"></i> ثالثاً: التقييم الوصفي وتحليل الأداء</h5>
                    
                    <div class="mb-4">
                        <h6 class="fw-bold text-success"><i class="fas fa-plus-circle me-1"></i> أبرز نقاط القوة:</h6>
                        <div class="p-3 border rounded eval-box bg-light">
                            <?php echo nl2br(htmlspecialchars($report['strengths'] ? $report['strengths'] : 'لم يتم إدخال نقاط قوة بعد.')); ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-danger"><i class="fas fa-minus-circle me-1"></i> أبرز نقاط الضعف / جوانب التطوير:</h6>
                        <div class="p-3 border rounded eval-box bg-light">
                            <?php echo nl2br(htmlspecialchars($report['weaknesses'] ? $report['weaknesses'] : 'لم يتم إدخال نقاط ضعف بعد.')); ?>
                        </div>
                    </div>

                    <h5 class="text-primary border-bottom pb-2 mb-3 fw-bold"><i class="fas fa-comments me-1"></i> رابعاً: التوصيات والملحوظات الختامية للمؤسسة</h5>
                    <div class="p-3 border rounded eval-box mb-4 bg-light">
                        <i class="fas fa-star text-warning me-1"></i> <strong>التقييم العام والتوصية:</strong> <?php echo $recommendation_text; ?>
                    </div>

                    <div class="row mt-5 pt-4 border-top text-center text-secondary">
                        <div class="col-6">
                            <p class="mb-5 fw-bold">ختم وتوقيع المؤسسة التدريبية</p>
                            <p>.......................................</p>
                        </div>
                        <div class="col-6">
                            <p class="mb-5 fw-bold">اعتماد المشرف الأكاديمي بجامعة الأقصى</p>
                            <p>.......................................</p>
                        </div>
                    </div>

                </div>
                
                <div class="card-footer bg-white d-flex justify-content-between no-print p-3">
                    <button onclick="window.print();" class="btn btn-dark px-4"><i class="fas fa-print me-2"></i> طباعة التقرير / حفظ PDF</button>
                    <a href="dashboard.php" class="btn btn-secondary px-4">العودة للوحة التحكم</a>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>