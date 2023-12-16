<?php
include "Class-Database.php";
global $conn;
if (isset($_SESSION['login'])) {
    header("Location: admin.php");
    exit();
}
?>
<?php
$emptyerror = "";
$error = "";
$username = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["user_name"];
    $password = $_POST["user_password"];

    // Kiểm tra xem các trường có rỗng không
    if (empty($username) || empty($password)) {
        $emptyerror = "Mời bạn nhập đầy đủ tên đăng nhập và mật khẩu";
    } else {
        // Nếu không có trường nào rỗng, tiếp tục xử lý đăng nhập
        $result = mysqli_query($conn, "SELECT * FROM users where username = '$username' and matkhau = '$password'");
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            $_SESSION["login"] = $row['role'];
            $_SESSION["name"] = $username;
            header("Location: admin.php");
            exit();
        } else {
            $error = "Tên đăng nhập hoặc mật khẩu không đúng";
        }
    }

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="icon" href="path/to/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="main.css">
    <title>Đăng nhập</title>
</head>

<body>

    <section class=" d-flex justify-content-center text-center align-items-center">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 image-background">
            <img src="./img/truong.webp" alt="">
        </div>
        <div class="form-login p-4 d-flex flex-column align-items-center justify-content-between">
            <div class="logo-eaut">
                <img src="./img/LOGO_EAUT.png" alt="" class="w-50">
            </div>

            <form id="login" action="" class="w-100" method="post">
                <h2 class="mb-5">Đăng nhập</h2>
                <div class="input-group mb-2">
                    <span class="input-group-text size-text"><i class="bi bi-person"></i></span>
                    <input type="text" id="username" class="form-control size-text" name="user_name"
                        placeholder="Tên đăng nhập" aria-label="Username" aria-describedby="basic-addon1"
                        value="<?php echo $username ?>">
                </div>
                <div class="input-group mb-4">
                    <span class="input-group-text size-text custom-cursor">
                        <i class="toggle-password bi bi-eye"></i></span>
                    <input type="password" id="password" class="form-control size-text" name="user_password"
                        placeholder="Nhập mật khẩu" aria-label="password" aria-describedby="basic-addon1">
                </div>
                <div class="input-group mb-4">
                    <span>
                        <?php echo $emptyerror ?>
                        <?php echo $error ?>
                    </span>
                </div>

                <div class="form-group mb-4">
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            <span class="float-start size-text">
                                <input type="checkbox" class="custom-cursor" name="remember"> Nhớ đăng nhập
                            </span>
                        </div>

                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            <a href="remember.php" id="forgotPassword" class="float-end size-text">Quên mật khẩu ?</a>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <input type="submit" name="submit" class="btn w-100 pt-2 pb-2 size-text rounded-2"
                            value="Đăng nhập">
                    </div>
                </div>
            </form>

            <div class="col-xs-12 text-center size-text w-100">
                <p>Wellcome to East Asia University of Technology</p>
            </div>
        </div>
    </section>
    <script src="./index.js"></script>
</body>

</html>