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
     $specialCharsPattern = "/[!@#\$%\^\&*()-]/";
    if (!empty($name)) {
        // Xử lý tìm kiếm theo tên phòng
        $searchPHSql = "SELECT * FROM khoa WHERE tenKhoa = '$name'";
        $result = $conn->query($searchPHSql);
        if (preg_match($specialCharsPattern, $name)) {
            $error = "Không nhập kí tự đặc biệt.";
        } else{
            if ($result) {
            if (mysqli_num_rows($result) > 0) {
                // Lưu kết quả tìm kiếm vào một mảng
                $searchResults = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $searchResults[] = $row;
                }
            } 
        } else {
            echo "Lỗi: " . $conn->error;
        }
        }
        
    }
}

// Xử lý sửa
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $idKhoa = $_POST["input1"];
    $nameKhoa = $_POST["input2"];
    // Biểu thức chính quy để kiểm tra ký tự đặc biệt
    $specialCharsPattern = "/[!@#\$%\^\&*()-]/";
    // Kiểm tra xem Tên Khoa đã tồn tại chưa (loại trừ khoa đang sửa)
    $checkDuplicateSql = "SELECT * FROM khoa WHERE tenKhoa = '$nameKhoa' AND idKhoa != '$idKhoa'";
    $result = $conn->query($checkDuplicateSql);
    if ($idKhoa == "" || $nameKhoa == "") {
        $error = "Mời bạn chọn khoa cần sửa";
    } else {
        // Kiểm tra xem có ký tự đặc biệt trong id hoặc name không
        if (preg_match($specialCharsPattern, $idKhoa) || preg_match($specialCharsPattern, $nameKhoa)) {
            $error = "Không nhập kí tự đặc biệt.";
        } else {
            if ($result->num_rows > 0) {
                // Tên Khoa đã tồn tại (trừ khoa đang sửa), thông báo lỗi
                $error = "Tên Khoa đã tồn tại.";
            } else {
                // Thực hiện cập nhật khi không có trùng lặp
                $updatePHSql = "UPDATE khoa SET tenKhoa = '$nameKhoa' WHERE idKhoa = '$idKhoa'";

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
    $idKhoa = $_POST["input1"];
    $nameKhoa = $_POST["input2"];
    // Biểu thức chính quy để kiểm tra ký tự đặc biệt
    $specialCharsPattern = "/[!@#\$%\^\&*()-]/";
    // Kiểm tra xem ID hoặc Tên Khoa đã tồn tại chưa
    $checkDuplicateSql = "SELECT * FROM khoa WHERE idKhoa = '$idKhoa' OR tenKhoa = '$nameKhoa'";
    $result = $conn->query($checkDuplicateSql);
    if ($idKhoa == "" || $nameKhoa == "") {
        $error = "Mời nhập đầy đủ thông tin";
    } else {
        // Kiểm tra xem có ký tự đặc biệt trong id hoặc name không
        if (preg_match($specialCharsPattern, $idKhoa) || preg_match($specialCharsPattern, $nameKhoa)) {
            $error = "Không nhập kí tự đặc biệt.";
        } else {
            if ($result->num_rows > 0) {
                // ID hoặc Tên Khoa đã tồn tại, thông báo lỗi
                $error = "ID hoặc Tên Khoa đã tồn tại.";
            } else {
                // Thực hiện thêm mới khi không có trùng lặp
                $insertPHSql = "INSERT INTO khoa (idKhoa, tenKhoa) VALUES ('$idKhoa', '$nameKhoa')";

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
}

// Xử lý xóa 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])) {
    $idkhoa = $_POST["idKhoa"];

    $deletePHSql = "DELETE FROM khoa WHERE idKhoa= '$idkhoa'";
    if ($conn->query($deletePHSql) === TRUE) {
    } else {
    }
}
$sql = mysqli_query($conn, "SELECT * FROM khoa");
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
                                                <?php echo $searchResult["idKhoa"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["tenKhoa"] ?>
                                            </td>
                                            <td>
                                                <form action="" method='post'>
                                                    <input type="hidden" name="idKhoa"
                                                        value="<?php echo $searchResult["idKhoa"] ?>">
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
                                                <?php echo $row["idKhoa"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["tenKhoa"] ?>
                                            </td>
                                            <td>
                                                <form action="" method='post'>
                                                    <input type="hidden" name="idKhoa" value="<?php echo $row["idKhoa"] ?>">
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
                                    <input type="text" name="search-name" placeholder="Nhập tên phòng học"
                                        value="<?php echo $name ?>">
                                    <input type="submit" name="search" value="Tìm kiếm" class="input-style me-4">
                                </form>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-xs-4 col-sm-12 col-md-12 col-lg-10">
                                <form action="" method="post">
                                    <div class="d-flex flex-column ms-2">
                                        <label for="">ID Khoa : </label>
                                        <input float="left" type="text" placeholder="Nhập ID khoa"
                                            style="padding: 2px 3px;" class="rounded" name="input1">
                                    </div>
                                    <div class="d-flex flex-column ms-2 mt-2">
                                        <label for="">Tên Khoa: </label>
                                        <input type="text" placeholder="Nhập tên khoa"
                                            style="padding: 2px 3px; text-align:left;" class="rounded" name="input2">
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