<?php 
include 'db.php';
include 'form.php';

// استعلام جلب فائز عشوائي
$sql = "SELECT * FROM users ORDER BY RAND() LIMIT 1";
$result = mysqli_query($conn, $sql);
$winner = mysqli_fetch_assoc($result);

if($result) {
    mysqli_free_result($result);
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مشروع السحب</title>
    <!-- استدعاء Bootstrap للمودال والتنسيقات الإضافية -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.rtl.min.css">
    
    <style>
        /* إعدادات الخلفية لكامل الصفحة */
        body {
            background-image: url('bg-image.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }


        
        /* تصميم المربع الشفاف */
        .content-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        /* تصميم صورة الجائزة (الآيفون) */
        .prize-image {
            max-width: 180px;
            height: auto;
            margin: 15px auto;
            display: block;
            filter: drop-shadow(0px 8px 15px rgba(0,0,0,0.25));
            transition: transform 0.3s ease;
        }
        
        .prize-image:hover {
            transform: scale(1.05); /* تكبير خفيف عند تمرير الماوس */
        }

        /* تصميم العداد الزمني */
        .countdown {
            font-size: 1.8rem;
            font-weight: bold;
            color: #dc3545;
            margin: 20px 0;
            padding: 15px;
            background-color: #fff;
            border-radius: 8px;
            border: 2px dashed #dc3545;
        }

        /* تنسيق الأخطاء */
        .error-text {
            color: #dc3545;
            font-size: 0.875em;
            text-align: right;
            display: block;
            margin-top: 5px;
        }

        /* إخفاء قسم الفائز افتراضياً */
        .winner-section {
            display: none;
            margin-top: 20px;
        }

/* خلفية التحميل الداكنة التي تغطي الشاشة */
#loader-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.85); /* لون أسود شفاف */
    z-index: 9999;
    display: flex; /* سيتم تغييره لـ flex عبر الجافاسكريبت عند التشغيل */
    justify-content: center;
    align-items: center;
}

/* الدائرة الزرقاء المضيئة */
.circular-progress {
    position: relative;
    height: 180px;
    width: 180px;
    border-radius: 50%;
    background: conic-gradient(#3b82f6 0deg, #222 0deg); /* لون أزرق مطابق للصورة */
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 30px rgba(59, 130, 246, 0.3);
}

/* الدائرة الداخلية لتفريغ المنتصف وإظهار النص */
.circular-progress::before {
    content: "";
    position: absolute;
    height: 150px;
    width: 150px;
    border-radius: 50%;
    background-color: #111; /* لون داكن لقلب الدائرة */
}

/* نص النسبة المئوية الأزرق */
.value-container {
    position: relative;
    font-size: 40px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-weight: bold;
    color: #3b82f6;
}

    </style>
</head>
<body>

    <div class="content-container">
        <h2 class="mb-3 text-primary fw-bold">🎉 اربح آيفون 18 برو ماكس 🎉</h2>
        
        <!-- صورة الجائزة -->
        <img src="iphone.webp" alt="جائزة السحب آيفون" class="prize-image">
        
        <!-- العداد التنازلي -->
        <div id="countdown" class="countdown">جاري تحميل العداد...</div>
        
        <!-- نموذج التسجيل -->
        <div id="registerForm">
            <form action="index.php" method="POST">
                <div class="mb-3">
                    <input type="text" name="firstName" class="form-control" placeholder="الاسم الأول" value="<?php echo htmlspecialchars($firstName ?? ''); ?>">
                    <span class="error-text"><?php echo $errors['firstNameError'] ?? ''; ?></span>
                </div>
                <div class="mb-3">
                    <input type="text" name="lastName" class="form-control" placeholder="اسم العائلة" value="<?php echo htmlspecialchars($lastName ?? ''); ?>">
                    <span class="error-text"><?php echo $errors['lastNameError'] ?? ''; ?></span>
                </div>
                <div class="mb-3">
                    <input type="text" name="email" class="form-control" placeholder="البريد الإلكتروني" value="<?php echo htmlspecialchars($email ?? ''); ?>">
                    <span class="error-text"><?php echo $errors['emailError'] ?? ''; ?></span>
                </div>
                <button type="submit" name="submit" class="btn btn-primary w-100 py-2 fs-5">تسجيل في السحب</button>
            </form>
        </div>

        <!-- زر إعلان الفائز -->
        <div class="winner-section" id="winnerSection">
            <button type="button" class="btn btn-success w-100 py-3 fs-4 fw-bold shadow" id="winnerBtn">
                اختيار الرابح 🏆
            </button>
        </div>
    </div>

    <!-- المودال الخاص بإعلان الفائز -->
    <div class="modal fade" id="winnerModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-5 border-0 shadow-lg">
          <div class="modal-header border-0 pb-0 justify-content-center">
              <h2 class="modal-title w-100">ألف مبروك للفائز! 🎉</h2>
          </div>
          <div class="modal-body pt-4">
             <?php if ($winner): ?>
                <h1 class="text-success fw-bold display-4">
                    <?php echo htmlspecialchars($winner['firstName'] . ' ' . $winner['lastName']); ?>
                </h1>
             <?php else: ?>
                <p class="text-muted">لا يوجد مشاركين في قاعدة البيانات حتى الآن.</p>
             <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- شاشة التحميل الدائرية المئوية -->
<div id="loader-overlay" style="display: none;">
    <div class="circular-progress" id="progress-circle">
        <div class="value-container" id="progress-text">0%</div>
    </div>
</div>

    <!-- مكتبة Confetti للاحتفالية -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.4.0/dist/confetti.browser.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- ملف الجافاسكريبت -->
   <script src="script.js?v=15"></script>

</body>
</html>