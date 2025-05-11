-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 11, 2025 lúc 04:43 PM
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
-- Cơ sở dữ liệu: `car-rental`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin`
--

CREATE TABLE `admin` (
  `Id` int(11) NOT NULL,
  `UserName` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `admin`
--

INSERT INTO `admin` (`Id`, `UserName`, `Password`) VALUES
(1, 'admin', '$2y$10$xnFQ6MFlCDiuKiyZ1MBXDOOIHitHjeotgNO08m1v4bG8yzXWEE9xu');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking`
--

CREATE TABLE `booking` (
  `BookingNumber` bigint(20) NOT NULL,
  `userId` int(11) NOT NULL,
  `VehicleId` int(11) NOT NULL,
  `FromDate` datetime NOT NULL,
  `ToDate` datetime NOT NULL,
  `message` varchar(100) NOT NULL,
  `Status` varchar(50) NOT NULL,
  `PostingDate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `brands`
--

CREATE TABLE `brands` (
  `Id` int(11) NOT NULL,
  `brandName` varchar(25) NOT NULL,
  `CreationDate` datetime NOT NULL DEFAULT current_timestamp(),
  `UpdationDate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `brands`
--

INSERT INTO `brands` (`Id`, `brandName`, `CreationDate`, `UpdationDate`) VALUES
(3, 'BMW', '2025-04-20 13:37:44', '2025-04-20 14:47:03'),
(4, 'Toyota', '2025-04-20 13:57:07', '2025-04-20 13:57:07'),
(5, 'Huyndai', '2025-04-21 10:21:40', '2025-04-21 10:21:40'),
(6, 'Honda', '2025-04-26 22:42:34', '2025-04-26 22:42:34'),
(7, 'Mercedes-Benz', '2025-04-26 22:42:34', '2025-04-26 22:42:34'),
(8, 'Ford', '2025-04-26 22:42:34', '2025-04-26 22:42:34'),
(9, 'Audi', '2025-04-26 22:42:34', '2025-04-26 22:42:34'),
(10, 'VinFast', '2025-04-26 22:42:34', '2025-04-26 22:42:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `Id` int(11) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` char(255) NOT NULL,
  `PhoneNumber` varchar(50) NOT NULL,
  `DateOfBirth` date NOT NULL,
  `Address` varchar(100) NOT NULL,
  `RegDate` datetime NOT NULL DEFAULT current_timestamp(),
  `UpdateDate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vehicles`
--

CREATE TABLE `vehicles` (
  `VehicleId` int(11) NOT NULL,
  `VehiclesTitle` varchar(50) NOT NULL,
  `BrandId` int(11) NOT NULL,
  `VehicleOverview` varchar(255) NOT NULL,
  `PricePerDay` int(11) NOT NULL,
  `FuelType` varchar(20) NOT NULL,
  `ModelYear` year(4) NOT NULL,
  `SeatingCapacity` int(11) NOT NULL,
  `AirConditioner` tinyint(1) NOT NULL,
  `PowerDoorLocks` tinyint(1) NOT NULL,
  `AntiLockBrakingSystem` tinyint(1) NOT NULL,
  `BrakeAssist` tinyint(1) NOT NULL,
  `PowerSteering` tinyint(1) NOT NULL,
  `DriverAirbag` tinyint(1) NOT NULL,
  `PassengerAirbag` tinyint(1) NOT NULL,
  `PowerWindows` tinyint(1) NOT NULL,
  `CDPlayer` tinyint(1) NOT NULL,
  `CentralLocking` tinyint(1) NOT NULL,
  `CrashSensor` tinyint(1) NOT NULL,
  `LeatherSeats` tinyint(1) NOT NULL,
  `Vimage1` varchar(255) DEFAULT NULL,
  `Vimage2` varchar(255) DEFAULT NULL,
  `Vimage3` varchar(255) DEFAULT NULL,
  `Vimage4` varchar(255) DEFAULT NULL,
  `Vimage5` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `vehicles`
--

INSERT INTO `vehicles` (`VehicleId`, `VehiclesTitle`, `BrandId`, `VehicleOverview`, `PricePerDay`, `FuelType`, `ModelYear`, `SeatingCapacity`, `AirConditioner`, `PowerDoorLocks`, `AntiLockBrakingSystem`, `BrakeAssist`, `PowerSteering`, `DriverAirbag`, `PassengerAirbag`, `PowerWindows`, `CDPlayer`, `CentralLocking`, `CrashSensor`, `LeatherSeats`, `Vimage1`, `Vimage2`, `Vimage3`, `Vimage4`, `Vimage5`) VALUES
(2, 'camry', 3, 'camry (Toyota Camry):\r\nMid-size sedan, fuel-efficient, comfortable, and reliable.', 100, 'Petrol', '2022', 7, 1, 1, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 'img/68105295ed815_download.jpg', 'img/68105295eda6c_download (2).jpg', 'img/68105295edbc0_download (3).jpg', 'img/68105295edd06_download (1).jpg', 'img/68105295ede2b_download (5).jpg'),
(3, 'Tucson', 5, 'Tucson (Hyundai Tucson):\r\nCompact SUV with modern design and advanced safety features.', 5000, 'Petrol', '2022', 7, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 1, 'img/681052df15e77_download.jpg', 'img/681052df161fe_download (2).jpg', 'img/681052df16d47_download (3).jpg', 'img/681052df17032_download (4).jpg', 'img/681052df171ff_download (1).jpg'),
(4, 'Honda Civic', 6, 'Honda Civic:\r\nCompact sporty sedan with youthful design and agile handling.', 80, 'Petrol', '2023', 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 'img/68105311831ae_download.jpg', 'img/6810531183581_download (1).jpg', 'img/6810531183995_download (3).jpg', 'img/6810531183b52_download (4).jpg', 'img/6810531183ed2_download (2).jpg'),
(5, 'Honda CR-V', 6, 'Honda CR-V:\r\nCompact SUV with spacious interior and fuel efficiency.', 120, 'Petrol', '2024', 7, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 'img/68105344d5a96_download.jpg', 'img/68105344d5d55_download (1).jpg', 'img/68105344d5f01_download (3).jpg', 'img/68105344d609f_download (4).jpg', 'img/68105344d61f6_download (2).jpg'),
(6, 'Honda Accord', 6, 'Honda Accord:\r\nMid-size premium sedan with luxurious interior and power.', 100, 'Petrol', '2023', 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 'img/68105377132d1_download.jpg', 'img/6810537713614_download (1).jpg', 'img/6810537713924_download (3).jpg', 'img/6810537713dd4_download (2).jpg', 'img/68105377140aa_download (5).jpg'),
(8, 'Honda Pilot', 6, 'Honda Pilot:\r\nMid-size family SUV with spacious cabin and robust performance.', 150, 'Petrol', '2024', 8, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 'img/681053e429e02_download.jpg', 'img/681053e42a2df_download (3).jpg', 'img/681053e42a6ec_download (1).jpg', 'img/681053e42aacc_download (2).jpg', 'img/681053e42b19a_download (4).jpg'),
(9, 'Mercedes C-Class', 7, 'Mercedes C-Class:\r\nCompact luxury sedan with refined interior and advanced tech.', 200, 'Petrol', '2023', 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 'img/681054110c9f7_download.jpg', 'img/681054110d745_download (1).jpg', 'img/681054110dbf4_download (2).jpg', 'img/681054110e026_download (3).jpg', 'img/681054110e2fd_download (5).jpg'),
(13, 'Mercedes S-Class', 7, 'Mercedes S-Class:\r\nFull-size luxury sedan, pinnacle of technology and comfort.', 400, 'Petrol', '2024', 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 'img/68105678e1b40_download.jpg', 'img/68105678e2339_download (1).jpg', 'img/68105678e2709_download (2).jpg', 'img/68105678e2b54_download (3).jpg', 'img/68105678e2e1e_download (5).jpg'),
(14, 'Ford Mustang', 8, 'Ford Mustang:\r\nIconic sports car with bold design and high performance.', 150, 'Petrol', '2023', 4, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 'img/681056c4e3c0f_download.jpg', 'img/681056c4e4598_download (1).jpg', 'img/681056c4e4961_download (2).jpg', 'img/681056c4e4d1e_download (3).jpg', 'img/681056c4e5086_download (4).jpg'),
(15, 'Ford Explorer', 8, 'Ford Explorer:\r\nMid-size SUV, spacious and versatile for all terrains.', 130, 'Petrol', '2024', 7, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 'img/681056e2bdb6e_download.jpg', 'img/681056e2be0a1_download (1).jpg', 'img/681056e2be515_download (2).jpg', 'img/681056e2be7cf_download (3).jpg', 'img/681056e2bea9f_download (4).jpg'),
(16, 'Ford F-150', 8, 'Ford F-150:\r\nFull-size pickup, durable and powerful for tough tasks.', 180, 'Diesel', '2023', 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 'img/68105708c736c_download.jpg', 'img/68105708c7866_download (1).jpg', 'img/68105708c7c12_download (2).jpg', 'img/68105708c7fe9_download (3).jpg', 'img/68105708c8363_download (4).jpg'),
(18, 'Ford Bronco', 8, 'Ford Bronco:\r\nRugged off-road SUV, bold design for adventure.', 160, 'Petrol', '2024', 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 'img/681057fcaacd6_download.jpg', 'img/681057fcab0fe_download (1).jpg', 'img/681057fcab405_download (2).jpg', 'img/681057fcab6ff_download (3).jpg', 'img/681057fcaba0f_download (4).jpg'),
(19, 'Audi A3', 9, 'Audi A3:\r\nCompact luxury sedan, youthful with cutting-edge tech.', 170, 'Petrol', '2023', 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 'img/6810581d44019_download.jpg', 'img/6810581d44448_download (1).jpg', 'img/6810581d44789_download (2).jpg', 'img/6810581d44ac4_download (3).jpg', 'img/6810581d44dca_download (5).jpg'),
(20, 'Audi Q5', 9, 'Audi Q5:\r\nMid-size luxury SUV with elegant design and strong performance.', 220, 'Diesel', '2024', 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 'img/6810584c4bcbc_download.jpg', 'img/6810584c4c0cc_download (1).jpg', 'img/6810584c4c4a2_download (2).jpg', 'img/6810584c4d117_download (3).jpg', 'img/6810584c4d5de_download (5).jpg'),
(21, 'Audi A6', 9, 'Audi A6:\r\nMid-size luxury sedan with premium interior and performance.', 250, 'Petrol', '2023', 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 'img/68105885a1d32_download.jpg', 'img/68105885a227a_download (1).jpg', 'img/68105885a2656_download (3).jpg', 'img/68105885a2a60_download (4).jpg', 'img/68105885a2ec7_download (2).jpg'),
(22, 'Audi Q3', 9, 'Audi Q3:\r\nSubcompact luxury crossover, agile for urban driving.', 190, 'Petrol', '2022', 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 'img/681058bb12a4e_download.jpg', 'img/681058bb12e96_download (1).jpg', 'img/681058bb131bd_download (2).jpg', 'img/681058bb13536_download (3).jpg', 'img/681058bb13881_download (4).jpg'),
(23, 'Audi A8', 9, 'Audi A8:\r\nFull-size luxury sedan with top-tier technology and comfort.', 350, 'Petrol', '2024', 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 'img/681058e8d91e1_download.jpg', 'img/681058e8d962c_download (1).jpg', 'img/681058e8d9938_download (2).jpg', 'img/681058e8d9c89_download (3).jpg', 'img/681058e8da1f4_download (5).jpg'),
(24, 'VinFast VF e34', 10, 'VinFast VF e34:\r\nCompact electric SUV, eco-friendly with modern technology.', 110, 'Petrol', '2023', 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 'img/681059168419e_download.jpg', 'img/6810591684708_download (1).jpg', 'img/6810591684b51_download (2).jpg', 'img/6810591684f74_download (3).jpg', 'img/6810591685306_download (4).jpg'),
(25, 'VinFast Lux A2.0', 10, 'VinFast Lux A2.0:\r\nMid-size luxury sedan with modern design and smooth ride.', 140, 'Petrol', '2022', 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 'img/68105946d19d1_download.jpg', 'img/68105946d1e2c_download (1).jpg', 'img/68105946d222c_download (2).jpg', 'img/68105946d25ff_download (3).jpg', 'img/68105946d293b_download (4).jpg');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Id`);

--
-- Chỉ mục cho bảng `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`BookingNumber`),
  ADD KEY `fk_userId_booking` (`userId`),
  ADD KEY `fk_VehicleId_booking` (`VehicleId`);

--
-- Chỉ mục cho bảng `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`Id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Id`);

--
-- Chỉ mục cho bảng `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`VehicleId`),
  ADD KEY `fk_BrandId_vehicles` (`BrandId`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admin`
--
ALTER TABLE `admin`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `booking`
--
ALTER TABLE `booking`
  MODIFY `BookingNumber` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `brands`
--
ALTER TABLE `brands`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT cho bảng `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `VehicleId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `fk_VehicleId_booking` FOREIGN KEY (`VehicleId`) REFERENCES `vehicles` (`VehicleId`),
  ADD CONSTRAINT `fk_userId_booking` FOREIGN KEY (`userId`) REFERENCES `users` (`Id`);

--
-- Các ràng buộc cho bảng `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `fk_BrandId_vehicles` FOREIGN KEY (`BrandId`) REFERENCES `brands` (`Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
