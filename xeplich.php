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
$name = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"])) {
    $name = $_POST["search-name"];
    if (!empty($name)) {
        // Xử lý tìm kiếm theo tên phòng
        $searchPHSql = "SELECT * FROM xeplich WHERE idPhong = '$name'";
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
        } else {
            echo "Lỗi: " . $conn->error;
        }
    }
}



// Xử lý sửa
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $id = $_POST["input1"];
    $idMon = $_POST["input2"];
    $idLop = $_POST["input3"];
    $idGV = $_POST["input4"];
    $idPhong = $_POST["input5"];
    $TGBD = $_POST["input6"];
    $TGKT = $_POST["input7"];

    $updatePHSql = "UPDATE xeplich SET
        idMon = '$idMon',
        idLop = '$idLop',
        idGV = '$idGV',
        idPhong = '$idPhong',
        thoiGianBatDau = '$TGBD',
        TgianKetThuc = '$TGKT'
    WHERE id = '$id'";
    if ($conn->query($updatePHSql) === TRUE) {
    } else {

    }
}

// Xử lý thêm 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    $id = $_POST["input1"];
    $idMon = $_POST["input2"];
    $idLop = $_POST["input3"];
    $idGV = $_POST["input4"];
    $idPhong = $_POST["input5"];
    $TGBD = $_POST["input6"];
    $TGKT = $_POST["input7"];

    $checkSql = "SELECT * FROM xeplich 
             WHERE idPhong = '$idPhong' 
             AND ((thoiGianBatDau >= '$TGBD' AND thoiGianBatDau < '$TGKT') 
             OR (TgianKetThuc > '$TGBD' AND TgianKetThuc <= '$TGKT'))";

    $result = $conn->query($checkSql);

    if ($result->num_rows > 0) {
        echo '<script>alert("Thông báo: Dữ liệu trùng lặp, vui lòng kiểm tra lại!");</script>';
    } else {
        // Không có trùng lặp: tiến hành thêm dữ liệu mới vào cơ sở dữ liệu
        $insertPHSql = "INSERT INTO xeplich (id, idMon, idLop, idGV, idPhong, thoiGianBatDau, TgianKetThuc) 
                      VALUES ('$id','$idMon', '$idLop', '$idGV', '$idPhong','$TGBD', '$TGKT')";
        if ($conn->query($insertPHSql) === TRUE) {
            // Thêm dữ liệu thành công
        } else {
            // Xử lý khi thêm dữ liệu thất bại
        }
    }

}
// Xử lý xóa 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])) {
    $id = $_POST["ID"];

    $deletePHSql = "DELETE FROM xeplich 
                WHERE id = '$id'";
    if ($conn->query($deletePHSql) === TRUE) {
    } else {
    }
}
$sql = mysqli_query($conn, "SELECT * FROM xeplich");
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
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-11 content-pane d-flex">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-9 table-responsive mt-3 ms-3 mb-3">
                        <table id="table"
                            class="col-xs-12 col-sm-12 col-md-12 col-lg-8 w-100 border-collapse text-center table">
                            <thead>
                                <tr class="table-dark text-white">
                                    <th>ID </th>
                                    <th>ID Môn</th>
                                    <th>ID Lớp</th>
                                    <th>ID Giảng viên</th>
                                    <th>ID Phòng</th>
                                    <th>Thời gian bắt đầu</th>
                                    <th>Thời gian kết thúc</th>
                                    <th>Tình trạng</th>
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
                                                <?php echo $searchResult["id"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["idMon"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["idLop"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["idGV"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["idPhong"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["thoiGianBatDau"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["TgianKetThuc"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["tinhTrang"] ?>
                                            </td>
                                            <td>
                                                <form action="" method='post'>
                                                    <input type="hidden" name="ID" value="<?php echo $searchResult["id"] ?>">
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
                                                <?php echo $row["id"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["idMon"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["idLop"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["idGV"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["idPhong"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["thoiGianBatDau"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["TgianKetThuc"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["tinhTrang"] ?>
                                            </td>
                                            <td>
                                                <form action="" method='post'>
                                                    <input type="hidden" name="ID" value="<?php echo $row["id"] ?>">
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
                    <div class="col-xs-4 col-sm-12 col-md-12 col-lg-3 mt-3">
                        <div class="row">
                            <div class="col-xs-4 col-sm-12 col-md-12 col-lg-12">
                                <form method="post" action="" class="d-flex justify-content-around form-search">
                                    <input type="text" name="search-name" placeholder="Nhập ID phòng học"
                                        value="<?php echo $name ?>">
                                    <input type="submit" name="search" value="Tìm kiếm" class="input-style me-4">
                                </form>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-xs-4 col-sm-12 col-md-12 col-lg-10">
                                <form action="" method="post">
                                    <div class="d-flex flex-column ms-2">
                                        <label for="">ID: </label>
                                        <input float="left" type="text" placeholder="Nhập ID" style="padding: 2px 3px;"
                                            class="rounded" name="input1">
                                    </div>
                                    <div class="d-flex flex-column ms-2">
                                        <label for="">ID Môn: </label>
                                        <?php
                                        $getEmptyRoomsSql = "SELECT idMon FROM monhoc";
                                        $emptyRoomsResult = $conn->query($getEmptyRoomsSql);

                                        if ($emptyRoomsResult && $emptyRoomsResult->num_rows > 0) {
                                            echo '<select name="input2" class="rounded" style="padding: 2px 3px;">';
                                            while ($row = $emptyRoomsResult->fetch_assoc()) {
                                                echo '<option value="' . $row['idMon'] . '">' . $row['idMon'] . '</option>';
                                            }
                                            echo '</select>';
                                        } else {

                                        }
                                        ?>
                                    </div>
                                    <div class="d-flex flex-column ms-2 mt-2">
                                        <label for="">ID Lớp: </label>
                                        <?php
                                        $getEmptyRoomsSql = "SELECT idLop FROM lop";
                                        $emptyRoomsResult = $conn->query($getEmptyRoomsSql);

                                        if ($emptyRoomsResult && $emptyRoomsResult->num_rows > 0) {
                                            echo '<select name="input3" class="rounded" style="padding: 2px 3px;">';
                                            while ($row = $emptyRoomsResult->fetch_assoc()) {
                                                echo '<option value="' . $row['idLop'] . '">' . $row['idLop'] . '</option>';
                                            }
                                            echo '</select>';
                                        } else {

                                        }
                                        ?>
                                    </div>
                                    <div class="d-flex flex-column ms-2 mt-2">
                                        <label for="">ID Giảng viên: </label>
                                        <?php
                                        $getEmptyRoomsSql = "SELECT idGiangVien FROM giangvien";
                                        $emptyRoomsResult = $conn->query($getEmptyRoomsSql);

                                        if ($emptyRoomsResult && $emptyRoomsResult->num_rows > 0) {
                                            echo '<select name="input4" class="rounded" style="padding: 2px 3px;">';
                                            while ($row = $emptyRoomsResult->fetch_assoc()) {
                                                echo '<option value="' . $row['idGiangVien'] . '">' . $row['idGiangVien'] . '</option>';
                                            }
                                            echo '</select>';
                                        } else {

                                        }
                                        ?>
                                    </div>
                                    <div class="d-flex flex-column ms-2 mt-2">
                                        <label for="">ID Phòng: </label>
                                        <?php
                                        $getEmptyRoomsSql = "SELECT idPhong FROM phonghoc";
                                        $emptyRoomsResult = $conn->query($getEmptyRoomsSql);

                                        if ($emptyRoomsResult && $emptyRoomsResult->num_rows > 0) {
                                            echo '<select name="input5" class="rounded" style="padding: 2px 3px;">';
                                            while ($row = $emptyRoomsResult->fetch_assoc()) {
                                                echo '<option value="' . $row['idPhong'] . '">' . $row['idPhong'] . '</option>';
                                            }
                                            echo '</select>';
                                        } else {
                                        }
                                        ?>
                                    </div>
                                    <div class="d-flex flex-column ms-2 mt-2">
                                        <label for="">Thời gian bắt đầu: </label>
                                        <input type="datetime-local" placeholder="Nhập số lượng"
                                            style="padding: 2px 3px" class="rounded" name="input6">
                                    </div>
                                    <div class="d-flex flex-column ms-2 mt-2">
                                        <label for="">Thời gian kết thúc: </label>
                                        <input type="datetime-local" placeholder="Nhập số lượng"
                                            style="padding: 2px 3px" class="rounded" name="input7">
                                    </div>
                                    <div class="d-flex  justify-content-between  ms-2 mt-3">
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