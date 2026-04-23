<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'anggota') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Ambil data profil anggota
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Proses update profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $no_telepon = mysqli_real_escape_string($conn, $_POST['no_telepon']);
        $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

        $update = "UPDATE users SET 
                   nama_lengkap = '$nama_lengkap',
                   email = '$email',
                   no_telepon = '$no_telepon',
                   alamat = '$alamat'
                   WHERE id = '$user_id'";

        if (mysqli_query($conn, $update)) {
            $_SESSION['nama_lengkap'] = $nama_lengkap;
            $success = "Profil berhasil diperbarui!";
            // Refresh data
            $result = mysqli_query($conn, $query);
            $user = mysqli_fetch_assoc($result);
        } else {
            $error = "Gagal memperbarui profil: " . mysqli_error($conn);
        }
    }

    // Proses ganti password
    if (isset($_POST['change_password'])) {
        $password_lama = md5($_POST['password_lama']);
        $password_baru = $_POST['password_baru'];
        $konfirmasi_password = $_POST['konfirmasi_password'];

        // Cek password lama
        $check = mysqli_query($conn, "SELECT id FROM users WHERE id = '$user_id' AND password = '$password_lama'");

        if (mysqli_num_rows($check) == 0) {
            $error = "Password lama salah!";
        } elseif (strlen($password_baru) < 6) {
            $error = "Password baru minimal 6 karakter!";
        } elseif ($password_baru != $konfirmasi_password) {
            $error = "Konfirmasi password tidak sesuai!";
        } else {
            $password_baru_md5 = md5($password_baru);
            $update_pass = "UPDATE users SET password = '$password_baru_md5' WHERE id = '$user_id'";

            if (mysqli_query($conn, $update_pass)) {
                $success = "Password berhasil diubah!";
            } else {
                $error = "Gagal mengubah password: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Perpustakaan Digital</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            color: #2d3748;
            font-size: 24px;
        }

        .header p {
            color: #718096;
            margin-top: 5px;
        }

        .btn-logout {
            background: #dc2626;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-logout:hover {
            background: #c53030;
            transform: translateY(-2px);
        }

        /* Menu Navigasi */
        .menu {
            background: white;
            padding: 12px 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .menu a {
            padding: 10px 20px;
            text-decoration: none;
            color: #4a5568;
            border-radius: 8px;
            transition: all 0.3s;
            font-weight: 500;
        }

        .menu a:hover {
            background: #667eea;
            color: white;
        }

        /* Alert */
        .alert-success {
            background: #d1fae5;
            color: #059669;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #059669;
        }

        .alert-error {
            background: #fee2e2;
            color: #dc2626;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #dc2626;
        }

        /* Profile Card */
        .profile-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 60px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .profile-header h2 {
            margin-bottom: 5px;
            font-size: 28px;
        }

        .profile-header .no-anggota {
            background: rgba(255, 255, 255, 0.2);
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            margin-top: 10px;
            font-size: 14px;
        }

        .profile-body {
            padding: 40px;
        }

        /* Form Sections */
        .form-section {
            margin-bottom: 40px;
            padding-bottom: 40px;
            border-bottom: 2px solid #e2e8f0;
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .form-section h3 {
            color: #2d3748;
            margin-bottom: 25px;
            font-size: 22px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #4a5568;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .readonly-field {
            background: #f7fafc;
            padding: 12px 15px;
            border-radius: 10px;
            color: #718096;
            border: 2px solid #e2e8f0;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(72, 187, 120, 0.4);
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-aktif {
            background: #d1fae5;
            color: #059669;
        }

        .info-box {
            background: #ebf8ff;
            padding: 20px;
            border-radius: 15px;
            margin-top: 30px;
            border-left: 4px solid #4299e1;
        }

        .info-box h4 {
            color: #2b6cb0;
            margin-bottom: 10px;
        }

        .info-box ul {
            margin-left: 20px;
            color: #4a5568;
        }

        .info-box li {
            margin-bottom: 5px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full-width {
                grid-column: span 1;
            }

            .profile-body {
                padding: 25px;
            }

            .profile-header {
                padding: 25px;
            }

            .menu {
                flex-wrap: wrap;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Profil Saya</h1>
                <p>Kelola informasi akun Anda di sini</p>
            </div>
            <a href="../logout.php" class="btn-logout">Logout</a>
        </div>

        <div class="menu">
            <a href="index.php">Dashboard</a>
            <a href="koleksi_buku.php">Koleksi Buku</a>
            <a href="riwayat.php">Riwayat Peminjaman</a>
            <a href="anggota/profil_saya" style="background: #667eea; color: white;">Profil Saya</a>
        </div>

        <?php if ($success): ?>
            <div class="alert-success">
                ✅ <?= $success ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert-error">
                ⚠️ <?= $error ?>
            </div>
        <?php endif; ?>

        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php
                    // Ambil inisial nama untuk avatar
                    $inisial = strtoupper(substr($user['nama_lengkap'], 0, 1));
                    echo $inisial;
                    ?>
                </div>
                <h2><?= htmlspecialchars($user['nama_lengkap']) ?></h2>
                <p><?= htmlspecialchars($user['email']) ?></p>
                <div class="no-anggota">
                    No. Anggota: <?= $user['no_anggota'] ?>
                </div>
            </div>

            <div class="profile-body">
                <form method="POST">
                    <div class="form-section">
                        <h3>
                            Informasi Pribadi
                        </h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>No. Anggota</label>
                                <div class="readonly-field"><?= $user['no_anggota'] ?></div>
                            </div>
                            <div class="form-group">
                                <label>Username</label>
                                <div class="readonly-field"><?= htmlspecialchars($user['username']) ?></div>
                            </div>
                            <div class="form-group">
                                <label>Nama Lengkap *</label>
                                <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>No. Telepon</label>
                                <input type="tel" name="no_telepon" value="<?= htmlspecialchars($user['no_telepon'] ?? '') ?>" placeholder="Contoh: 08123456789">
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <div class="readonly-field">
                                    <span class="status-badge status-<?= $user['status'] ?>">
                                        <?= $user['status'] == 'aktif' ? '✅ Aktif' : '❌ Nonaktif' ?>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group full-width">
                                <label>Alamat Lengkap</label>
                                <textarea name="alamat" placeholder="Masukkan alamat lengkap Anda"><?= htmlspecialchars($user['alamat'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <button type="submit" name="update_profile" class="btn-primary">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>

                <form method="POST">
                    <div class="form-section">
                        <h3>
                            Keamanan Akun
                        </h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Password Lama *</label>
                                <input type="password" name="password_lama" placeholder="Masukkan password lama" required>
                            </div>
                            <div></div>
                            <div class="form-group">
                                <label>Password Baru *</label>
                                <input type="password" name="password_baru" placeholder="Minimal 6 karakter" required>
                                <small style="color: #718096;">Minimal 6 karakter</small>
                            </div>
                            <div class="form-group">
                                <label>Konfirmasi Password Baru *</label>
                                <input type="password" name="konfirmasi_password" placeholder="Ulangi password baru" required>
                            </div>
                        </div>
                        <button type="submit" name="change_password" class="btn-secondary">
                            Ganti Password
                        </button>
                    </div>
                </form>

                <div class="info-box">
                    <h4>Informasi Penting</h4>
                    <ul>
                        <li>No. Anggota dan Username tidak dapat diubah karena merupakan identitas unik Anda</li>
                        <li>Email akan digunakan untuk komunikasi penting dari perpustakaan</li>
                        <li>Gunakan password yang kuat dan jangan bagikan kepada siapapun</li>
                        <li>Pastikan nomor telepon yang didaftarkan aktif</li>
                        <li>Jika ada perubahan data, segera update agar kami bisa menghubungi Anda</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>

</html>