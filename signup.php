<?php
session_start();
include_once('includes/config.php');
require 'vendor/autoload.php'; // PHPMailer Autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($con, trim($_POST['fullname'] ?? ''));
    $email = mysqli_real_escape_string($con, trim($_POST['emailid'] ?? ''));
    $contactno = mysqli_real_escape_string($con, trim($_POST['contactnumber'] ?? ''));
    $passwordInput = trim($_POST['inputuserpwd'] ?? '');

    if (!empty($name) && !empty($email) && !empty($contactno) && !empty($passwordInput)) {
        $password = password_hash($passwordInput, PASSWORD_DEFAULT);

        $checkEmail = mysqli_query($con, "SELECT id FROM users WHERE email='$email'");
        if (mysqli_num_rows($checkEmail) == 0) {
            $query = mysqli_query($con, "INSERT INTO users(name, email, contactno, password) VALUES('$name', '$email', '$contactno', '$password')");

            if ($query) {
                $mail = new PHPMailer(true);
                try {
                    // SMTP configuration (COMPANY EMAIL)
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'manish200e3@gmail.com'; // Your company Gmail
                    $mail->Password = 'qsbtxdbhqqpzrtek'; // App password from Google
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('manish200e3@gmail.com', 'AnkleGaming Support'); // Sender
                    $mail->addAddress($email, $name); // Recipient

                    $mail->isHTML(true);
                    $mail->Subject = "🎉 Registration Successful - Welcome to AnkleGaming!";
                    $mail->Body = "
                        <h3>Hi $name,</h3>
                        <p>Thank you for registering with AnkleGaming.</p>
                        <p>We are thrilled to have you onboard!</p>
                        <br>
                        <p><strong>Happy Shopping!</strong></p>
                        <p>Team AnkleGaming</p>
                    ";
                    $mail->AltBody = "Hi $name,\n\nThank you for registering with AnkleGaming.\nWe're excited to have you onboard!\n\n- Team AnkleGaming";

                    $mail->send();
                    echo "Mail successfully sent!"; // Debug confirmation
                    echo "<script>alert('Registration successful! A confirmation email has been sent.');</script>";
                    echo "<script>window.location.href='login.php';</script>";
                } catch (Exception $e) {
                    echo "<script>alert('Registration successful, but email could not be sent.');</script>";
                    echo "Mailer Error: " . $mail->ErrorInfo;
                    echo "<script>window.location.href='login.php';</script>";
                }
            } else {
                echo "<script>alert('Registration failed. Please try again later.');</script>";
                echo "<script>window.location.href='signup.php';</script>";
            }
        } else {
            echo "<script>alert('This email is already registered. Please use another email.');</script>";
            echo "<script>window.location.href='signup.php';</script>";
        }
    } else {
        echo "<script>alert('Please fill all fields.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Shopping | User Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="js/jquery.min.js"></script>
    <script>
    function emailAvailability() {
        $("#loaderIcon").show();
        jQuery.ajax({
            url: "check_availability.php",
            data: 'email=' + $("#emailid").val(),
            type: "POST",
            success: function(data) {
                $("#user-email-status").html(data);
                $("#loaderIcon").hide();
            },
            error: function() {}
        });
    }
    </script>
</head>
<body>
<?php include_once('includes/header.php'); ?>

<header class="bg-dark py-5">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder">User Signup</h1>
            <p class="lead fw-normal text-white-50 mb-0">One Time Registration is Required for Shopping</p>
        </div>
    </div>
</header>

<section class="py-5">
    <div class="container px-4 mt-5">
        <form method="post" name="signup">
            <div class="row mb-3">
                <div class="col-2">Full Name</div>
                <div class="col-6">
                    <input type="text" name="fullname" class="form-control" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-2">Email ID</div>
                <div class="col-6">
                    <input type="email" name="emailid" id="emailid" class="form-control" onBlur="emailAvailability()" required>
                    <span id="user-email-status" style="font-size:12px;"></span>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-2">Contact Number</div>
                <div class="col-6">
                    <input type="text" name="contactnumber" pattern="[0-9]{10}" title="10 numeric characters only" class="form-control" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-2">Password</div>
                <div class="col-6">
                    <input type="password" name="inputuserpwd" class="form-control" required>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-2">&nbsp;</div>
                <div class="col-6">
                    <input type="submit" name="submit" class="btn btn-primary" value="Register">
                </div>
            </div>
        </form>
    </div>
</section>

<?php include_once('includes/footer.php'); ?>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/scripts.js"></script>
</body>
</html>
