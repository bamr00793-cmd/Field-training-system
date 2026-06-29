<?php
session_start();
include "../config/db.php";

if(isset($_POST['login']))
{
    $email = $_POST['email'];
    $password = $_POST['password'];

    // جلب الحساب ومطابقته بأمان
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0)
    {
        $user = $result->fetch_assoc();

        // التحقق من كلمة المرور المشفرة
        if(password_verify($password, $user['password']))
        {
            // تخزين البيانات الأساسية في الـ Session
            $_SESSION['id']   = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // 🚀 التوجيه الفوري والمباشر بناءً على رتب النظام الخاصة بمشروعك
            if ($_SESSION['role'] == 0) {
                // 0 -> طالب متدرب
                header("Location: ../student/dashboard.php");
                exit();
            } 
            else if ($_SESSION['role'] == 1) {
                // 1 -> مشرف أكاديمي
                header("Location: ../supervisor/dashboard.php");
                exit();
            } 
            else if ($_SESSION['role'] == 2) {
                // 2 -> مؤسسة تدريبية
                header("Location: ../company/dashboard.php"); // أو المجلد المخصص للشركات عندك
                exit();
            } 
            else if ($_SESSION['role'] == 3) {
                // 3 -> مسجل الكلية (الذي نقلنا ملفاته لمجلد registrar)
                header("Location: ../registrar/dashboard.php");
                exit();
            }
            
            // في حال وجود رتبة أخرى غير معرفة
            header("Location: ../index.php");
            exit();
        }
        else {
            echo "<script>alert('كلمة المرور التي أدخلتها غير صحيحة');</script>";
        }
    }
    else {
        echo "<script>alert('هذا البريد الإلكتروني غير مسجل لدينا في النظام');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .login-container h2 { text-align: center; color: #333; margin-bottom: 20px; }
        .form-control { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 14px; }
        .btn-login { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; font-weight: bold; }
        .btn-login:hover { background-color: #0069d9; }
    </style>
</head>
<body>

<div class="login-container">
    <h2>تسجيل الدخول للنظام</h2>
    <form method="POST">
        <input type="email" name="email" class="form-control" placeholder="البريد الإلكتروني" required>
        <input type="password" name="password" class="form-control" placeholder="كلمة المرور" required>
        <button type="submit" name="login" class="btn-login">دخول</button>
    </form> 
</div>

</body>
</html>