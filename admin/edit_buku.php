<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = (int)$_GET['id'];

/* =========================
   Ambil Data Buku
========================= */
$query = mysqli_query($conn, "SELECT * FROM buku WHERE id = $id");
$data  = mysqli_fetch_assoc($query);

if (!$data) {
    header("Location: buku.php");
    exit();
}

/* =========================
   Update Buku
========================= */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $judul     = mysqli_real_escape_string($conn, $_POST['judul']);
    $penulis   = mysqli_real_escape_string($conn, $_POST['penulis']);
    $penerbit  = mysqli_real_escape_string($conn, $_POST['penerbit']);
    $tahun     = (int)$_POST['tahun_terbit'];
    $stok      = (int)$_POST['stok'];

    $cover = $data['cover'];

    /* =========================
       Upload Cover Baru
    ========================= */
    if (!empty($_FILES['cover']['name'])) {

        $folder = "../uploads/";

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $namaFile = $_FILES['cover']['name'];
        $tmpFile  = $_FILES['cover']['tmp_name'];
        $ext      = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed)) {

            $cover = time() . '_' . rand(100,999) . '.' . $ext;
            move_uploaded_file($tmpFile, $folder . $cover);

            /* hapus cover lama */
            if (!empty($data['cover']) && file_exists($folder . $data['cover'])) {
                unlink($folder . $data['cover']);
            }
        }
    }

    /* =========================
       Update Database
    ========================= */
    $update = "UPDATE buku SET
                judul='$judul',
                penulis='$penulis',
                penerbit='$penerbit',
                tahun_terbit=$tahun,
                stok=$stok,
                cover='$cover'
               WHERE id=$id";

    if (mysqli_query($conn, $update)) {
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
<title>Edit Buku</title>
<link rel="stylesheet" href="../assets/style.css">

<style>
.preview{
    width:100px;
    height:130px;
    object-fit:cover;
    border-radius:8px;
    border:1px solid #ddd;
    margin-top:10px;
}
</style>

</head>
<body>

<div class="container">

    <div class="header">
        <h1>Edit Buku</h1>
        <a href="buku.php" class="btn-back">Kembali</a>
    </div>

    <form method="POST" enctype="multipart/form-data" class="form">

        <div class="form-group">
            <label>Judul Buku:</label>
            <input type="text" name="judul"
                   value="<?= htmlspecialchars($data['judul']); ?>" required>
        </div>

        <div class="form-group">
            <label>Penulis:</label>
            <input type="text" name="penulis"
                   value="<?= htmlspecialchars($data['penulis']); ?>" required>
        </div>

        <div class="form-group">
            <label>Penerbit:</label>
            <input type="text" name="penerbit"
                   value="<?= htmlspecialchars($data['penerbit']); ?>">
        </div>

        <div class="form-group">
            <label>Tahun Terbit:</label>
            <input type="number" name="tahun_terbit"
                   value="<?= $data['tahun_terbit']; ?>">
        </div>

        <div class="form-group">
            <label>Stok:</label>
            <input type="number" name="stok"
                   value="<?= $data['stok']; ?>" required>
        </div>

        <div class="form-group">
            <label>Cover Buku:</label>
            <input type="file" name="cover" accept="image/*">

            <?php if (!empty($data['cover'])) : ?>
                <br>
                <img src="../uploads/<?= $data['cover']; ?>" class="preview">
            <?php endif; ?>
        </div>

        <button type="submit" class="btn">Update</button>

    </form>

</div>

</body>
</html>