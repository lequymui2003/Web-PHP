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

$error = "";
$succes = "";
// Xử lý sửa
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["update"])) {
        $idKhoa = $_POST["input1"];
        $idMon = $_POST["input3"];
        $idLop = $_POST["input2"];
        $idGV = $_POST["input4"];
        $idPhong = $_POST["input5"];
        $date = $_POST["input6"];
        $time = $_POST["input7"];

        $result = mysqli_query($conn, "SELECT * FROM xeplich");
        $row = mysqli_fetch_assoc($result);
        $id = $row['id'];

        $checkUpdateSql = "SELECT * FROM xeplich 
            WHERE idPhong = '$idPhong' 
            AND (Date = '$date' AND ThoiGian = '$time') 
            AND id != '$id'"; // Loại trừ bản ghi hiện tại đang được sửa

        $result = $conn->query($checkUpdateSql);

        if ($idMon == "" || $idLop == "" || $idGV == "" || $date == "" || $time == "") {
            $error = "Mời bạn chọn lịch học cần sửa và nhập đầy đủ thông tin";
        } else {
            if ($result->num_rows > 0) {
                $error = "Phòng này đã có lịch trong khoảng thời gian này!";

            } else {
                // Kiểm tra trùng lịch cho giảng viên
                $checkSql = "SELECT * FROM xeplich 
                    WHERE idGV = '$idGV' 
                    AND (Date = '$date' AND ThoiGian = '$time') 
                    AND id != '$id'";

                $result = $conn->query($checkSql);

                if ($result->num_rows > 0) {
                    $error = "Giảng viên này đã có lịch trong khoảng thời gian này!";
                } else {
                    // Kiểm tra trùng lịch cho lớp học
                    $checkSql = "SELECT * FROM xeplich 
                        WHERE idLop = '$idLop' 
                        AND (Date = '$date' AND ThoiGian = '$time') 
                        AND id != '$id'";

                    $result = $conn->query($checkSql);

                    if ($result->num_rows > 0) {
                        $error = "Lớp đã có lịch học trong khoảng thời gian này!";

                    } else {
                        // Tiến hành cập nhật dữ liệu vào cơ sở dữ liệu
                        $updatePHSql = "UPDATE xeplich SET
                            idMon = '$idMon',
                            idLop = '$idLop',
                            idGV = '$idGV',
                            idPhong = '$idPhong',
                            idKhoa = '$idKhoa',
                            Date = '$date',
                            ThoiGian = '$time'
                        WHERE id = '$id'";

                        if ($conn->query($updatePHSql) === TRUE) {
                            $succes = "Cập nhật thông tin lịch học thành công";
                        } else {
                            $error = "Sửa lịch học thất bại";
                        }
                    }
                }
            }
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
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-9 table-responsive mt-3 ms-3 mb-3 flex-wrap">
                        <table id="table"
                            class="col-xs-12 col-sm-12 col-md-12 col-lg-8 w-100 border-collapse text-center table">
                            <thead>
                                <tr class="table-dark text-white">
                                    <!-- <th>ID </th> -->
                                    <th>ID Môn</th>
                                    <th>ID Lớp</th>
                                    <th>ID Giảng viên</th>
                                    <th>ID Phòng</th>
                                    <th>ID Khoa</th>
                                    <th>Ngày</th>
                                    <th>Thời gian</th>
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
                                                <?php echo $searchResult["idKhoa"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["Date"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["ThoiGian"] ?>
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
                                                <?php echo $row["idKhoa"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["Date"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["ThoiGian"] ?>
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

                    <div class="col-xs-4 col-sm-12 col-md-12 col-lg-3 mt-3 ">
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
                                    <div class="d-flex justify-content-between ms-2 mt-2">
                                        <label for="">ID Khoa: </label>
                                        <?php
                                        $getEmptyRoomsSql = "SELECT idKhoa FROM khoa";
                                        $emptyRoomsResult = $conn->query($getEmptyRoomsSql);
                                        if ($emptyRoomsResult && $emptyRoomsResult->num_rows > 0) {
                                            echo '<select id="input1" name="input1" class="rounded w-75" style="padding: 2px 3px;">';
                                            while ($row = $emptyRoomsResult->fetch_assoc()) {
                                                echo '<option value="' . $row['idKhoa'] . '">' . $row['idKhoa'] . '</option>';
                                            }
                                            echo '</select>';
                                        } else {
                                        }
                                        ?>
                                    </div>
                                    <div class="d-flex justify-content-between  ms-2 mt-2">
                                        <label for="">ID Lớp: </label>
                                        <select id="input2" name="input2" class="rounded w-75"
                                            style="padding: 2px 3px;">
                                        </select>
                                    </div>
                                    <div class="d-flex justify-content-between  ms-2 mt-2" id="monDropdown">
                                        <label for="">ID Môn: </label>
                                        <select id="input3" name="input3" class="rounded w-75"
                                            style="padding: 2px 3px;"></select>
                                    </div>
                                    <div class="d-flex justify-content-between  ms-2 mt-2" id="giangVienDropdown">
                                        <label for="">ID Giảng viên: </label>
                                        <select id="input4" name="input4" class="rounded w-50"
                                            style="padding: 2px 3px;">
                                        </select>
                                    </div>
                                    <div class="d-flex justify-content-between  ms-2 mt-2">
                                        <label for="">ID Phòng: </label>
                                        <?php
                                        $getEmptyRoomsSql = "SELECT idPhong FROM phonghoc";
                                        $emptyRoomsResult = $conn->query($getEmptyRoomsSql);

                                        if ($emptyRoomsResult && $emptyRoomsResult->num_rows > 0) {
                                            echo '<select name="input5" class="rounded w-50" style="padding: 2px 3px;">';
                                            while ($row = $emptyRoomsResult->fetch_assoc()) {
                                                echo '<option value="' . $row['idPhong'] . '">' . $row['idPhong'] . '</option>';
                                            }
                                            echo '</select>';
                                        } else {
                                        }
                                        ?>
                                    </div>
                                    <div class="d-flex ms-2 mt-2">
                                        <label for="">Ngày: </label>
                                        <input type="date" style="padding: 2px 3px; margin-left: 30px"
                                            class="rounded w-75" name="input6">
                                    </div>
                                    <div class="d-flex justify-content-between ms-2 mt-2">
                                        <label for="">Thời gian: </label>
                                        <select style="padding: 2px 3px; margin-left: 50px" class="rounded w-50"
                                            name="input7">
                                            <option value="Ca 1">Ca 1</option>
                                            <option value="Ca 2">Ca 2</option>
                                            <option value="Ca 3">Ca 3</option>
                                            <option value="Ca 4">Ca 4</option>
                                        </select>
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
                                    <div class="d-flex  justify-content-between  ms-2 mt-2">
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