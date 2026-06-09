@extends('layouts.sidebar-admin')
@section('title','Manajemen Pengguna - SIPESEL')
@section('page-title','Manajemen Pengguna')
@section('page-sub','Kelola data pedagang, pengawas, dan admin')

@section('content')
@if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-3 mb-5 flex items-center gap-2 text-sm"><svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ session('success') }}</div>@endif
@if(session('error'))<div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-3 mb-5 text-sm">{{ session('error') }}</div>@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-5">
    <div class="flex flex-wrap gap-3 items-end justify-between">
        <form method="GET" class="flex flex-wrap gap-3 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, username, email..."
                class="flex-1 min-w-48 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            <select name="filter" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">Semua Role</option>
                <option value="pedagang" {{ request('filter')=='pedagang'?'selected':'' }}>Pedagang</option>
                <option value="pengawas" {{ request('filter')=='pengawas'?'selected':'' }}>Pengawas</option>
                <option value="admin"    {{ request('filter')=='admin'   ?'selected':'' }}>Admin</option>
            </select>
            <button type="submit" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-sm">Cari</button>
        </form>
        <button onclick="document.getElementById('modalAdd').classList.remove('hidden')"
            class="px-5 py-2.5 bg-green-700 hover:bg-green-800 text-white font-semibold rounded-xl text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Tambah User
        </button>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-bold text-gray-800 text-sm">Daftar Pengguna</h2>
        <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full">{{ $users->count() }} pengguna</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead><tr class="bg-gray-50 border-b border-gray-100">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pengguna</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Role</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kios</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $u)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-sm flex-shrink-0">{{ strtoupper(substr($u->username,0,1)) }}</div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">{{ $u->nama }}</p>
                                <p class="text-gray-400 text-xs">{{ $u->username }} · {{ $u->nomor_hp }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-sm text-gray-600">{{ $u->email }}</td>
                    <td class="px-5 py-4"><span class="px-3 py-1 rounded-full text-xs font-semibold {{ $u->role==='admin'?'bg-red-100 text-red-700':($u->role==='pengawas'?'bg-blue-100 text-blue-700':'bg-green-100 text-green-700') }}">{{ ucfirst($u->role) }}</span></td>
                    <td class="px-5 py-4">@if($u->no_kios)<span class="px-2 py-1 bg-blue-50 text-blue-700 font-bold text-xs rounded-lg">{{ $u->no_kios }}</span>@else<span class="text-gray-300">—</span>@endif</td>
                    <td class="px-5 py-4">
                        <div class="flex gap-2">
                            <button onclick='showEditModal(@json($u))' class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold rounded-lg text-xs">Edit</button>
                            <form method="POST" action="{{ route('admin.reset-pw') }}" onsubmit="return confirm('Reset password ke password123?')">
                                @csrf<input type="hidden" name="id" value="{{ $u->id_user }}">
                                <button class="px-3 py-1.5 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 font-semibold rounded-lg text-xs">Reset PW</button>
                            </form>
                            @if($u->id_user !== auth()->id())
                            <form method="POST" action="{{ route('admin.delete-user') }}" onsubmit="return confirm('Hapus user ini?')">
                                @csrf @method('DELETE')<input type="hidden" name="id" value="{{ $u->id_user }}">
                                <button class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 font-semibold rounded-lg text-xs">Hapus</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400 text-sm">Tidak ada pengguna</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Tambah --}}
<div id="modalAdd" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-5"><h3 class="font-bold text-gray-800">Tambah User Baru</h3><button onclick="document.getElementById('modalAdd').classList.add('hidden')" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-500">✕</button></div>
        <form method="POST" action="{{ route('admin.add-user') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Nama</label><input type="text" name="nama" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Username</label><input type="text" name="username" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Email</label><input type="email" name="email" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Nomor HP</label><input type="text" name="nomor_hp" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Gender</label><select name="gender" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-500"><option value="">Pilih</option><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Role</label><select name="role" id="addRole" onchange="toggleKiosAdd()" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-500"><option value="">Pilih</option><option value="pedagang">Pedagang</option><option value="pengawas">Pengawas</option><option value="admin">Admin</option></select></div>
            <div id="kiosFieldAdd" style="display:none"><label class="block text-xs font-medium text-gray-600 mb-1">No. Kios</label><input type="text" name="no_kios" placeholder="cth: A-01" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500"></div>
            <p class="text-xs text-gray-400">*Password default: password123</p>
            <div class="flex gap-3 pt-2"><button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')" class="flex-1 py-2.5 bg-gray-100 text-gray-600 font-semibold rounded-xl text-sm">Batal</button><button type="submit" class="flex-1 py-2.5 bg-green-700 text-white font-semibold rounded-xl text-sm">Tambah</button></div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEdit" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-5"><h3 class="font-bold text-gray-800">Edit Pengguna</h3><button onclick="document.getElementById('modalEdit').classList.add('hidden')" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-500">✕</button></div>
        <form method="POST" action="{{ route('admin.update-user') }}" class="space-y-3">@csrf
            <input type="hidden" name="id_user" id="editId">
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Nama</label><input type="text" name="nama" id="editNama" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Email</label><input type="email" name="email" id="editEmail" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Nomor HP</label><input type="text" name="nomor_hp" id="editNomorHp" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Gender</label><select name="gender" id="editGender" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Role</label><select name="role" id="editRole" onchange="toggleKiosEdit()" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"><option value="pedagang">Pedagang</option><option value="pengawas">Pengawas</option><option value="admin">Admin</option></select></div>
            <div id="kiosFieldEdit"><label class="block text-xs font-medium text-gray-600 mb-1">No. Kios</label><input type="text" name="no_kios" id="editNoKios" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></div>
            <div class="flex gap-3 pt-2"><button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')" class="flex-1 py-2.5 bg-gray-100 text-gray-600 font-semibold rounded-xl text-sm">Batal</button><button type="submit" class="flex-1 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm">Update</button></div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
function showEditModal(u) {
    document.getElementById('editId').value = u.id_user;
    document.getElementById('editNama').value = u.nama;
    document.getElementById('editEmail').value = u.email;
    document.getElementById('editNomorHp').value = u.nomor_hp;
    document.getElementById('editGender').value = u.gender;
    document.getElementById('editRole').value = u.role;
    document.getElementById('editNoKios').value = u.no_kios || '';
    toggleKiosEdit();
    document.getElementById('modalEdit').classList.remove('hidden');
}
function toggleKiosAdd() { document.getElementById('kiosFieldAdd').style.display = document.getElementById('addRole').value==='pedagang'?'block':'none'; }
function toggleKiosEdit() { document.getElementById('kiosFieldEdit').style.display = document.getElementById('editRole').value==='pedagang'?'block':'none'; }
@endsection