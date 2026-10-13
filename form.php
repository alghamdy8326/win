<?php
include 'db.php';

$firstName = $_POST['firstName'] ?? '';
$lastName  = $_POST['lastName'] ?? '';
$email     = $_POST['email'] ?? '';

$errors = [
    'firstNameError' => '',
    'lastNameError' => '',
    'emailError' => ''
];

if (isset($_POST['submit'])) {
    
    // التحقق من الحقول
    if (empty($firstName)) {
        $errors['firstNameError'] = 'يرجى إدخال الاسم الأول';
    }
    if (empty($lastName)) {
        $errors['lastNameError'] = 'يرجى إدخال اسم العائلة';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['emailError'] = 'يرجى إدخال بريد إلكتروني صحيح';
    }

    // إذا لم تكن هناك أخطاء، قم بتخزين البيانات
    if (!array_filter($errors)) {
        // حماية البيانات من ثغرات الحقن SQL Injection
        $firstName = mysqli_real_escape_string($conn, $firstName);
        $lastName  = mysqli_real_escape_string($conn, $lastName);
        $email     = mysqli_real_escape_string($conn, $email);

        $sql = "INSERT INTO users (firstName, lastName, email) VALUES ('$firstName', '$lastName', '$email')";
        
        if (mysqli_query($conn, $sql)) {
            // إعادة توجيه لنفس الصفحة لتفريغ الحقول ومنع التكرار عند التحديث
            header('Location: ' . $_SERVER['PHP_SELF']); 
            exit;
        } else {
            echo 'Error: ' . mysqli_error($conn);
        }
    }
}
?>