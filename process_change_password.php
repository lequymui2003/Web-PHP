<?php
include "./database/Class-Database.php";
global $conn;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["oldPassword"]) && isset($_POST["newPassword"]) && isset($_POST["confirmPassword"])) {
    $oldPassword = md5($_POST["oldPassword"]); // Mã hóa mật khẩu cũ bằng MD5
    $newPassword = $_POST["newPassword"];
    $confirmPassword = $_POST["confirmPassword"];

    // Xác định tên người dùng từ session hoặc cookie (tùy vào cách bạn lưu thông tin người dùng)
    $username = $_SESSION["name"]; // Đây là giả sử bạn lưu tên người dùng trong session

    // Truy vấn để lấy mật khẩu hiện tại của người dùng từ cơ sở dữ liệu
    $sql = "SELECT password FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $currentPassword = $row['password'];

        // Kiểm tra mật khẩu cũ có khớp với mật khẩu hiện tại không
        if ($oldPassword === $currentPassword) { // So sánh mật khẩu cũ đã mã hóa với mật khẩu trong cơ sở dữ liệu
            // Kiểm tra mật khẩu mới và xác nhận mật khẩu có khớp nhau không
            if ($newPassword === $confirmPassword) {
                // Mã hóa mật khẩu mới bằng MD5 trước khi cập nhật
                $newPasswordMd5 = md5($newPassword);

                // Cập nhật mật khẩu mới cho người dùng
                $updateSql = "UPDATE users SET password = '$newPasswordMd5' WHERE username = '$username'";
                if (mysqli_query($conn, $updateSql)) {
                    echo "success"; // Trả về thông báo thành công
                    exit();
                } else {
                    echo "Lỗi khi cập nhật mật khẩu: " . mysqli_error($conn);
                    exit();
                }
            } else {
                echo "Xác nhận mật khẩu không khớp";
                exit();
            }
        } else {
            echo "Mật khẩu cũ không đúng";
            exit();
        }
    } else {
        echo "Không tìm thấy người dùng";
        exit();
    }
} else {
    echo "Yêu cầu không hợp lệ";
    exit();
}

?>