<?php
include "../includes/session.php";
include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

// جلب المؤسسات لعرضها في القائمة المنسدلة
$org_query = "SELECT * FROM organizations";
$org_result = $conn->query($org_query);
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-success text-white"><h4>تقديم طلب تدريب جديد</h4></div>
        <div class="card-body">
            <form action="save_apply.php" method="POST">
                <div class="mb-3">
                    <label>اختر المؤسسة:</label>
                    <select name="organization_id" class="form-control" required>
                        <?php while($row = $org_result->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['organization_name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>تاريخ البدء:</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>تاريخ الانتهاء:</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">إرسال الطلب</button>
            </form>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>