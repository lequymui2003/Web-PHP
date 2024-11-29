<?php
include "./database/Class-Database.php";
global $conn;
// if (!isset($_SESSION["login"]) || empty($_SESSION["login"])) {
//     header("Location: login.php");
//     exit();
// }

// if ($_SESSION["login"] !== "admin") {
//     header("Location: user.php");
//     exit();
// }
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
$error = "";
$succes = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"])) {
    $name = $_POST["search-name"];
    // Biểu thức chính quy để kiểm tra ký tự đặc biệt
    $specialCharsPattern = "/[!@#\$%\^\&*()-]/";
    if (!empty($name)) {
        // Xử lý tìm kiếm theo tên phòng
        $searchPHSql = "SELECT * FROM users WHERE role = '$name'";
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
                    // echo "Không tìm thấy kết quả.";
                }
            } else {
                echo "Lỗi: " . $conn->error;
            }
        }

    }
}

// Xử lý sửa
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $username = $_POST["input1"];
    $password = md5($_POST["input2"]);
    $name = $_POST["input3"];
    $email = $_POST["input4"];
    
    // Biểu thức chính quy để kiểm tra ký tự đặc biệt
    $specialCharsPattern = "/[!@#\$%\^\&*()-]/";

    // Kiểm tra xem Username đã tồn tại chưa (loại trừ user đang sửa)
    $checkDuplicateSql = "SELECT * FROM users WHERE username = '$username' AND username != '$username'";
    $result = $conn->query($checkDuplicateSql);

    // Kiểm tra xem Email đã tồn tại chưa (loại trừ user đang sửa)
    $checkEmailSql = "SELECT * FROM users WHERE email = '$email' AND username != '$username'";
    $resultEmail = $conn->query($checkEmailSql);

    // Kiểm tra định dạng Email
    $isValidEmail = preg_match('/^[a-zA-Z0-9](\.?[a-zA-Z0-9]){5,}@gmail\.com$/', $email);

    // Kiểm tra điều kiện rỗng
    if (empty($username) || empty($password) || empty($name) || empty($email)) {
        $error = "Mời nhập đầy đủ thông tin.";
    } elseif (preg_match($specialCharsPattern, $username) || preg_match($specialCharsPattern, $name)) {
        // Kiểm tra ký tự đặc biệt
        $error = "Không nhập kí tự đặc biệt.";
    } elseif ($result->num_rows > 0) {
        // Username đã tồn tại (loại trừ user đang sửa)
        $error = "Username đã tồn tại.";
    } elseif ($resultEmail->num_rows > 0) {
        // Email đã tồn tại (loại trừ user đang sửa)
        $error = "Email đã tồn tại.";
    } elseif (!$isValidEmail) {
        // Email không hợp lệ
        $error = "Email không đúng định dạng.";
    } else {
        // Thực hiện cập nhật khi tất cả kiểm tra đều hợp lệ
        $updatePHSql = "UPDATE users SET email = '$email', Name = '$name'
                        WHERE username = '$username'";
        if ($conn->query($updatePHSql) === TRUE) {
            $succes = "Cập nhật thành công.";
        } else {
            $error = "Lỗi: " . $conn->error;
        }
    }
}


// Xử lý thêm 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    $username = $_POST["input1"];
    $password = md5($_POST["input2"]);
    $name = $_POST["input3"];
    $email = $_POST["input4"];
    // $phanquyen = $_POST["input5"];
    // Biểu thức chính quy để kiểm tra ký tự đặc biệt
    $specialCharsPattern = "/[!@#\$%\^\&*()-]/";
    // Kiểm tra xem Username hoặc Email đã tồn tại chưa
    $checkDuplicateSql = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $result = $conn->query($checkDuplicateSql);
    // Kiểm tra định dạng Email
    $isValidEmail = preg_match('/^[a-zA-Z0-9](\.?[a-zA-Z0-9]){5,}@gmail\.com$/', $email);
    //    if (empty($username) || empty($_POST["input2"]) || empty($name) || empty($email)) {
    if (empty($name) || empty($email)) {
        $error = "Mời nhập đầy đủ thông tin";
    } else {
        // Kiểm tra xem có ký tự đặc biệt trong id hoặc name không
        if (
            preg_match($specialCharsPattern, $username) || preg_match($specialCharsPattern, $password)
            || preg_match($specialCharsPattern, $name)
        ) {
            $error = "Không nhập kí tự đặc biệt.";
        } else {
            if ($result->num_rows > 0) {
                // Username hoặc Email đã tồn tại, thông báo lỗi
                $error = "Username hoặc Email đã tồn tại.";
            } elseif (!$isValidEmail) {
                // Email không hợp lệ, thông báo lỗi
                $error = "Email không đúng định dạng.";
            } else {
                // Thực hiện thêm mới khi không có trùng lặp và Email hợp lệ
                $insertPHSql = "INSERT INTO users (username, password, Name, email) 
                        VALUES ('$username', '$password', '$name', '$email')";

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
    $username = $_POST["username"];

    $deletePHSql = "DELETE FROM users WHERE username= '$username'";
    if ($conn->query($deletePHSql) === TRUE) {
    } else {
    }
}
$sql = mysqli_query($conn, "SELECT * FROM users");
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
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Họ và tên</th>
                                    <th>Email</th>
                                    <th>Phân quyền</th>
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
                                                <?php echo $searchResult["username"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["password"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["Name"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["email"] ?>
                                            </td>
                                            <td>
                                                <?php echo $searchResult["role"] ?>
                                            </td>
                                            <td>
                                                <form action="" method='post'>
                                                    <input type="hidden" name="username"
                                                        value="<?php echo $searchResult["username"] ?>">
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
                                                <?php echo $row["username"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["password"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["Name"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["email"] ?>
                                            </td>
                                            <td>
                                                <?php echo $row["role"] ?>
                                            </td>
                                            <td>
                                                <form action="" method='post'>
                                                    <input type="hidden" name="username" value="<?php echo $row["username"] ?>">
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
                                    <input type="text" name="search-name" placeholder="Nhập phân quyền"
                                        value="<?php echo $name ?>">
                                    <input type="submit" name="search" value="Tìm kiếm" class="input-style me-4">
                                </form>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-xs-4 col-sm-12 col-md-12 col-lg-10">
                                <form action="" method="post">
                                    <div class="d-flex flex-column ms-2">
                                        <label for="">Username : </label>
                                        <input float="left" type="text" placeholder="Nhập mã nhân viên"
                                            style="padding: 2px 3px;" class="rounded" name="input1">
                                    </div>
                                    <div class="d-flex flex-column ms-2 mt-2">
                                        <label for="">Password: </label>
                                        <input type="password" placeholder="Nhập mật khẩu"
                                            style="padding: 2px 3px; text-align:left;" class="rounded" name="input2">
                                    </div>
                                    <div class="d-flex flex-column ms-2 mt-2">
                                        <label for="">Họ và tên: </label>
                                        <input type="text" placeholder="Nhập tên"
                                            style="padding: 2px 3px; text-align:left;" class="rounded" name="input3">
                                    </div>
                                    <div class="d-flex flex-column ms-2 mt-2">
                                        <label for="">Email: </label>
                                        <input type="text" placeholder="Nhập Email" style="padding: 2px 3px"
                                            class="rounded" name="input4">
                                    </div>
                                    <!-- <div class="d-flex flex-column ms-2 mt-2">
                                        <label for="">Phân quyền: </label>
                                        <select name="input5" style="padding: 2px 3px" class="rounded">
                                            <option value="admin">admin</option>
                                            <option value="user">user</option>
                                        </select>
                                    </div> -->
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