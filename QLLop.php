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
        $searchPHSql = "SELECT * FROM lop WHERE tenLop = '$name'";
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
// Xử lý sửa
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $idLop = $_POST["input1"];
    $nameLop = $_POST["input2"];
    $idKhoa = $_POST["input3"];

    // Kiểm tra xem ID Lớp đã tồn tại chưa (loại trừ lớp đang sửa)
    $checkDuplicateSql = "SELECT * FROM lop WHERE idLop = '$idLop'";
    $result = $conn->query($checkDuplicateSql);

    if ($result->num_rows > 0) {
        // ID Lớp đã tồn tại (trừ lớp đang sửa), thông báo lỗi
        $error = "ID Lớp đã tồn tại.";
    } else {
        // Thực hiện cập nhật khi không có trùng lặp
        $updatePHSql = "UPDATE lop SET tenLop = '$nameLop', idKhoa='$idKhoa'
                        WHERE idLop = '$idLop'";

        if ($conn->query($updatePHSql) === TRUE) {
            // Cập nhật thành công
            $succes = "Cập nhật thành công.";
        } else {
            // Lỗi khi cập nhật
            $error = "Lỗi: " . $conn->error;
        }
    }
}

// Xử lý thêm 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    $idLop = $_POST["input1"];
    $nameLop = $_POST["input2"];
    $idKhoa = $_POST["input3"];

    // Kiểm tra xem ID Lớp hoặc Tên Lớp đã tồn tại chưa
    $checkDuplicateSql = "SELECT * FROM lop WHERE idLop = '$idLop' OR tenLop = '$nameLop'";
    $result = $conn->query($checkDuplicateSql);

    if ($result->num_rows > 0) {
        // ID Lớp hoặc Tên Lớp đã tồn tại, thông báo lỗi
        $error = "ID Lớp hoặc Tên Lớp đã tồn tại.";
    } else {
        // Thực hiện thêm mới khi không có trùng lặp
        $insertPHSql = "INSERT INTO lop (idLop, tenLop, idKhoa) VALUES ('$idLop', '$nameLop', '$idKhoa')";

        if ($conn->query($insertPHSql) === TRUE) {
            // Thêm mới thành công
            $succes = "Thêm mới thành công.";
        } else {
            // Lỗi khi thêm mới
            $error = "Lỗi: " . $conn->error;
        }
    }
}

// Xử lý xóa 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])) {
    $idLop = $_POST["idLop"];

    $deletePHSql = "DELETE FROM lop WHERE idLop= '$idLop'";
    if ($conn->query($deletePHSql) === TRUE) {
    } else {
    }
}
$sql = mysqli_query($conn, "SELECT lop.idLop, lop.tenLop, 
lop.idKhoa, khoa.tenKhoa 
FROM lop 
join khoa on khoa.idKhoa = lop.idKhoa");
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
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-8 content-pane d-flex">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-8 table-responsive mt-3 ms-3 mb-3">
                        <table id="table"
                            class="col-xs-12 col-sm-12 col-md-12 col-lg-8 w-100 border-collapse text-center table">
                            <thead>
                                <tr class="table-dark text-white">
                                    <th>ID Lớp</th>
                                    <th>Tên Lớp</th>
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
                                                <?php echo $searchResult["idLop"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["tenLop"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["idKhoa"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["tenKhoa"] ?>
                                            </td>
                                            <td>
                                                <form action="" method='post'>
                                                    <input type="hidden" name="idLop"
                                                        value="<?php echo $searchResult["idLop"] ?>">
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
                                                <?php echo $row["idLop"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["tenLop"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["idKhoa"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["tenKhoa"] ?>
                                            </td>
                                            <td>
                                                <form action="" method='post'>
                                                    <input type="hidden" name="idLop" value="<?php echo $row["idLop"] ?>">
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
                                    <input type="text" name="search-name" placeholder="Nhập tên lớp"
                                        value="<?php echo $name ?>">
                                    <input type="submit" name="search" value="Tìm kiếm" class="input-style me-4">
                                </form>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-xs-4 col-sm-12 col-md-12 col-lg-10">
                                <form action="" method="post">
                                    <div class="d-flex flex-column ms-2">
                                        <label for="">ID lớp : </label>
                                        <input float="left" type="text" placeholder="Nhập ID lớp"
                                            style="padding: 2px 3px;" class="rounded" name="input1">
                                    </div>
                                    <div class="d-flex flex-column ms-2 mt-2">
                                        <label for="">Tên lớp: </label>
                                        <input type="text" placeholder="Nhập tên lớp" style="padding: 2px 3px;"
                                            class="rounded" name="input2">
                                    </div>
                                    <div class="d-flex flex-column ms-2 mt-2">
                                        <label for="">ID Khoa: </label>
                                        <?php
                                        $getEmptyRoomsSql = "SELECT idKhoa FROM khoa";
                                        $emptyRoomsResult = $conn->query($getEmptyRoomsSql);

                                        if ($emptyRoomsResult && $emptyRoomsResult->num_rows > 0) {
                                            echo '<select name="input3" class="rounded" style="padding: 2px 3px;">';
                                            while ($row = $emptyRoomsResult->fetch_assoc()) {
                                                echo '<option value="' . $row['idKhoa'] . '">' . $row['idKhoa'] . '</option>';
                                            }
                                            echo '</select>';
                                        } else {

                                        }
                                        ?>
                                    </div>
                                    <div class="ms-2 mt-2">
                                        <label for="" class="text-red">
                                            <?php
                                            echo $error;
                                            echo $succes;
                                            ?>
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