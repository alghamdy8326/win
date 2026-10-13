// تحديد تاريخ ووقت انتهاء السحب الحقيقي 
// (هنا حددنا تاريخ 30 سبتمبر 2026 الساعة 23:59:59 كمساء)
var countDownDate = new Date("Sep 1, 2026 23:59:59").getTime(); 

var x = setInterval(function() {
  var now = new Date().getTime();
  var distance = countDownDate - now;

  if (distance > 0) {
      // حساب الأيام والساعات والدقائق والثواني
      var days = Math.floor(distance / (1000 * 60 * 60 * 24));
      var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      var seconds = Math.floor((distance % (1000 * 60)) / 1000);

      // عرض النتيجة
      document.getElementById("countdown").innerHTML = days + " يوم " + hours + " ساعة "
      + minutes + " دقيقة " + seconds + " ثانية ";
  } else {
      // عند انتهاء الوقت
      clearInterval(x);
      document.getElementById("countdown").innerHTML = "انتهى وقت التسجيل!";
      document.getElementById("registerForm").style.display = "none";
      document.getElementById("winnerSection").style.display = "block";
  }
}, 1000);

// --- ضع كود زر اختيار الفائز وشاشة التحميل الدائرية هنا أسفل العداد ---

// تفعيل شاشة التحميل المئوية ثم إعلان الفائز
var winnerBtn = document.getElementById('winnerBtn');
var loaderOverlay = document.getElementById('loader-overlay');
var progressCircle = document.getElementById('progress-circle');
var progressText = document.getElementById('progress-text');

if(winnerBtn) {
    winnerBtn.addEventListener('click', function() {
        // إظهار شاشة التحميل الداكنة
        loaderOverlay.style.display = 'flex';
        
        let progressValue = 0;
        let progressEndValue = 100;
        let speed = 35; // سرعة العداد (كلما قل الرقم، كان أسرع)

        let progress = setInterval(() => {
            progressValue++;
            
            // تحديث الرقم الظاهر في الشاشة
            progressText.textContent = `${progressValue}%`;
            
            // تحديث رسم الدائرة الزرقاء (3.6 درجة لكل 1%)
            progressCircle.style.background = `conic-gradient(
                #3b82f6 ${progressValue * 3.6}deg, 
                #222 ${progressValue * 3.6}deg
            )`;

            // عند الوصول لـ 100%
            if (progressValue == progressEndValue) {
                clearInterval(progress); // إيقاف العداد
                
                // انتظار نصف ثانية ثم إخفاء التحميل وإظهار الفائز
                setTimeout(() => {
                    loaderOverlay.style.display = 'none';
                    
                    // إظهار نافذة الفائز (المودال)
                    var myModal = new bootstrap.Modal(document.getElementById('winnerModal'), {
                        keyboard: false
                    });
                    myModal.show();

                    // تشغيل احتفالية الأوراق المتناثرة (Confetti)
                    var duration = 5 * 1000;
                    var animationEnd = Date.now() + duration;
                    var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 1060 };

                    function randomInRange(min, max) {
                      return Math.random() * (max - min) + min;
                    }

                    var interval = setInterval(function() {
                      var timeLeft = animationEnd - Date.now();

                      if (timeLeft <= 0) {
                        return clearInterval(interval);
                      }

                      var particleCount = 50 * (timeLeft / duration);
                      confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
                      confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
                    }, 250);

                }, 500); 
            }
        }, speed);
    });
}