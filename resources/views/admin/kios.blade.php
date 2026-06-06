<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Manajemen Kios - SIPESEL</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>body{font-family:'Poppins',sans-serif;}</style>
</head>
<body style="background-image:url('{{ asset('images/kios2.jpg') }}');" class="relative min-h-screen text-white bg-cover bg-center">
<div class="absolute inset-0 bg-black/40"></div>
@include('layouts.admin-header')

<section class="relative z-10 px-6 md:px-20 py-8 min-h-screen">
<div class="max-w-7xl mx-auto">
    <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
        <div><h1 class="text-3xl font-bold">Manajemen Kios</h1><p class="text-sm opacity-80">Kelola data kios pasar</p></div>
        <button onclick="document.getElementById('modal-tambah').classList.remove('hidden')" class="bg-yellow-400 hover:bg-yellow-300 text-green-900 font-bold px-6 py-2 rounded-lg transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Kios
        </button>
    </div>

    @if(session('success'))<div class="bg-green-500/30 border border-green-400 text-green-200 px-4 py-3 rounded-lg mb-4">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-500/30 border border-red-400 text-red-200 px-4 py-3 rounded-lg mb-4">{{ session('error') }}</div>@endif

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 text-center"><p class="text-3xl font-bold">{{ $stat['total'] }}</p><p class="text-xs opacity-70 mt-1">Total Kios</p></div>
        <div class="bg-green-500/20 backdrop-blur-md rounded-xl p-4 border border-green-400/30 text-center"><p class="text-3xl font-bold text-green-300">{{ $stat['terisi'] }}</p><p class="text-xs opacity-70 mt-1">Terisi</p></div>
        <div class="bg-yellow-500/20 backdrop-blur-md rounded-xl p-4 border border-yellow-400/30 text-center"><p class="text-3xl font-bold text-yellow-300">{{ $stat['kosong'] }}</p><p class="text-xs opacity-70 mt-1">Kosong</p></div>
        <div class="bg-red-500/20 backdrop-blur-md rounded-xl p-4 border border-red-400/30 text-center"><p class="text-3xl font-bold text-red-300">{{ $stat['maintenance'] }}</p><p class="text-xs opacity-70 mt-1">Maintenance</p></div>
    </div>

    <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 mb-4">
        <form method="GET" class="flex flex-wrap gap-3 items-center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no kios / lokasi..." class="bg-white/10 border border-white/30 rounded-lg px-4 py-2 text-sm text-white placeholder-white/50 focus:outline-none focus:border-yellow-400">
            <select name="status" class="bg-green-900 border border-white/30 rounded-lg px-4 py-2 text-sm text-white focus:outline-none focus:border-yellow-400">
                <option value="">Semua Status</option>
                <option value="terisi" {{ request('status')=='terisi'?'selected':'' }}>Terisi</option>
                <option value="kosong" {{ request('status')=='kosong'?'selected':'' }}>Kosong</option>
                <option value="maintenance" {{ request('status')=='maintenance'?'selected':'' }}>Maintenance</option>
            </select>
            <button type="submit" class="bg-yellow-400 text-green-900 font-semibold px-4 py-2 rounded-lg text-sm hover:bg-yellow-300 transition-all">Filter</button>
            <a href="{{ route('admin.kios') }}" class="text-green-300 hover:text-white text-sm">Reset</a>
        </form>
    </div>

    <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="bg-green-800/60 text-left">
                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">No. Kios</th>
                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">Lokasi</th>
                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">Ukuran (m²)</th>
                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">Tarif/Bulan</th>
                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">Aksi</th>
                </tr></thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($kiosList as $k)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold text-yellow-300">{{ $k->no_kios }}</td>
                        <td class="px-4 py-3">{{ $k->lokasi }}</td>
                        <td class="px-4 py-3">{{ number_format($k->ukuran,2) }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($k->tarif_bulanan,0,',','.') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $k->status=='terisi'?'bg-green-500/30 text-green-300':($k->status=='kosong'?'bg-yellow-500/30 text-yellow-300':'bg-red-500/30 text-red-300') }}">{{ ucfirst($k->status) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <button onclick='editKios(@json($k))' class="bg-blue-500/30 hover:bg-blue-500/50 text-blue-300 px-3 py-1 rounded-lg text-xs transition-all">Edit</button>
                                <form method="POST" action="{{ route('admin.hapus-kios') }}" onsubmit="return confirm('Hapus kios {{ $k->no_kios }}?')">
                                    @csrf
                                    <input type="hidden" name="id_kios" value="{{ $k->id_kios }}">
                                    <button type="submit" class="bg-red-500/30 hover:bg-red-500/50 text-red-300 px-3 py-1 rounded-lg text-xs transition-all">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center opacity-50">Tidak ada data kios</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</section>

{{-- Modal Tambah --}}
<div id="modal-tambah" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-green-900 border border-white/20 rounded-2xl p-6 w-full max-w-md mx-4 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold">Tambah Kios Baru</h2>
            <button onclick="document.getElementById('modal-tambah').classList.add('hidden')" class="text-white/60 hover:text-white">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.tambah-kios') }}">@csrf
            <div class="space-y-3">
                <div><label class="text-xs opacity-70 block mb-1">Nomor Kios</label><input type="text" name="no_kios" required placeholder="cth: A-01" class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400"></div>
                <div><label class="text-xs opacity-70 block mb-1">Lokasi</label><input type="text" name="lokasi" required placeholder="cth: Blok A Lantai 1" class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="text-xs opacity-70 block mb-1">Ukuran (m²)</label><input type="number" name="ukuran" step="0.01" value="10.00" class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400"></div>
                    <div><label class="text-xs opacity-70 block mb-1">Tarif/Bulan (Rp)</label><input type="number" name="tarif_bulanan" value="250000" class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400"></div>
                </div>
                <div><label class="text-xs opacity-70 block mb-1">Status</label>
                    <select name="status" class="w-full bg-green-900 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400">
                        <option value="kosong">Kosong</option><option value="terisi">Terisi</option><option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div><label class="text-xs opacity-70 block mb-1">Keterangan (opsional)</label><textarea name="keterangan" rows="2" class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400"></textarea></div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="submit" class="flex-1 bg-yellow-400 hover:bg-yellow-300 text-green-900 font-bold py-2 rounded-lg transition-all">Simpan</button>
                <button type="button" onclick="document.getElementById('modal-tambah').classList.add('hidden')" class="flex-1 bg-white/10 hover:bg-white/20 py-2 rounded-lg transition-all">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-green-900 border border-white/20 rounded-2xl p-6 w-full max-w-md mx-4 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold">Edit Kios</h2>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-white/60 hover:text-white">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.edit-kios') }}">@csrf
            <input type="hidden" name="id_kios" id="edit_id_kios">
            <div class="space-y-3">
                <div><label class="text-xs opacity-70 block mb-1">Nomor Kios</label><input type="text" name="no_kios" id="edit_no_kios" required class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400"></div>
                <div><label class="text-xs opacity-70 block mb-1">Lokasi</label><input type="text" name="lokasi" id="edit_lokasi" required class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="text-xs opacity-70 block mb-1">Ukuran (m²)</label><input type="number" name="ukuran" id="edit_ukuran" step="0.01" class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400"></div>
                    <div><label class="text-xs opacity-70 block mb-1">Tarif/Bulan (Rp)</label><input type="number" name="tarif_bulanan" id="edit_tarif" class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400"></div>
                </div>
                <div><label class="text-xs opacity-70 block mb-1">Status</label>
                    <select name="status" id="edit_status" class="w-full bg-green-900 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400">
                        <option value="kosong">Kosong</option><option value="terisi">Terisi</option><option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div><label class="text-xs opacity-70 block mb-1">Keterangan</label><textarea name="keterangan" id="edit_keterangan" rows="2" class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400"></textarea></div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="submit" class="flex-1 bg-yellow-400 hover:bg-yellow-300 text-green-900 font-bold py-2 rounded-lg transition-all">Update</button>
                <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')" class="flex-1 bg-white/10 hover:bg-white/20 py-2 rounded-lg transition-all">Batal</button>
            </div>
        </form>
    </div>
</div>

<footer class="bg-green-800 text-green-300 text-center text-xs py-4 mt-10 relative z-10">&copy; 2026 <span class="text-yellow-400 font-bold">SIPESEL</span> — Admin Panel.</footer>
<script>
function editKios(data){
    document.getElementById('edit_id_kios').value=data.id_kios;
    document.getElementById('edit_no_kios').value=data.no_kios;
    document.getElementById('edit_lokasi').value=data.lokasi;
    document.getElementById('edit_ukuran').value=data.ukuran;
    document.getElementById('edit_tarif').value=data.tarif_bulanan;
    document.getElementById('edit_status').value=data.status;
    document.getElementById('edit_keterangan').value=data.keterangan||'';
    document.getElementById('modal-edit').classList.remove('hidden');
}
</script>
</body>
</html>
