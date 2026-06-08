@extends('layouts.sidebar-admin')
@section('title','Verifikasi Pembayaran - SIPESEL')
@section('page-title','Verifikasi Pembayaran')
@section('page-sub','Approve atau reject pembayaran dari pedagang')

@section('content')

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-4 mb-5 flex items-center gap-3">
    <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif

<div class="flex gap-2 mb-5">
    <button onclick="switchTab('pending')" id="tab-pending"
        class="px-5 py-2 bg-green-700 text-white font-semibold rounded-xl text-sm transition flex items-center gap-2">
        Menunggu Verifikasi
        @if($pending->count() > 0)<span class="px-2 py-0.5 bg-red-500 text-white text-xs rounded-full">{{ $pending->count() }}</span>@endif
    </button>
    <button onclick="switchTab('verified')" id="tab-verified"
        class="px-5 py-2 bg-white text-gray-600 font-semibold rounded-xl text-sm border border-gray-200 hover:bg-green-50 transition">
        Riwayat Verifikasi
    </button>
</div>

<div id="content-pending" class="tab-content active">
    @if($pending->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($pending as $row)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
            <div class="bg-gradient-to-r from-yellow-400 to-orange-400 px-5 py-2.5 flex items-center justify-between">
                <span class="text-yellow-900 text-xs font-bold uppercase tracking-wider">Menunggu Verifikasi</span>
                <span class="w-2 h-2 bg-yellow-900/40 rounded-full animate-pulse"></span>
            </div>
            <div class="px-5 py-4 flex-1">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-black text-sm flex-shrink-0">{{ strtoupper(substr($row->user->nama??'?',0,1)) }}</div>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">{{ $row->user->nama ?? '-' }}</p>
                        <p class="text-gray-400 text-xs">{{ $row->user->username ?? '-' }}</p>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between py-1 border-b border-gray-50"><span class="text-gray-400">Kios</span><span class="font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded text-xs">{{ $row->nomor_kios }}</span></div>
                    <div class="flex justify-between py-1 border-b border-gray-50"><span class="text-gray-400">Jenis</span><span class="font-semibold">{{ $row->jenis_pajak }}</span></div>
                    <div class="flex justify-between py-1 border-b border-gray-50"><span class="text-gray-400">Metode</span><span class="font-semibold">{{ $row->metode_pembayaran }}</span></div>
                    <div class="flex justify-between py-1 border-b border-gray-50"><span class="text-gray-400">Tanggal</span><span class="font-semibold">{{ $row->tanggal->format('d M Y') }}</span></div>
                    <div class="flex justify-between py-2"><span class="text-gray-400">Total</span><span class="font-black text-green-700 text-base">Rp {{ number_format($row->nominal,0,',','.') }}</span></div>
                </div>
            </div>
            <div class="px-5 pb-4 grid grid-cols-2 gap-2">
                <form method="POST" action="{{ route('admin.proses-verifikasi') }}" onsubmit="return confirm('Setujui?')">
                    @csrf<input type="hidden" name="id_transaksi" value="{{ $row->id_transaksi }}"><input type="hidden" name="action" value="approve">
                    <button type="submit" class="w-full py-2 bg-green-700 hover:bg-green-800 text-white font-semibold rounded-xl text-sm transition flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Approve
                    </button>
                </form>
                <button onclick="showRejectModal({{ $row->id_transaksi }},'{{ addslashes($row->user->nama??'') }}')"
                    class="py-2 bg-red-50 hover:bg-red-100 text-red-600 font-semibold rounded-xl text-sm border border-red-100 flex items-center justify-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Reject
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-2xl p-16 text-center border border-gray-100">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="font-bold text-gray-700 mb-1">Tidak Ada Pembayaran Pending</p>
        <p class="text-gray-400 text-sm">Semua pembayaran sudah diverifikasi</p>
    </div>
    @endif
</div>

<div id="content-verified" class="tab-content hidden">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pedagang</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kios</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Nominal</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Verifikator</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($verified as $row)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4"><p class="font-semibold text-gray-800 text-sm">{{ $row->user->nama ?? '-' }}</p><p class="text-gray-400 text-xs">{{ $row->user->username ?? '-' }}</p></td>
                        <td class="px-5 py-4"><span class="px-2 py-1 bg-blue-50 text-blue-700 font-bold text-xs rounded-lg">{{ $row->nomor_kios }}</span></td>
                        <td class="px-5 py-4 text-right font-bold text-gray-800 text-sm">Rp {{ number_format($row->nominal,0,',','.') }}</td>
                        <td class="px-5 py-4 text-center"><span class="px-3 py-1 rounded-full text-xs font-semibold {{ $row->status==='approved'?'bg-green-100 text-green-700':'bg-red-100 text-red-600' }}">{{ $row->status==='approved'?'✓ Approved':'✗ Rejected' }}</span></td>
                        <td class="px-5 py-4 text-sm text-gray-600">{{ $row->verifier->username ?? '-' }}</td>
                        <td class="px-5 py-4 text-sm text-gray-600">{{ $row->verified_at?->format('d M Y') ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400 text-sm">Belum ada riwayat</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Reject --}}
<div id="modalReject" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-800">Tolak Pembayaran</h3>
            <button onclick="closeRejectModal()" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-500">✕</button>
        </div>
        <p class="text-sm text-gray-600 mb-4">Alasan penolakan untuk <span id="rejectNama" class="font-bold text-green-700"></span>:</p>
        <form method="POST" action="{{ route('admin.proses-verifikasi') }}">@csrf
            <input type="hidden" name="id_transaksi" id="rejectId">
            <input type="hidden" name="action" value="reject">
            <textarea name="catatan_penolakan" rows="4" required placeholder="Tulis alasan penolakan..."
                class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-400 mb-4 resize-none"></textarea>
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-xl text-sm">Batal</button>
                <button type="submit" class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl text-sm">Tolak</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
if(window.history.replaceState) window.history.replaceState(null,null,window.location.href);
function switchTab(tab){
    document.querySelectorAll('.tab-content').forEach(c=>c.classList.add('hidden'));
    document.getElementById('content-'+tab).classList.remove('hidden');
    document.getElementById('tab-pending').className='px-5 py-2 bg-white text-gray-600 font-semibold rounded-xl text-sm border border-gray-200 hover:bg-green-50 transition flex items-center gap-2';
    document.getElementById('tab-verified').className='px-5 py-2 bg-white text-gray-600 font-semibold rounded-xl text-sm border border-gray-200 hover:bg-green-50 transition';
    document.getElementById('tab-'+tab).className='px-5 py-2 bg-green-700 text-white font-semibold rounded-xl text-sm transition flex items-center gap-2';
}
function showRejectModal(id,nama){document.getElementById('rejectId').value=id;document.getElementById('rejectNama').textContent=nama;document.getElementById('modalReject').classList.remove('hidden');}
function closeRejectModal(){document.getElementById('modalReject').classList.add('hidden');}
document.getElementById('modalReject').addEventListener('click',function(e){if(e.target===this)closeRejectModal();});
@endsection