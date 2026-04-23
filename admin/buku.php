<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| Ambil data buku
|--------------------------------------------------------------------------
*/
$query = "SELECT * FROM buku ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Buku</title>
<link rel="stylesheet" href="../assets/style.css">

<style>
.cover-img{
    width:60px;
    height:80px;
    object-fit:cover;
    border-radius:6px;
    border:1px solid #ddd;
}

.no-cover{
    width:60px;
    height:80px;
    background:#eee;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:12px;
    color:#666;
    border-radius:6px;
}
</style>

</head>

<body>

<div class="container">

    <div class="header">
        <h1>Kelola Data Buku</h1>
        <a href="index.php" class="btn-back">Kembali</a>
        <a href="../logout.php" class="btn-logout">Logout</a>
    </div>

    <div class="action-bar">
        <a href="tambah_buku.php" class="btn-add">+ Tambah Buku</a>
    </div>

    <table class="data-table">

        <thead>
        <tr>
            <th>No</th>
            <th>Cover</th>
            <th>Judul Buku</th>
            <th>Penulis</th>
            <th>Penerbit</th>
            <th>Tahun</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
        </thead>

        <tbody>

        <?php
        $no = 1;

        while ($row = mysqli_fetch_assoc($result)) :
        ?>

        <tr>
            <td><?= $no++; ?></td>

            <td>
                <?php if (!empty($row['cover'])) : ?>
                    <img src="../uploads/<?= $row['cover']; ?>" class="cover-img">
                <?php else : ?>
                    <div class="no-cover">No Cover</div>
                <?php endif; ?>
            </td>

            <td><?= htmlspecialchars($row['judul'] ?? '-'); ?></td>

            <td><?= htmlspecialchars($row['penulis'] ?? '-'); ?></td>

            <td><?= htmlspecialchars($row['penerbit'] ?? '-'); ?></td>

            <td><?= htmlspecialchars($row['tahun_terbit'] ?? '-'); ?></td>

            <td><?= htmlspecialchars($row['stok'] ?? '0'); ?></td>

            <td>
                <a href="edit_buku.php?id=<?= $row['id']; ?>" class="btn-edit">Edit</a>

                <a href="hapus_buku.php?id=<?= $row['id']; ?>"
                   class="btn-delete"
                   onclick="return confirm('Yakin hapus buku ini?')">
                   Hapus
                </a>
            </td>
        </tr>

        <?php endwhile; ?>

        </tbody>

    </table>

</div>

</body>
</html>