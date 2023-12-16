<?php
include "Class-Database.php";
global $conn;
if (!isset($_SESSION["login"]) || empty($_SESSION["login"])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION["login"] !== "admin") {
    header("Location: user.php");
    exit();
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
    <link rel="stylesheet" href="main.css">
    <title>Admin</title>
</head>

<body>
    <header class="container-fluid">
        <div class="row row-header">
            <div class="col-xs-12 text-center">
                <img src="./img/truong-dai-hoc-cong-nghe-dong-a-eaut-3.jpg" alt="" class="w-5 h-50">
            </div>
        </div>
    </header>

    <section class="container-fluid">
        <div class="row row-navbar">
            <div class="col-xs-4 col-sm-12 col-md-12 col-lg-6">
                <ul class="d-flex align-items-center justify-content-between h-100 ps-5">
                    <li><a href="" class="text-decoration-none ">Trang chủ</a></li>
                    <li><a href="" class="text-decoration-none ">Phòng học</a></li>
                    <li><a href="" class="text-decoration-none ">Giới thiệu</a></li>
                    <li><a href="" class="text-decoration-none ">Hướng dẫn</a></li>
                </ul>
            </div>
            <div class="col-xs-8 col-sm-6 col-md-6 col-lg-6">

                <ul class="d-flex align-items-center h-100 float-end pe-5">
                    <li class="text-white">Admin</li>
                    <li>
                        <a href="#" id="icon-user">
                            <i class="bi bi-person-circle icon-user ms-2"></i>
                        </a>
                        <ul id="sub-nav" class="sub-nav position-absolute d-none ps-0 rounded-2">
                            <li class="border-bottom"><a href="#" class="text-decoration-none p-2 text-black">Tài
                                    khoản</a></li>
                            <li><a href="logout.php" class="p-2 text-decoration-none text-black">Đăng xuất</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </section>
    <script src="index.js"></script>
</body>

</html>