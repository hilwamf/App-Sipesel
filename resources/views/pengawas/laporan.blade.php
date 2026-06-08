@extends('layouts.sidebar-admin')
@section('title','Laporan - SIPESEL Pengawas')
@section('page-title','Laporan Jatuh Tempo')
@section('page-sub','Status tagihan dan jatuh tempo pedagang')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center">
        <p class="text-2xl font-black text-gray-800">{{ $totalPedagang }}</p>
        <p class="text-xs text-gray-400 mt-1">Total Pedagang</p>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-green-100 text-center">
        <p class="text-2xl font-black text-green-600">{{ $belumJatuh }}</p>
        <p class="text-xs text-gray-400 mt-1">Belum Jatuh Tempo</p>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-red-100 text-center">
        <p class="text-2xl font-black text-red-600">{{ $sudahJatuh }}</p>
        <p class="text-xs text-gray-400 mt-1">Jatuh Tempo / Belum Bayar</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-5">
    <div class="flex flex-wrap gap-3">
        <input id="searchInput" type="text" oninput="filterTabel()" placeholder="Cari nama / kios..."
            class="flex-1 min-w-40 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
        <select id="filterStatus" onchange="filterTabel()" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="semua">Semua</option>
            <option value="jatuh_tempo">Jatuh Tempo</option>
            <option value="aman">Belum Jatuh Tempo</option>
        </select>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full" id="tabelLaporan">
            <thead><tr class="bg-gray-50 border-b border-gray-100">
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pedagang</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kios</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jenis</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Terakhir Bayar</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jatuh Tempo</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
            </tr></thead>
            <tbody>
            @foreach($pedagangList as $p)
            @php
                $lewat = $p['sudah_lewat'];
                $belumBayar = empty($p['tanggal_bayar']);
                $hp = preg_replace('/\D/', '', $p['nomor_hp'] ?? '');
                if (str_starts_with($hp, '0')) $hp = '62'.substr($hp,1);
                $pesan = urlencode("Yth. {$p['nama']},\nTagihan pajak kios ".($p['no_kios']??'-')." telah jatuh tempo.\nSegera lakukan pembayaran melalui SIPESEL.\nTerima kasih.");
                $waUrl = "https://web.whatsapp.com/send?phone={$hp}&text={$pesan}";
            @endphp
            <tr class="baris border-b border-gray-50 hover:bg-gray-50 transition"
                data-status="{{ ($lewat||$belumBayar)?'jatuh_tempo':'aman' }}"
                data-nama="{{ strtolower($p['nama']) }}"
                data-kios="{{ strtolower($p['no_kios']??'') }}">
                <td class="px-4 py-3">
                    <p class="font-semibold text-gray-800 text-sm">{{ $p['nama'] }}</p>
                    <p class="text-gray-400 text-xs">{{ $p['username'] }}</p>
                </td>
                <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-50 text-blue-700 font-bold text-xs rounded-lg">{{ $p['no_kios'] ?? '-' }}</span></td>
                <td class="px-4 py-3">@if($p['jenis_pajak'])<span class="px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">{{ $p['jenis_pajak'] }}</span>@else<span class="text-gray-300 text-xs">—</span>@endif</td>
                <td class="px-4 py-3 text-sm text-gray-600">{!! $p['tanggal_bayar'] ? \Carbon\Carbon::parse($p['tanggal_bayar'])->format('d M Y') : '<span class="text-gray-300">Belum pernah</span>' !!}</td>
                <td class="px-4 py-3">
                    @if(!empty($p['jatuh_tempo']))
                    <p class="font-semibold text-sm {{ $lewat?'text-red-600':'text-green-600' }}">{{ \Carbon\Carbon::parse($p['jatuh_tempo'])->format('d M Y') }}</p>
                    <p class="text-xs {{ $lewat?'text-red-400':'text-green-400' }}">{{ $lewat?'Terlambat '.$p['selisih_hari'].' hari':'Sisa '.$p['selisih_hari'].' hari' }}</p>
                    @else<span class="text-red-500 text-xs">Belum ada</span>@endif
                </td>
                <td class="px-4 py-3 text-center">
                    @if($belumBayar||$lewat)
                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-600">⚠ Jatuh Tempo</span>
                    @else
                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✓ Aman</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ $waUrl }}" target="_blank" class="px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 font-semibold rounded-lg text-xs">WA</a>
                        <div>
                            <p class="text-[10px] text-gray-400 mb-1 uppercase tracking-wider font-semibold text-center">Reminder</p>
                            @if($belumBayar||$lewat)
                            <button
                                onclick="bukaModalNotif({{ $p['id_user'] }},'{{ addslashes($p['nama']) }}','{{ $p['no_kios']??'-' }}')"
                                class="px-3 py-1.5 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 font-semibold rounded-lg text-xs flex items-center gap-1">
                                🔔 Kirim Notif
                            </button>
                            @else
                            <button disabled class="px-3 py-1.5 bg-gray-50 text-gray-300 font-semibold rounded-lg text-xs cursor-not-allowed border border-gray-100">
                                ✓ Sudah Bayar
                            </button>
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
            @if($pedagangList->isEmpty())
            <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400 text-sm">Belum ada data pedagang</td></tr>
            @endif
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL KIRIM NOTIF --}}
<div id="modalNotif" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-800">Kirim Notifikasi</h3>
            <button onclick="tutupModalNotif()" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-500">✕</button>
        </div>
        <p class="text-sm text-gray-500 mb-1">Kepada: <span id="notifNama" class="font-bold text-green-700"></span></p>
        <p class="text-xs text-gray-400 mb-4">Kios: <span id="notifKios"></span></p>
        <div class="mb-4">
            <label class="block text-xs font-semibold text-gray-600 mb-2">Isi Pesan Notifikasi</label>
            <textarea id="notifPesan" rows="4" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none"></textarea>
        </div>
        <div class="flex gap-3">
            <button onclick="tutupModalNotif()" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-xl text-sm">Batal</button>
            <button onclick="kirimNotif()" class="flex-1 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-xl text-sm flex items-center justify-center gap-2">
                🔔 Kirim
            </button>
        </div>
    </div>
</div>

<div id="toastBox" style="position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:8px;"></div>
@endsection

@section('scripts')
let notifUserId = null;
const csrfToken = '{{ csrf_token() }}';
const kirimUrl  = '{{ route("pengawas.kirim-notifikasi") }}';

function bukaModalNotif(idUser, nama, kios) {
    notifUserId = idUser;
    document.getElementById('notifNama').textContent = nama;
    document.getElementById('notifKios').textContent = kios;
    document.getElementById('notifPesan').value =
        `Yth. ${nama},\n\nKami mengingatkan bahwa tagihan pajak kios ${kios} Anda telah jatuh tempo.\n\nMohon segera lakukan pembayaran melalui aplikasi SIPESEL.\n\nTerima kasih.`;
    document.getElementById('modalNotif').classList.remove('hidden');
}

function tutupModalNotif() {
    document.getElementById('modalNotif').classList.add('hidden');
    notifUserId = null;
}

function kirimNotif() {
    if (!notifUserId) return;
    const pesan = document.getElementById('notifPesan').value.trim();
    if (!pesan) { alert('Isi pesan tidak boleh kosong!'); return; }

    const fd = new FormData();
    fd.append('id_user', notifUserId);
    fd.append('pesan_custom', pesan);
    fd.append('_token', csrfToken);

    fetch(kirimUrl, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
        tutupModalNotif();
        if (d.success) toast('Notifikasi berhasil dikirim!', 'success');
        else toast('Gagal mengirim notifikasi', 'error');
    }).catch(() => toast('Error jaringan', 'error'));
}

function toast(msg, type) {
    const box = document.getElementById('toastBox');
    const el  = document.createElement('div');
    el.className = 'px-4 py-3 rounded-xl text-white text-sm font-medium shadow-lg ' + (type==='success'?'bg-green-600':'bg-red-600');
    el.textContent = msg;
    box.appendChild(el);
    setTimeout(() => { el.style.opacity='0'; setTimeout(()=>el.remove(),300); }, 3000);
}

function filterTabel() {
    const kw = document.getElementById('searchInput').value.toLowerCase();
    const st = document.getElementById('filterStatus').value;
    document.querySelectorAll('#tabelLaporan .baris').forEach(r => {
        const nm = r.dataset.nama.includes(kw) || r.dataset.kios.includes(kw);
        const sm = st==='semua' || r.dataset.status===st;
        r.style.display = (nm && sm) ? '' : 'none';
    });
}
@endsection