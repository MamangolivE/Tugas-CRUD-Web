<?php
include 'koneksi.php';

$data_per_halaman = 5;
$halaman_saat_ini = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$offset = ($halaman_saat_ini - 1) * $data_per_halaman;

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$where_clause = '';
$bind_params = [];

if (!empty($keyword)) {
    $where_clause = " WHERE nama LIKE :keyword_nama OR jenis LIKE :keyword_jenis ";
    $keyword_search = "%" . $keyword . "%";
    $bind_params[':keyword_nama'] = $keyword_search;
    $bind_params[':keyword_jenis'] = $keyword_search;
}

$query_total = "SELECT COUNT(*) FROM hewan" . $where_clause;
$stmt_total = $pdo->prepare($query_total);
$stmt_total->execute($bind_params);
$total_data = $stmt_total->fetchColumn();
$total_halaman = ceil($total_data / $data_per_halaman);

$query = "SELECT * FROM hewan" . $where_clause . " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);

foreach ($bind_params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->bindParam(':limit', $data_per_halaman, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$hewan_data = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Daftar Hewan Kebun Binatang</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <div class="container">
            <header>
                <h1>Daftar Hewan Kebun Binatang</h1>
                <a href="tambah.php" class="btn btn-tambah">Tambah Hewan Baru</a>
            </header>
            <form method="GET" action="index.php" class="search-form">
                <input type="text" name="keyword" placeholder="Cari Nama atau Jenis Hewan..." value="<?php echo htmlspecialchars($keyword); ?>">
                <button type="submit" class="btn btn-search">Cari</button>
                <?php if (!empty($keyword)): ?>
                    <a href="index.php" class="btn btn-reset">Reset</a>
                <?php endif; ?>
            </form>
            <main>
                <?php
                if(isset($_GET['pesan'])){
                    $p = $_GET['pesan'];
                    if($p == 'sukses_tambah') echo "<p class='alert-success'>✅ Data berhasil ditambahkan!</p>";
                    if($p == 'sukses_ubah') echo "<p class='alert-success'>✅ Data berhasil diubah!</p>";
                    if($p == 'sukses_hapus') echo "<p class='alert-success'>🗑️ Data berhasil dihapus!</p>";
                    if(strpos($p, 'gagal') !== false) echo "<p class='alert-error'>❌ Terjadi kesalahan!</p>";
                }

                if($total_data > 0) {
                ?>
                <p class="data-info">Menampilkan <?php echo $total_data; ?> data hewan.</p>
                <table>
                    <thead>
                        <tr>
                            <th>Key ID</th>
                            <th>Nama Hewan</th>
                            <th>Jenis</th>
                            <th>Asal</th>
                            <th>Deskripsi Singkat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        foreach($hewan_data as $data) {
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($data['id']); ?></td>
                            <td><?php echo htmlspecialchars($data['nama']); ?></td>
                            <td><?php echo htmlspecialchars($data['jenis']); ?></td>
                            <td><?php echo htmlspecialchars($data['asal']); ?></td>
                            <td><?php echo substr(htmlspecialchars($data['deskripsi']), 0, 50) . "..."; ?></td>
                            <td>
                                <a href="detail.php?id=<?php echo $data['id']; ?>" class="btn btn-detail">Detail</a>
                                <a href="ubah.php?id=<?php echo $data['id']; ?>" class="btn btn-edit">Ubah</a> 
                                <button
                                    class="btn btn-hapus btn-modal-hapus" 
                                    data-bs-id="<?php echo $data['id']; ?>" 
                                    data-bs-nama="<?php echo htmlspecialchars($data['nama']); ?>">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        <?php 
                        } 
                        ?>
                    </tbody>
                </table>
                <div class="pagination">
                    <?php if ($total_halaman > 1): ?>
                        <?php
                        $base_url = 'index.php?keyword=' . urlencode($keyword) . '&halaman=';
                        ?>
                        
                        <?php if ($halaman_saat_ini > 1): ?>
                            <a href="<?php echo $base_url . ($halaman_saat_ini - 1); ?>" class="page-link">← Sebelumnya</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                            <a href="<?php echo $base_url . $i; ?>" class="page-link <?php echo ($i == $halaman_saat_ini) ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($halaman_saat_ini < $total_halaman): ?>
                            <a href="<?php echo $base_url . ($halaman_saat_ini + 1); ?>" class="page-link">Selanjutnya →</a>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>
                
                <?php 
                } else {
                    echo "<p class='info'>Tidak ada data hewan yang ditemukan " . (!empty($keyword) ? "untuk keyword '<b>" . htmlspecialchars($keyword) . "</b>'." : "di kebun binatang ini.") . "</p>";
                }
                ?>
            </main>
            <footer>
                <p>&copy; TUGAS CRUD 2409106020_ZULFIKAR ARYAWINATA</p>
            </footer>
        </div>
        <div id="modalHapus" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Konfirmasi Hapus Data</h2>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menghapus data hewan "<span id="hewanNama"></span>"?</p>
                    <p style="font-size: 0.9em; color: #777;">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" id="btnBatal">Batal</button>
                    <a href="#" id="btnKonfirmasiHapus" class="btn btn-hapus">Ya, Hapus!</a>
                </div>
            </div>
        </div>
    </body>
<script src="scripts.js"></script>
</html>