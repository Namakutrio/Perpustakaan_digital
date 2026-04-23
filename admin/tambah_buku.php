<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $judul     = mysqli_real_escape_string($conn, $_POST['judul']);
    $penulis   = mysqli_real_escape_string($conn, $_POST['penulis']);
    $penerbit  = mysqli_real_escape_string($conn, $_POST['penerbit']);
    $tahun     = (int)$_POST['tahun_terbit'];
    $stok      = (int)$_POST['stok'];

    /* =========================
       Upload Cover Buku
    ========================= */
    $cover = '';

    if (!empty($_FILES['cover']['name'])) {

        $folder = "../uploads/";

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $namaFile  = $_FILES['cover']['name'];
        $tmpFile   = $_FILES['cover']['tmp_name'];
        $ext       = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed)) {
            $cover = time() . '_' . rand(100,999) . '.' . $ext;
            move_uploaded_file($tmpFile, $folder . $cover);
        }
    }

    /* =========================
       Simpan ke Database
    ========================= */
    $query = "INSERT INTO buku 
              (judul, penulis, penerbit, tahun_terbit, stok, cover)
              VALUES
              ('$judul', '$penulis', '$penerbit', $tahun, $stok, '$cover')";

    if (mysqli_query($conn, $query)) {
        header("Location: buku.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Buku</title>
<link rel="stylesheet" href="../assets/style.css">
</head>

<body>

<div class="container">

    <div class="header">
        <h1>Tambah Buku Baru</h1>
        <a href="buku.php" class="btn-back">Kembali</a>
    </div>

    <form method="POST" enctype="multipart/form-data" class="form">

        <div class="form-group">
            <label>Judul Buku:</label>
            <input type="text" name="judul" required>
        </div>

        <div class="form-group">
            <label>Penulis:</label>
            <input type="text" name="penulis" required>
        </div>

        <div class="form-group">
            <label>Penerbit:</label>
            <input type="text" name="penerbit">
        </div>

        <div class="form-group">
            <label>Tahun Terbit:</label>
            <input type="number" name="tahun_terbit">
        </div>

        <div class="form-group">
            <label>Stok:</label>
            <input type="number" name="stok" value="1" required>
        </div>

        <div class="form-group">
            <label>Cover Buku:</label>
            <input type="file" name="cover" accept="image/*">
        </div>

        <button type="submit" class="btn">Simpan</button>

    </form>

</div>

</body>
</html>