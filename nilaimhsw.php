<?php

// baca data dari form
$nim = $_POST['nim'];
$namalengkap = $_POST['namalengkap'];
$nilaiakhir = $_POST['nilaiakhir'];

require_once __DIR__ . '/koneksi.php';

// menentukan nilai huruf dan predikat
if ($nilaiakhir <= 34.50) {
    $nilaihuruf = "E";
    $predikat = "Tidak lulus";
} elseif ($nilaiakhir <= 50.50) {
    $nilaihuruf = "D";
    $predikat = "Kurang memuaskan";
} elseif ($nilaiakhir <= 65.50) {
    $nilaihuruf = "C";
    $predikat = "Cukup Memuaskan";
} elseif ($nilaiakhir <= 80.50) {
    $nilaihuruf = "B";
    $predikat = "Memuaskan";
} else {
    $nilaihuruf = "A";
    $predikat = "Sangat Memuaskan";
}

// SQL untuk memasukkan data
$sql = "INSERT INTO nilaimhsw
(nim, namalengkap, nilaiakhir, nilaihuruf, predikat)
VALUES
('$nim', '$namalengkap', '$nilaiakhir', '$nilaihuruf', '$predikat')";

if (mysqli_query($conn, $sql)) {
    header("Location: adminlte/dist/users.php?pesan=tambah_berhasil");
    exit;
} else {
    header("Location: adminlte/dist/users.php?pesan=tambah_gagal");
    exit;
}

mysqli_close($conn);
