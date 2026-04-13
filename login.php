<?php
session_start();
require_once 'koneksi.php';

// =============================================
// CEK COOKIE "Remember Me"
// Kalau cookie ada, langsung redirect ke dashboard
// tanpa perlu login lagi
// =============================================
if (isset($_COOKIE['remember_user'])) {
    $cookie_data = $_COOKIE['remember_user'];
    
    // Cookie menyimpan format: "username|role"
    $parts = explode('|', $cookie_data);
    
    if (count($parts) == 2) {
        $saved_username = $parts[0];
        $saved_role     = $parts[1];
        
        // Isi session dari cookie
        $_SESSION['username'] = $saved_username;
        $_SESSION['role']     = $saved_role;
        
        // Redirect sesuai role
        if ($saved_role == 'pengawas') {
            header("Location: dashboard pengawas.php");
        } elseif ($saved_role == 'admin') {
            header("Location: Dashboard admin.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    }
}

// Cek session biasa (tanpa remember me)
if (isset($_SESSION['id_user']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'pengawas') {
        header("Location: dashboard pengawas.php");
    } elseif ($_SESSION['role'] == 'admin') {
        header("Location: Dashboard admin.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];

    // Cek apakah user mencentang "Remember Me"
    // Kalau dicentang → $_POST['remember'] ada, kalau tidak → tidak ada
    $remember = isset($_POST['remember']);

    if (empty($username) || empty($password)) {
        $error = "Username dan Password wajib diisi!";
    } else {
        $sql    = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            if (password_verify($password, $row['password'])) {
                // Simpan data ke session seperti biasa
                $_SESSION['id_user']  = $row['id_user'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role']     = $row['role'];
                $_SESSION['nama']     = $row['nama'];
                $_SESSION['no_kios']  = $row['no_kios'];
                $_SESSION['last_activity'] = time();
                

                // =============================================
                // SIMPAN COOKIE kalau "Remember Me" dicentang
                // =============================================
                if ($remember) {
                    $cookie_value = $row['username'] . '|' . $row['role'];
                    setcookie(
                        'remember_user',        // Nama cookie
                        $cookie_value,          // Nilai: "username|role"
                        time() + (3 * 60), // Expired bisa diatur
                        '/'                     // Berlaku di seluruh halaman
                    );
                }

                if ($row['role'] == 'pengawas') {
                    header("Location: dashboard pengawas.php");
                } elseif ($row['role'] == 'admin') {
                    header("Location: Dashboard admin.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                $error = "Password yang Anda masukkan salah!";
            }
        } else {
            $error = "Username tidak ditemukan!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login SIPESEL</title>
    <link href="./src/output.css" rel="stylesheet">
</head>

<body
    style="background-image: url('images/kios-pasar.jpg.jpeg');"
    class="min-h-screen flex items-center justify-center bg-cover bg-center">

    <div class="absolute inset-0 bg-black/50"></div>

    <div class="relative z-10 bg-white/10 backdrop-blur-lg p-8 md:p-10 rounded-2xl w-[90%] max-w-sm text-white text-center shadow-2xl">

        <img src="images/logo-sipesel.png" class="w-24 mx-auto mb-4" alt="Logo SIPESEL">
        <h2 class="text-xl md:text-2xl font-semibold mb-6">LOGIN</h2>

        <?php if ($error): ?>
            <div class="bg-red-500/80 text-white text-sm p-3 rounded-lg mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4">

            <input type="text" name="username" placeholder="Username" required
                   class="w-full p-3 rounded-lg bg-white text-black outline-none focus:ring-2 focus:ring-yellow-400"
                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">

            <input type="password" name="password" placeholder="Password" required
                   class="w-full p-3 rounded-lg bg-white text-black outline-none focus:ring-2 focus:ring-yellow-400">


            <!-- nambah checkbox remember me   -->
            <div class="flex items-center gap-2 text-sm text-left">
                <input type="checkbox" name="remember" id="remember"
                       class="w-4 h-4 accent-yellow-400">
                <label for="remember" class="text-gray-200 cursor-pointer">
                    remember me
                </label>
            </div>

            <button type="submit" name="login"
                    class="w-full bg-green-700 py-3 rounded-lg font-semibold hover:bg-green-800 transition shadow-lg">
                LOGIN
            </button>

        </form>

        <p class="text-sm text-gray-200 mt-6">
            Belum punya akun?
            <a href="register.php" class="text-yellow-400 font-semibold hover:underline">Daftar di sini</a>
        </p>
    </div>
</body>
</html>