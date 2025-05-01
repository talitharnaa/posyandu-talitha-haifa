<?php
$database = new Database();
$db = $database->getConnection();

function validateUserInput($username, $password, $confirm_password, $action, $id, $db) {
    $errors = [];
    if ($password !== $confirm_password) {
        $errors[] = "Konfirmasi password tidak cocok";
    }
    $stmt = $db->prepare("SELECT id FROM users WHERE username = :username AND id != :id");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $errors[] = "Username sudah digunakan";
    }
    return $errors;
}

function saveundangan($action, $id, $nama, $alamat, $db) {
    if ($action === 'edit' && $id) {
            $stmt = $db->prepare("UPDATE undangan SET Nama=?, Alamat=? WHERE id=?");
            $stmt->execute([$nama, $alamat, $id]);
        $_SESSION['success'] = "undangan berhasil diperbarui!";
    } else {
        $stmt = $db->prepare("INSERT INTO undangan (Nama, Alamat) VALUES (?, ?)");
        $stmt->execute([$nama, $alamat]);
        $_SESSION['success'] = "undangan berhasil ditambahkan!";
    }
    header("Location: ?page=undangan");
    exit();
}

function deleteundangan($id, $db) {
    if ($id == $_SESSION['user_id']) {
        $_SESSION['error'] = "Anda tidak dapat menghapus akun sendiri!";
    } else {
        $stmt = $db->prepare("DELETE FROM undangan WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "undangan berhasil dihapus!";
    }
    header("Location: ?page=undangan");
    exit();
}

function getundanganById($id, $db) {
    $stmt = $db->prepare("SELECT * FROM undangan WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getAllundangan($db) {
    $query = "SELECT * FROM undangan ORDER BY id ASC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll();
}

function searchundangan($search, $db) {
    $query = "SELECT * FROM undangan";
    if ($search) {
        $query .= " WHERE Nama LIKE ? OR Alamat LIKE ?";
    }
    $query .= " ORDER BY id ASC";
    $stmt = $db->prepare($query);
    if ($search) {
        $search_param = "%$search%";
        $stmt->execute([$search_param, $search_param]);
    } else {
        $stmt->execute();
    }
    return $stmt->fetchAll();
}
