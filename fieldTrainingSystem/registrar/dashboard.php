<?php
session_start();
include "../config/db.php";

// 1. التحقق من صلاحية الدخول للمسؤول فقط (تأكد من رقم الـ Role الخاص بالإدمن في مشروعك، سنفترض هنا أنه 2)
if (!isset($_SESSION['id']) || $_SESSION['role'] != 3) {
    header("Location: ../auth/login.php");
    exit();
}

$admin_name = $_SESSION['name'];
$message = "";
$msg_class = "";

// 2. معالجة تفعيل أو حظر حسابات المشرفين الأكاديميين (Role = 1)
if (isset($_GET['action']) && isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    $action = $_GET['action'];
    
    // سنقوم بتغيير حقل افتراضي مثل الحالة (status) إذا كان متوفراً بجدولك، أو يمكنك تخصيصه لاحقاً
    // هنا سنفترض تفعيل الحساب أو حظره (كمثال: تحديث حقل مخصص أو إبقاء الإجراء برمجياً)
    if ($action == 'activate') {
        $sql_act = "UPDATE users SET status = 'Active' WHERE id = '$user_id' AND role = 1";
        $msg_text = "✅ تم تفعيل حساب المشرف بنجاح!";
    } else if ($action == 'deactivate') {
        $sql_act = "UPDATE users SET status = 'Pending' WHERE id = '$user_id' AND role = 1";
        $msg_text = "⚠️ تم وضع حساب المشرف في قائمة الانتظار.";
    }
    
    if (isset($sql_act) && $conn->query($sql_act)) {
        $message = $msg_text;
        $msg_class = "alert-success";
    }
}

// 3. جلب الإحصائيات العامة للنظام
$total_students = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 0")->fetch_assoc()['total'];
$total_supervisors = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 1")->fetch_assoc()['total'];
$total_orgs = $conn->query("SELECT COUNT(*) AS total FROM organizations")->fetch_assoc()['total'];

// 4. جلب قائمة المشرفين الأكاديميين المسجلين بالنظام
$supervisors_q = $conn->query("SELECT * FROM users WHERE role = 1 ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم النظام - المسؤول الرئيسي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, sans-serif; }
        .card-stats { border: none; border-radius: 15px; }
        .main-card { border: none; border-radius: 12px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-user-shield text-danger me-2"></i> بوابة المسؤول (Admin)</a>
            <div class="d-flex align-items-center">
                <span class="navbar-text text-white mb-0 me-3 d-none d-md-inline">
                     مرحباً: <strong><?php echo htmlspecialchars($admin_name); ?></strong>
                </span>
                <a href="manage_organizations.php" class="btn btn-danger btn-sm me-2 fw-bold"><i class="fas fa-building"></i> إدارة المؤسسات</a>
                <a href="../auth/logout.php" class="btn btn-light btn-sm text-dark fw-bold"><i class="fas fa-sign-out-alt"></i> خروج</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        
        <div class="mb-4">
            <h2 class="text-dark fw-bold">لوحة التحكم والمراقبة الشاملة</h2>
            <p class="text-muted">نظام إدارة التدريب الميداني - صلاحيات المسؤول الرئيسي عن النظام.</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert <?php echo $msg_class; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-3 mb-5">
            <div class="col-md-4">
                <div class="card card-stats bg-white shadow-sm p-3 border-start border-primary border-5">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted small mb-1">إجمالي الطلاب (Role 0)</h6>
                            <h3 class="fw-bold text-primary mb-0"><?php echo $total_students; ?></h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary"><i class="fas fa-user-graduate fa-2x"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-stats bg-white shadow-sm p-3 border-start border-success border-5">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted small mb-1">المشرفين الأكاديميين (Role 1)</h6>
                            <h3 class="fw-bold text-success mb-0"><?php echo $total_supervisors; ?></h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success"><i class="fas fa-user-tie fa-2x"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-stats bg-white shadow-sm p-3 border-start border-danger border-5">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted small mb-1">المؤسسات والشركات الشريكة</h6>
                            <h3 class="fw-bold text-danger mb-0"><?php echo $total_orgs; ?></h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded-circle text-danger"><i class="fas fa-building fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card main-card shadow-sm bg-white mb-5">
            <div class="card-header bg-secondary text-white p-3">
                <h5 class="mb-0"><i class="fas fa-users-cog me-2"></i> إدارة واعتماد الحسابات للمشرفين الأكاديميين</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">اسم المشرف</th>
                                <th>البريد الإلكتروني</th>
                                <th>رقم الهاتف</th>
                                <th class="text-center">حالة الحساب</th>
                                <th class="text-center">الإجراءات والتحكم</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($supervisors_q && $supervisors_q->num_rows > 0): ?>
                                <?php while ($user = $supervisors_q->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark"><?php echo htmlspecialchars($user['name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['phone'] ? $user['phone'] : 'غير متوفر'); ?></td>
                                        <td class="text-center">
                                            <?php if (isset($user['status']) && $user['status'] == 'Active'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill">نشط ومفعل</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-warning border border-warning px-3 py-2 rounded-pill">بانتظار المراجعة</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex gap-2 justify-content-center">
                                                <a href="dashboard.php?action=activate&user_id=<?php echo $user['id']; ?>" class="btn btn-sm btn-success fw-bold">
                                                    <i class="fas fa-check"></i> تفعيل الحساب
                                                </a>
                                                <a href="dashboard.php?action=deactivate&user_id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-warning fw-bold">
                                                    <i class="fas fa-user-slash"></i> إلغاء التفعيل
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">لا يوجد مشرفين مسجلين بالنظام حالياً.</td>
                                e>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <footer class="text-center py-4 text-muted bg-white border-top mt-auto">
        <p class="small mb-0">نظام إدارة التدريب الميداني &copy; 2026 - مشروع تخرج نظم المعلومات الإدارية (MIS)</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>