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
$error = "";
$name = "";
if (isset($_POST['search'])) {
    $searchPHSql = "SELECT * FROM phonghoc WHERE tinhTrang = 'Được sử dụng'";
    $result = $conn->query($searchPHSql);
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            // Lưu kết quả tìm kiếm vào một mảng
            $searchResults1 = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $searchResults1[] = $row;
            }
        }
    }
} elseif (isset($_POST['search2'])) {
    $searchPHSql = "SELECT * FROM phonghoc WHERE tinhTrang = 'Đang bảo trì'";
    $result = $conn->query($searchPHSql);
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            // Lưu kết quả tìm kiếm vào một mảng
            $searchResults1 = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $searchResults1[] = $row;
            }
        } else {

        }
    }
} elseif (isset($_POST['search3'])) {
    $name = $_POST["search-name"];
    // Biểu thức chính quy để kiểm tra ký tự đặc biệt
    $specialCharsPattern = "/[!@#\$%\^\&*()]/";
    if (!empty($name)) {
        // Xử lý tìm kiếm theo tên phòng
        $searchPHSql = "SELECT cosovatchat.ten, ctcosovatchat.SoLuongTot, ctcosovatchat.SoLuongXau, ctcosovatchat.idPhong, phonghoc.tenPhong
                        FROM  ctcosovatchat
                        join cosovatchat on cosovatchat.id = ctcosovatchat.id
                        join phonghoc on ctcosovatchat.idPhong = phonghoc.idPhong
                        WHERE phonghoc.idPhong = '$name'";
        $result = $conn->query($searchPHSql);
        if (preg_match($specialCharsPattern, $name)) {
            $error = "Không nhập kí tự đặc biệt.";
        } else {
            if ($result) {
                if (mysqli_num_rows($result) > 0) {
                    // Lưu kết quả tìm kiếm vào một mảng
                    $searchResults2 = [];
                    while ($row = mysqli_fetch_assoc($result)) {
                        $searchResults2[] = $row;
                    }
                }
            }
        }

    }
}

$sql = mysqli_query($conn, "SELECT * FROM phonghoc");
if (mysqli_num_rows($sql) === 0) {
}

$sql2 = mysqli_query($conn, "SELECT cosovatchat.ten, ctcosovatchat.SoLuongTot,
ctcosovatchat.SoLuongXau, ctcosovatchat.idPhong, phonghoc.tenPhong
FROM  ctcosovatchat
join cosovatchat on cosovatchat.id = ctcosovatchat.id
join phonghoc on ctcosovatchat.idPhong = phonghoc.idPhong");
if (mysqli_num_rows($sql2) === 0) {
}
?>
<?php
require_once 'header.php';
?>
<main>
    <section class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-8 content-pane">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex">
                            <div
                                class="col-xs-12 col-sm-12 col-md-12 col-lg-8 table-responsive mt-3 ms-3 mb-3  mx-h-230">
                                <table id="table"
                                    class="col-xs-12 col-sm-12 col-md-12 col-lg-8 w-100 border-collapse text-center table">
                                    <thead>
                                        <tr class="table-dark text-white">
                                            <th>ID Phòng</th>
                                            <th>Tên Phòng</th>
                                            <th>Tình trạng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 0;
                                        if (!empty($searchResults1)) {
                                            foreach ($searchResults1 as $searchResult) {
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
                                                        <?php echo $searchResult["tinhTrang"] ?>
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
                                                        <?php echo $row["tinhTrang"] ?>
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
                                <div class="row d-flex justify-content-center">
                                    <div class="col-xs-4 col-sm-12 col-md-12 col-lg-6">
                                        <form method="post" action="" class="d-flex justify-content-around form-search">
                                            <input type="submit" name="search" value="Phòng sử dụng"
                                                class="input-style me-4">
                                        </form>
                                    </div>
                                    <div class="col-xs-4 col-sm-12 col-md-12 col-lg-6">
                                        <form method="post" action="" class="d-flex justify-content-around form-search">
                                            <input type="submit" name="search2" value="Phòng bảo trì"
                                                class="input-style me-5">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex">
                            <div
                                class="col-xs-12 col-sm-12 col-md-12 col-lg-8 table-responsive mt-3 ms-3 mb-3 mx-h-200">
                                <table id="table"
                                    class="col-xs-12 col-sm-12 col-md-12 col-lg-8 w-100 border-collapse text-center table">
                                    <thead>
                                        <tr class="table-dark text-white">
                                            <th>ID Phòng</th>
                                            <th>Tên Phòng</th>
                                            <th>Tên dụng cụ</th>
                                            <th>Số lượng tốt</th>
                                            <th>Số lượng xấu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 0;
                                        if (!empty($searchResults2)) {
                                            foreach ($searchResults2 as $searchResult) {
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
                                                        <?php echo $searchResult["ten"] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $searchResult["SoLuongTot"] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $searchResult["SoLuongXau"] ?>
                                                    </td>
                                                </tr>
                                                <?php
                                                $i++;
                                            }
                                        } elseif (mysqli_num_rows($sql2) > 0) {
                                            ?>
                                            <?php
                                            $i = 0;
                                            while ($row = mysqli_fetch_assoc($sql2)) {
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
                                                        <?php echo $row["ten"] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row["SoLuongTot"] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row["SoLuongXau"] ?>
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
                                <div class="row d-flex justify-content-center">
                                    <div class="col-xs-4 col-sm-12 col-md-12 col-lg-12">
                                        <form method="post" action="" class="d-flex justify-content-around form-search">
                                            <input type="text" name="search-name" placeholder="Nhập ID phòng"
                                                value="<?php echo $name ?>">
                                            <input type="submit" name="search3" value="Tìm kiếm"
                                                class="input-style me-4">
                                        </form>
                                        <div class="ms-2 mt-2">
                                                <label for="" class="text-red">
                                                    <?php
                                                    echo $error;
                                                    ?>
                                                </label>
                                            </div>
                                    </div>
                                </div>
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