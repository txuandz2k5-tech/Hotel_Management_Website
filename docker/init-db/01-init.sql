-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3307
-- Thời gian đã tạo: Th6 28, 2026 lúc 06:32 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `web_hotel_mngt`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `authentication_admin`
--

CREATE TABLE `authentication_admin` (
  `MaAdmin` varchar(20) NOT NULL,
  `TenDangNhap` varchar(30) NOT NULL,
  `MatKhau` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `authentication_admin`
--

INSERT INTO `authentication_admin` (`MaAdmin`, `TenDangNhap`, `MatKhau`) VALUES
('ADM01', 'admin', '123');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `authentication_login`
--

CREATE TABLE `authentication_login` (
  `MaDangNhap` varchar(20) NOT NULL,
  `TenDangNhap` varchar(30) NOT NULL,
  `MatKhau` varchar(30) NOT NULL,
  `MaNhanVien` varchar(20) DEFAULT NULL,
  `NguoiDungMoi` enum('Yes','No') DEFAULT 'Yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `authentication_login`
--

INSERT INTO `authentication_login` (`MaDangNhap`, `TenDangNhap`, `MatKhau`, `MaNhanVien`, `NguoiDungMoi`) VALUES
('HT_NV001', 'NV001', '123456', 'NV001', 'Yes'),
('HT_NV003', 'NV003', '123456', 'NV003', 'Yes'),
('NV001', 'Vietanh25', '123', 'NV001', 'Yes');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bookings_booking`
--

CREATE TABLE `bookings_booking` (
  `MaDatPhong` int(11) NOT NULL,
  `NgayDatPhong` date NOT NULL,
  `ThoiGianLuuTru` int(11) NOT NULL,
  `NgayNhanPhong` date NOT NULL,
  `NgayTraPhong` date NOT NULL,
  `SoTienDatPhong` int(11) NOT NULL,
  `MaNhanVien` varchar(20) DEFAULT NULL,
  `MaKhachHang` int(11) NOT NULL,
  `MaLoaiPhong` varchar(50) DEFAULT NULL,
  `MaGiamGia` varchar(20) DEFAULT NULL,
  `TrangThai` enum('Pending','Confirmed','Checkin','Checkout','Cancelled') DEFAULT 'Pending',
  `GhiChu` varchar(255) DEFAULT NULL,
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp(),
  `NgayCapNhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bookings_booking`
--

INSERT INTO `bookings_booking` (`MaDatPhong`, `NgayDatPhong`, `ThoiGianLuuTru`, `NgayNhanPhong`, `NgayTraPhong`, `SoTienDatPhong`, `MaNhanVien`, `MaKhachHang`, `MaLoaiPhong`, `MaGiamGia`, `TrangThai`, `GhiChu`, `NgayTao`, `NgayCapNhat`) VALUES
(1, '2025-12-30', 3, '2024-01-15', '2024-01-17', 4500000, NULL, 1, NULL, NULL, '', 'ROOMTYPE:DELUXE|Test Hotel', '2025-12-30 02:18:09', '2026-05-06 16:35:31'),
(2, '2025-12-30', 12, '2025-12-30', '2026-01-11', 9600000, NULL, 2, NULL, NULL, 'Cancelled', 'ROOMTYPE:STD|', '2025-12-30 05:07:09', '2025-12-30 06:38:07'),
(3, '2025-12-30', 3, '2025-12-30', '2026-01-02', 4500000, NULL, 2, NULL, NULL, 'Checkout', 'ROOMTYPE:DELUXE|', '2025-12-30 06:48:01', '2026-01-12 15:08:30'),
(4, '2025-12-30', 2, '2025-12-30', '2026-01-01', 1600000, NULL, 3, NULL, NULL, 'Checkout', 'ROOMTYPE:STD|', '2025-12-30 07:02:04', '2026-01-12 14:11:10'),
(5, '2026-01-10', 1, '2026-01-10', '2026-01-29', 1500000, NULL, 1, NULL, NULL, 'Cancelled', 'ROOMTYPE:DELUXE | TỔNG: 1,500,000 | ĐÃ CỌC 50%: 750,000 | Note: ', '2026-01-10 16:17:25', '2026-04-21 09:25:09'),
(6, '2026-01-12', 2, '2026-01-12', '2026-01-14', 1600000, NULL, 1, NULL, NULL, 'Checkout', 'ROOMTYPE:STD | TỔNG: 1,600,000 | ĐÃ CỌC 50%: 800,000 | Note: ', '2026-01-12 15:07:16', '2026-01-16 11:38:14'),
(8, '2026-01-16', 9, '2026-01-16', '2026-01-25', 7200000, NULL, 1, 'STD', NULL, 'Checkout', '', '2026-01-16 11:36:07', '2026-04-21 05:49:56'),
(12, '0000-00-00', 0, '2025-04-19', '2026-05-30', 0, NULL, 2, 'STD', 'GG01', 'Checkout', '', '2026-04-18 19:50:23', '2026-04-21 09:27:51'),
(13, '2026-04-21', 1, '2026-04-21', '2026-04-22', 1500000, NULL, 12, NULL, NULL, 'Checkout', 'ROOMTYPE:DELUXE|', '2026-04-21 08:56:33', '2026-04-21 08:57:59');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bookings_discount`
--

CREATE TABLE `bookings_discount` (
  `MaGiamGia` varchar(20) NOT NULL,
  `TenGiamGia` varchar(100) NOT NULL,
  `MoTaGiamGia` varchar(100) DEFAULT NULL,
  `TyLeGiamGia` int(11) NOT NULL,
  `MaNhanVien` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bookings_discount`
--

INSERT INTO `bookings_discount` (`MaGiamGia`, `TenGiamGia`, `MoTaGiamGia`, `TyLeGiamGia`, `MaNhanVien`) VALUES
('GG002', 'Ưu tiên trẻ nhỏ', 'Gia đình đi có trẻ nhỏ dưới 10 tuổi được ưu đãi', 20, 'NV001'),
('GG01', 'Giảm Tet HoliDay', 'Happy New Year 2026', 10, 'NV001');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bookings_payments`
--

CREATE TABLE `bookings_payments` (
  `MaThanhToan` int(11) NOT NULL,
  `TrangThaiThanhToan` varchar(20) NOT NULL,
  `LoaiThanhToan` varchar(50) NOT NULL,
  `SoTienThanhToan` int(11) NOT NULL,
  `MaDatPhong` int(11) NOT NULL,
  `NgayThanhToan` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bookings_payments`
--

INSERT INTO `bookings_payments` (`MaThanhToan`, `TrangThaiThanhToan`, `LoaiThanhToan`, `SoTienThanhToan`, `MaDatPhong`, `NgayThanhToan`) VALUES
(1, 'Paid', 'Transfer', 800000, 4, '2026-01-12 14:11:10'),
(2, 'Paid', 'Cash', 36750000, 5, '2026-01-12 15:08:24'),
(3, 'Paid', 'Cash', 22750000, 3, '2026-01-12 15:08:30'),
(4, 'Paid', 'Cash', 36300000, 6, '2026-01-16 11:38:14'),
(5, 'Paid', 'Transfer', 3600000, 8, '2026-04-21 05:49:56'),
(6, 'Paid', 'Card', 21500000, 13, '2026-04-21 08:57:59'),
(7, 'Paid', 'Transfer', 1755000, 12, '2026-04-21 09:27:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hotelservice_services`
--

CREATE TABLE `hotelservice_services` (
  `MaDichVu` varchar(20) NOT NULL,
  `TenDichVu` varchar(50) NOT NULL,
  `MoTaDichVu` varchar(100) DEFAULT NULL,
  `ChiPhiDichVu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hotelservice_services`
--

INSERT INTO `hotelservice_services` (`MaDichVu`, `TenDichVu`, `MoTaDichVu`, `ChiPhiDichVu`) VALUES
('DV01', 'Chăm sóc sức khỏe', 'Y tế, làm đẹp, ....', 10000000),
('DV02', 'Ăn sáng', 'Các đồ ăn nhẹ', 500000),
('DV03', 'Ăn trưa', 'Các móm hải sản Pro Vip', 5000000),
('DV04', 'Ăn tối', 'Các món lẩu thập cẩm,.....', 20000000),
('DV05', 'Giặt quần áo', 'Giặt là các loại quần áo', 450000),
('DV06', 'Thuê xe máy', 'thuê xe máy đi chơi', 500000),
('DV08', 'Test Service', 'Test service description', 100000),
('DV099', 'Karaoke', 'Karaoke GMV-CLUB', 1500000),
('DV11', 'Dọn dẹp phòng', 'Dịch vụ siêu đặc biệt', 250000),
('DV13', 'Tư vấn khách hàng', 'Hỗ trợ khách hàng trực tiếp và trực tuyến', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hotelservice_servicesused`
--

CREATE TABLE `hotelservice_servicesused` (
  `MaDichVuSuDung` int(11) NOT NULL,
  `MaDichVu` varchar(20) DEFAULT NULL,
  `MaDatPhong` int(11) NOT NULL,
  `SoLuong` int(11) DEFAULT 1,
  `DonGia` int(11) NOT NULL,
  `ThanhTien` int(11) NOT NULL,
  `NgaySuDung` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hotelservice_servicesused`
--

INSERT INTO `hotelservice_servicesused` (`MaDichVuSuDung`, `MaDichVu`, `MaDatPhong`, `SoLuong`, `DonGia`, `ThanhTien`, `NgaySuDung`) VALUES
(1, 'DV04', 1, 1, 20000000, 20000000, '2025-12-30 04:52:34'),
(2, 'DV02', 1, 1, 500000, 500000, '2025-12-30 04:55:47'),
(3, 'DV03', 1, 1, 5000000, 5000000, '2025-12-30 04:55:47'),
(4, 'DV01', 1, 1, 10000000, 10000000, '2025-12-30 04:55:47'),
(5, 'DV05', 1, 1, 450000, 450000, '2025-12-30 04:55:47'),
(6, 'DV02', 2, 1, 500000, 500000, '2025-12-30 05:07:49'),
(7, 'DV04', 2, 1, 20000000, 20000000, '2025-12-30 05:07:49'),
(8, 'DV01', 2, 1, 10000000, 10000000, '2025-12-30 05:07:49'),
(9, 'DV03', 2, 1, 5000000, 5000000, '2025-12-30 05:07:49'),
(10, 'DV02', 3, 1, 500000, 500000, '2025-12-30 06:48:21'),
(11, 'DV04', 3, 1, 20000000, 20000000, '2025-12-30 06:48:21'),
(12, 'DV06', 5, 1, 500000, 500000, '2026-01-10 16:18:28'),
(13, 'DV03', 5, 1, 5000000, 5000000, '2026-01-10 16:18:28'),
(14, 'DV01', 5, 1, 10000000, 10000000, '2026-01-10 16:18:28'),
(15, 'DV04', 5, 1, 20000000, 20000000, '2026-01-10 16:18:28'),
(16, 'DV02', 5, 1, 500000, 500000, '2026-01-10 16:18:28'),
(17, 'DV02', 6, 1, 500000, 500000, '2026-01-12 15:33:35'),
(18, 'DV04', 6, 1, 20000000, 20000000, '2026-01-12 15:33:35'),
(19, 'DV03', 6, 1, 5000000, 5000000, '2026-01-12 15:33:35'),
(20, 'DV01', 6, 1, 10000000, 10000000, '2026-01-12 15:33:35'),
(21, 'DV04', 13, 1, 20000000, 20000000, '2026-04-21 08:57:38'),
(22, 'DV099', 12, 1, 1500000, 1500000, '2026-04-21 09:25:41'),
(23, 'DV05', 12, 1, 450000, 450000, '2026-04-21 09:25:47');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hotels_departments`
--

CREATE TABLE `hotels_departments` (
  `MaBoPhan` varchar(20) NOT NULL,
  `TenBoPhan` varchar(50) NOT NULL,
  `MoTaBoPhan` varchar(100) DEFAULT NULL,
  `LuongKhoiDiem` int(11) NOT NULL,
  `ChucDanh` varchar(50) NOT NULL DEFAULT 'Intern'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hotels_departments`
--

INSERT INTO `hotels_departments` (`MaBoPhan`, `TenBoPhan`, `MoTaBoPhan`, `LuongKhoiDiem`, `ChucDanh`) VALUES
('BP_BV', 'Bảo Vệ', 'Bộ phận bảo vệ', 6000000, 'Security guard'),
('BP_KT', 'Kế Toán', 'Bộ phận kế toán', 4000000, 'Accountant'),
('BP_LT', 'Lễ tân', 'Bộ phận tiếp đón', 8000000, 'Receptionist'),
('BP_PV', 'Phục Vụ', 'Bộ phận phục vụ', 5000000, 'Servise'),
('BP_QL', 'Quản lý', 'Bộ phận quản lý', 20000000, 'Manager'),
('BP_VS', 'Bộ phận vệ sinh', 'Bộ phận vệ sinh', 7000000, 'Service Clean');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hotels_employees`
--

CREATE TABLE `hotels_employees` (
  `MaNhanVien` varchar(20) NOT NULL,
  `TenNhanVien` varchar(50) NOT NULL,
  `HoNhanVien` varchar(50) NOT NULL,
  `ChucDanhNV` varchar(50) NOT NULL,
  `SoDienThoaiNV` varchar(15) NOT NULL,
  `EmailNhanVien` varchar(50) NOT NULL,
  `NgayVaoLam` date NOT NULL,
  `DiaChi` varchar(50) NOT NULL,
  `MaBoPhan` varchar(20) DEFAULT NULL,
  `CMND_CCCD` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hotels_employees`
--

INSERT INTO `hotels_employees` (`MaNhanVien`, `TenNhanVien`, `HoNhanVien`, `ChucDanhNV`, `SoDienThoaiNV`, `EmailNhanVien`, `NgayVaoLam`, `DiaChi`, `MaBoPhan`, `CMND_CCCD`) VALUES
('NV001', 'Việt Anh', 'Nguyễn Văn', 'Receptionist', '0901234567', 'an.nguyen@hotel.com', '2023-01-15', 'TP.HCM', 'BP_LT', '123456789'),
('NV003', 'Nguyễn Mạnh Hùng', '', 'Kế Toán', '0981000003', 'hung.nm@gmail.com', '2024-01-20', '', 'BP_KT', '0012050003');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hotels_guests`
--

CREATE TABLE `hotels_guests` (
  `MaKhachHang` int(11) NOT NULL,
  `TenKhachHang` varchar(50) NOT NULL,
  `HoKhachHang` varchar(50) NOT NULL,
  `EmailKhachHang` varchar(50) NOT NULL,
  `SoDienThoaiKhachHang` varchar(15) NOT NULL,
  `CMND_CCCDKhachHang` varchar(30) NOT NULL,
  `DiaChi` varchar(50) NOT NULL,
  `MatKhau` varchar(30) DEFAULT NULL,
  `TrangThai` enum('Reserved','Not Reserved') NOT NULL DEFAULT 'Not Reserved',
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hotels_guests`
--

INSERT INTO `hotels_guests` (`MaKhachHang`, `TenKhachHang`, `HoKhachHang`, `EmailKhachHang`, `SoDienThoaiKhachHang`, `CMND_CCCDKhachHang`, `DiaChi`, `MatKhau`, `TrangThai`, `NgayTao`) VALUES
(1, 'Huân', 'Nguyễn', 'xuankhang@.com', '03123423211', '001090001234', '456 Oak St', '123456', 'Not Reserved', '2025-12-28 17:00:00'),
(2, 'Minh', 'Nguyên', 'johnsmith@example.com', '013412342312', '03598902234', '456 Oak St', '123456', 'Not Reserved', '2025-12-30 05:06:36'),
(3, 'Vinh', 'Lê', 'Levinh@gmail.com', '0911223344', '03500340043', 'Ninh Bình', '123456', 'Not Reserved', '2025-12-30 07:01:29'),
(12, 'Việt Anh', 'Nguyễn', 'xuanng205@gmail.com', '0795376900', '001205037378', 'Phú Thọ', '123', 'Not Reserved', '2026-04-21 05:57:54'),
(21, 'Xuân 11A4.35.Nguyễn Thanh', '', 'xuanng2005@gmail.com', '0795376982', '1234567891', 'Triều Khúc, Thanh Xuân, Hà Nội, Việt Nam', '123', 'Not Reserved', '2026-06-28 02:45:41');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `rooms_room`
--

CREATE TABLE `rooms_room` (
  `MaPhong` varchar(20) NOT NULL,
  `SoPhong` varchar(10) NOT NULL,
  `MaLoaiPhong` varchar(20) DEFAULT NULL,
  `KhaDung` enum('Yes','No') DEFAULT 'Yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `rooms_room`
--

INSERT INTO `rooms_room` (`MaPhong`, `SoPhong`, `MaLoaiPhong`, `KhaDung`) VALUES
('P101', '101', 'DELUXE', 'Yes'),
('P102', '102', 'STD', 'No'),
('P103', ' 103', 'DELUXE', 'Yes'),
('P104', ' 104', 'STD', 'Yes'),
('P105', '105', 'DELUXE', 'Yes'),
('P106', '106', 'STD', 'Yes'),
('P201', '201', 'DELUXE', 'Yes'),
('P202', '202', 'DELUXE', 'Yes'),
('P203', '203', 'DELUXE', 'Yes'),
('P204', '204', 'DELUXE', 'Yes'),
('P701', '701', 'BD', 'Yes'),
('P702', '702', 'BD2', 'Yes');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `rooms_roombooked`
--

CREATE TABLE `rooms_roombooked` (
  `MaPhongDaDat` int(11) NOT NULL,
  `MaDatPhong` int(11) NOT NULL,
  `MaPhong` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `rooms_roombooked`
--

INSERT INTO `rooms_roombooked` (`MaPhongDaDat`, `MaDatPhong`, `MaPhong`) VALUES
(4, 4, 'P102');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `rooms_roomtype`
--

CREATE TABLE `rooms_roomtype` (
  `MaLoaiPhong` varchar(20) NOT NULL,
  `TenLoaiPhong` varchar(50) NOT NULL,
  `GiaPhong` int(11) NOT NULL,
  `MoTaPhong` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `rooms_roomtype`
--

INSERT INTO `rooms_roomtype` (`MaLoaiPhong`, `TenLoaiPhong`, `GiaPhong`, `MoTaPhong`) VALUES
('BD', 'Phòng Dịch Vụ (Cưới hỏi)', 40000000, 'Phòng dành cho đặt tiệc'),
('BD2', 'Phòng Dịch Vụ (Mini-Tiệc, Sinh Nhật)', 10000000, 'Phòng tiệc dành cho không gian nhỏ, ấm cúng'),
('DELUXE', 'Phòng Deluxe', 1500000, 'Phòng cao cấp hướng biển'),
('STD', 'Phòng Standard', 800000, 'Phòng tiêu chuẩn');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `authentication_admin`
--
ALTER TABLE `authentication_admin`
  ADD PRIMARY KEY (`MaAdmin`),
  ADD UNIQUE KEY `TenDangNhap` (`TenDangNhap`);

--
-- Chỉ mục cho bảng `authentication_login`
--
ALTER TABLE `authentication_login`
  ADD PRIMARY KEY (`MaDangNhap`),
  ADD UNIQUE KEY `TenDangNhap` (`TenDangNhap`),
  ADD KEY `FK_EmployeeId_Login` (`MaNhanVien`);

--
-- Chỉ mục cho bảng `bookings_booking`
--
ALTER TABLE `bookings_booking`
  ADD PRIMARY KEY (`MaDatPhong`),
  ADD KEY `FK_EmployeeId_Booking` (`MaNhanVien`),
  ADD KEY `FK_GuestId_Booking` (`MaKhachHang`),
  ADD KEY `FK_DiscountId_Booking` (`MaGiamGia`),
  ADD KEY `fk_booking_roomtype` (`MaLoaiPhong`);

--
-- Chỉ mục cho bảng `bookings_discount`
--
ALTER TABLE `bookings_discount`
  ADD PRIMARY KEY (`MaGiamGia`),
  ADD KEY `FK_EmployeeId_Discount` (`MaNhanVien`);

--
-- Chỉ mục cho bảng `bookings_payments`
--
ALTER TABLE `bookings_payments`
  ADD PRIMARY KEY (`MaThanhToan`),
  ADD KEY `FK_BookingId_Payments` (`MaDatPhong`);

--
-- Chỉ mục cho bảng `hotelservice_services`
--
ALTER TABLE `hotelservice_services`
  ADD PRIMARY KEY (`MaDichVu`);

--
-- Chỉ mục cho bảng `hotelservice_servicesused`
--
ALTER TABLE `hotelservice_servicesused`
  ADD PRIMARY KEY (`MaDichVuSuDung`),
  ADD KEY `FK_ServiceId_ServicesUsed` (`MaDichVu`),
  ADD KEY `FK_BookingId_ServicesUsed` (`MaDatPhong`);

--
-- Chỉ mục cho bảng `hotels_departments`
--
ALTER TABLE `hotels_departments`
  ADD PRIMARY KEY (`MaBoPhan`);

--
-- Chỉ mục cho bảng `hotels_employees`
--
ALTER TABLE `hotels_employees`
  ADD PRIMARY KEY (`MaNhanVien`),
  ADD UNIQUE KEY `EmailNhanVien` (`EmailNhanVien`),
  ADD UNIQUE KEY `CMND_CCCD` (`CMND_CCCD`),
  ADD KEY `FK_DepartmentId_Employee` (`MaBoPhan`);

--
-- Chỉ mục cho bảng `hotels_guests`
--
ALTER TABLE `hotels_guests`
  ADD PRIMARY KEY (`MaKhachHang`);

--
-- Chỉ mục cho bảng `rooms_room`
--
ALTER TABLE `rooms_room`
  ADD PRIMARY KEY (`MaPhong`),
  ADD KEY `FK_RoomTypeID_Room` (`MaLoaiPhong`);

--
-- Chỉ mục cho bảng `rooms_roombooked`
--
ALTER TABLE `rooms_roombooked`
  ADD PRIMARY KEY (`MaPhongDaDat`),
  ADD KEY `FK_BookingId_RoomBooked` (`MaDatPhong`),
  ADD KEY `FK_RoomId_RoomBooked` (`MaPhong`);

--
-- Chỉ mục cho bảng `rooms_roomtype`
--
ALTER TABLE `rooms_roomtype`
  ADD PRIMARY KEY (`MaLoaiPhong`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bookings_booking`
--
ALTER TABLE `bookings_booking`
  MODIFY `MaDatPhong` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT cho bảng `bookings_payments`
--
ALTER TABLE `bookings_payments`
  MODIFY `MaThanhToan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `hotelservice_servicesused`
--
ALTER TABLE `hotelservice_servicesused`
  MODIFY `MaDichVuSuDung` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT cho bảng `hotels_guests`
--
ALTER TABLE `hotels_guests`
  MODIFY `MaKhachHang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `rooms_roombooked`
--
ALTER TABLE `rooms_roombooked`
  MODIFY `MaPhongDaDat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `authentication_login`
--
ALTER TABLE `authentication_login`
  ADD CONSTRAINT `FK_EmployeeId_Login` FOREIGN KEY (`MaNhanVien`) REFERENCES `hotels_employees` (`MaNhanVien`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `bookings_booking`
--
ALTER TABLE `bookings_booking`
  ADD CONSTRAINT `FK_DiscountId_Booking` FOREIGN KEY (`MaGiamGia`) REFERENCES `bookings_discount` (`MaGiamGia`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_EmployeeId_Booking` FOREIGN KEY (`MaNhanVien`) REFERENCES `hotels_employees` (`MaNhanVien`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_GuestId_Booking` FOREIGN KEY (`MaKhachHang`) REFERENCES `hotels_guests` (`MaKhachHang`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_booking_roomtype` FOREIGN KEY (`MaLoaiPhong`) REFERENCES `rooms_roomtype` (`MaLoaiPhong`);

--
-- Các ràng buộc cho bảng `bookings_discount`
--
ALTER TABLE `bookings_discount`
  ADD CONSTRAINT `FK_EmployeeId_Discount` FOREIGN KEY (`MaNhanVien`) REFERENCES `hotels_employees` (`MaNhanVien`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `bookings_payments`
--
ALTER TABLE `bookings_payments`
  ADD CONSTRAINT `FK_BookingId_Payments` FOREIGN KEY (`MaDatPhong`) REFERENCES `bookings_booking` (`MaDatPhong`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `hotelservice_servicesused`
--
ALTER TABLE `hotelservice_servicesused`
  ADD CONSTRAINT `FK_BookingId_ServicesUsed` FOREIGN KEY (`MaDatPhong`) REFERENCES `bookings_booking` (`MaDatPhong`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ServiceId_ServicesUsed` FOREIGN KEY (`MaDichVu`) REFERENCES `hotelservice_services` (`MaDichVu`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `hotels_employees`
--
ALTER TABLE `hotels_employees`
  ADD CONSTRAINT `FK_DepartmentId_Employee` FOREIGN KEY (`MaBoPhan`) REFERENCES `hotels_departments` (`MaBoPhan`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `rooms_room`
--
ALTER TABLE `rooms_room`
  ADD CONSTRAINT `FK_RoomTypeID_Room` FOREIGN KEY (`MaLoaiPhong`) REFERENCES `rooms_roomtype` (`MaLoaiPhong`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `rooms_roombooked`
--
ALTER TABLE `rooms_roombooked`
  ADD CONSTRAINT `FK_BookingId_RoomBooked` FOREIGN KEY (`MaDatPhong`) REFERENCES `bookings_booking` (`MaDatPhong`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_RoomId_RoomBooked` FOREIGN KEY (`MaPhong`) REFERENCES `rooms_room` (`MaPhong`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
