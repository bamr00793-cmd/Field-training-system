<?php 
include "../includes/session.php"; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'supervisor') {
    header("Location: ../login.php");
    exit();
}

include "../includes/header.php"; 
include "../includes/sidebar.php"; 
include "../config/db.php"; 

$student_id = $_GET['student_id'];

// جلب اسم الطالب للتوثيق
$student_stmt = $conn->prepare("SELECT name FROM users WHERE id = ? LIMIT 1");
$student_stmt->bind_param("i", $student_id);
$student_stmt->execute();
$student_name = $student_stmt->get_result()->fetch_assoc()['name'] ?? "غير معروف";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $final_grade = $_POST['final_grade'];

    // تحديث حقل الدرجة النهائية decimal(5,2) في جدول evaluations الخاص بك
    $stmt = $conn->prepare("UPDATE evaluations SET final_grade = ? WHERE student_id = ?");
    $stmt->bind_param("di", $final_grade, $student_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('تم رصد وتثبيت الدرجة النهائية بنجاح ($final_grade)'); window.location.href='dashboard.php';</script>";
        exit();
    } else {
        echo "<div class='alert alert-danger'>حدث خطأ أثناء رصد الدرجة: " . $conn->error . "</div>";
    }
}
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h4><i class="fas fa-graduation-cap"></i> رصد الدرجة الأكاديمية النهائية للمساق</h4>
        </div>
        <div class="card-body">
            <h5 class="mb-4">إدخال علامة الطالب: <span class="text-danger"><strong><?php echo $student_name; ?></strong></span></h5>
            
            <form action="" method="POST">
                <div class="mb-4">
                    <label class="form-label font-weight-bold">الدرجة النهائية المستحقة للمساق (من 100.00):</label>
                    <input type="number" name="final_grade" class="form-control" min="0" max="100" step="0.01" placeholder="مثال: 92.50" required>
                    <small class="text-muted">ملاحظة: سيتم تخزين هذه الدرجة مباشرة في خانة final_grade بجدول التقييمات الخاص بك.</small>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> اعتماد وحفظ الدرجة النهائية</button>
                <a href="dashboard.php" class="btn btn-secondary">إلغاء</a>
            </form>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>