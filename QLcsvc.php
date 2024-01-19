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
        $searchPHSql = "SELECT cosovatchat.ten, ctcosovatchat.id,
        ctcosovatchat.idPhong, ctcosovatchat.SoLuongTot, ctcosovatchat.SoLuongXau, ctcosovatchat.stt
        FROM ctcosovatchat
        join cosovatchat on ctcosovatchat.id = cosovatchat.id WHERE idPhong = '$name'";
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
    $id = $_POST["input1"];
    $SLT = $_POST["input3"];
    $SLX = $_POST["input4"];
    $idPhong = $_POST["input5"];

    // Kiểm tra xem ID hoặc ID Phòng đã tồn tại chưa (loại trừ dòng đang sửa)
    $checkDuplicateSql = "SELECT * FROM ctcosovatchat WHERE (id = '$id' OR idPhong = '$idPhong') AND NOT (id = '$id' AND idPhong = '$idPhong')";
    $result = $conn->query($checkDuplicateSql);

    if ($result->num_rows > 0) {
        // ID hoặc ID Phòng đã tồn tại (loại trừ dòng đang sửa), thông báo lỗi
        $error = "ID hoặc ID Phòng đã tồn tại.";
    } else {
        // Thực hiện cập nhật khi không có trùng lặp
        $updatePHSql = "UPDATE ctcosovatchat SET SoLuongTot = '$SLT', 
                        SoLuongXau = '$SLX' WHERE id = '$id' AND idPhong = '$idPhong'";

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
    $id = $_POST["input1"];
    $SLT = $_POST["input3"];
    $SLX = $_POST["input4"];
    $idPhong = $_POST["input5"];

    // Kiểm tra xem ID hoặc ID Phòng đã tồn tại chưa
    $checkDuplicateSql = "SELECT * FROM ctcosovatchat WHERE id = '$id' and idPhong = '$idPhong'";
    $result = $conn->query($checkDuplicateSql);

    if ($result->num_rows > 0) {
        // ID hoặc ID Phòng đã tồn tại, thông báo lỗi
        $error = "ID hoặc ID Phòng đã tồn tại.";
    } else {
        // Thực hiện thêm mới khi không có trùng lặp
        $insertPHSql = "INSERT INTO ctcosovatchat (id, SoLuongTot, SoLuongXau, idPhong) 
                        VALUES ('$id', '$SLT', '$SLX', '$idPhong')";

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
    $id = $_POST["ID"];

    $deletePHSql = "DELETE FROM ctcosovatchat WHERE stt= '$id'";
    if ($conn->query($deletePHSql) === TRUE) {
    } else {
    }
}
?>
<?php
$sql = mysqli_query($conn, "SELECT cosovatchat.ten, ctcosovatchat.id,
ctcosovatchat.idPhong, ctcosovatchat.SoLuongTot, ctcosovatchat.SoLuongXau, ctcosovatchat.stt
FROM ctcosovatchat
join cosovatchat on ctcosovatchat.id = cosovatchat.id");
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
                                    <th>ID</th>
                                    <th>Tên</th>
                                    <th>Số lượng tốt</th>
                                    <th>Số lượng xấu</th>
                                    <th>ID Phòng</th>
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
                                                <?php echo $searchResult["ten"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["SoLuongTot"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["SoLuongXau"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["idPhong"] ?>
                                            </td>
                                            <td>
                                                <form action="" method='post'>
                                                    <input type="hidden" name="ID" value="<?php echo $searchResult["stt"] ?>">
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
                                                <?php echo $row["ten"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["SoLuongTot"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["SoLuongXau"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["idPhong"] ?>
                                            </td>
                                            <td>
                                                <form action="" method='post'>
                                                    <input type="hidden" name="ID" value="<?php echo $row["stt"] ?>">
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
                                    <input type="text" name="search-name" placeholder="Nhập ID phòng học"
                                        value="<?php echo $name ?>">
                                    <input type="submit" name="search" value="Tìm kiếm" class="input-style me-4">
                                </form>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-xs-4 col-sm-12 col-md-12 col-lg-10">
                                <form action="" method="post">
                                    <div class="d-flex flex-column ms-2">
                                        <label for="">ID: </label>
                                        <select id="input1" name="input1" class="rounded" style="padding: 2px 3px;">
                                            <?php
                                            // Hiển thị danh sách ID từ CSDL
                                            $sql = "SELECT id FROM cosovatchat";
                                            $result = $conn->query($sql);
                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $id = $row['id'];
                                                    echo '<option value="' . $id . '">' . $id . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="d-flex flex-column ms-2">
                                        <label for="">Tên: </label>
                                        <input id="input2" float="left" type="text" placeholder="Nhập tên"
                                            style="padding: 2px 3px;" class="rounded" name="input2"
                                            value="<?php echo isset($selectedName) ? $selectedName : ''; ?>">
                                    </div>
                                    <div class="d-flex flex-column ms-2 mt-2">
                                        <label for="">Số lượng tốt: </label>
                                        <input type="text" placeholder="Nhập số lượng" style="padding: 2px 3px"
                                            class="rounded" name="input3">
                                    </div>
                                    <div class="d-flex flex-column ms-2 mt-2">
                                        <label for="">Số lượng xấu: </label>
                                        <input type="text" placeholder="Nhập số lượng" style="padding: 2px 3px"
                                            class="rounded" name="input4">
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