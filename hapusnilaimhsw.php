<?php

// koneksi ke database
require_once __DIR__ . "/koneksi.php";

// terima data dari form hapus
$nim = $_POST['nim'];

// SQL untuk menghapus data
$sql = "DELETE FROM nilaimhsw WHERE nim='$nim'";

if (mysqli_query($conn, $sql)) {
    header("Location: adminlte/dist/users.php?pesan=hapus_berhasil");
    exit;
} else {
    header("Location: adminlte/dist/users.php?pesan=hapus_gagal");
    exit;
}

mysqli_close($conn);
