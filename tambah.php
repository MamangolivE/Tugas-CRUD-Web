<?php
include 'koneksi.php';

if (isset($_POST['submit'])) {
    $nama = trim($_POST['nama']);
    $jenis = trim($_POST['jenis']);
    $asal = trim($_POST['asal']);
    $deskripsi = trim($_POST['deskripsi']);
    $pesan_error = '';
    if (empty($nama) || empty($jenis) || empty($asal)) {
        $pesan_error = "Nama, Jenis, dan Asal wajib diisi!";
    } else {
        try {
            $sql = "INSERT INTO hewan (nama, jenis, asal, deskripsi) VALUES (:nama, :jenis, :asal, :deskripsi)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nama' => $nama,
                ':jenis' => $jenis,
                ':asal' => $asal,
                ':deskripsi' => $deskripsi
            ]);
            header('location: index.php?pesan=sukses_tambah');
            exit();

        } catch (\PDOException $e) {
            $pesan_error = "Gagal menyimpan data (PDO Error): " . $e->getMessage();
        }
    }
} else {
    $nama = '';
    $jenis = '';
    $asal = '';
    $deskripsi = '';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Hewan Baru</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container form-container">
        <h1>Tambah Hewan Baru</h1>
        <a href="index.php" class="btn btn-back">← Kembali ke Daftar</a>
        
        <?php if (!empty($pesan_error)): ?>
            <p class="alert-error"><?php echo htmlspecialchars($pesan_error); ?></p>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="nama">Nama Hewan *</label>
                <input type="text" id="nama" name="nama" required value="<?php echo htmlspecialchars($nama); ?>">
            </div>
            <div class="form-group">
                <label for="jenis">Jenis (Mamalia/Reptil/dll) *</label>
                <input type="text" id="jenis" name="jenis" required value="<?php echo htmlspecialchars($jenis); ?>">
            </div>
            <div class="form-group">
                <label for="asal">Asal Negara-Benua *</label>
                <input type="text" id="asal" name="asal" required value="<?php echo htmlspecialchars($asal); ?>">
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="5"><?php echo htmlspecialchars($deskripsi); ?></textarea>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Simpan Data</button>
        </form>
    </div>
</body>
</html>