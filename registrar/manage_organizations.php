<?php
session_start();
include "../config/db.php";

// 1. التحقق من صلاحية الدخول للمسؤول
if (!isset($_SESSION['id']) || $_SESSION['role'] != 3) {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";
$msg_class = "";

// 2. معالجة إضافة مؤسسة تدريبية جديدة عند إرسال الفورم
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_org'])) {
    $org_name = $conn->real_escape_string($_POST['organization_name']);
    $org_email = $conn->real_escape_string($_POST['org_email']);
    $org_phone = $conn->real_escape_string($_POST['org_phone']);
    $org_address = $conn->real_escape_string($_POST['org_address']);

    if (!empty($org_name)) {
        // الاستعلام متوافق مع بنية حقول جدول الـ organizations الخاص بمشروعك
        $sql_add = "INSERT INTO organizations (organization_name, email, phone, address) 
                    VALUES ('$org_name', '$org_email', '$org_phone', '$org_address')";
        
        if ($conn->query($sql_add)) {
            $message = "✅ تم إضافة المؤسسة التدريبية الجديدة بنجاح وإدراجها بالنظام!";
            $msg_class = "alert-success";
        } else {
            $message = "❌ حدث خطأ أثناء الإضافة: " . $conn->error;
            $msg_class = "alert-danger";
        }
    } else {
        $message = "⚠️ يرجى كتابة اسم المؤسسة أولاً.";
        $msg_class = "alert-warning";
    }
}

// 3. جلب قائمة الشركات الحالية لعرضها في الجدول
$orgs_q = $conn->query("SELECT * FROM organizations ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المؤسسات الشريكة - لوحة المسؤول</title>
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
            <a class="navbar-brand" href="dashboard.php"><i class="fas fa-user-shield text-danger me-2"></i> إدارة المؤسسات والشركات</a>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm fw-bold"><i class="fas fa-arrow-right"></i> العودة للوحة الرئيسية</a>
        </div>
    </nav>

    <div class="container my-5">
        
        <?php if (!empty($message)): ?>
            <div class="alert <?php echo $msg_class; ?> alert-dismissible fade show shadow-sm" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            
            <div class="col-lg-4">
                <div class="card card-custom shadow-sm p-4 bg-white">
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-plus-circle text-danger me-1"></i> إضافة مؤسسة جديدة</h5>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">اسم المؤسسة/الشركة:</label>
                            <input type="text" name="organization_name" class="form-control" placeholder="مثال: شركة حضارة، تكنوزون" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">البريد الإلكتروني للمؤسسة:</label>
                            <input type="email" name="org_email" class="form-control" placeholder="org@company.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">رقم هاتف التواصل:</label>
                            <input type="text" name="org_phone" class="form-control" placeholder="059XXXXXXX">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">عنوان ومقر الشركة:</label>
                            <input type="text" name="org_address" class="form-control" placeholder="غزة - الرمال">
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="add_org" class="btn btn-danger fw-bold shadow-sm">
                                <i class="fas fa-save me-1"></i> حفظ وإدراج المؤسسة
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card card-custom shadow-sm bg-white">
                    <div class="card-header bg-secondary text-white p-3">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-city me-2"></i> دليل جهات ومؤسسات التدريب الحالية</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">رقم المعرّف</th>
                                        <th>اسم الجهة التدريبية</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>الهاتف</th>
                                        <th>العنوان والمقر</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($orgs_q && $orgs_q->num_rows > 0): ?>
                                        <?php while ($org = $orgs_q->fetch_assoc()): ?>
                                            <tr>
                                                <td class="ps-4 text-secondary fw-bold"># <?php echo $org['id']; ?></td>
                                                <td class="fw-bold text-dark"><?php echo htmlspecialchars($org['organization_name']); ?></td>
                                                <td><?php echo htmlspecialchars($org['email'] ? $org['email'] : 'غير مدخل'); ?></td>
                                                <td><?php echo htmlspecialchars($org['phone'] ? $org['phone'] : 'غير متوفر'); ?></td>
                                                <td><span class="badge bg-light text-dark border p-2"><?php echo htmlspecialchars($org['address'] ? $org['address'] : 'غير محدد'); ?></span></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">لا توجد أي جهات تدريبية مضافة حتى الآن في قاعدة البيانات.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</body>
</html>