<?php
$page_title = "Daftar Undangan";
// Menyisipkan file header, navbar, dan fungsi-fungsi terkait user
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'function/undangan_functions.php';

// Mendapatkan parameter dari URL
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';
$search = $_GET['search'] ?? '';
$search_query = $_POST['search_query'] ?? '';

// Handle pengiriman form tambah/edit user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$search_query) {
    $nama = htmlspecialchars($_POST['Nama']);
    $alamat = $_POST['Alamat'];
   
        // Simpan user (tambah/edit)
        saveundangan($action, $id, $nama, $alamat, $db);
}

// Handle aksi hapus user
if ($action === 'delete' && $id) {
    deleteundangan($id, $db);
}

// Ambil data user untuk form edit (jika ada)
$current_undangan = ($action === 'edit' && $id) ? getundanganById($id, $db) : null;

// Ambil data semua user atau hasil pencarian
if ($search_query) {
    $undangan = searchundangan($search_query, $db);
} else {
    $undangan = getAllundangan($db);
}
?>

<!-- Tampilkan pesan sukses jika ada -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<!-- Tampilkan pesan error jika ada -->
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="container">
    <!-- Judul dan tombol tambah/edit user -->
    <div>
        <h2>Manajemen Undangan</h2>
        <button class="btn btn-primary" onclick="toggleUserForm()">
            <?= $current_undangan ? 'Edit undangan' : 'Tambah undangan' ?>
        </button>
    </div>

    <!-- Form pencarian user -->
    <form method="POST" action="?page=undangan<?= $current_undangan ?>">
        <div class="input-group">
            <input type="text" name="search_query" class="form-control" placeholder="Cari..."
             value="<?= htmlspecialchars($search_query) ?>">
            <button class="btn btn-outline-secondary" type="submit">Cari</button>
            <?php if ($search_query): ?>
                <a href="?page=undangan" class="btn btn-outline-danger">Reset</a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Form Tambah/Edit User -->
    <div id="userForm" style="display: <?= ($action === 'add' || $action === 'edit') ? 'block' : 'none' ?>;">
        <div class="card mb-4">
            <div class="card-body">
                <h4><?= $current_undangan ? 'Edit User' : 'Tambah Undangan Baru' ?></h4>
                <?php if (!empty($errors)): ?>
                    <!-- Tampilkan error validasi -->
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <p><?= $error ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <!-- Form input user -->
                <form method="POST" action="?page=undangan<?= $current_undangan ? '&action=edit&id=' . $current_undangan['id'] : '&action=add' ?>">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="Nama" class="form-control" required
                         value="<?= $current_undangan['Nama'] ?? '' ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="Alamat"><?= $current_undangan['Alamat'] ?? '' ?> </textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="?page=undangan" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabel Data User -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($undangan) > 0): ?>
                            <?php foreach ($undangan as $i => $u): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($u['Nama']) ?></td>
                                    <td><?= htmlspecialchars($u['Alamat']) ?></td>
                                    <td>
                                        <a href="?page=undangan&action=edit&id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                            <a href="?page=undangan&action=delete&id=<?= $u['id'] ?>" onclick="return confirm('Apakah Anda yakin?')" class="btn btn-sm btn-danger">Hapus</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data user</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
