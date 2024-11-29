-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 25, 2024 lúc 04:24 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `db_phonghoc`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cosovatchat`
--

CREATE TABLE `cosovatchat` (
  `id` char(5) NOT NULL,
  `ten` varchar(50) NOT NULL,
  `tong` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cosovatchat`
--

INSERT INTO `cosovatchat` (`id`, `ten`, `tong`) VALUES
('CS01', 'Ghế', 10000),
('CS02', 'Bàn', 5000),
('CS03', 'Máy chiếu', 500),
('CS04', 'Bóng điện', 2000),
('CS05', 'Điều hoà', 500),
('CS06', 'Bảng', 500),
('CS07', 'Màn hình chiếu', 500);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ctcosovatchat`
--

CREATE TABLE `ctcosovatchat` (
  `stt` int(10) NOT NULL,
  `SoLuongTot` int(10) NOT NULL,
  `SoLuongXau` int(10) NOT NULL,
  `id` char(5) NOT NULL,
  `idPhong` char(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `ctcosovatchat`
--

INSERT INTO `ctcosovatchat` (`stt`, `SoLuongTot`, `SoLuongXau`, `id`, `idPhong`) VALUES
(24, 20, 2, 'CS01', 'P001'),
(26, 20, 5, 'CS02', 'P002'),
(27, 20, 20, 'CS02', 'P001'),
(28, 20, 30, 'CS01', 'P002'),
(29, 20, 10, 'CS01', 'P003'),
(30, 20, 5, 'CS04', 'P004');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `giangvien`
--

CREATE TABLE `giangvien` (
  `idGiangVien` char(5) NOT NULL,
  `sdt` varchar(10) NOT NULL,
  `tenGV` varchar(50) NOT NULL,
  `idKhoa` char(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `giangvien`
--

INSERT INTO `giangvien` (`idGiangVien`, `sdt`, `tenGV`, `idKhoa`) VALUES
('GV01', '0934734734', 'Nguyễn Xuân Phong', 'K02'),
('GV02', '0967837352', 'Lê Quý Mùi', 'K02'),
('GV03', '0955748498', 'Nguyễn Ngọc Minh', 'K03'),
('GV04', '0946573868', 'Nguyễn Văn Lâm', 'K04'),
('GV05', '0902898239', 'Nguyễn Thùy Dương', 'K01');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khoa`
--

CREATE TABLE `khoa` (
  `idKhoa` char(5) NOT NULL,
  `tenKhoa` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `khoa`
--

INSERT INTO `khoa` (`idKhoa`, `tenKhoa`) VALUES
('K01', 'Công nghệ thông tin'),
('K02', 'Du lịch'),
('K03', 'Kế toán'),
('K04', 'Quản trị kinh doanh');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lop`
--

CREATE TABLE `lop` (
  `idLop` char(5) NOT NULL,
  `tenLop` varchar(50) NOT NULL,
  `idKhoa` char(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `lop`
--

INSERT INTO `lop` (`idLop`, `tenLop`, `idKhoa`) VALUES
('L001', 'Lớp 1', 'K01'),
('L002', 'Lớp 2', 'K02'),
('L003', 'Lớp 3', 'K03'),
('L004', 'Lớp 4', 'K04'),
('L006', 'Lớp 6', 'K02'),
('L007', 'Lớp 7', 'K03'),
('L009', 'Lớp 9', 'K03'),
('L010', 'Lớp 10', 'K04'),
('L011', 'Lớp 11', 'K03'),
('L012', 'Lớp 12', 'K01'),
('L013', 'Lớp 13', 'K01');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `monhoc`
--

CREATE TABLE `monhoc` (
  `idMon` char(5) NOT NULL,
  `tenMon` varchar(50) NOT NULL,
  `soTinChi` int(11) DEFAULT NULL,
  `idKhoa` char(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `monhoc`
--

INSERT INTO `monhoc` (`idMon`, `tenMon`, `soTinChi`, `idKhoa`) VALUES
('M001', 'Cơ sở dữ liệu', 7, 'K03'),
('M002', 'Lập trình web', 2, 'K01'),
('M003', 'Toán cao cấp', 2, 'K01'),
('M004', 'Thiết kế hệ thống', 2, 'K02'),
('M005', 'Đại số', 3, 'K02'),
('M006', 'Quản trị doanh nghiệp', 4, 'K04'),
('M007', 'Pháp luật đại cương', 2, 'K03'),
('M008', 'Toán đại cương', 5, 'K03');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phonghoc`
--

CREATE TABLE `phonghoc` (
  `idPhong` char(5) NOT NULL,
  `tenPhong` varchar(50) NOT NULL,
  `tinhTrang` varchar(100) NOT NULL DEFAULT 'Được sử dụng'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `phonghoc`
--

INSERT INTO `phonghoc` (`idPhong`, `tenPhong`, `tinhTrang`) VALUES
('P001', 'Phòng 1', 'Được sử dụng'),
('P002', 'Phòng 2', 'Được sử dụng'),
('P003', 'Phòng 3', 'Đang bảo trì'),
('P004', 'Phòng 4', 'Đang bảo trì'),
('P005', 'Phòng 5', 'Được sử dụng'),
('P006', 'Phòng 6', 'Đang bảo trì'),
('P007', 'Phòng 7', 'Được sử dụng'),
('P008', 'Phòng 8', 'Đang bảo trì'),
('P009', 'Phòng 9', 'Được sử dụng'),
('P010', 'Phòng 10', 'Đang bảo trì');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `token`
--

CREATE TABLE `token` (
  `username` varchar(50) NOT NULL,
  `token` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `token`
--

INSERT INTO `token` (`username`, `token`) VALUES
('admin', '92577fdcaa610653a01e6c9e745dfede94d30b77c52f8458c33fa043328cde2c');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `Name` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `role` varchar(10) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`username`, `password`, `Name`, `email`, `role`) VALUES
('admin', '0192023a7bbd73250516f069df18b500', 'admin', 'admin@gmail.com', 'admin'),
('lequymui', '0e41abc2243af1dc7ed2a4a9fb3e022f', 'Lê Quý Mùi', 'lequymui290603@gmail.com', 'user'),
('lethuyduong', 'da99780bfdb1fcf304211f312a15f0db', 'lê thùy dương', 'duong3108@gmail.com', 'user');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `xeplich`
--

CREATE TABLE `xeplich` (
  `id` int(10) NOT NULL,
  `idMon` varchar(5) DEFAULT NULL,
  `idLop` varchar(5) DEFAULT NULL,
  `idGV` varchar(5) DEFAULT NULL,
  `idPhong` varchar(5) NOT NULL,
  `idKhoa` char(5) NOT NULL,
  `Date` date NOT NULL,
  `ThoiGian` varchar(100) NOT NULL,
  `tinhTrang` varchar(50) NOT NULL DEFAULT 'Đã đăng ký'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `xeplich`
--

INSERT INTO `xeplich` (`id`, `idMon`, `idLop`, `idGV`, `idPhong`, `idKhoa`, `Date`, `ThoiGian`, `tinhTrang`) VALUES
(148, 'M008', 'L007', 'GV03', 'P005', 'K03', '2024-10-29', 'Ca 2', 'Đã đăng ký'),
(149, 'M001', 'L007', 'GV03', 'P001', 'K03', '2024-10-29', 'Ca 4', 'Đã đăng ký'),
(150, 'M001', 'L002', 'GV03', 'P005', 'K02', '2024-11-11', 'Ca 2', 'Đã đăng ký'),
(151, 'M004', 'L002', 'GV01', 'P003', 'K02', '2024-11-18', 'Ca 1', 'Đã đăng ký');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `cosovatchat`
--
ALTER TABLE `cosovatchat`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `ctcosovatchat`
--
ALTER TABLE `ctcosovatchat`
  ADD PRIMARY KEY (`stt`),
  ADD KEY `FK_ctcsvc_csvc` (`id`),
  ADD KEY `FK_ctcsvc_phonghoc` (`idPhong`);

--
-- Chỉ mục cho bảng `giangvien`
--
ALTER TABLE `giangvien`
  ADD PRIMARY KEY (`idGiangVien`),
  ADD KEY `FK_giangvien_khoa` (`idKhoa`);

--
-- Chỉ mục cho bảng `khoa`
--
ALTER TABLE `khoa`
  ADD PRIMARY KEY (`idKhoa`);

--
-- Chỉ mục cho bảng `lop`
--
ALTER TABLE `lop`
  ADD PRIMARY KEY (`idLop`),
  ADD KEY `FK_lop_khoa` (`idKhoa`);

--
-- Chỉ mục cho bảng `monhoc`
--
ALTER TABLE `monhoc`
  ADD PRIMARY KEY (`idMon`),
  ADD KEY `FK_MonHoc_Khoa` (`idKhoa`);

--
-- Chỉ mục cho bảng `phonghoc`
--
ALTER TABLE `phonghoc`
  ADD PRIMARY KEY (`idPhong`);

--
-- Chỉ mục cho bảng `token`
--
ALTER TABLE `token`
  ADD KEY `FK_token_users` (`username`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`username`);

--
-- Chỉ mục cho bảng `xeplich`
--
ALTER TABLE `xeplich`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_xeplich_MonHoc` (`idMon`),
  ADD KEY `FK_xeplich_GiangVien` (`idGV`),
  ADD KEY `FK_xeplich_PhongHoc` (`idPhong`),
  ADD KEY `FK_xeplich_Lop` (`idLop`),
  ADD KEY `FK-xeplich_khoa` (`idKhoa`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `ctcosovatchat`
--
ALTER TABLE `ctcosovatchat`
  MODIFY `stt` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT cho bảng `xeplich`
--
ALTER TABLE `xeplich`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `ctcosovatchat`
--
ALTER TABLE `ctcosovatchat`
  ADD CONSTRAINT `FK_ctcsvc_csvc` FOREIGN KEY (`id`) REFERENCES `cosovatchat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ctcsvc_phonghoc` FOREIGN KEY (`idPhong`) REFERENCES `phonghoc` (`idPhong`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `giangvien`
--
ALTER TABLE `giangvien`
  ADD CONSTRAINT `FK_giangvien_khoa` FOREIGN KEY (`idKhoa`) REFERENCES `khoa` (`idKhoa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `lop`
--
ALTER TABLE `lop`
  ADD CONSTRAINT `FK_lop_khoa` FOREIGN KEY (`idKhoa`) REFERENCES `khoa` (`idKhoa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `monhoc`
--
ALTER TABLE `monhoc`
  ADD CONSTRAINT `FK_MonHoc_Khoa` FOREIGN KEY (`idKhoa`) REFERENCES `khoa` (`idKhoa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `token`
--
ALTER TABLE `token`
  ADD CONSTRAINT `FK_token_users` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `xeplich`
--
ALTER TABLE `xeplich`
  ADD CONSTRAINT `FK-xeplich_khoa` FOREIGN KEY (`idKhoa`) REFERENCES `khoa` (`idKhoa`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_xeplich_GiangVien` FOREIGN KEY (`idGV`) REFERENCES `giangvien` (`idGiangVien`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_xeplich_Lop` FOREIGN KEY (`idLop`) REFERENCES `lop` (`idLop`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_xeplich_MonHoc` FOREIGN KEY (`idMon`) REFERENCES `monhoc` (`idMon`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_xeplich_PhongHoc` FOREIGN KEY (`idPhong`) REFERENCES `phonghoc` (`idPhong`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
