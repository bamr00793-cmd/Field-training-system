<?php
// استدعاء الجلسة والاتصال بقاعدة البيانات
include "../includes/session.php";
include "../config/db.php";

// التحقق من وجود المعرف (ID) في الرابط
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // تحديث حالة الطلب إلى 'Approved'
    $sql = "UPDATE training_requests SET status = 'Approved' WHERE id = '$id'";
    
    if($conn->query($sql)) {
        // إذا تمت العملية بنجاح، أعد المشرف للوحة التحكم
        header("Location: dashboard.php");
        exit();
    } else {
        echo "حدث خطأ: " . $conn->error;
    }
} else {
    echo "معرف الطلب مفقود.";
}
?>