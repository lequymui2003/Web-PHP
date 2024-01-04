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


// Xử lý sửa
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["update"])) {
        $id = $_POST["input1"];
        $idMon = $_POST["input2"];
        $idLop = $_POST["input3"];
        $idGV = $_POST["input4"];
        $idPhong = $_POST["input5"];
        $TGBD = $_POST["input6"];
        $TGKT = $_POST["input7"];

        $checkUpdateSql = "SELECT * FROM xeplich 
            WHERE idPhong = '$idPhong' 
            AND ((thoiGianBatDau >= '$TGBD' AND thoiGianBatDau < '$TGKT') 
            OR (TgianKetThuc > '$TGBD' AND TgianKetThuc <= '$TGKT')) 
            AND id != '$id'"; // Loại trừ bản ghi hiện tại đang được sửa

        $result = $conn->query($checkUpdateSql);

        if ($result->num_rows > 0) {
            // echo '<script>alert("Thông báo: Phòng này đã có lịch trong khoảng thời gian này!");</script>';
            echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
            echo '<script>
                        $(document).ready(function(){
                            $("#notificationMessage").text("Phòng này đã có lịch trong khoảng thời gian này!");
                            $("#notificationModal").show();
                            $(".close").click(function(){
                            $("#notificationModal").hide();
                            });
                        });
                    </script>';
        } else {
            // Kiểm tra trùng lịch cho giảng viên
            $checkSql = "SELECT * FROM xeplich 
                WHERE idGV = '$idGV' 
                AND ((thoiGianBatDau >= '$TGBD' AND thoiGianBatDau < '$TGKT') 
                OR (TgianKetThuc > '$TGBD' AND TgianKetThuc <= '$TGKT')) 
                AND id != '$id'";

            $result = $conn->query($checkSql);

            if ($result->num_rows > 0) {
                echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
                echo '<script>
                    $(document).ready(function(){
                        $("#notificationMessage").text("Giảng viên này đã có lịch dạy trong khoảng thời gian này!");
                        $("#notificationModal").show();
                        $(".close").click(function(){
                        $("#notificationModal").hide();
                        });
                    });
                </script>';
            } else {
                // Kiểm tra trùng lịch cho lớp học
                $checkSql = "SELECT * FROM xeplich 
                    WHERE idLop = '$idLop' 
                    AND ((thoiGianBatDau >= '$TGBD' AND thoiGianBatDau < '$TGKT') 
                    OR (TgianKetThuc > '$TGBD' AND TgianKetThuc <= '$TGKT')) 
                    AND id != '$id'";

                $result = $conn->query($checkSql);

                if ($result->num_rows > 0) {
                    // echo '<script>alert("Thông báo: Lớp đã có lịch học trong khoảng thời gian này!");</script>';
                    echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
                    echo '<script>
                                $(document).ready(function(){
                                    $("#notificationMessage").text("Lớp này đã có lịch trong khoảng thời gian này!");
                                    $("#notificationModal").show();
                                    $(".close").click(function(){
                                    $("#notificationModal").hide();
                                    });
                                });
                            </script>';
                } else {
                    // Tiến hành cập nhật dữ liệu vào cơ sở dữ liệu
                    $updatePHSql = "UPDATE xeplich SET
                        idMon = '$idMon',
                        idLop = '$idLop',
                        idGV = '$idGV',
                        idPhong = '$idPhong',
                        thoiGianBatDau = '$TGBD',
                        TgianKetThuc = '$TGKT'
                    WHERE id = '$id'";

                    if ($conn->query($updatePHSql) === TRUE) {
                        echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
                        echo '<script>
                                    $(document).ready(function(){
                                        $("#notificationMessage").text("Sửa thành công!");
                                        $("#notificationModal").show();
                                        $(".close").click(function(){
                                        $("#notificationModal").hide();
                                        });
                                    });
                                </script>';
                    } else {

                    }
                }
            }
        }
    }
}


// chức năng thêm
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    // Lấy dữ liệu từ form thêm
    $id = $_POST["input1"];
    $idMon = $_POST["input2"];
    $idLop = $_POST["input3"];
    $idGV = $_POST["input4"];
    $idPhong = $_POST["input5"];
    $TGBD = $_POST["input6"];
    $TGKT = $_POST["input7"];

    // Kiểm tra xem giáo viên đã có lịch dạy trong khoảng thời gian này chưa
    $checkSql = "SELECT * FROM xeplich 
                 WHERE idGV = '$idGV' 
                 AND ((thoiGianBatDau >= '$TGBD' AND thoiGianBatDau < '$TGKT') 
                 OR (TgianKetThuc > '$TGBD' AND TgianKetThuc <= '$TGKT'))";

    $result = $conn->query($checkSql);

    // Kiểm tra số lượng hàng trả về
    if ($result->num_rows > 0) {
        // Nếu đã có lịch, hiển thị thông báo và ngăn chặn thêm dữ liệu
        // echo '<script>alert("Thông báo: Giáo viên này đã có lịch dạy trong khoảng thời gian này!");</script>';
        echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
        echo '<script>
                    $(document).ready(function(){
                        $("#notificationMessage").text("Giáo viên này đã có lịch dạy trong khoảng thời gian này!");
                        $("#notificationModal").show();
                        $(".close").click(function(){
                        $("#notificationModal").hide();
                        });
                    });
                </script>';
    } else {
        // Kiểm tra xem đã có lịch học trong khoảng thời gian này cho mã lớp đã chọn chưa
        $checkSql = "SELECT * FROM xeplich 
                     WHERE idLop = '$idLop' 
                     AND ((thoiGianBatDau >= '$TGBD' AND thoiGianBatDau < '$TGKT') 
                     OR (TgianKetThuc > '$TGBD' AND TgianKetThuc <= '$TGKT'))";

        $result = $conn->query($checkSql);

        // Kiểm tra số lượng hàng trả về
        if ($result->num_rows > 0) {
            // Nếu đã có lịch, hiển thị thông báo và ngăn chặn thêm dữ liệu
            // echo '<script>alert("Thông báo: Lớp này đã có lịch trong khoảng thời gian này!");</script>';
            echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
            echo '<script>
                        $(document).ready(function(){
                            $("#notificationMessage").text("Lớp này đã có lịch trong khoảng thời gian này!");
                            $("#notificationModal").show();
                            $(".close").click(function(){
                            $("#notificationModal").hide();
                            });
                        });
                    </script>';
        } else {
            // Kiểm tra xem đã có lịch học trong khoảng thời gian này cho mã phòng đã chọn chưa
            $checkSql = "SELECT * FROM xeplich 
                         WHERE idPhong = '$idPhong' 
                         AND ((thoiGianBatDau >= '$TGBD' AND thoiGianBatDau < '$TGKT') 
                         OR (TgianKetThuc > '$TGBD' AND TgianKetThuc <= '$TGKT'))";

            $result = $conn->query($checkSql);

            // Kiểm tra số lượng hàng trả về
            if ($result->num_rows > 0) {
                // Nếu đã có lịch, hiển thị thông báo và ngăn chặn thêm dữ liệu
                // echo '<script>alert("Thông báo: Phòng này đã có lịch trong khoảng thời gian này!");</script>';
                echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
                echo '<script>
                            $(document).ready(function(){
                                $("#notificationMessage").text("Phòng này đã có lịch trong khoảng thời gian này!");
                                $("#notificationModal").show();
                                $(".close").click(function(){
                                $("#notificationModal").hide();
                                });
                            });
                        </script>';
            } else {
                // Nếu không có lịch, thực hiện thêm dữ liệu vào cơ sở dữ liệu
                $insertPHSql = "INSERT INTO xeplich (id, idMon, idLop, idGV, idPhong, thoiGianBatDau, TgianKetThuc) 
                                VALUES ('$id','$idMon', '$idLop', '$idGV', '$idPhong','$TGBD', '$TGKT')";

                // Thực hiện câu lệnh INSERT và kiểm tra kết quả
                if ($conn->query($insertPHSql) === TRUE) {
                    // Thêm dữ liệu thành công
                    echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
                    echo '<script>
                                $(document).ready(function(){
                                    $("#notificationMessage").text("Thêm thành công!");
                                    $("#notificationModal").show();
                                    $(".close").click(function(){
                                    $("#notificationModal").hide();
                                    });
                                });
                            </script>';
                } else {
                    // Xử lý khi thêm dữ liệu thất bại
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
                                    <div class="d-flex flex-column ms-2 mt-2">
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
                                    <div class="d-flex ms-2 mt-2">
                                        <label for="">Bắt đầu: </label>
                                        <input type="datetime-local" placeholder="Nhập số lượng"
                                            style="padding: 2px 3px; margin-left: 8px" class="rounded w-75"
                                            name="input6">
                                    </div>
                                    <div class="d-flex ms-2 mt-2">
                                        <label for="">Kết thúc: </label>
                                        <input type="datetime-local" placeholder="Nhập số lượng"
                                            style="padding: 2px 3px" class="rounded w-75 ms-1" name="input7">
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
    <div id="notificationModal" class="modal">
        <div class="modal-content">
            <div class="d-flex justify-content-between align-items-end bg-warning ps-2 pe-2 rounded-top">
                <h4>Thông báo</h4>
                <span class="close fs-2 fw-bold">&times;</span>
            </div>
            <div class="mt-4 pe-2 ps-2 ">
                <p id="notificationMessage"></p>
            </div>
        </div>
    </div>
</main>
<!-- <?php
require_once 'footer.php';
?> -->