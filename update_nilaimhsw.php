<?php

// koneksi ke database
require_once __DIR__ . "/koneksi.php";

// terima data dari form_editnilaimhsw.php
$nim = $_POST['nim'];
$namalengkap = $_POST['namalengkap'];
$nilaiakhir = $_POST['nilaiakhir'];

// menentukan nilai huruf dan predikat
if ($nilaiakhir <= 34.50) {
    $nilaihuruf = "E";
    $predikat = "Tidak lulus";
} elseif ($nilaiakhir <= 50.50) {
    $nilaihuruf = "D";
    $predikat = "Kurang Memuaskan";
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

// SQL to update a record
$sql = "UPDATE nilaimhsw
        SET namalengkap='$namalengkap',
            nilaiakhir='$nilaiakhir',
            nilaihuruf='$nilaihuruf',
            predikat='$predikat'
        WHERE nim='$nim'";


if (mysqli_query($conn, $sql)) {
    header("Location: adminlte/dist/users.php?pesan=edit_berhasil");
    exit;
} else {
    header("Location: adminlte/dist/users.php?pesan=edit_gagal");
    exit;
}


// $conn->close();
