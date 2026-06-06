<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Pembayaran - SIPESEL Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body{font-family:'Poppins',sans-serif;}.tab-active{background:rgba(250,204,21,.2);border-color:rgba(250,204,21,.5);color:rgb(253,224,71);}.tab-content{display:none;}.tab-content.active{display:block;}.modal{display:none;}.modal.show{display:flex;}</style>
</head>
<body style="background-image:url('{{ asset('images/kios2.jpg') }}');" class="relative min-h-screen text-white bg-cover bg-center">
<div class="absolute inset-0 bg-black/40"></div>
@php $totalPending = $pending->count(); @endphp
@include('layouts.admin-header')

<section class="relative z-10 px-6 md:px-20 py-8 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl md:text-4xl font-bold mb-2">Verifikasi Pembayaran</h1>
            <p class="text-sm opacity-90">Approve atau reject pembayaran dari pedagang</p>
        </div>

        @if(session('success'))
        <div class="bg-green-500/30 border border-green-500/50 text-white rounded-lg p-4 mb-6 flex items-center gap-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="bg-red-500/30 border border-red-500/50 text-white rounded-lg p-4 mb-6">{{ session('error') }}</div>
        @endif

        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 mb-6">
            <div class="flex flex-wrap gap-2 p-4 border-b border-white/20">
                <button onclick="switchTab('pending')" id="tab-pending" class="tab-btn tab-active px-4 py-2 rounded-lg text-sm font-medium transition-all border border-transparent flex items-center">
                    Menunggu Verifikasi
                    @if($pending->count() > 0)<span class="ml-2 px-2 py-0.5 bg-red-500 text-white text-xs rounded-full">{{ $pending->count() }}</span>@endif
                </button>
                <button onclick="switchTab('verified')" id="tab-verified" class="tab-btn px-4 py-2 rounded-lg text-green-200 hover:bg-white/10 text-sm font-medium transition-all border border-transparent">
                    Riwayat Verifikasi
                </button>
            </div>

            <div id="content-pending" class="tab-content active p-6">
                @if($pending->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($pending as $row)
                    <div class="bg-white/5 backdrop-blur-sm border border-white/20 rounded-xl p-6 hover:border-yellow-400/50 transition-all flex flex-col">
                        <div class="flex items-start justify-between mb-4">
                            <div><h3 class="font-bold text-lg">{{ $row->user->nama ?? '-' }}</h3><p class="text-sm opacity-80">@{{ $row->user->username ?? '-' }}</p></div>
                            <span class="px-3 py-1 bg-yellow-500/30 text-yellow-300 rounded-full text-xs font-medium whitespace-nowrap">Pending</span>
                        </div>
                        <div class="space-y-2 mb-4 text-sm flex-grow">
                            <div class="flex justify-between"><span class="opacity-70">Kios:</span><span class="font-semibold">{{ $row->nomor_kios }}</span></div>
                            <div class="flex justify-between"><span class="opacity-70">Nominal:</span><span class="font-semibold text-green-400">Rp {{ number_format($row->nominal,0,',','.') }}</span></div>
                            <div class="flex justify-between"><span class="opacity-70">Jenis Pajak:</span><span class="font-semibold">{{ ucfirst($row->jenis_pajak) }}</span></div>
                            <div class="flex justify-between"><span class="opacity-70">Tanggal:</span><span class="font-semibold">{{ $row->tanggal->format('d M Y') }}</span></div>
                        </div>
                        <div class="mt-auto grid grid-cols-2 gap-3">
                            <form method="POST" action="{{ route('admin.proses-verifikasi') }}" onsubmit="return confirm('Setujui pembayaran ini?');" class="w-full">
                                @csrf
                                <input type="hidden" name="id_transaksi" value="{{ $row->id_transaksi }}">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="w-full px-4 py-2 bg-green-500/30 hover:bg-green-500/50 border border-green-500/50 rounded-lg text-sm font-medium transition-all flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Approve
                                </button>
                            </form>
                            <button onclick="showRejectModal({{ $row->id_transaksi }}, '{{ $row->user->nama ?? '' }}')" class="px-4 py-2 bg-red-500/30 hover:bg-red-500/50 border border-red-500/50 rounded-lg text-sm font-medium transition-all w-full flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reject
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-16 opacity-60">
                    <svg class="w-20 h-20 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="text-xl font-semibold mb-2">Tidak Ada Pembayaran Pending</h3>
                    <p class="text-sm">Semua pembayaran sudah diverifikasi</p>
                </div>
                @endif
            </div>

            <div id="content-verified" class="tab-content p-6">
                @if($verified->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead><tr class="bg-white/10 border-b border-white/20">
                            <th class="px-6 py-4 text-sm font-semibold whitespace-nowrap">Pedagang</th>
                            <th class="px-6 py-4 text-sm font-semibold whitespace-nowrap">Kios</th>
                            <th class="px-6 py-4 text-sm font-semibold whitespace-nowrap">Nominal</th>
                            <th class="px-6 py-4 text-sm font-semibold whitespace-nowrap">Status</th>
                            <th class="px-6 py-4 text-sm font-semibold whitespace-nowrap">Verifikator</th>
                            <th class="px-6 py-4 text-sm font-semibold whitespace-nowrap">Tanggal</th>
                        </tr></thead>
                        <tbody>
                            @foreach($verified as $row)
                            <tr class="border-b border-white/10 hover:bg-white/5 transition-all">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-semibold">{{ $row->user->nama ?? '-' }}</div>
                                    <div class="text-xs opacity-70">@{{ $row->user->username ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 font-semibold whitespace-nowrap">{{ $row->nomor_kios }}</td>
                                <td class="px-6 py-4 font-semibold text-green-400 whitespace-nowrap">Rp {{ number_format($row->nominal,0,',','.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $row->status==='approved'?'bg-green-500/30 text-green-300':'bg-red-500/30 text-red-300' }}">
                                        {{ ucfirst($row->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm whitespace-nowrap">{{ $row->verifier->username ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm whitespace-nowrap">{{ $row->verified_at?->format('d M Y') ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-16 opacity-60"><p>Belum ada riwayat verifikasi</p></div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Modal Reject --}}
<div id="modalReject" class="modal fixed inset-0 bg-black/70 backdrop-blur-sm items-center justify-center z-50 p-4">
    <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 w-full max-w-md border border-white/20">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold">Tolak Pembayaran</h3>
            <button onclick="closeRejectModal()" class="text-white hover:text-red-400 transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <p class="text-sm opacity-90 mb-4">Berikan alasan penolakan untuk <span id="rejectNama" class="font-semibold text-yellow-400"></span>:</p>
        <form method="POST" action="{{ route('admin.proses-verifikasi') }}">
            @csrf
            <input type="hidden" name="id_transaksi" id="rejectId">
            <input type="hidden" name="action" value="reject">
            <textarea name="catatan_penolakan" rows="4" required placeholder="Contoh: Bukti pembayaran tidak jelas, nominal tidak sesuai, dll."
                class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-red-400 mb-4"></textarea>
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg font-medium transition-all">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-red-500/30 hover:bg-red-500/50 border border-red-500/50 rounded-lg font-medium transition-all">Tolak Pembayaran</button>
            </div>
        </form>
    </div>
</div>

<footer class="bg-green-800 text-green-300 text-center text-xs py-4 mt-10 relative z-10">&copy; 2026 <span class="text-yellow-400 font-bold">SIPESEL</span> — Admin Panel.</footer>

<script>
if(window.history.replaceState){window.history.replaceState(null,null,window.location.href);}
function switchTab(tab){
    document.querySelectorAll('.tab-content').forEach(c=>c.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b=>{b.classList.remove('tab-active');b.classList.add('text-green-200','hover:bg-white/10');});
    document.getElementById('content-'+tab).classList.add('active');
    const btn=document.getElementById('tab-'+tab);
    btn.classList.add('tab-active');btn.classList.remove('text-green-200','hover:bg-white/10');
}
function showRejectModal(id,nama){
    document.getElementById('rejectId').value=id;
    document.getElementById('rejectNama').textContent=nama;
    document.getElementById('modalReject').classList.add('show');
}
function closeRejectModal(){document.getElementById('modalReject').classList.remove('show');}
document.getElementById('modalReject').addEventListener('click',function(e){if(e.target===this)closeRejectModal();});
</script>
</body>
</html>
