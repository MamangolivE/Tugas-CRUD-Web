<?php
include 'koneksi.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_hewan = (int)$_GET['id'];

    try {
        $sql = "DELETE FROM hewan WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id_hewan, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount()) {
             header('location: index.php?pesan=sukses_hapus');
        } else {
             header('location: index.php?pesan=gagal_hapus_tidak_ada_data');
        }
    } catch (\PDOException $e) {
        header('location: index.php?pesan=gagal_hapus_error');
    }

} else {
    header('location: index.php?pesan=id_tidak_valid');
}

exit();
?>