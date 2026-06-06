<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Manajemen User - SIPESEL Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>body{font-family:'Poppins',sans-serif;}.modal{display:none;}.modal.show{display:flex;}</style>
</head>
<body style="background-image:url('{{ asset('images/kios2.jpg') }}');" class="relative min-h-screen text-white bg-cover bg-center">
<div class="absolute inset-0 bg-black/40"></div>
@php $totalPending=\App\Models\Transaksi::where('status','pending')->count(); @endphp
@include('layouts.admin-header')

<section class="relative z-10 px-6 md:px-20 py-8 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="mb-6"><h1 class="text-3xl md:text-4xl font-bold mb-2">Manajemen Pengguna</h1><p class="text-sm opacity-90">Kelola data pedagang, pengawas, dan admin</p></div>

        @if(session('success'))<div class="bg-green-500/30 border border-green-500/50 text-white rounded-lg p-4 mb-6 flex items-center gap-3"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ session('success') }}</div>@endif
        @if(session('error'))<div class="bg-red-500/30 border border-red-500/50 text-white rounded-lg p-4 mb-6">{{ session('error') }}</div>@endif

        <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20 mb-6">
            <div class="flex flex-col md:flex-row gap-4 justify-between">
                <form method="GET" class="flex-1">
                    <input type="text" name="search" placeholder="Cari nama, username, atau email..." value="{{ request('search') }}"
                        class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </form>
                <form method="GET" class="flex gap-2">
                    <select name="filter" onchange="this.form.submit()" class="px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <option value="">Semua Role</option>
                        <option value="pedagang" {{ request('filter')=='pedagang'?'selected':'' }}>Pedagang</option>
                        <option value="pengawas" {{ request('filter')=='pengawas'?'selected':'' }}>Pengawas</option>
                        <option value="admin" {{ request('filter')=='admin'?'selected':'' }}>Admin</option>
                    </select>
                </form>
                <button onclick="showAddModal()" class="px-6 py-2 bg-green-500/30 hover:bg-green-500/50 border border-green-500/50 rounded-lg font-semibold transition-all flex items-center gap-2 whitespace-nowrap">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah User
                </button>
            </div>
        </div>

        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead><tr class="bg-white/10 border-b border-white/20">
                        <th class="px-6 py-4 text-left text-sm font-semibold">Nama</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Username</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Email</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Role</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">No. Kios</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Aksi</th>
                    </tr></thead>
                    <tbody>
                        @foreach($users as $u)
                        <tr class="border-b border-white/10 hover:bg-white/5 transition-all">
                            <td class="px-6 py-4"><div class="font-semibold">{{ $u->nama }}</div><div class="text-xs opacity-70">{{ $u->nomor_hp }}</div></td>
                            <td class="px-6 py-4">{{ $u->username }}</td>
                            <td class="px-6 py-4 text-sm">{{ $u->email }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $u->role==='admin'?'bg-red-500/30 text-red-300':($u->role==='pengawas'?'bg-blue-500/30 text-blue-300':'bg-green-500/30 text-green-300') }}">
                                    {{ ucfirst($u->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-semibold">{{ $u->no_kios ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <button onclick='showEditModal(@json($u))' class="px-3 py-1 bg-blue-500/30 hover:bg-blue-500/50 border border-blue-500/50 rounded text-xs font-medium transition-all">Edit</button>
                                    <form method="POST" action="{{ route('admin.reset-pw') }}" onsubmit="return confirm('Reset password ke password123?')">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $u->id_user }}">
                                        <button type="submit" class="px-3 py-1 bg-yellow-500/30 hover:bg-yellow-500/50 border border-yellow-500/50 rounded text-xs font-medium transition-all">Reset PW</button>
                                    </form>
                                    @if($u->id_user !== auth()->id())
                                    <form method="POST" action="{{ route('admin.delete-user') }}" onsubmit="return confirm('Hapus user ini?')">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="id" value="{{ $u->id_user }}">
                                        <button type="submit" class="px-3 py-1 bg-red-500/30 hover:bg-red-500/50 border border-red-500/50 rounded text-xs font-medium transition-all">Hapus</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- Modal Add --}}
<div id="modalAdd" class="modal fixed inset-0 bg-black/70 backdrop-blur-sm items-center justify-center z-50">
    <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 max-w-md mx-4 border border-white/20 max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold mb-4">Tambah User Baru</h3>
        <form method="POST" action="{{ route('admin.add-user') }}">@csrf
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
                <button type="submit" class="flex-1 px-4 py-2 bg-green-500/30 hover:bg-green-500/50 border border-green-500/50 rounded-lg font-medium transition-all">Tambah</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEdit" class="modal fixed inset-0 bg-black/70 backdrop-blur-sm items-center justify-center z-50">
    <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 max-w-md mx-4 border border-white/20 max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold mb-4">Edit User</h3>
        <form method="POST" action="{{ route('admin.update-user') }}">@csrf
            <input type="hidden" name="id_user" id="editId">
            <div class="space-y-4">
                <input type="text" name="nama" id="editNama" placeholder="Nama Lengkap" required class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <input type="email" name="email" id="editEmail" placeholder="Email" required class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <input type="text" name="nomor_hp" id="editNomorHp" placeholder="Nomor HP" required class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <select name="gender" id="editGender" required class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option>
                </select>
                <select name="role" id="editRole" onchange="toggleKiosEdit()" required class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="pedagang">Pedagang</option><option value="pengawas">Pengawas</option><option value="admin">Admin</option>
                </select>
                <div id="kiosFieldEdit"><input type="text" name="no_kios" id="editNoKios" placeholder="No. Kios" class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400"></div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeEditModal()" class="flex-1 px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg font-medium transition-all">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-500/30 hover:bg-blue-500/50 border border-blue-500/50 rounded-lg font-medium transition-all">Update</button>
            </div>
        </form>
    </div>
</div>

<footer class="bg-green-800 text-green-300 text-center text-xs py-4 mt-10 relative z-10">&copy; 2026 <span class="text-yellow-400 font-bold">SIPESEL</span> — Admin Panel.</footer>
<script>
function showAddModal(){document.getElementById('modalAdd').classList.add('show');}
function closeAddModal(){document.getElementById('modalAdd').classList.remove('show');}
function showEditModal(user){
    document.getElementById('editId').value=user.id_user;
    document.getElementById('editNama').value=user.nama;
    document.getElementById('editEmail').value=user.email;
    document.getElementById('editNomorHp').value=user.nomor_hp;
    document.getElementById('editGender').value=user.gender;
    document.getElementById('editRole').value=user.role;
    document.getElementById('editNoKios').value=user.no_kios||'';
    toggleKiosEdit();
    document.getElementById('modalEdit').classList.add('show');
}
function closeEditModal(){document.getElementById('modalEdit').classList.remove('show');}
function toggleKiosAdd(){document.getElementById('kiosFieldAdd').style.display=document.getElementById('addRole').value==='pedagang'?'block':'none';}
function toggleKiosEdit(){document.getElementById('kiosFieldEdit').style.display=document.getElementById('editRole').value==='pedagang'?'block':'none';}
</script>
</body>
</html>
