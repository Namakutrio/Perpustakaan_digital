<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'anggota') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Proses peminjaman
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pinjam'])) {
    $buku_id = (int) $_POST['buku_id'];
    $tanggal_pinjam = date('Y-m-d');
    $tanggal_kembali = date('Y-m-d', strtotime('+7 days'));

    // Cek stok
    $cek_stok = mysqli_query($conn, "SELECT stok FROM buku WHERE id = $buku_id");
    $stok = mysqli_fetch_assoc($cek_stok);

    if ($stok && $stok['stok'] > 0) {

        // Kurangi stok
        mysqli_query($conn, "UPDATE buku SET stok = stok - 1 WHERE id = $buku_id");

        // Simpan transaksi
        $query = "INSERT INTO transaksi (user_id, buku_id, tanggal_pinjam, tanggal_kembali)
                  VALUES ($user_id, $buku_id, '$tanggal_pinjam', '$tanggal_kembali')";

        if (mysqli_query($conn, $query)) {
            $success = "Buku berhasil dipinjam!";
        } else {
            $error = "Gagal meminjam buku!";
        }

    } else {
        $error = "Stok buku habis!";
    }
}

// Ambil daftar buku tersedia
$query_buku = "SELECT * FROM buku WHERE stok > 0 ORDER BY judul ASC";
$buku_tersedia = mysqli_query($conn, $query_buku);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pinjam Buku</title>
<link rel="stylesheet" href="../assets/style.css">

<style>
.cover-img{
    width:60px;
    height:80px;
    object-fit:cover;
    border-radius:8px;
    border:1px solid #ddd;
}

.no-cover{
    width:60px;
    height:80px;
    background:#eee;
    color:#666;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:11px;
    border-radius:8px;
}
</style>

</head>

<body>

<div class="container">

    <div class="header">
        <h1>Pinjam Buku</h1>
        <a href="index.php" class="btn-back">Kembali</a>
        <a href="../logout.php" class="btn-logout">Logout</a>
    </div>

    <?php if($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <div class="section">
        <h2>Daftar Buku Tersedia</h2>

        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Cover</th>
                    <th>Judul Buku</th>
                    <th>Penulis</th>
                    <th>Penerbit</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php
            $no = 1;
            while($buku = mysqli_fetch_assoc($buku_tersedia)):
            ?>

                <tr>
                    <td><?= $no++; ?></td>

                    <td>
                        <?php if(!empty($buku['cover'])): ?>
                            <img src="../uploads/<?= $buku['cover']; ?>" class="cover-img">
                        <?php else: ?>
                            <div class="no-cover">No Cover</div>
                        <?php endif; ?>
                    </td>

                    <td><?= htmlspecialchars($buku['judul']) ?></td>
                    <td><?= htmlspecialchars($buku['penulis']) ?></td>
                    <td><?= htmlspecialchars($buku['penerbit']) ?></td>
                    <td><?= $buku['stok'] ?></td>

                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="buku_id" value="<?= $buku['id'] ?>">
                            <button type="submit" name="pinjam" class="btn-pinjam">
                                Pinjam
                            </button>
                        </form>
                    </td>
                </tr>

            <?php endwhile; ?>

            </tbody>
        </table>

    </div>

</div>

</body>
</html>