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
    <!-- Include the SweetAlert2 library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./assets/css/main.css">
    <title>Admin</title>
</head>

<body class="custom-scrollbar">
    <header class="container-fluid">
        <div class="row row-header">
            <div class="col-xs-12 text-center">
                <img src="./assets/img/truong-dai-hoc-cong-nghe-dong-a-eaut-3.jpg" alt="" class="w-5 h-40">
            </div>
        </div>
    </header>

    <section class="container-fluid">
        <div class="row row-navbar">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-10">
                <ul class="d-flex align-items-center justify-content-between h-100 ps-2 nav nav-tabs">
                    <li><a href="admin.php" class="text-decoration-none ">Quản lý phòng học</a></li>
                    <li><a href="QLKhoa.php" class="text-decoration-none ">Quản lý Khoa</a></li>
                    <li><a href="QLMon.php" class="text-decoration-none ">Quản lý môn học</a></li>
                    <li><a href="QLGiangvien.php" class="text-decoration-none ">Quản lý giảng viên</a></li>
                    <li><a href="QLLop.php" class="text-decoration-none ">Quản lý lớp</a></li>
                    <li><a href="QLcsvc.php" class="text-decoration-none ">Cơ sở vật chất</a></li>
                    <li><a href="QLtaikhoan.php" class="text-decoration-none ">Quản lý tài khoản</a></li>
                    <li><a href="xeplich.php" class="text-decoration-none ">Xếp lịch</a></li>
                    <li><a href="Baocaothongke.php" class="text-decoration-none ">Báo cáo thống kê</a></li>
                </ul>
            </div>
            <div class="col-xs-8 col-sm-6 col-md-6 col-lg-2">

                <ul class="d-flex align-items-center h-100 float-end pe-2">
                    <li class="text-white">Admin</li>
                    <li>
                        <a href="#" id="icon-user">
                            <i class="bi bi-person-circle icon-user ms-2"></i>
                        </a>
                        <ul id="sub-nav" class="sub-nav position-absolute d-none ps-0 rounded-2">
                            <li><a id="logout" href="admin.php?logout=true"
                                    class="p-2 text-decoration-none text-white">Đăng
                                    xuất</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </section>
    <script src="./assets/scripts/index.js"></script>
</body>

</html>