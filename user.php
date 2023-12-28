<?php
include "./database/Class-Database.php";
global $conn;
if (!$_SESSION["login"]) {
    header("Location: login.php");
}

// Thực hiện truy vấn để lấy thông tin người dùng từ hai bảng
// var_dump($_SESSION['name']);
if (isset($_COOKIE['name']) || isset($_SESSION['name'])) {
    $username = $_COOKIE['name'] = $_SESSION['name'];
    $sql = "SELECT users.username, users.role, nguoidung.Name 
        FROM users 
        LEFT JOIN nguoidung ON users.username = nguoidung.ID 
        WHERE users.username = '$username'";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($row) {
            $displayName = $row['Name'] ?: $row['username']; // Hiển thị tên người dùng hoặc username nếu không có tên
        } else {
            $displayName = "Tên không xác định"; // Hoặc có thể gán một giá trị mặc định
        }
    } else {
        echo "Lỗi truy vấn: " . mysqli_error($conn); // Hiển thị lỗi nếu có
    }
}

if (isset($_GET['logout'])) {
    // Xóa cookie
    setcookie('username', '', time() - (86400) * 30); // Đặt thời gian hết hạn ở quá khứ
    setcookie('password', '', time() - (86400) * 30); // Đặt thời gian hết hạn ở quá khứ

    // Xóa phiên đăng nhập
    unset($_SESSION['login']);
    unset($_SESSION['name']);

    // Chuyển hướng người dùng sau khi đăng xuất
    header("Location: login.php"); // Thay thế 'login.php' bằng trang đăng nhập của bạn
    exit();
}
//xử lý tìm kiếm bằng trống và phòng đã đăng ký
$name = "";
if (isset($_POST['search2'])) {
    $searchPHSql = "SELECT xeplich.idPhong, phonghoc.tenPhong, 
    monhoc.tenMon, giangvien.tenGV, 
    xeplich.thoiGianBatDau, xeplich.TgianKetThuc, xeplich.tinhTrang
    FROM xeplich 
    join phonghoc on xeplich.idPhong = phonghoc.idPhong 
    join giangvien on xeplich.idGV = giangvien.idGiangVien
    join monhoc on xeplich.idMon = monhoc.idMon 
    WHERE xeplich.tinhTrang = 'Trống'";
    $result = $conn->query($searchPHSql);
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            // Lưu kết quả tìm kiếm vào một mảng
            $searchResults = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $searchResults[] = $row;
            }
        }
    }
} elseif (isset($_POST['search3'])) {
    $searchPHSql = "SELECT xeplich.idPhong, phonghoc.tenPhong, 
    monhoc.tenMon, giangvien.tenGV, 
    xeplich.thoiGianBatDau, xeplich.TgianKetThuc, xeplich.tinhTrang
    FROM xeplich 
    join phonghoc on xeplich.idPhong = phonghoc.idPhong 
    join giangvien on xeplich.idGV = giangvien.idGiangVien
    join monhoc on xeplich.idMon = monhoc.idMon
        WHERE xeplich.tinhTrang = 'Đã đăng ký'";
    $result = $conn->query($searchPHSql);
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            // Lưu kết quả tìm kiếm vào một mảng
            $searchResults = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $searchResults[] = $row;
            }
        } else {

        }
    }
} elseif (isset($_POST["search"])) {
    $name = $_POST["search-name"];
    if (!empty($name)) {
        // Xử lý tìm kiếm theo tên phòng
        $searchPHSql = "SELECT xeplich.idPhong, phonghoc.tenPhong, 
        monhoc.tenMon, giangvien.tenGV, 
        xeplich.thoiGianBatDau, xeplich.TgianKetThuc, xeplich.tinhTrang
        FROM xeplich 
        join phonghoc on xeplich.idPhong = phonghoc.idPhong 
        join giangvien on xeplich.idGV = giangvien.idGiangVien
        join monhoc on xeplich.idMon = monhoc.idMon 
        WHERE phonghoc.tenPhong = '$name'";
        $result = $conn->query($searchPHSql);
        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                // Lưu kết quả tìm kiếm vào một mảng
                $searchResults = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $searchResults[] = $row;
                }
            } else {
                // echo "Không tìm thấy kết quả.";
            }
        } else {
            echo "Lỗi: " . $conn->error;
        }
    }
}

// Xử lý đăng ký
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["dangky"])) {
    $idPhong = $_POST["idPhong"];
    $TGBD = $_POST["TGBD"];
    $TGKT = $_POST["TGKT"];

    $deletePHSql = "UPDATE  xeplich set tinhTrang ='Đã đăng ký' 
    WHERE idPhong ='$idPhong' and thoiGianBatDau ='$TGBD' 
    and TgianKetThuc ='$TGKT'";
    if ($conn->query($deletePHSql) === TRUE) {
    } else {
    }
}

$sql = mysqli_query($conn, "SELECT xeplich.idPhong, phonghoc.tenPhong, 
monhoc.tenMon, giangvien.tenGV, 
xeplich.thoiGianBatDau, xeplich.TgianKetThuc, xeplich.tinhTrang
FROM xeplich 
join phonghoc on xeplich.idPhong = phonghoc.idPhong 
join giangvien on xeplich.idGV = giangvien.idGiangVien
join monhoc on xeplich.idMon = monhoc.idMon");
if (mysqli_num_rows($sql) === 0) {
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="main.css">
    <title>User</title>
</head>

<body>
    <header class="container-fluid">
        <div class="row row-header">
            <div class="col-xs-12 text-center">
                <img src="./img/truong-dai-hoc-cong-nghe-dong-a-eaut-3.jpg" alt="" class="w-5 h-40">
            </div>
        </div>
    </header>

    <section class="container-fluid" title="Phần menu">
        <div class="row row-navbar">
            <div class="col-xs-4 col-sm-12 col-md-12 col-lg-6">
                <ul class="d-flex align-items-center justify-content-between h-100 ps-5">
                    <li><a href="user.php" class="text-decoration-none ">Phòng học</a></li>
                </ul>
            </div>
            <div class="col-xs-8 col-sm-6 col-md-6 col-lg-6">
                <ul class="d-flex align-items-center h-100 float-end pe-5">
                    <li class="text-white">
                        <?php echo $displayName ?>
                    </li>
                    <li>
                        <a href="#" id="icon-user">
                            <i class="bi bi-person-circle icon-user ms-2"></i>
                        </a>
                        <ul id="sub-nav" class="sub-nav position-absolute d-none ps-0 rounded-2">
                            <li class="border-bottom"><a href="#" class="text-decoration-none p-2 text-white"
                                    data-bs-toggle="modal" data-bs-target="#changePasswordModal">Đổi mật
                                    khẩu</a></li>
                            <li><a id="logout" href="user.php?logout=true"
                                    class="p-2 text-decoration-none text-white">Đăng
                                    xuất</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </section>
    <main>
        <section class="container-fluid">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 content-pane">
                        <div class="row">

                            <div class="col-xs-4 col-sm-12 col-md-12 col-lg-6 mt-3">
                                <div class="row">
                                    <div class="col-xs-4 col-sm-12 col-md-12 col-lg-12">
                                        <form method="post" action="" class="ms-4 form-search">
                                            <input type="text" name="search-name" placeholder="Nhập tên phòng học"
                                                value="<?php echo $name ?>">
                                            <input type="submit" name="search" value="Tìm kiếm"
                                                class="input-style ms-3">
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-4 col-sm-12 col-md-12 col-lg-6 mt-3">
                                <div class="row">
                                    <div class="col-xs-4 col-sm-12 col-md-12 col-lg-12 d-flex flex-row-reverse">
                                        <form method="post" action="" class="form-search me-4">
                                            <input type="submit" name="search2" value="Phòng trống"
                                                class="input-style me-3">
                                            <input type="submit" name="search3" value="Phòng đã đăng ký"
                                                class="input-style">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 table-responsive mt-3 mb-3">
                                <table id="table"
                                    class="col-xs-12 col-sm-12 col-md-12 col-lg-8 w-100 border-collapse text-center table">
                                    <thead>
                                        <tr class="table-dark text-white">
                                            <th>ID Phòng</th>
                                            <th>Tên phòng</th>
                                            <th>Môn</th>
                                            <th>GIảng viên</th>
                                            <th>Thời gian bắt đầu</th>
                                            <th>Thời gian kết thúc</th>
                                            <th>Chức năng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 0;
                                        if (!empty($searchResults)) {
                                            foreach ($searchResults as $searchResult) {
                                                $class = ($i % 2 != 0) ? "table-secondary" : "";
                                                ?>
                                                <tr class="<?php echo $class ?>">
                                                    <td>
                                                        <?php echo $searchResult["idPhong"] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $searchResult["tenPhong"] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $searchResult["tenMon"] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $searchResult["tenGV"] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $searchResult["thoiGianBatDau"] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $searchResult["TgianKetThuc"] ?>
                                                    </td>
                                                    <?php
                                                    if ($searchResult['tinhTrang'] === 'Trống') {
                                                        ?>
                                                        <td>
                                                            <form action="" method='post'>
                                                                <input type="hidden" name="idPhong"
                                                                    value="<?php echo $row["idPhong"] ?>">
                                                                <input type="hidden" name="TGBD"
                                                                    value="<?php echo $row["thoiGianBatDau"] ?>">
                                                                <input type="hidden" name="TGKT"
                                                                    value="<?php echo $row["TgianKetThuc"] ?>">
                                                                <input type="submit" name="dangky" value="Đăng ký"
                                                                    class="input-style"></input>
                                                            </form>
                                                        </td>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <td>Đã đăng ký</td>
                                                        <?php
                                                    }
                                                    ?>
                                                </tr>
                                                <?php
                                                $i++;
                                            }
                                        } elseif (mysqli_num_rows($sql) > 0) {
                                            ?>
                                            <?php
                                            $i = 0;
                                            while ($row = mysqli_fetch_assoc($sql)) {
                                                $class = ($i % 2 != 0) ? "table-secondary" : "";
                                                ?>
                                                <tr class="<?php echo $class ?>">
                                                    <td>
                                                        <?php echo $row["idPhong"] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row["tenPhong"] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row["tenMon"] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row["tenGV"] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row["thoiGianBatDau"] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row["TgianKetThuc"] ?>
                                                    </td>
                                                    <?php
                                                    if ($row['tinhTrang'] === 'Trống') {
                                                        ?>
                                                        <td>
                                                            <form action="" method='post'>
                                                                <input type="hidden" name="idPhong"
                                                                    value="<?php echo $row["idPhong"] ?>">
                                                                <input type="hidden" name="TGBD"
                                                                    value="<?php echo $row["thoiGianBatDau"] ?>">
                                                                <input type="hidden" name="TGKT"
                                                                    value="<?php echo $row["TgianKetThuc"] ?>">
                                                                <input type="submit" name="dangky" value="Đăng ký"
                                                                    class="input-style"></input>
                                                            </form>
                                                        </td>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <td>Đã đăng ký</td>
                                                        <?php
                                                    }
                                                    ?>
                                                </tr>
                                                <?php
                                                $i++;
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
        $error = "";
        $succes = "";
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["changepassword"])) {
            $oldpass = $_POST['oldPassword'];
            $newpass = $_POST['newPassword'];
            $confirmpass = $_POST['confirmPassword'];

            if (empty($oldpass) || empty($newpass) || empty($confirmpass)) {
                $error = "Mời bạn điền đầy đủ thông tin";
            } elseif ($confirmpass !== $newpass) {
                $error = "Xác nhận mật khẩu không khớp, mời nhập lại";
            } else {
                // Lấy mật khẩu hiện tại của người dùng từ cơ sở dữ liệu
                $username = $_SESSION['name']; // Sử dụng tên người dùng từ phiên đăng nhập
                $sql = "SELECT password FROM users WHERE username = '$username'";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $currentPassword = $row['password'];

                    // So sánh mật khẩu hiện tại với mật khẩu đã nhập
                    if ($oldpass == $currentPassword) {
                        $updateSql = "UPDATE users SET password = '$newpass' WHERE username = '$username'";
                        if ($conn->query($updateSql) === TRUE) {
                            $succes = "Đổi mật khẩu thành công";
                        } else {
                            $error = "Lỗi khi cập nhật mật khẩu: " . $conn->error;
                        }
                    } else {
                        $error = "Mật khẩu cũ không đúng";
                    }
                } else {
                    $error = "Không tìm thấy người dùng";
                }
            }
        }
        ?>

        <!-- Thêm modal vào trang -->
        <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="changePasswordModalLabel">Đổi mật khẩu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="oldPassword" class="form-label">Mật khẩu cũ:</label>
                                <input type="password" class="form-control" id="oldPassword" name="oldPassword"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="newPassword" class="form-label">Mật khẩu mới:</label>
                                <input type="password" class="form-control" id="newPassword" name="newPassword"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Xác nhận mật khẩu mới:</label>
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword"
                                    required>
                            </div>
                            <p>
                                <?php echo $error ?>
                                <?php echo $succes ?>
                            </p>
                            <button name="changepassword" type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>


    </main>
    <script src="./assets/scripts/index.js"></script>
</body>

</html>