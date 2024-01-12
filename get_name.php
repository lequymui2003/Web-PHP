<?php
include "./database/Class-Database.php";
global $conn;

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
    $selectedId = $_GET["id"];

    // Thực hiện truy vấn để lấy tên từ ID
    $query = "SELECT ten FROM cosovatchat WHERE id = '$selectedId'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo $row['ten'];
    } else {
        echo "Không có tên";
    }
} else {
    echo "Lỗi: Yêu cầu không hợp lệ";
}
?>