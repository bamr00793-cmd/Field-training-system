<?php
// استدعاء ملف الاتصال بقاعدة البيانات
include "../config/db.php";

if(isset($_POST['register'])) {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // تشفير آمن لكلمة المرور
    $phone    = $_POST['phone'];
    $role     = $_POST['role'];

    // فحص مسبق لمنع تكرار البريد الإلكتروني
    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $res_email = $check_email->get_result();

    if($res_email->num_rows > 0) {
        echo "<script>alert('البريد الإلكتروني مسجل مسبقاً في النظام!');</script>";
    } else {
        // استخدام الاستعلامات المُجهزة لحماية البيانات من ثغرات الـ SQL Injection
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $name, $email, $password, $phone, $role);

        if($stmt->execute()) {
            echo "<script>alert('تم تسجيل الحساب بنجاح في النظام!'); window.location.href='login.php';</script>";
            exit();
        } else {
            echo "<div style='color:red; text-align:center; margin-top:10px;'>خطأ في التسجيل: " . $conn->error . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إنشاء حساب جديد</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .reg-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .reg-container h3 { text-align: center; color: #333; margin-bottom: 20px; }
        .form-control { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 14px; }
        .btn-submit { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; font-weight: bold; }
        .btn-submit:hover { background-color: #218838; }
        label { font-weight: bold; display: block; margin-bottom: 5px; color: #555; }
    </style>
</head>
<body>

<div class="reg-container">
    <h3>إنشاء حساب جديد في النظام</h3>
    <form method="POST">
        <input type="text" name="name" class="form-control" placeholder="الاسم الرباعي الكامل" required>
        <input type="email" name="email" class="form-control" placeholder="البريد الإلكتروني" required>
        <input type="text" name="phone" class="form-control" placeholder="رقم الجوال">
        <input type="password" name="password" class="form-control" placeholder="كلمة المرور" required>
        
        <label>نوع الحساب (الدور برتبة النظام):</label>
        <select name="role" class="form-control">
            <option value="0">طالب متدرب (Student)</option>
            <option value="1">مشرف أكاديمي (Supervisor)</option>
            <option value="2">مؤسسة تدريبية (Organization)</option>
            <option value="3">مسجل الكلية (Registrar)</option>
        </select>
        
        <button type="submit" name="register" class="btn-submit">تأكيد تسجيل الحساب</button>
    </form>
</div>

</body>
</html>