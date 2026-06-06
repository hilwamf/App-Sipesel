<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Monitoring Pembayaran - SIPESEL</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>body{font-family:'Poppins',sans-serif;} #modalBukti{display:none;} #modalBukti.show{display:flex;}</style>
</head>
<body style="background-image:url('{{ asset('images/kios2.jpg') }}');" class="relative min-h-screen text-white bg-cover bg-center">
<div class="absolute inset-0 bg-black/30"></div>
@include('layouts.pengawas-header')

<section class="relative z-10 px-6 md:px-20 py-8 min-h-screen">
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl md:text-4xl font-bold mb-2">Monitoring Pembayaran</h1>
        <p class="text-sm opacity-90">Pantau semua transaksi pembayaran pedagang secara real-time</p>
    </div>

    <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">Filter Status</label>
                <select onchange="window.location.href='{{ route('pengawas.monitoring') }}?filter='+this.value" class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <option value="semua" {{ $filter==='semua'?'selected':'' }}>Semua Status</option>
                    <option value="pending" {{ $filter==='pending'?'selected':'' }}>Pending</option>
                    <option value="berhasil" {{ $filter==='berhasil'?'selected':'' }}>Berhasil</option>
                    <option value="gagal" {{ $filter==='gagal'?'selected':'' }}>Gagal</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-2">Cari Pedagang / Nomor Kios</label>
                <input id="searchInput" type="text" placeholder="Ketik nama pedagang atau nomor kios..." oninput="filterTable()" class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>
            <div class="flex items-end">
                <button onclick="document.getElementById('searchInput').value='';filterTable();" class="w-full px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-green-900 font-semibold rounded-lg transition-all">Reset</button>
            </div>
        </div>
    </div>

    <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="tabelTransaksi">
                <thead><tr class="bg-green-800/50 border-b border-white/20">
                    <th class="px-5 py-4 text-left text-sm font-semibold">No. Transaksi</th>
                    <th class="px-5 py-4 text-left text-sm font-semibold">Nomor Kios</th>
                    <th class="px-5 py-4 text-left text-sm font-semibold">Nama Pedagang</th>
                    <th class="px-5 py-4 text-left text-sm font-semibold">Jenis Pajak</th>
                    <th class="px-5 py-4 text-left text-sm font-semibold">Metode</th>
                    <th class="px-5 py-4 text-left text-sm font-semibold">Tanggal</th>
                    <th class="px-5 py-4 text-left text-sm font-semibold">Nominal</th>
                    <th class="px-5 py-4 text-left text-sm font-semibold">Status</th>
                    <th class="px-5 py-4 text-left text-sm font-semibold">Aksi</th>
                </tr></thead>
                <tbody>
                    @forelse($transaksi as $t)
                    @php
                        $jenisBadge=['Harian'=>'bg-sky-500/30 text-sky-200','Mingguan'=>'bg-purple-500/30 text-purple-200','Bulanan'=>'bg-indigo-500/30 text-indigo-200'];
                        $jc=$jenisBadge[$t->jenis_pajak]??'bg-white/20 text-white';
                        $st=strtolower($t->status);
                    @endphp
                    <tr class="baris-transaksi border-b border-white/10 hover:bg-white/5 transition-all">
                        <td class="px-5 py-4 text-xs font-mono font-medium">{{ $t->no_trx }}</td>
                        <td class="px-5 py-4 text-sm">
                            <span class="inline-flex items-center gap-2 px-3 py-1 bg-blue-500/30 rounded-lg">{{ $t->nomor_kios }}</span>
                        </td>
                        <td class="px-5 py-4 text-sm">{{ $t->user->username ?? '-' }}</td>
                        <td class="px-5 py-4 text-sm"><span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $jc }}">{{ $t->jenis_pajak }}</span></td>
                        <td class="px-5 py-4 text-sm text-white/80">{{ $t->metode_pembayaran }}</td>
                        <td class="px-5 py-4 text-sm text-white/80">{{ $t->tanggal->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-4 text-sm font-semibold">Rp {{ number_format($t->nominal,0,',','.') }}</td>
                        <td class="px-5 py-4 text-sm">
                            @if($st==='approved')
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-500/30 text-green-300 rounded-full text-xs font-medium">✔ Berhasil</span>
                            @elseif($st==='pending')
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-500/30 text-yellow-300 rounded-full text-xs font-medium">⏳ Pending</span>
                            @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-red-500/30 text-red-300 rounded-full text-xs font-medium">✖ Ditolak</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <button onclick="lihatBukti('{{ $t->no_trx }}','{{ addslashes($t->user->username??'-') }}','{{ $t->nomor_kios }}','{{ $t->jenis_pajak }}','{{ $t->metode_pembayaran }}','{{ number_format($t->nominal,0,',','.') }}','{{ $t->tanggal->format('d/m/Y H:i') }}','{{ $t->status }}')"
                                class="inline-flex items-center gap-2 px-3 py-2 bg-blue-500/30 hover:bg-blue-500/50 rounded-lg text-xs font-medium transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Lihat Bukti
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="px-6 py-14 text-center text-white/40 text-sm">Belum ada data transaksi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
            <p class="text-sm text-green-200 mb-1">Total Transaksi</p>
            <p class="text-2xl font-bold">{{ $transaksi->count() }}</p>
        </div>
        <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
            <p class="text-sm text-green-200 mb-1">Total Nominal</p>
            <p class="text-2xl font-bold">Rp {{ number_format($totalNominal,0,',','.') }}</p>
        </div>
        <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
            <p class="text-sm text-green-200 mb-1">Rata-rata Pembayaran</p>
            <p class="text-2xl font-bold">Rp {{ number_format($rataRata,0,',','.') }}</p>
        </div>
    </div>
</div>
</section>

{{-- Modal Detail Transaksi --}}
<div id="modalBukti" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 items-center justify-center px-4">
    <div class="bg-green-900/90 backdrop-blur-xl border border-white/20 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-white/20 bg-green-800/60">
            <h3 class="font-bold text-lg">Detail Transaksi</h3>
            <button onclick="tutupModal()" class="p-2 rounded-lg hover:bg-white/10 transition-all text-white/70 hover:text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="px-6 py-5">
            <div class="bg-white/5 rounded-xl overflow-hidden">
                <div class="bg-green-700/50 px-5 py-4 text-center border-b border-white/10">
                    <p class="text-yellow-400 font-extrabold text-lg tracking-widest" style="font-family:'Montserrat',sans-serif;">SIPESEL</p>
                    <p class="text-green-300 text-xs mt-0.5">Bukti Pembayaran Pajak Pasar</p>
                </div>
                <div class="flex items-center px-4 py-1"><div class="flex-1 border-t border-dashed border-white/20"></div></div>
                <div class="px-5 py-4 space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-white/50">No. Transaksi</span><span class="font-mono text-xs font-semibold text-white" id="mNoTrx">-</span></div>
                    <div class="flex justify-between"><span class="text-white/50">Nama Pedagang</span><span class="font-medium text-white" id="mPedagang">-</span></div>
                    <div class="flex justify-between"><span class="text-white/50">Nomor Kios</span><span class="font-medium text-white" id="mKios">-</span></div>
                    <div class="flex justify-between"><span class="text-white/50">Jenis Pajak</span><span class="font-medium text-white" id="mJenis">-</span></div>
                    <div class="flex justify-between"><span class="text-white/50">Metode Bayar</span><span class="font-medium text-white" id="mMetode">-</span></div>
                    <div class="flex justify-between"><span class="text-white/50">Tanggal</span><span class="font-medium text-white" id="mTanggal">-</span></div>
                </div>
                <div class="flex items-center px-4 py-1"><div class="flex-1 border-t border-dashed border-white/20"></div></div>
                <div class="px-5 py-4 bg-white/5">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-white/50 text-sm">Total Nominal</span>
                        <span class="font-extrabold text-xl text-yellow-300" id="mNominal" style="font-family:'Montserrat',sans-serif;">-</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-white/50 text-sm">Status</span>
                        <span id="mStatus">-</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-6 pb-5 flex justify-end">
            <button onclick="tutupModal()" class="px-6 py-2.5 bg-green-600 hover:bg-green-500 text-white font-semibold rounded-lg text-sm transition-all">Tutup</button>
        </div>
    </div>
</div>

<script>
function lihatBukti(noTrx,pedagang,kios,jenis,metode,nominal,tanggal,status){
    document.getElementById('mNoTrx').textContent=noTrx;
    document.getElementById('mPedagang').textContent=pedagang;
    document.getElementById('mKios').textContent=kios;
    document.getElementById('mJenis').textContent=jenis;
    document.getElementById('mMetode').textContent=metode;
    document.getElementById('mNominal').textContent='Rp '+nominal;
    document.getElementById('mTanggal').textContent=tanggal;
    const badges={approved:'<span class="inline-flex items-center gap-1 px-3 py-1 bg-green-500/30 text-green-300 rounded-full text-xs font-medium">✔ Berhasil</span>',pending:'<span class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-500/30 text-yellow-300 rounded-full text-xs font-medium">⏳ Pending</span>',rejected:'<span class="inline-flex items-center gap-1 px-3 py-1 bg-red-500/30 text-red-300 rounded-full text-xs font-medium">✖ Ditolak</span>'};
    document.getElementById('mStatus').innerHTML=badges[status.toLowerCase()]||`<span class="text-white/60 text-xs">${status}</span>`;
    document.getElementById('modalBukti').classList.add('show');
}
function tutupModal(){document.getElementById('modalBukti').classList.remove('show');}
document.getElementById('modalBukti').addEventListener('click',function(e){if(e.target===this)tutupModal();});
function filterTable(){
    const keyword=document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#tabelTransaksi .baris-transaksi').forEach(row=>{
        row.style.display=row.textContent.toLowerCase().includes(keyword)?'':'none';
    });
}
</script>
</body>
</html>
