<?php
// الاتصال بقاعدة البيانات محلياً
$conn = mysqli_connect('localhost', 'root', '', 'win');

// التأكد من نجاح الاتصال
if(!$conn){
    echo 'خطأ في الاتصال بقاعدة البيانات: ' . mysqli_connect_error();
}
?>