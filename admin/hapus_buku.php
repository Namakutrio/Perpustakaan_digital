<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = (int)$_GET['id'];

/* cek apakah buku dipakai di transaksi / peminjaman */
$cek1 = mysqli_query($conn, "SELECT id FROM transaksi WHERE buku_id='$id' LIMIT 1");
$cek2 = mysqli_query($conn, "SELECT id FROM peminjaman WHERE buku_id='$id' LIMIT 1");

if (mysqli_num_rows($cek1) > 0 || mysqli_num_rows($cek2) > 0) {

    echo "
    <script>
    alert('Buku tidak bisa dihapus karena sudah pernah dipinjam!');
    window.location='buku.php';
    </script>
    ";
    exit();
}

/* hapus buku */
mysqli_query($conn, "DELETE FROM buku WHERE id='$id'");

header("Location: buku.php");
exit();
?>