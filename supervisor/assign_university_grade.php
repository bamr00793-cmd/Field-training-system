<?php
session_start();
include "../config/db.php";

// 1. التحقق من صلاحية الدخول للمشرف الأكاديمي فقط
if (!isset($_SESSION['id']) || $_SESSION['role'] != 1) {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";
$msg_class = "";

if (!isset($_GET['student_id']) || empty($_GET['student_id'])) {
    header("Location: dashboard.php");
    exit();
}

$student_id = intval($_GET['student_id']);

// جلب بيانات الطالب والتقييم الحالي بناءً على معرّف الدور الصحيح (0 للطلاب)
$sql_data = "SELECT u.name AS student_name, ev.final_grade, ev.strengths, ev.weaknesses, ev.recommendation 
             FROM users u 
             LEFT JOIN evaluations ev ON u.id = ev.student_id 
             WHERE u.id = '$student_id' AND u.role = 0";
$result_data = $conn->query($sql_data);

if (!$result_data || $result_data->num_rows == 0) {
    die("<div style='text-align:center; margin-top:50px;' dir='rtl'><h2>الطالب غير مسجل بالنظام!</h2></div>");
}

$data = $result_data->fetch_assoc();

// 2. معالجة التحديث أو الإدخال عند إرسال النموذج
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_grade'])) {
    $final_grade = floatval($_POST['final_grade']);
    $recommendation = $conn->real_escape_string($_POST['recommendation']);
    $strengths = $conn->real_escape_string($_POST['strengths']);
    $weaknesses = $conn->real_escape_string($_POST['weaknesses']);

    $check = $conn->query("SELECT id FROM evaluations WHERE student_id = '$student_id'");
    
    if ($check && $check->num_rows > 0) {
        // تحديث البيانات الحالية
        $sql_save = "UPDATE evaluations 
                     SET final_grade = '$final_grade', recommendation = '$recommendation', strengths = '$strengths', weaknesses = '$weaknesses'
                     WHERE student_id = '$student_id'";
    } else {
        // إدخال سجل تقييم جديد بالكامل إذا لم تقم المؤسسة برصده بعد
        $sql_save = "INSERT INTO evaluations (student_id, strengths, weaknesses, recommendation, final_grade) 
                     VALUES ('$student_id', '$strengths', '$weaknesses', '$recommendation', '$final_grade')";
    }

    if ($conn->query($sql_save)) {
        $message = "✅ تم رصد واعتماد الدرجة والملحوظات بنجاح في قاعدة البيانات!";
        $msg_class = "alert-success";
        
        // تحديث مصفوفة العرض فوراً بالبيانات الجديدة
        $data['final_grade'] = $final_grade;
        $data['recommendation'] = $recommendation;
        $data['strengths'] = $strengths;
        $data['weaknesses'] = $weaknesses;
    } else {
        $message = "❌ حدث خطأ غير متوقع أثناء الحفظ: " . $conn->error;
        $msg_class = "alert-danger";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة المشرف - رصد الدرجة النهائية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, sans-serif; }
        .card-custom { border: none; border-radius: 12px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php"><i class="fas fa-graduation-cap text-warning me-2"></i> رصد درجات الجامعة</a>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm"><i class="fas fa-arrow-right me-1"></i> العودة للوحة المشرف</a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                
                <?php if (!empty($message)): ?>
                    <div class="alert <?php echo $msg_class; ?> alert-dismissible fade show shadow-sm" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card card-custom shadow-sm bg-white p-4">
                    <h4 class="fw-bold text-dark mb-3 border-bottom pb-2"><i class="fas fa-user-check text-warning me-2"></i> رصد واعتماد درجة التدريب</h4>
                    
                    <div class="alert alert-primary py-2 mb-4">
                        اسم الطالب الحالي: <strong><?php echo htmlspecialchars($data['student_name']); ?></strong>
                    </div>

                    <form action="" method="POST">
                        
                        <div class="mb-3">
                            <label for="final_grade" class="form-label fw-bold">الدرجة المعتمدة للطالب (من 100):</label>
                            <input type="number" step="0.01" min="0" max="100" id="final_grade" name="final_grade" class="form-control form-control-lg text-center fw-bold text-success" 
                                   value="<?php echo !is_null($data['final_grade']) ? $data['final_grade'] : ''; ?>" placeholder="أدخل القيمة الرقمية للدرجة" required>
                        </div>

                        <div class="mb-3">
                            <label for="recommendation" class="form-label fw-bold">التوصية والتقدير العام (Recommendation):</label>
                            <select id="recommendation" name="recommendation" class="form-select" required>
                                <option value="">-- اختر التقدير المناسب للطالب --</option>
                                <option value="Excellent" <?php echo ($data['recommendation'] == 'Excellent') ? 'selected' : ''; ?>>Excellent (ممتاز)</option>
                                <option value="Good" <?php echo ($data['recommendation'] == 'Good') ? 'selected' : ''; ?>>Good (جيد جداً)</option>
                                <option value="Average" <?php echo ($data['recommendation'] == 'Average') ? 'selected' : ''; ?>>Average (متوسط)</option>
                                <option value="Weak" <?php echo ($data['recommendation'] == 'Weak') ? 'selected' : ''; ?>>Weak (ضعيف)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="strengths" class="form-label fw-bold">أبرز نقاط قوة الطالب الملاحظة:</label>
                            <textarea id="strengths" name="strengths" class="form-control" rows="3" required><?php echo htmlspecialchars($data['strengths'] ?? ''); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="weaknesses" class="form-label fw-bold">نقاط الضعف والتوصيات البرمجية الموجهة للطالب:</label>
                            <textarea id="weaknesses" name="weaknesses" class="form-control" rows="3" required><?php echo htmlspecialchars($data['weaknesses'] ?? ''); ?></textarea>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" name="update_grade" class="btn btn-warning btn-lg text-dark fw-bold shadow-sm">
                                <i class="fas fa-save me-1"></i> حفظ واعتماد الدرجة النهائية بالنظام
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</body>
</html>