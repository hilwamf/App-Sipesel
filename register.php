<?php
require_once 'koneksi.php';

$message = '';
$message_type = '';

// Proses form jika ada submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil data dari form
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $nomor_hp = mysqli_real_escape_string($conn, trim($_POST['nomor_hp']));
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $no_kios = mysqli_real_escape_string($conn, trim($_POST['no_kios'])); // Menangkap input dari dropdown
    
    // Validasi server-side
    if (empty($nama) || empty($username) || empty($password) || empty($email) || empty($nomor_hp) || empty($gender) || empty($role)) {
        $message = 'Semua field harus diisi!';
        $message_type = 'error';
    } else {
        // Cek username sudah ada atau belum
        $check_username = "SELECT * FROM users WHERE username = '$username'";
        $result_username = mysqli_query($conn, $check_username);
        
        // Cek email sudah ada atau belum
        $check_email = "SELECT * FROM users WHERE email = '$email'";
        $result_email = mysqli_query($conn, $check_email);
        
        if (mysqli_num_rows($result_username) > 0) {
            $message = 'Username sudah digunakan!';
            $message_type = 'error';
        } elseif (mysqli_num_rows($result_email) > 0) {
            $message = 'Email sudah terdaftar!';
            $message_type = 'error';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert ke database (Alamat dihapus, Role ditambahkan)
            $sql = "INSERT INTO users (nama, username, password, email, nomor_hp, gender, role, no_kios) 
                    VALUES ('$nama', '$username', '$hashed_password', '$email', '$nomor_hp', '$gender', '$role', '$no_kios')";
            
            if (mysqli_query($conn, $sql)) {
                $message = 'Berhasil daftar 🎉';
                $message_type = 'success';
                
                // Redirect ke login setelah 1.5 detik
                header("refresh:1.5;url=login.php");
            } else {
                $message = 'Gagal mendaftar: ' . mysqli_error($conn);
                $message_type = 'error';
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
    <title>Register SIPESEL</title>
    <link href="./src/output.css" rel="stylesheet">
</head>

<body 
    style="background-image: url('images/kios2.jpg');"
    class="min-h-screen bg-cover bg-center flex items-center justify-center p-4">

    <div class="absolute inset-0 bg-black/60"></div>

    <div class="relative z-10 flex w-full max-w-5xl rounded-2xl overflow-hidden shadow-2xl">

        <div class="hidden md:flex w-1/2 bg-gradient-to-br from-green-800 to-green-900 flex-col items-center justify-center text-white p-8 text-center">
            <h1 class="text-5xl font-bold text-yellow-400 mb-3">SIPESEL</h1>
            <p class="text-lg opacity-90">Sistem Pengelolaan Sewa Kios</p>
        </div>

        <div class="w-full md:w-1/2 bg-white p-8 md:p-10">

            <h2 class="text-2xl font-semibold text-green-800 mb-6">
                Form Pendaftaran
            </h2>

            <?php if ($message): ?>
                <div class="mb-4 p-3 rounded-lg <?php echo $message_type == 'success' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form id="registerForm" method="POST" action="" class="space-y-4">

                <input type="text" id="nama" name="nama" placeholder="Nama Lengkap" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent outline-none transition"
                    value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>">

                <input type="text" name="username" placeholder="Username" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent outline-none transition"
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">

                <input type="email" name="email" placeholder="Email" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent outline-none transition"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

                <input type="text" name="nomor_hp" placeholder="Nomor HP" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent outline-none transition"
                    value="<?php echo isset($_POST['nomor_hp']) ? htmlspecialchars($_POST['nomor_hp']) : ''; ?>">

                <input type="text" name="no_kios" id="no_kios" placeholder="Nomor Kios (Contoh: A-01)" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent outline-none transition"
                    value="<?php echo isset($_POST['no_kios']) ? htmlspecialchars($_POST['no_kios']) : ''; ?>">            

                <input type="password" name="password" placeholder="Password" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent outline-none transition">

                <select name="role" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent outline-none transition bg-white text-gray-700 cursor-pointer">
                    <option value="" disabled <?php echo empty($_POST['role']) ? 'selected' : ''; ?>>Pilih Peran...</option>
                    <option value="pedagang" <?php echo (isset($_POST['role']) && $_POST['role'] == 'pedagang') ? 'selected' : ''; ?>>Pedagang</option>
                    <option value="pengawas" <?php echo (isset($_POST['role']) && $_POST['role'] == 'pengawas') ? 'selected' : ''; ?>>Pengawas</option>
                </select>

                <div>
                    <p class="text-sm text-gray-700 font-medium mb-2">Gender</p>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="gender" value="Laki-laki" required
                                class="w-4 h-4 text-green-600 focus:ring-green-500"
                                <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Laki-laki') ? 'checked' : ''; ?>>
                            <span class="text-gray-700">Laki-laki</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="gender" value="Perempuan" required
                                class="w-4 h-4 text-green-600 focus:ring-green-500"
                                <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Perempuan') ? 'checked' : ''; ?>>
                            <span class="text-gray-700">Perempuan</span>
                        </label>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-green-700 text-white py-3 rounded-lg font-semibold hover:bg-green-800 hover:shadow-lg transition-all duration-200">
                    DAFTAR
                </button>

            </form>

            <p class="text-sm text-center mt-6 text-gray-600">
                Sudah punya akun?
                <a href="login.php" class="text-green-700 font-semibold hover:underline">
                    Login sekarang
                </a>
            </p>

        </div>

    </div>

<script>
document.addEventListener("DOMContentLoaded", function(){
    const form = document.getElementById("registerForm");
    const roleSelect = document.querySelector('select[name="role"]');
    const kiosContainer = document.getElementById("kios_container");
    const kiosInput = document.getElementById("no_kios");

    form.addEventListener("submit", function(e){
        
        const regexNama = /^[a-zA-Z0-9' ]+$/;

        if (!regexNama.test(namaInput.value)) {
            e.preventDefault();
            alert("Nama hanya boleh huruf, angka, spasi dan tanda petik (')!");
            namaInput.focus();
            return;
        }

        // Fungsi toggle input kios
    roleSelect.addEventListener("change", function() {
        if (this.value === "pedagang") {
            kiosContainer.classList.remove("hidden");
            kiosInput.setAttribute("required", "required");
        } else {
            kiosContainer.classList.add("hidden");
            kiosInput.removeAttribute("required");
            kiosInput.value = ""; // reset jika bukan pedagang
        }
    });

    // Cek saat halaman dimuat (jika ada error tapi data tetap ada)
    if (roleSelect.value === "pedagang") {
        kiosContainer.classList.remove("hidden");
    }

        // Cek semua input dan select dropdown
        const inputs = form.querySelectorAll("input, select");
        let lengkap = true;

        inputs.forEach(input => {
            if(input.type !== "radio" && input.value.trim() === ""){
                lengkap = false;
            }
        });

        const gender = form.querySelector('input[name="gender"]:checked');

        if(!lengkap || !gender){
            e.preventDefault();
            alert("Form harus diisi semua! ⚠️");
            return;
        }
    });

});
</script>

</body>
</html>