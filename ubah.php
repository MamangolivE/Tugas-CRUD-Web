<?php
include 'koneksi.php';
$pesan_error = '';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header('location: index.php?pesan=id_tidak_valid');
    exit();
}

$id_hewan = (int)$_GET['id'];

try {
    $stmt_read = $pdo->prepare("SELECT * FROM hewan WHERE id = :id_hewan");
    $stmt_read->bindParam(':id_hewan', $id_hewan, PDO::PARAM_INT);
    $stmt_read->execute();
    $data_hewan = $stmt_read->fetch();

    if (!$data_hewan) {
        header('location: index.php?pesan=data_tidak_ditemukan');
        exit();
    }
    $nama = $data_hewan['nama'];
    $jenis = $data_hewan['jenis'];
    $asal = $data_hewan['asal'];
    $deskripsi = $data_hewan['deskripsi'];

} catch (\PDOException $e) {
    $pesan_error = "Gagal mengambil data: " . $e->getMessage();
}

if (isset($_POST['submit'])) {
    $nama = trim($_POST['nama']);
    $jenis = trim($_POST['jenis']);
    $asal = trim($_POST['asal']);
    $deskripsi = trim($_POST['deskripsi']);
    if (empty($nama) || empty($jenis) || empty($asal)) {
        $pesan_error = "Nama, Jenis, dan Asal wajib diisi!";
    } else {
        try {
            $sql_update = "UPDATE hewan SET nama = :nama, jenis = :jenis, asal = :asal, deskripsi = :deskripsi WHERE id = :id";            
            $stmt_update = $pdo->prepare($sql_update);            
            $stmt_update->execute([
                ':nama' => $nama,
                ':jenis' => $jenis,
                ':asal' => $asal,
                ':deskripsi' => $deskripsi,
                ':id' => $id_hewan
            ]);
            header('location: index.php?pesan=sukses_ubah');
            exit();

        } catch (\PDOException $e) {
            $pesan_error = "Gagal mengubah data (PDO Error): " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Data Hewan: <?php echo htmlspecialchars($nama); ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container form-container">
        <h1>Ubah Data Hewan</h1>
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
                <label for="asal">Asal Negara/Wilayah *</label>
                <input type="text" id="asal" name="asal" required value="<?php echo htmlspecialchars($asal); ?>">
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="5"><?php echo htmlspecialchars($deskripsi); ?></textarea>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</body>
</html>