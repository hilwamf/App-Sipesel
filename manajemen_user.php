<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("location:login.php?pesan=akses_ditolak");
    exit();
}

require_once 'koneksi.php';

$username = $_SESSION['username'];
$inisial = strtoupper(substr($username, 0, 1));

// Handle CRUD Operations
$success_msg = '';
$error_msg = '';

// DELETE USER
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id_user = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Tidak boleh hapus diri sendiri
    if ($id_user == $_SESSION['id_user']) {
        $error_msg = "Anda tidak dapat menghapus akun Anda sendiri!";
    } else {
        $sql = "DELETE FROM users WHERE id_user = $id_user";
        if (mysqli_query($conn, $sql)) {
            $success_msg = "User berhasil dihapus!";
        } else {
            $error_msg = "Gagal menghapus user: " . mysqli_error($conn);
        }
    }
}

// ADD USER
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $username_new = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $nomor_hp = mysqli_real_escape_string($conn, trim($_POST['nomor_hp']));
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $password = password_hash('password123', PASSWORD_DEFAULT); // Default password
    $no_kios = $role == 'pedagang' ? mysqli_real_escape_string($conn, trim($_POST['no_kios'])) : NULL;
    
    // Check username
    $check = "SELECT * FROM users WHERE username = '$username_new'";
    if (mysqli_num_rows(mysqli_query($conn, $check)) > 0) {
        $error_msg = "Username sudah digunakan!";
    } else {
        $sql = "INSERT INTO users (nama, username, password, email, nomor_hp, gender, role, no_kios) 
                VALUES ('$nama', '$username_new', '$password', '$email', '$nomor_hp', '$gender', '$role', " . ($no_kios ? "'$no_kios'" : "NULL") . ")";
        
        if (mysqli_query($conn, $sql)) {
            $success_msg = "User berhasil ditambahkan! Password default: password123";
        } else {
            $error_msg = "Gagal menambahkan user: " . mysqli_error($conn);
        }
    }
}

// UPDATE USER
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $id_user = mysqli_real_escape_string($conn, $_POST['id_user']);
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $nomor_hp = mysqli_real_escape_string($conn, trim($_POST['nomor_hp']));
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $no_kios = $role == 'pedagang' ? mysqli_real_escape_string($conn, trim($_POST['no_kios'])) : NULL;
    
    $sql = "UPDATE users SET 
            nama = '$nama', 
            email = '$email', 
            nomor_hp = '$nomor_hp', 
            gender = '$gender', 
            role = '$role',
            no_kios = " . ($no_kios ? "'$no_kios'" : "NULL") . "
            WHERE id_user = $id_user";
    
    if (mysqli_query($conn, $sql)) {
        $success_msg = "User berhasil diupdate!";
    } else {
        $error_msg = "Gagal mengupdate user: " . mysqli_error($conn);
    }
}

// RESET PASSWORD
if (isset($_GET['reset_password']) && isset($_GET['id'])) {
    $id_user = mysqli_real_escape_string($conn, $_GET['id']);
    $new_password = password_hash('password123', PASSWORD_DEFAULT);
    
    $sql = "UPDATE users SET password = '$new_password' WHERE id_user = $id_user";
    if (mysqli_query($conn, $sql)) {
        $success_msg = "Password berhasil direset ke: password123";
    } else {
        $error_msg = "Gagal reset password: " . mysqli_error($conn);
    }
}

// GET ALL USERS
$filter_role = isset($_GET['filter']) ? mysqli_real_escape_string($conn, $_GET['filter']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$sql = "SELECT * FROM users WHERE 1=1";
if ($filter_role) {
    $sql .= " AND role = '$filter_role'";
}
if ($search) {
    $sql .= " AND (nama LIKE '%$search%' OR username LIKE '%$search%' OR email LIKE '%$search%')";
}
$sql .= " ORDER BY created_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - SIPESEL Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .modal { display: none; }
        .modal.show { display: flex; }
    </style>
</head>

<body style="background-image: url('images/kios2.jpg');" class="relative min-h-screen text-white bg-cover bg-center">
    <div class="absolute inset-0 bg-black/40"></div>

    <!-- Header (sama seperti file lain) -->
    <header class="bg-green-800 shadow-xl sticky top-0 z-50">
        <div class="border-b border-green-600/50 py-2 px-6">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <span class="font-extrabold text-yellow-400 text-2xl tracking-widest drop-shadow-sm" style="font-family: 'Montserrat', sans-serif;">SIPESEL</span>
                <span class="text-green-300 text-xs font-medium tracking-wide hidden sm:block">Sistem Informasi Pembayaran Pajak Pasar - ADMIN PANEL</span>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center text-green-900 font-bold text-sm"><?php echo $inisial; ?></div>
                    <span class="text-green-100 text-sm font-medium hidden sm:block"><?php echo htmlspecialchars($username); ?></span>
                    <span class="px-2 py-1 bg-red-500/80 text-white text-xs font-bold rounded ml-2">ADMIN</span>
                </div>
            </div>
        </div>
        <nav class="max-w-7xl mx-auto px-6 flex items-center gap-1 py-2 overflow-x-auto">
            <a href="Dashboard admin.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200 whitespace-nowrap">Dashboard</a>
            <a href="verifikasi_admin.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200 whitespace-nowrap">Verifikasi</a>
            <a href="manajemen_user.php" class="px-4 py-2 rounded-lg bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold text-sm whitespace-nowrap">Users</a>
            <a href="manajemen_kios.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200 whitespace-nowrap">Kios</a>
            <a href="laporan_admin.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200 whitespace-nowrap">Laporan</a>
            <a href="monitoring_admin.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200 whitespace-nowrap">Monitoring</a>
            <a href="setting_sistem.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200 whitespace-nowrap">Setting</a>
            <div class="ml-auto">
                <a href="logout.php" class="px-4 py-2 rounded-lg bg-red-500/80 hover:bg-red-500 text-white text-sm font-semibold transition-all duration-200 whitespace-nowrap">Keluar</a>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <section class="relative z-10 px-6 md:px-20 py-8 min-h-screen">
        <div class="max-w-7xl mx-auto">
            
            <div class="mb-6">
                <h1 class="text-3xl md:text-4xl font-bold mb-2">Manajemen Pengguna</h1>
                <p class="text-sm md:text-base opacity-90">Kelola data pedagang, pengawas, dan admin</p>
            </div>

            <!-- Alert Messages -->
            <?php if ($success_msg): ?>
            <div class="bg-green-500/30 border border-green-500/50 text-white rounded-lg p-4 mb-6">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span><?php echo $success_msg; ?></span>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($error_msg): ?>
            <div class="bg-red-500/30 border border-red-500/50 text-white rounded-lg p-4 mb-6">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <span><?php echo $error_msg; ?></span>
                </div>
            </div>
            <?php endif; ?>

            <!-- Filters & Actions -->
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20 mb-6">
                <div class="flex flex-col md:flex-row gap-4 justify-between">
                    <div class="flex flex-col md:flex-row gap-4 flex-1">
                        <!-- Search -->
                        <form method="GET" class="flex-1">
                            <input type="text" name="search" placeholder="Cari nama, username, atau email..." value="<?php echo htmlspecialchars($search); ?>"
                                class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        </form>

                        <!-- Filter Role -->
                        <form method="GET" class="flex gap-2">
                            <select name="filter" onchange="this.form.submit()" class="px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                <option value="">Semua Role</option>
                                <option value="pedagang" <?php echo $filter_role == 'pedagang' ? 'selected' : ''; ?>>Pedagang</option>
                                <option value="pengawas" <?php echo $filter_role == 'pengawas' ? 'selected' : ''; ?>>Pengawas</option>
                                <option value="admin" <?php echo $filter_role == 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </form>
                    </div>

                    <!-- Add User Button -->
                    <button onclick="showAddModal()" class="px-6 py-2 bg-green-500/30 hover:bg-green-500/50 border border-green-500/50 rounded-lg font-semibold transition-all flex items-center gap-2 whitespace-nowrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah User
                    </button>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-white/10 border-b border-white/20">
                                <th class="px-6 py-4 text-left text-sm font-semibold">Nama</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Username</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Email</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Role</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">No. Kios</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="border-b border-white/10 hover:bg-white/5 transition-all">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="font-semibold"><?php echo htmlspecialchars($row['nama']); ?></div>
                                        <div class="text-xs opacity-70"><?php echo htmlspecialchars($row['nomor_hp']); ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($row['username']); ?></td>
                                <td class="px-6 py-4 text-sm"><?php echo htmlspecialchars($row['email']); ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        <?php 
                                            if ($row['role'] == 'admin') echo 'bg-red-500/30 text-red-300';
                                            elseif ($row['role'] == 'pengawas') echo 'bg-blue-500/30 text-blue-300';
                                            else echo 'bg-green-500/30 text-green-300';
                                        ?>">
                                        <?php echo ucfirst($row['role']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-semibold"><?php echo $row['no_kios'] ? htmlspecialchars($row['no_kios']) : '-'; ?></td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <button onclick='showEditModal(<?php echo json_encode($row); ?>)' class="px-3 py-1 bg-blue-500/30 hover:bg-blue-500/50 border border-blue-500/50 rounded text-xs font-medium transition-all">Edit</button>
                                        <a href="?reset_password=1&id=<?php echo $row['id_user']; ?>" onclick="return confirm('Reset password ke password123?')" class="px-3 py-1 bg-yellow-500/30 hover:bg-yellow-500/50 border border-yellow-500/50 rounded text-xs font-medium transition-all">Reset PW</a>
                                        <?php if ($row['id_user'] != $_SESSION['id_user']): ?>
                                        <a href="?delete=1&id=<?php echo $row['id_user']; ?>" onclick="return confirm('Hapus user ini?')" class="px-3 py-1 bg-red-500/30 hover:bg-red-500/50 border border-red-500/50 rounded text-xs font-medium transition-all">Hapus</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>

    <!-- Modal Add User -->
    <div id="modalAdd" class="modal fixed inset-0 bg-black/70 backdrop-blur-sm items-center justify-center z-50">
        <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 max-w-md mx-4 border border-white/20 max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-bold mb-4">Tambah User Baru</h3>
            <form method="POST">
                <div class="space-y-4">
                    <input type="text" name="nama" placeholder="Nama Lengkap" required class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-green-400">
                    <input type="text" name="username" placeholder="Username" required class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-green-400">
                    <input type="email" name="email" placeholder="Email" required class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-green-400">
                    <input type="text" name="nomor_hp" placeholder="Nomor HP" required class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-green-400">
                    
                    <select name="gender" required class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-400">
                        <option value="">Pilih Gender</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>

                    <select name="role" id="addRole" onchange="toggleKiosAdd()" required class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-400">
                        <option value="">Pilih Role</option>
                        <option value="pedagang">Pedagang</option>
                        <option value="pengawas">Pengawas</option>
                        <option value="admin">Admin</option>
                    </select>

                    <div id="kiosFieldAdd" style="display:none;">
                        <input type="text" name="no_kios" placeholder="No. Kios (contoh: A-01)" class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>

                    <p class="text-xs opacity-70">*Password default: password123</p>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="closeAddModal()" class="flex-1 px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg font-medium transition-all">Batal</button>
                    <button type="submit" name="add_user" class="flex-1 px-4 py-2 bg-green-500/30 hover:bg-green-500/50 border border-green-500/50 rounded-lg font-medium transition-all">Tambah</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit User -->
    <div id="modalEdit" class="modal fixed inset-0 bg-black/70 backdrop-blur-sm items-center justify-center z-50">
        <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 max-w-md mx-4 border border-white/20 max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-bold mb-4">Edit User</h3>
            <form method="POST">
                <input type="hidden" name="id_user" id="editId">
                <div class="space-y-4">
                    <input type="text" name="nama" id="editNama" placeholder="Nama Lengkap" required class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <input type="email" name="email" id="editEmail" placeholder="Email" required class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <input type="text" name="nomor_hp" id="editNomorHp" placeholder="Nomor HP" required class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    
                    <select name="gender" id="editGender" required class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>

                    <select name="role" id="editRole" onchange="toggleKiosEdit()" required class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="pedagang">Pedagang</option>
                        <option value="pengawas">Pengawas</option>
                        <option value="admin">Admin</option>
                    </select>

                    <div id="kiosFieldEdit">
                        <input type="text" name="no_kios" id="editNoKios" placeholder="No. Kios" class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="closeEditModal()" class="flex-1 px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg font-medium transition-all">Batal</button>
                    <button type="submit" name="update_user" class="flex-1 px-4 py-2 bg-blue-500/30 hover:bg-blue-500/50 border border-blue-500/50 rounded-lg font-medium transition-all">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showAddModal() {
            document.getElementById('modalAdd').classList.add('show');
        }
        function closeAddModal() {
            document.getElementById('modalAdd').classList.remove('show');
        }

        function showEditModal(user) {
            document.getElementById('editId').value = user.id_user;
            document.getElementById('editNama').value = user.nama;
            document.getElementById('editEmail').value = user.email;
            document.getElementById('editNomorHp').value = user.nomor_hp;
            document.getElementById('editGender').value = user.gender;
            document.getElementById('editRole').value = user.role;
            document.getElementById('editNoKios').value = user.no_kios || '';
            toggleKiosEdit();
            document.getElementById('modalEdit').classList.add('show');
        }
        function closeEditModal() {
            document.getElementById('modalEdit').classList.remove('show');
        }

        function toggleKiosAdd() {
            const role = document.getElementById('addRole').value;
            const kiosField = document.getElementById('kiosFieldAdd');
            if (role === 'pedagang') {
                kiosField.style.display = 'block';
            } else {
                kiosField.style.display = 'none';
            }
        }

        function toggleKiosEdit() {
            const role = document.getElementById('editRole').value;
            const kiosField = document.getElementById('kiosFieldEdit');
            if (role === 'pedagang') {
                kiosField.style.display = 'block';
            } else {
                kiosField.style.display = 'none';
            }
        }
    </script>

</body>
</html>