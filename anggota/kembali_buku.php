<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'anggota') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$id = (int)$_GET['id'];

/* ===================================
   CEK TRANSAKSI
=================================== */
$query = mysqli_query($conn,"
SELECT * FROM transaksi
WHERE id='$id'
AND user_id='$user_id'
AND status='dipinjam'
");

$data = mysqli_fetch_assoc($query);

if($data){

    $today = date('Y-m-d');
    $denda = 0;

    /* ===============================
       HITUNG DENDA JIKA TERLAMBAT
    =============================== */
    if(strtotime($today) > strtotime($data['tanggal_kembali_rencana'])){

        $hari = ceil(
            (strtotime($today) - strtotime($data['tanggal_kembali_rencana']))
            / 86400
        );

        $denda = $hari * 2000; // 2000 per hari
    }

    /* ===============================
       UPDATE TRANSAKSI
    =============================== */
    mysqli_query($conn,"
    UPDATE transaksi SET
    status='dikembalikan',
    tanggal_kembali='$today',
    denda='$denda'
    WHERE id='$id'
    ");

    /* ===============================
       TAMBAH STOK BUKU
    =============================== */
    mysqli_query($conn,"
    UPDATE buku SET stok = stok + 1
    WHERE id='".$data['buku_id']."'
    ");
}

header("Location: index.php");
exit();
?>