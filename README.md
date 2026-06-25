[ريدمي.md](https://github.com/user-attachments/files/29359416/default.md)
 نظام إدارة التدريب الميداني الجامعي | University Field Training Management System

نظام ويب متكامل مصمم خصيصاً لإدارة، متابعة، وتقييم مساقات التدريب الميداني لطلاب الجامعات. تم تطوير هذا النظام كشروع تخرج لنظم المعلومات الإدارية (MIS)، ويستهدف أتمتة العمليات الإدارية والأكاديمية بين أربعة أطراف أساسية: إدارة الكلية (المسجّل)، المشرف الأكاديمي، المؤسسة التدريبية، والطالب المتدرب.

*Bootstrap 5** مع دعم كامل للمحاذاة من اليمين لليسار (RTL) لتجربة مستخدم مريحة.
* **إدارة المرفقات:
* ----

## 🛠️ التقنيات المستخدمة (Tech Stack)
* **Back-End:** PHP (Object-Oriented & Procedural blend)
* **Database:** MySQL / MariaDB
* **Front-End:** HTML5, CSS3, JavaScript (Vanilla JS)
* **Frameworks:** Bootstrap 5 (RTL)
* **Icons:** Font Awesome 6.4.0

---

## 📂 الهيكل التنظيمي للمجلدات (Project Architecture)

README generation block executed successfully.

```text
fieldTrainingSystem/
│
├── auth/                  # صفحات التحكم بالدخول (Login, Register)
├── config/                # ملفات الاتصال بقاعدة البيانات (db.php)
├── includes/              # الملفات المشتركة (header.php, sidebar.php, footer.php)
├── registrar/             # لوحة تحكم مسجل الكلية / Admin
├── student/               # لوحة تحكم وصلاحيات الطالب المتدرب
├── supervisor/            # لوحة تحكم المشرف الأكاديمي
├── company/               # لوحة تحكم المؤسسة التدريبية
├── uploads/               # المجلد المخصص لحفظ التقارير والمرفقات المرفوعة
└── index.php              # نقطة الانطلاق والتوجيه المركزي للمستخدمين
