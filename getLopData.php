<?php
include "./database/Class-Database.php";
global $conn;
// Lấy giá trị idKhoa từ yêu cầu POST
if (isset($_POST['idKhoa'])) {
    $idKhoa = $_POST['idKhoa'];

    /// ...
// Sử dụng prepared statement để bảo vệ giá trị
    $getLopSql = "SELECT idLop FROM lop WHERE idKhoa = ?";
    $stmt = $conn->prepare($getLopSql);
    $stmt->bind_param("s", $idKhoa); // "s" đại diện cho kiểu dữ liệu string, bạn có thể điều chỉnh nếu cần thiết
    $stmt->execute();

    $lopResult = $stmt->get_result();

    // Xây dựng danh sách tùy chọn cho dropdown "ID Lớp"
    $options = "";
    while ($row = $lopResult->fetch_assoc()) {
        $options .= '<option value="' . $row['idLop'] . '">' . $row['idLop'] . '</option>';
    }

    // Trả về danh sách tùy chọn
    echo $options;

    // Đóng prepared statement
    $stmt->close();
}
// ...
