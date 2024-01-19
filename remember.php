<?php
include "./database/Class-Database.php";
global $conn;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./assets/img/LOGO_EAUT.png" type="image/x-icon" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="./assets/css/main.css">
    <link rel="stylesheet" href="./assets/css/responsive.css">
    <title>Remember Pass</title>
</head>

<body>

    <section class=" d-flex justify-content-center text-center align-items-center">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 image-background">
            <img src="./assets/img/truong.webp" alt="">
        </div>
        <div class="form-login p-4 d-flex flex-column align-items-center justify-content-between">
            <div class="logo-eaut">
                <img src="./assets/img/LOGO_EAUT.png" alt="" class="w-50">
            </div>
            <?php
            $loi = "";
            $thongbao = "";

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $tendangnhap = $_POST["tendangnhap"];
                $email = $_POST["email"];

                if (empty($tendangnhap) || empty($email)) {
                    $loi = "Vui lòng nhập đầy đủ thông tin.";
                } else {
                    // Kiểm tra sự tồn tại của tên đăng nhập và email trong cơ sở dữ liệu
                    $sql = mysqli_query($conn, "SELECT * FROM users WHERE username = '$tendangnhap' AND email = '$email'");
                    $row = mysqli_fetch_assoc($sql);

                    if ($row) {
                        // Tạo mật khẩu mới
                        $newPassword = generateRandomPassword(); // Hàm này sẽ tạo một mật khẩu ngẫu nhiên, bạn có thể thay đổi cách tạo.
            
                        // Mã hóa mật khẩu mới và cập nhật vào cơ sở dữ liệu
                        $hashedPassword = md5($newPassword);
                        $updatePasswordSql = "UPDATE users SET password = '$hashedPassword' WHERE username = " . $row['username'];

                        if (mysqli_query($conn, $updatePasswordSql)) {
                            // Hiển thị mật khẩu mới cho người dùng
                            $thongbao = "Mật khẩu mới của bạn là: " . $newPassword;
                        } else {
                            $loi = "Có lỗi xảy ra khi cập nhật mật khẩu mới.";
                        }
                    } else {
                        $loi = "Tên đăng nhập hoặc email không đúng.";
                    }
                }
            }

            // Hàm tạo mật khẩu ngẫu nhiên
            function generateRandomPassword($length = 10)
            {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $password = '';
                for ($i = 0; $i < $length; $i++) {
                    $password .= $characters[rand(0, strlen($characters) - 1)];
                }
                return $password;
            }
            ?>


            <form id="form-forgotPassword" action="" method="POST">
                <h2 class="mb-5"> Bạn quên mật khẩu ?</h2>
                <div class="input-group mb-2">
                    <span class="input-group-text size-text"><i class="bi bi-person"></i></span>
                    <input type="text" id="forgotUsername" class="form-control size-text" name="tendangnhap"
                        placeholder="Tên đăng nhập" aria-label="Username" aria-describedby="basic-addon1">
                </div>

                <div class="input-group mb-2">
                    <span class="input-group-text size-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" id="email" class="form-control size-text" name="email" placeholder="Nhập email"
                        aria-label="email" aria-describedby="basic-addon1">
                </div>

                <div class="input-group mb-4 d-flex justify-content-center align-items-center">
                    <span>
                        <?php
                        echo $loi;
                        echo $thongbao;
                        ?>
                    </span>
                </div>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <input type="submit" name="submit" class="btn w-100 pt-2 pb-2 size-text rounded-2"
                            value="Gửi đi">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 mt-3">
                        <a href="./login.php" class="text-decoration-none">Quay trở lại</a>
                    </div>
                </div>
            </form>

            <div class="col-xs-12 text-center size-text w-100">
                <p>Wellcome to East Asia University of Technology</p>
            </div>
        </div>
    </section>
    <script src="./assets/scripts/index.js"></script>
</body>

</html>