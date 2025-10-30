<?php
include 'koneksi.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header('location: index.php?pesan=id_tidak_valid');
    exit();
}

$id_hewan = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM hewan WHERE id = :id");
    $stmt->bindParam(':id', $id_hewan, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch();

    if (!$data) {
        header('location: index.php?pesan=data_tidak_ditemukan');
        exit();
    }

} catch (\PDOException $e) {
    die("Error mengambil data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Hewan: <?php echo htmlspecialchars($data['nama']); ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container form-container">
        <h1>Detail Hewan: <?php echo htmlspecialchars($data['nama']); ?></h1>
        <a href="index.php" class="btn btn-back">← Kembali ke Daftar</a>

        <div class="detail-card">
            <table>
                <tr>
                    <th>ID Kunci</th>
                    <td><?php echo htmlspecialchars($data['id']); ?></td>
                </tr>
                <tr>
                    <th>Jenis</th>
                    <td><?php echo htmlspecialchars($data['jenis']); ?></td>
                </tr>
                <tr>
                    <th>Asal</th>
                    <td><?php echo htmlspecialchars($data['asal']); ?></td>
                </tr>
                <tr>
                    <th>Ditambahkan Pada</th>
                    <td><?php echo date('d M Y H:i:s', strtotime($data['created_at'])); ?></td>
                </tr>
            </table>
            <div class="deskripsi-section">
                <h3>Deskripsi Lengkap:</h3>
                <p><?php echo nl2br(htmlspecialchars($data['deskripsi'])); ?></p>
            </div>
            <div class="aksi-detail">
                <a href="ubah.php?id=<?php echo $data['id']; ?>" class="btn btn-edit">Ubah Data</a> 
                <button 
                    class="btn btn-hapus btn-modal-hapus" 
                    data-bs-id="<?php echo $data['id']; ?>" 
                    data-bs-nama="<?php echo htmlspecialchars($data['nama']); ?>">
                    Hapus
                </button>
            </div>
        </div>
    </div>
</body>
</html>