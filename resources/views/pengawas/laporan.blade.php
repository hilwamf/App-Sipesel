<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Laporan Pembayaran - SIPESEL</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>body{font-family:'Poppins',sans-serif;} #toastBox{position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px;}</style>
</head>
<body style="background-image:url('{{ asset('images/kios2.jpg') }}');" class="relative min-h-screen text-white bg-cover bg-center">
<div class="absolute inset-0 bg-black/30"></div>
@include('layouts.pengawas-header')

<section class="relative z-10 px-6 md:px-20 py-8 min-h-screen">
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl md:text-4xl font-bold mb-1">Laporan Pembayaran</h1>
        <p class="text-sm opacity-80">Status tagihan dan jatuh tempo seluruh pedagang terdaftar</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white/10 backdrop-blur-md rounded-xl p-5 border border-white/20">
            <p class="text-sm text-green-200 mb-1">Total Pedagang</p>
            <p class="text-3xl font-bold" style="font-family:'Montserrat',sans-serif;">{{ $totalPedagang }}</p>
        </div>
        <div class="bg-green-500/20 backdrop-blur-md rounded-xl p-5 border border-green-400/30">
            <p class="text-sm text-green-200 mb-1">Belum Jatuh Tempo</p>
            <p class="text-3xl font-bold text-green-300" style="font-family:'Montserrat',sans-serif;">{{ $belumJatuh }}</p>
        </div>
        <div class="bg-red-500/20 backdrop-blur-md rounded-xl p-5 border border-red-400/30">
            <p class="text-sm text-red-200 mb-1">Sudah Jatuh Tempo / Belum Bayar</p>
            <p class="text-3xl font-bold text-red-300" style="font-family:'Montserrat',sans-serif;">{{ $sudahJatuh }}</p>
        </div>
    </div>

    <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input id="searchInput" type="text" oninput="filterTabel()" placeholder="Cari nama pedagang atau nomor kios..."
                    class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-yellow-400 text-sm">
            </div>
            <select id="filterStatus" onchange="filterTabel()" class="px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 text-sm">
                <option value="semua">Semua Status</option>
                <option value="jatuh_tempo">Sudah Jatuh Tempo</option>
                <option value="aman">Belum Jatuh Tempo</option>
            </select>
        </div>
    </div>

    <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="tabelLaporan">
                <thead><tr class="bg-green-800/50 border-b border-white/20">
                    <th class="px-5 py-4 text-left text-sm font-semibold whitespace-nowrap">Pedagang</th>
                    <th class="px-5 py-4 text-left text-sm font-semibold whitespace-nowrap">Nomor Kios</th>
                    <th class="px-5 py-4 text-left text-sm font-semibold whitespace-nowrap">Jenis Pajak</th>
                    <th class="px-5 py-4 text-left text-sm font-semibold whitespace-nowrap">Terakhir Bayar</th>
                    <th class="px-5 py-4 text-left text-sm font-semibold whitespace-nowrap">Jatuh Tempo</th>
                    <th class="px-5 py-4 text-left text-sm font-semibold whitespace-nowrap">Status</th>
                    <th class="px-5 py-4 text-left text-sm font-semibold whitespace-nowrap">Aksi</th>
                </tr></thead>
                <tbody>
                @foreach($pedagangList as $p)
                @php
                    $lewat = $p['sudah_lewat'];
                    $belumBayar = empty($p['tanggal_bayar']);
                    $jenisBadge=['Harian'=>'bg-sky-500/30 text-sky-200','Mingguan'=>'bg-purple-500/30 text-purple-200','Bulanan'=>'bg-indigo-500/30 text-indigo-200'];
                    $jc = isset($p['jenis_pajak']) ? ($jenisBadge[$p['jenis_pajak']] ?? 'bg-white/20 text-white') : '';

                    // WhatsApp link
                    $hp = preg_replace('/\D/', '', $p['nomor_hp'] ?? '');
                    if (str_starts_with($hp, '0')) $hp = '62'.substr($hp,1);
                    $pesan = urlencode("Yth. Bapak/Ibu ".$p['nama'].",\nKami dari pengelola Pasar ingin mengingatkan bahwa tagihan pajak kios ".($p['no_kios']??'-')." Anda telah jatuh tempo.\nMohon segera lakukan pembayaran melalui aplikasi SIPESEL.\n\nTerima kasih.");
                    $waUrl = "https://web.whatsapp.com/send?phone={$hp}&text={$pesan}";
                @endphp
                <tr class="baris border-b border-white/10 hover:bg-white/5 transition-all"
                    data-status="{{ ($lewat||$belumBayar)?'jatuh_tempo':'aman' }}"
                    data-nama="{{ strtolower($p['nama']) }}"
                    data-kios="{{ strtolower($p['no_kios']??'') }}">
                    <td class="px-5 py-4">
                        <p class="font-semibold text-sm">{{ $p['nama'] }}</p>
                        <p class="text-xs text-white/50">@{{ $p['username'] }}</p>
                    </td>
                    <td class="px-5 py-4 text-sm">
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-500/30 rounded-lg text-xs">{{ $p['no_kios'] ?? '-' }}</span>
                    </td>
                    <td class="px-5 py-4 text-sm">
                        @if($p['jenis_pajak'])
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $jc }}">{{ $p['jenis_pajak'] }}</span>
                        @else<span class="text-white/40 text-xs">Belum ada</span>@endif
                    </td>
                    <td class="px-5 py-4 text-sm text-white/80">
                        {{ $p['tanggal_bayar'] ? \Carbon\Carbon::parse($p['tanggal_bayar'])->format('d M Y') : '<span class="text-white/40">Belum pernah</span>' }}
                    </td>
                    <td class="px-5 py-4 text-sm">
                        @if(!empty($p['jatuh_tempo']))
                        <p class="font-medium {{ $lewat?'text-red-300':'text-green-300' }}">{{ \Carbon\Carbon::parse($p['jatuh_tempo'])->format('d M Y') }}</p>
                        <p class="text-xs {{ $lewat?'text-red-400':'text-green-400' }}">{{ $lewat?'Terlambat '.$p['selisih_hari'].' hari':'Sisa '.$p['selisih_hari'].' hari' }}</p>
                        @else<span class="text-red-300 text-xs font-medium">Belum ada pembayaran</span>@endif
                    </td>
                    <td class="px-5 py-4 text-sm">
                        @if($belumBayar||$lewat)
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-red-500/30 text-red-300 rounded-full text-xs font-medium">⚠ Jatuh Tempo</span>
                        @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-500/30 text-green-300 rounded-full text-xs font-medium">✔ Aman</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ $waUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 bg-green-600/40 hover:bg-green-600/60 border border-green-500/50 rounded-lg text-xs font-semibold transition-all" title="Hubungi via WhatsApp Web">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.126.554 4.122 1.524 5.856L0 24l6.306-1.501A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.882a9.879 9.879 0 01-5.034-1.376l-.36-.214-3.742.981 1.001-3.648-.235-.375A9.834 9.834 0 012.118 12C2.118 6.542 6.542 2.118 12 2.118c5.457 0 9.882 4.424 9.882 9.882 0 5.457-4.425 9.882-9.882 9.882z"/></svg>
                                WhatsApp
                            </a>
                            @if($belumBayar||$lewat)
                            <button onclick="kirimNotifikasi({{ $p['id_user'] }},'{{ addslashes($p['nama']) }}','{{ $p['no_kios']??'-' }}',this)"
                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-yellow-500/30 hover:bg-yellow-500/50 border border-yellow-500/50 rounded-lg text-xs font-semibold transition-all" title="Kirim notifikasi ke dashboard pedagang">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                Notifikasi
                            </button>
                            @else
                            <button disabled class="inline-flex items-center gap-1.5 px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-xs font-semibold text-white/30 cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                Notifikasi
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
                @if($pedagangList->isEmpty())
                <tr><td colspan="7" class="px-6 py-14 text-center text-white/40 text-sm">Belum ada data pedagang terdaftar.</td></tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
</section>

<div id="toastBox"></div>
<footer class="relative z-10 bg-green-800 text-green-300 text-center text-xs py-4 mt-10">&copy; 2026 <span class="text-yellow-400 font-bold">SIPESEL</span> — Sistem Informasi Pembayaran Pajak Pasar.</footer>

<script>
const csrfToken = '{{ csrf_token() }}';
const kirimNotifikasiUrl = '{{ route('pengawas.kirim-notifikasi') }}';

function filterTabel(){
    const keyword=document.getElementById('searchInput').value.toLowerCase();
    const status=document.getElementById('filterStatus').value;
    document.querySelectorAll('#tabelLaporan .baris').forEach(row=>{
        const namaMatch=row.dataset.nama.includes(keyword)||row.dataset.kios.includes(keyword);
        const statusMatch=status==='semua'||row.dataset.status===status;
        row.style.display=(namaMatch&&statusMatch)?'':'none';
    });
}

function kirimNotifikasi(idUser,nama,kios,btn){
    btn.disabled=true;
    btn.innerHTML=`<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Mengirim...`;

    const formData=new FormData();
    formData.append('id_user',idUser);
    formData.append('nama',nama);
    formData.append('kios',kios);
    formData.append('_token',csrfToken);

    fetch(kirimNotifikasiUrl,{method:'POST',body:formData})
    .then(r=>r.json())
    .then(data=>{
        if(data.success){
            tampilToast('Notifikasi berhasil dikirim ke '+nama,'success');
            btn.innerHTML=`<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Terkirim`;
            btn.classList.replace('bg-yellow-500/30','bg-green-500/30');
            btn.classList.replace('border-yellow-500/50','border-green-500/50');
        }else{
            tampilToast('Gagal mengirim notifikasi: '+(data.message||''),'error');
            btn.disabled=false;
            btn.innerHTML=`<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg> Notifikasi`;
        }
    })
    .catch(()=>{tampilToast('Terjadi kesalahan jaringan','error');btn.disabled=false;});
}

function tampilToast(pesan,tipe){
    const box=document.getElementById('toastBox');
    const toast=document.createElement('div');
    const warna=tipe==='success'?'bg-green-600 border-green-400':'bg-red-600 border-red-400';
    toast.className=`px-5 py-3 rounded-xl border text-white text-sm font-medium shadow-xl ${warna} transition-all duration-300`;
    toast.textContent=pesan;
    box.appendChild(toast);
    setTimeout(()=>{toast.style.opacity='0';setTimeout(()=>toast.remove(),300);},3500);
}
</script>
</body>
</html>
