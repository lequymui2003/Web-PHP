<?php
include "./database/Class-Database.php";
global $conn;
if (!isset($_SESSION["login"]) || empty($_SESSION["login"])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION["login"] !== "admin") {
    header("Location: user.php");
    exit();
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
?>
<?php
// Xử lý tìm kiếm
$error = "";
$succes = "";
$name = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"])) {
    $name = $_POST["search-name"];
    // Biểu thức chính quy để kiểm tra ký tự đặc biệt
    $specialCharsPattern = "/[!@#\$%\^\&*()]/";
    if (!empty($name)) {
        // Xử lý tìm kiếm theo tên giảng viên
        $searchPHSql = "SELECT giangvien.idGiangVien, giangvien.sdt, 
        giangvien.tenGV, giangvien.idKhoa, khoa.tenKhoa, khoa.idKhoa FROM giangvien 
        join khoa on giangvien.idKhoa = khoa.idKhoa WHERE tenGV LIKE '%$name%'";
        $result = $conn->query($searchPHSql);
        if (preg_match($specialCharsPattern, $name)) {
            $error = "Không nhập kí tự đặc biệt.";
        } else {
            if ($result) {
                if (mysqli_num_rows($result) > 0) {
                    // Lưu kết quả tìm kiếm vào một mảng
                    $searchResults = [];
                    while ($row = mysqli_fetch_assoc($result)) {
                        $searchResults[] = $row;
                    }
                } else {
                    echo "Lỗi: " . $conn->error;
                }
            }
        }
    }
}



// Xử lý sửa
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $idGV = $_POST["input1"];
    $SDT = $_POST["input3"];
    $name = $_POST["input2"];
    $idKhoa = $_POST["input4"];

    // Biểu thức chính quy để kiểm tra ký tự đặc biệt
    $specialCharsPattern = "/[!@#\$%\^\&*()]/";
    // Kiểm tra xem ID Giảng viên đã tồn tại chưa (loại trừ giảng viên đang sửa)
    $checkDuplicateSql = "SELECT * FROM giangvien WHERE idGiangVien = '$idGV' AND idGiangVien != '$idGV'";
    $result = $conn->query($checkDuplicateSql);

    // Kiểm tra định dạng số điện thoại
    $phonePattern = '/^(09|03)\d{8}$/'; // Mẫu số điện thoại: 10 chữ số
    $isValidPhone = preg_match($phonePattern, $SDT);

    if ($idGV == "" || $SDT == "" || $name == "" || $idKhoa == "") {
        $error = "Mời bạn chọn giảng viên cần sửa";
    } else {
        if (
            preg_match($specialCharsPattern, $idGV) || preg_match($specialCharsPattern, $SDT) ||
            preg_match($specialCharsPattern, $name) || preg_match($specialCharsPattern, $idKhoa)
        ) {
            $error = "Không nhập kí tự đặc biệt.";
        } else {
            if ($result->num_rows > 0) {
                // ID Giảng viên đã tồn tại (trừ giảng viên đang sửa), thông báo lỗi
                $error = "ID Giảng viên đã tồn tại.";
            } elseif (!$isValidPhone) {
                // Số điện thoại không hợp lệ, thông báo lỗi
                $error = "Số điện thoại không đúng định dạng.";
            } else {
                // Thực hiện cập nhật khi không có trùng lặp và số điện thoại hợp lệ
                $updatePHSql = "UPDATE giangvien SET sdt = '$SDT', tenGV = '$name', idKhoa = '$idKhoa'
                        WHERE idGiangVien = '$idGV'";

                if ($conn->query($updatePHSql) === TRUE) {
                    // Cập nhật thành công
                    $succes = "Cập nhật thành công.";
                } else {
                    // Lỗi khi cập nhật
                    $error = "Lỗi: " . $conn->error;
                }
            }
        }

    }

}

// Xử lý thêm
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    $idGV = $_POST["input1"];
    $SDT = $_POST["input3"];
    $name = $_POST["input2"];
    $idKhoa = $_POST["input4"];
    // Biểu thức chính quy để kiểm tra ký tự đặc biệt
    // $specialCharsPattern = "/[!@#\$%\^\&*()]/";
    // Kiểm tra xem ID Giảng viên đã tồn tại chưa
    $checkDuplicateSql = "SELECT * FROM giangvien WHERE idGiangVien = '$idGV'";
    $result = $conn->query($checkDuplicateSql);

    // Kiểm tra định dạng số điện thoại
    $phonePattern = '/^(09|03)\d{8}$/'; // Mẫu số điện thoại: 10 chữ số
    $isValidPhone = preg_match($phonePattern, $SDT);
    if ($idGV == "" || $SDT == "" || $name == "" || $idKhoa == "") {
        $error = "Mời bạn nhập đầy đủ thông tin";
    // } else {
    //     if (
    //         preg_match($specialCharsPattern, $idGV) || preg_match($specialCharsPattern, $SDT) ||
    //         preg_match($specialCharsPattern, $name) || preg_match($specialCharsPattern, $idKhoa)
    //     ) {
    //         $error = "Không nhập kí tự đặc biệt.";
         } else {
            if ($result->num_rows > 0) {
                // ID Giảng viên đã tồn tại, thông báo lỗi
                $error = "ID Giảng viên đã tồn tại.";
            } elseif (!$isValidPhone) {
                // Số điện thoại không hợp lệ, thông báo lỗi
                $error = "Số điện thoại không đúng định dạng.";
            } else {
                // Thực hiện thêm mới khi không có trùng lặp và số điện thoại hợp lệ
                $insertPHSql = "INSERT INTO giangvien (idGiangVien, sdt, tenGV, idKhoa) VALUES ('$idGV', '$SDT', '$name', '$idKhoa')";

                if ($conn->query($insertPHSql) === TRUE) {
                    // Thêm mới thành công
                    $succes = "Thêm mới thành công.";
                } else {
                    // Lỗi khi thêm mới
                    $error = "Lỗi: " . $conn->error;
                }
            }
        }

    }


// Xử lý xóa 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])) {
    $idGV = $_POST["IDGiangVien"];

    $deletePHSql = "DELETE FROM giangvien WHERE idGiangVien = '$idGV'";
    if ($conn->query($deletePHSql) === TRUE) {
    } else {
    }
}
$sql = mysqli_query($conn, "SELECT giangvien.idGiangVien, giangvien.sdt, 
giangvien.tenGV, giangvien.idKhoa, khoa.tenKhoa, khoa.idKhoa FROM giangvien 
join khoa on giangvien.idKhoa = khoa.idKhoa");
if (mysqli_num_rows($sql) === 0) {
}
?>
<?php
require_once 'header.php';
?>
<main>
    <section class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-10 content-pane d-flex">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-8 table-responsive mt-3 ms-3 mb-3">
                        <table id="table"
                            class="col-xs-12 col-sm-12 col-md-12 col-lg-8 w-100 border-collapse text-center table">
                            <thead>
                                <tr class="table-dark text-white">
                                    <th>ID Giảng Viên</th>
                                    <th>Họ và tên</th>
                                    <th>Số điện thoại</th>
                                    <th>ID Khoa</th>
                                    <th>Tên Khoa</th>
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
                                                <?php echo $searchResult["idGiangVien"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["tenGV"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["sdt"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["idKhoa"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["tenKhoa"] ?>
                                            </td>
                                            <td>
                                                <form action="" method='post'>
                                                    <input type="hidden" name="IDGiangVien"
                                                        value="<?php echo $searchResult["idGiangVien"] ?>">
                                                    <input type="submit" name="delete" value="Xóa" class="input-style"></input>
                                                </form>
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
                                                <?php echo $row["idGiangVien"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["tenGV"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["sdt"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["idKhoa"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["tenKhoa"] ?>
                                            </td>
                                            <td>
                                                <form action="" method='post'>
                                                    <input type="hidden" name="IDGiangVien"
                                                        value="<?php echo $row["idGiangVien"] ?>">
                                                    <input type="submit" name="delete" value="Xóa" class="input-style"></input>
                                                </form>
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
                    <div class="col-xs-4 col-sm-12 col-md-12 col-lg-4 mt-3">
                        <div class="row">
                            <div class="col-xs-4 col-sm-12 col-md-12 col-lg-12">
                                <form method="post" action="" class="d-flex justify-content-around form-search">
                                    <input type="text" name="search-name" placeholder="Nhập tên giảng viên"
                                        value="<?php echo $name ?>">
                                    <input type="submit" name="search" value="Tìm kiếm" class="input-style me-4">
                                </form>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-xs-4 col-sm-12 col-md-12 col-lg-10">
                                <form action="" method="post">
                                    <div class="d-flex flex-column ms-2">
                                        <label for="">ID Giảng Viên : </label>
                                        <input float="left" type="text" placeholder="Nhập ID giảng viên"
                                            style="padding: 2px 3px;" class="rounded" name="input1">
                                    </div>
                                    <div class="d-flex flex-column ms-2 mt-2">
                                        <label for="">Họ và tên: </label>
                                        <input type="text" placeholder="Nhập họ và tên" style="padding: 2px 3px;"
                                            class="rounded" name="input2">
                                    </div>
                                    <div class="d-flex flex-column ms-2 mt-2">
                                        <label for="">Số điện thoại: </label>
                                        <input type="text" placeholder="Nhập số điện thoại" style="padding: 2px 3px"
                                            class="rounded" name="input3">
                                    </div>
                                    <div class="d-flex flex-column ms-2 mt-2">
                                        <label for="">ID Khoa: </label>
                                        <?php
                                        $getEmptyRoomsSql = "SELECT idKhoa FROM khoa";
                                        $emptyRoomsResult = $conn->query($getEmptyRoomsSql);

                                        if ($emptyRoomsResult && $emptyRoomsResult->num_rows > 0) {
                                            echo '<select name="input4" class="rounded" style="padding: 2px 3px;">';
                                            while ($row = $emptyRoomsResult->fetch_assoc()) {
                                                echo '<option value="' . $row['idKhoa'] . '">' . $row['idKhoa'] . '</option>';
                                            }
                                            echo '</select>';
                                        } else {

                                        }
                                        ?>
                                    </div>
                                    <div class="ms-2 mt-2">
                                        <label for="" class="">
                                            <label for="" class="text-red">
                                                <?php
                                                echo $error
                                                    ?>
                                            </label>
                                            <label for="" class="text-green">
                                                <?php
                                                echo $succes
                                                    ?>
                                            </label>
                                        </label>
                                    </div>
                                    <div class="d-flex  justify-content-between  ms-2 mt-4">
                                        <div>
                                            <input type="submit" name="add" value="Thêm" class="input-style">
                                        </div>
                                        <div>
                                            <input type="submit" name="update" value="Sửa" class="input-style">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<!-- <?php
require_once 'footer.php';
?> -->