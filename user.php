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
    $sql = "SELECT * FROM users WHERE users.username = '$username'";

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
if (isset($_POST["search"])) {
    $name = $_POST["search-name"];
    if (!empty($name)) {
        // Xử lý tìm kiếm theo tên phòng
        $searchPHSql = "SELECT xeplich.idPhong, phonghoc.tenPhong, 
        monhoc.tenMon, giangvien.tenGV, lop.tenLop,
        xeplich.Date, xeplich.ThoiGian, xeplich.tinhTrang
        FROM xeplich 
        join phonghoc on xeplich.idPhong = phonghoc.idPhong 
        join giangvien on xeplich.idGV = giangvien.idGiangVien
        join monhoc on xeplich.idMon = monhoc.idMon 
        join lop on xeplich.idLop = lop.idLop
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
$error = "";
$succes = "";
// Xử lý đăng ký
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["dangky"])) {
    // Lấy dữ liệu từ form thêm
    $idKhoa = $_POST["input1"];
    $idMon = $_POST["input3"];
    $idLop = $_POST["input2"];
    $idGV = $_POST["input4"];
    $idPhong = $_POST["input5"];
    $time = $_POST["input7"];
    $date = $_POST["input6"];

    // Kiểm tra xem phòng học có đang bảo trì hay không
    $checkPhongSql = "SELECT tinhTrang FROM phonghoc WHERE idPhong = '$idPhong'";
    $phongResult = $conn->query($checkPhongSql);

    if ($phongResult->num_rows > 0) {
        $phongData = $phongResult->fetch_assoc();
        if ($phongData['tinhTrang'] === "Đang bảo trì") {
            $error = "Phòng học này đang bảo trì, không thể đăng ký!";
        } else {
            // Tiếp tục kiểm tra các điều kiện khác
            if ($idKhoa == "" || $idMon == "" || $idLop == "" || $idGV == "" || $idPhong == "" || $date == "" || $time == "") {
                $error = "Mời nhập đầy đủ thông tin";
            } else {
                // Kiểm tra xem giáo viên đã có lịch dạy trong khoảng thời gian này chưa
                $checkSql = "SELECT * FROM xeplich 
                             WHERE idGV = '$idGV' 
                             AND (Date = '$date' AND ThoiGian = '$time')";

                $result = $conn->query($checkSql);

                // Kiểm tra số lượng hàng trả về
                if ($result->num_rows > 0) {
                    $error = "Giáo viên này đã có lịch dạy trong khoảng thời gian này!";
                } else {
                    // Kiểm tra xem lớp đã có lịch trong khoảng thời gian này chưa
                    $checkSql = "SELECT * FROM xeplich 
                                 WHERE idLop = '$idLop' 
                                 AND (Date = '$date' AND ThoiGian = '$time')";

                    $result = $conn->query($checkSql);

                    if ($result->num_rows > 0) {
                        $error = "Lớp này đã có lịch trong khoảng thời gian này!";
                    } else {
                        // Kiểm tra xem phòng học đã có lịch chưa
                        $checkSql = "SELECT * FROM xeplich 
                                     WHERE idPhong = '$idPhong' 
                                     AND (Date = '$date' AND ThoiGian = '$time')";

                        $result = $conn->query($checkSql);

                        if ($result->num_rows > 0) {
                            $error = "Phòng này đã có lịch trong khoảng thời gian này!";
                        } else {
                            // Nếu không có lỗi, thêm lịch học vào cơ sở dữ liệu
                            $insertPHSql = "INSERT INTO xeplich (idMon, idLop, idGV, idPhong, idKhoa, Date, ThoiGian) 
                                            VALUES ('$idMon', '$idLop', '$idGV', '$idPhong', '$idKhoa', '$date', '$time')";

                            if ($conn->query($insertPHSql) === TRUE) {
                                $succes = "Thêm lịch học thành công";
                            } else {
                                $error = "Thêm lịch học thất bại";
                            }
                        }
                    }
                }
            }
        }
    } else {
        $error = "Phòng học không tồn tại!";
    }
}


$sql = mysqli_query($conn, "SELECT xeplich.idPhong, phonghoc.tenPhong, 
    monhoc.tenMon, giangvien.tenGV, lop.tenLop,
    xeplich.Date, xeplich.ThoiGian, xeplich.tinhTrang
    FROM xeplich 
    JOIN phonghoc ON xeplich.idPhong = phonghoc.idPhong 
    JOIN giangvien ON xeplich.idGV = giangvien.idGiangVien
    JOIN monhoc ON xeplich.idMon = monhoc.idMon
    JOIN lop ON xeplich.idLop = lop.idLop
    WHERE xeplich.Date >= CURDATE()");
if (mysqli_num_rows($sql) === 0) {
}
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
    <!-- Include the SweetAlert2 library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./assets/css/main.css">
    <title>User</title>
    <style>
        /* Giới hạn chiều cao modal để không có thanh cuộn */
        .modal-content {
            max-height: 90vh;
            /* Giới hạn chiều cao tối đa modal */
            overflow-y: auto;
            /* Tự động cuộn nội dung nếu vượt quá */
        }

        .modal-body {
            padding: 20px;
        }

        .modal-lg {
            max-width: 700px;
            /* Điều chỉnh độ rộng modal */
        }

        /* Style cho nút đăng ký */
        button[type="submit"] {
            background-color: #0d6efd;
            border: none;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
        }

        button[type="submit"]:hover {
            background-color: #084298;
        }
    </style>
</head>

<body class="custom-scrollbar">
    <header class="container-fluid">
        <div class="row row-header">
            <div class="col-xs-12 text-center">
                <img src="./assets/img/truong-dai-hoc-cong-nghe-dong-a-eaut-3.jpg" alt="" class="w-5 h-40">
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
                            <li class="border-bottom  border-secondary"><a href="#"
                                    class="text-decoration-none p-2 text-white" data-bs-toggle="modal"
                                    data-bs-target="#changePasswordModal">Đổi mật
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
                                        <!-- Nút mở modal để đăng ký phòng -->
                                        <button type="button" class="input-style me-3" data-bs-toggle="modal"
                                            data-bs-target="#registerModal">Đăng ký phòng</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row me-1">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 table-responsive mt-3 mx-h-400">
                                <table id="table"
                                    class="col-xs-12 col-sm-12 col-md-12 col-lg-8 w-100 border-collapse text-center table">
                                    <thead>
                                        <tr class="table-dark text-white">
                                            <th>ID Phòng</th>
                                            <th>Tên phòng</th>
                                            <th>Tên lớp</th>
                                            <th>Môn</th>
                                            <th>Giảng viên</th>
                                            <th>Ngày</th>
                                            <th>Thời gian</th>
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
                                                        <?php echo $searchResult["tenLop"] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $searchResult["tenMon"] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $searchResult["tenGV"] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $searchResult["Date"] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $searchResult["ThoiGian"] ?>
                                                    </td>
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
                                                        <?php echo $row["tenLop"] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row["tenMon"] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row["tenGV"] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row["Date"] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row["ThoiGian"] ?>
                                                    </td>
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
        <!-- Thêm modal đổi mật khẩu vào trang -->
        <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content" style="margin: 200px auto">
                    <div class="modal-header">
                        <h5 class="modal-title" id="changePasswordModalLabel">Đổi mật khẩu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <form id="changePasswordForm" method="post" action="">
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
                            <div id="messageContainer">
                                <!-- Các thông báo sẽ được hiển thị ở đây -->
                            </div>
                            <button name="changepassword" type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <!-- Modal để đăng ký phòng -->
        <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerModalLabel">Đăng ký phòng học</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Form đăng ký phòng học -->
                        <form id="registerForm" method="post" action="">
                            <div class="mb-3 row">
                                <label for="idKhoa" class="col-sm-4 col-form-label">ID Khoa:</label>
                                <div class="col-sm-8">
                                    <?php
                                    $getEmptyRoomsSql = "SELECT idKhoa FROM khoa";
                                    $emptyRoomsResult = $conn->query($getEmptyRoomsSql);
                                    if ($emptyRoomsResult && $emptyRoomsResult->num_rows > 0) {
                                        echo '<select id="input1" name="input1" class="form-control">';
                                        while ($row = $emptyRoomsResult->fetch_assoc()) {
                                            echo '<option value="' . $row['idKhoa'] . '">' . $row['idKhoa'] . '</option>';
                                        }
                                        echo '</select>';
                                    } else {
                                        echo 'Không có dữ liệu.';
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="idLop" class="col-sm-4 col-form-label">ID Lớp:</label>
                                <div class="col-sm-8">
                                    <select id="input2" name="input2" class="form-control">
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="idMon" class="col-sm-4 col-form-label">ID Môn:</label>
                                <div class="col-sm-8">
                                    <select id="input3" name="input3" class="form-control">
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="idGV" class="col-sm-4 col-form-label">ID Giảng viên:</label>
                                <div class="col-sm-8">
                                    <select id="input4" name="input4" class="form-control">
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="idPhong" class="col-sm-4 col-form-label">ID Phòng:</label>
                                <div class="col-sm-8">
                                    <?php
                                    $getEmptyRoomsSql = "SELECT idPhong FROM phonghoc";
                                    $emptyRoomsResult = $conn->query($getEmptyRoomsSql);

                                    if ($emptyRoomsResult && $emptyRoomsResult->num_rows > 0) {
                                        echo '<select name="input5" class="form-control">';
                                        while ($row = $emptyRoomsResult->fetch_assoc()) {
                                            echo '<option value="' . $row['idPhong'] . '">' . $row['idPhong'] . '</option>';
                                        }
                                        echo '</select>';
                                    } else {
                                        echo 'Không có dữ liệu.';
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="Date" class="col-sm-4 col-form-label">Ngày:</label>
                                <div class="col-sm-8">
                                    <input type="date" class="form-control" name="input6">
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="ThoiGian" class="col-sm-4 col-form-label">Thời gian:</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="input7">
                                        <option value="Ca 1">Ca 1</option>
                                        <option value="Ca 2">Ca 2</option>
                                        <option value="Ca 3">Ca 3</option>
                                        <option value="Ca 4">Ca 4</option>
                                    </select>
                                </div>
                            </div>
                            <div class="ms-2 mt-2">
                                <label for="" class="text-red">
                                    <?php
                                    echo $error;
                                    echo $succes;
                                    ?>
                                </label>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="dangky" class="btn btn-primary">Đăng ký</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </main>
    <script src="./assets/scripts/index.js"></script>
</body>

</html>