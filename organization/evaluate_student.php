<?php
session_start();
include "../config/db.php";

// 1. التحقق من صلاحية الدخول للمؤسسات التدريبية فقط
if (!isset($_SESSION['id']) || $_SESSION['role'] != 2) {
    header("Location: ../auth/login.php");
    exit();
}

// الحصول على اسم المؤسسة من الجلسة لجلب الـ id الخاص بها
$org_name = $_SESSION['name'];
$organization_id = 0;
$org_check = $conn->query("SELECT id FROM organizations WHERE organization_name = '$org_name'");

if ($org_check && $org_check->num_rows > 0) {
    $org_data = $org_check->fetch_assoc();
    $organization_id = $org_data['id'];
} else {
    $organization_id = $_SESSION['id'];
}

// 2. التأكد من إرسال رقم الطالب المراد تقييمه عبر الرابط
if (!isset($_GET['student_id']) || empty($_GET['student_id'])) {
    header("Location: dashboard.php");
    exit();
}

$student_id = intval($_GET['student_id']);

// 3. جلب بيانات الطالب الحالية للتأكد من وجوده وأنه تابع لهذه المؤسسة ومقبول
$sql_student = "SELECT u.id, u.name, u.email 
                FROM training_requests tr
                JOIN users u ON tr.student_id = u.id
                WHERE tr.student_id = '$student_id' AND tr.organization_id = '$organization_id' AND tr.status = 'Approved'";
$result_student = $conn->query($sql_student);

if (!$result_student || $result_student->num_rows == 0) {
    header("Location: dashboard.php");
    exit();
}

$student = $result_student->fetch_assoc();

// 4. معالجة إرسال النموذج وحفظ التقييم في قاعدة البيانات
$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // استقبال درجات المعايير الأربعة
    $attendance  = isset($_POST['attendance']) ? intval($_POST['attendance']) : 0;
    $performance = isset($_POST['performance']) ? intval($_POST['performance']) : 0;
    $behavior    = isset($_POST['behavior']) ? intval($_POST['behavior']) : 0;
    $initiative  = isset($_POST['initiative']) ? intval($_POST['initiative']) : 0;
    
    // حساب المجموع النهائي تلقائياً من 100
    $final_grade = $attendance + $performance + $behavior + $initiative;
    
    // استقبال الحقول الجديدة وتأمينها لتتوافق 100% مع جدول evaluations
    $strengths = $conn->real_escape_string(trim($_POST['strengths']));
    $weaknesses = $conn->real_escape_string(trim($_POST['weaknesses']));
    $recommendation = $conn->real_escape_string(trim($_POST['recommendation']));

    if (empty($recommendation) || empty($strengths) || empty($weaknesses)) {
        $error_message = "يرجى تعبئة جميع الحقول المطلوبة (نقاط القوة، نقاط الضعف، والتوصيات).";
    } else {
        // فحص ما إذا كان هناك تقييم سابق مخزن لتجنب التكرار
        $check_existing = $conn->query("SELECT id FROM evaluations WHERE student_id = '$student_id'");
        
        if ($check_existing && $check_existing->num_rows > 0) {
            // تحديث التقييم الحالي بناءً على الحقول الستة الجديدة
            $sql_save = "UPDATE evaluations 
                         SET strengths = '$strengths', weaknesses = '$weaknesses', recommendation = '$recommendation', final_grade = '$final_grade' 
                         WHERE student_id = '$student_id'";
        } else {
            // إدخال تقييم جديد بالكامل متوافق مع الحقول الستة في قاعدة البيانات
            $sql_save = "INSERT INTO evaluations (student_id, strengths, weaknesses, recommendation, final_grade) 
                         VALUES ('$student_id', '$strengths', '$weaknesses', '$recommendation', '$final_grade')";
        }

        if ($conn->query($sql_save)) {
            header("Location: dashboard.php?msg=success");
            exit();
        } else {
            $error_message = "حدث خطأ أثناء حفظ التقييم في قاعدة البيانات: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نموذج تقييم الطالب</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border: none; border-radius: 12px; }
        .form-label { font-weight: 600; color: #495057; }
        .score-badge { font-size: 1.2rem; font-weight: bold; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php"><i class="fas fa-building text-info me-2"></i> بوابة المؤسسات التدريبية</a>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm"><i class="fas fa-arrow-right"></i> العودة للوحة التحكم</a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card p-4 bg-white shadow-sm mb-4 border-start border-success border-4">
                    <h5 class="text-muted mb-3"><i class="fas fa-user-graduate me-2"></i> نموذج تقييم الطالب الميداني</h5>
                    <h3 class="text-dark fw-bold mb-1"><?php echo htmlspecialchars($student['name']); ?></h3>
                    <p class="text-secondary mb-0"><i class="fas fa-envelope me-1"></i> البريد الإلكتروني: <?php echo htmlspecialchars($student['email']); ?></p>
                </div>

                <form action="evaluate_student.php?student_id=<?php echo $student_id; ?>" method="POST" class="needs-validation" novalidate>
                    <div class="card shadow-sm bg-white p-4 mb-4">
                        <h4 class="text-secondary fw-bold mb-4 border-bottom pb-2"><i class="fas fa-star me-2"></i> معايير التقييم والدرجات</h4>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="attendance" class="form-label">1. الالتزام بالدوام والمواعيد (من 20 درجة)</label>
                                <input type="number" class="form-control score-input" id="attendance" name="attendance" min="0" max="20" placeholder="أدخل درجة من 0 إلى 20" required>
                                <div class="invalid-feedback">يرجى إدخال درجة صحيحة بين 0 و 20.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="performance" class="form-label">2. جودة وكفاءة تنفيذ المهام (من 30 درجة)</label>
                                <input type="number" class="form-control score-input" id="performance" name="performance" min="0" max="30" placeholder="أدخل درجة من 0 إلى 30" required>
                                <div class="invalid-feedback">يرجى إدخال درجة صحيحة بين 0 و 30.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="behavior" class="form-label">3. السلوك والتعاون مع الفريق (من 20 درجة)</label>
                                <input type="number" class="form-control score-input" id="behavior" name="behavior" min="0" max="20" placeholder="أدخل درجة من 0 إلى 20" required>
                                <div class="invalid-feedback">يرجى إدخال درجة صحيحة بين 0 و 20.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="initiative" class="form-label">4. روح المبادرة والرغبة بالتعلم (من 30 درجة)</label>
                                <input type="number" class="form-control score-input" id="initiative" name="initiative" min="0" max="30" placeholder="أدخل درجة من 0 إلى 30" required>
                                <div class="invalid-feedback">يرجى إدخال درجة صحيحة بين 0 و 30.</div>
                            </div>
                        </div>

                        <div class="row mt-4 pt-3 bg-light rounded p-3 align-items-center">
                            <div class="col-7">
                                <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-calculator me-2"></i> المجموع الكلي المستحق للطالب:</h5>
                            </div>
                            <div class="col-5 text-end">
                                <span class="badge bg-primary p-2 px-4 score-badge" id="total_display">0 / 100</span>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm bg-white p-4 mb-4">
                        <h4 class="text-secondary fw-bold mb-3"><i class="fas fa-analysis me-2"></i> التقييم الوصفي للطالب</h4>
                        
                        <div class="mb-3">
                            <label for="strengths" class="form-label">أبرز نقاط القوة التي تميز بها الطالب أثناء التدريب:</label>
                            <textarea class="form-control" id="strengths" name="strengths" rows="3" placeholder="مثال: سرعة التعلم، الالتزام بالأدوات البرمجية، دقة تحليل النظم..." required></textarea>
                            <div class="invalid-feedback">يرجى كتابة نقاط القوة للمناقشة.</div>
                        </div>

                        <div class="mb-3">
                            <label for="weaknesses" class="form-label">أبرز نقاط الضعف أو الجوانب التي تحتاج إلى تطوير:</label>
                            <textarea class="form-control" id="weaknesses" name="weaknesses" rows="3" placeholder="مثال: بحاجة لزيادة الخبرة في التعامل مع قواعد البيانات المعقدة..." required></textarea>
                            <div class="invalid-feedback">يرجى كتابة نقاط الضعف للمناقشة.</div>
                        </div>

                        <div class="mb-3">
                            <label for="recommendation" class="form-label">التوصيات والملحوظات الختامية:</label>
                            <textarea class="form-control" id="recommendation" name="recommendation" rows="4" placeholder="أكتب ملاحظاتك وتوصياتك الشاملة هنا ليطلع عليها المشرف الأكاديمي..." required></textarea>
                            <div class="invalid-feedback">يرجى كتابة التوصيات والملاحظات الختامية.</div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-5">
                        <a href="dashboard.php" class="btn btn-light px-4 me-md-2">إلغاء وتراجع</a>
                        <button type="submit" class="btn btn-success px-5 shadow"><i class="fas fa-cloud-upload-alt me-2"></i> اعتماد وإرسال التقييم نهائياً</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <footer class="text-center py-4 text-muted bg-white border-top mt-auto">
        <p class="small mb-0">نظام إدارة التدريب الميداني &copy; 2026 - مشروع تخرج نظم المعلومات الإدارية (MIS)</p>
    </footer>

    <script>
        const inputs = document.querySelectorAll('.score-input');
        const totalDisplay = document.getElementById('total_display');

        inputs.forEach(input => {
            input.addEventListener('input', () => {
                let total = 0;
                inputs.forEach(i => {
                    let val = parseInt(i.value);
                    if (!isNaN(val)) {
                        total += val;
                    }
                });
                totalDisplay.innerText = total + " / 100";
            });
        });

        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>