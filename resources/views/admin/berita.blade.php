@extends('layouts.sidebar-admin')
@section('title','Berita Pajak - SIPESEL')
@section('page-title','Berita & Info Pajak')
@section('page-sub','Kelola informasi dan edukasi pajak untuk pedagang')

@section('content')
@if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-3 mb-5 text-sm flex items-center gap-2"><svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ session('success') }}</div>@endif

<div class="flex items-center justify-between mb-5">
    <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 flex items-center gap-2 text-sm flex-1 mr-4">
        <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-blue-700">Berita <strong>Aktif</strong> tampil sebagai carousel di dashboard pedagang.</p>
    </div>
    <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="px-5 py-2.5 bg-green-700 hover:bg-green-800 text-white font-semibold rounded-xl text-sm flex items-center gap-2 flex-shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Tambah Berita
    </button>
</div>

@if($berita->count() > 0)
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($berita as $b)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
        <div class="relative h-36 bg-gray-100 overflow-hidden flex-shrink-0">
            @if($b->thumbnail && file_exists(public_path($b->thumbnail)))
            <img src="{{ asset($b->thumbnail) }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
            @else
            <div class="w-full h-full bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center">
                <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            @endif
            <span class="absolute top-2 right-2 px-2.5 py-1 rounded-full text-[10px] font-bold {{ $b->status==='aktif'?'bg-green-500 text-white':'bg-gray-400 text-white' }}">{{ $b->status==='aktif'?'● Aktif':'○ Nonaktif' }}</span>
        </div>
        <div class="px-4 py-3 flex-1">
            <p class="text-gray-400 text-[10px] mb-1">{{ $b->created_at->format('d M Y') }}</p>
            <h3 class="font-bold text-gray-800 text-sm mb-1.5 leading-snug">{{ $b->judul }}</h3>
            <p class="text-gray-500 text-xs line-clamp-2">{{ $b->isi }}</p>
        </div>
        <div class="px-4 py-3 border-t border-gray-50 flex gap-2">
            <button onclick='showEditModal(@json($b))' class="flex-1 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold rounded-xl text-xs">Edit</button>
            <form method="POST" action="{{ route('admin.toggle-berita') }}" class="flex-1">@csrf<input type="hidden" name="id" value="{{ $b->id }}"><button class="w-full py-2 {{ $b->status==='aktif'?'bg-yellow-50 hover:bg-yellow-100 text-yellow-700':'bg-green-50 hover:bg-green-100 text-green-700' }} font-semibold rounded-xl text-xs">{{ $b->status==='aktif'?'Nonaktifkan':'Aktifkan' }}</button></form>
            <form method="POST" action="{{ route('admin.hapus-berita') }}" onsubmit="return confirm('Hapus?')">@csrf<input type="hidden" name="id" value="{{ $b->id }}"><button class="py-2 px-3 bg-red-50 hover:bg-red-100 text-red-600 font-semibold rounded-xl text-xs">Hapus</button></form>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="bg-white rounded-2xl p-16 text-center border border-gray-100">
    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3"><svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg></div>
    <p class="font-bold text-gray-700 mb-1">Belum Ada Berita</p>
    <p class="text-gray-400 text-sm mb-4">Tambahkan berita edukasi pajak untuk pedagang</p>
    <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="px-6 py-2.5 bg-green-700 text-white font-semibold rounded-xl text-sm">Tambah Pertama</button>
</div>
@endif

{{-- Modal Tambah --}}
<div id="modalTambah" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-5"><h3 class="font-bold text-gray-800">Tambah Berita</h3><button onclick="document.getElementById('modalTambah').classList.add('hidden')" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-500">✕</button></div>
        <form method="POST" action="{{ route('admin.tambah-berita') }}" enctype="multipart/form-data" class="space-y-4">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Judul</label><input type="text" name="judul" required class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Isi</label><textarea name="isi" required rows="5" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 resize-none"></textarea></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Thumbnail <span class="text-gray-400 font-normal">(opsional)</span></label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-green-400 transition cursor-pointer" onclick="document.getElementById('thumbInput').click()">
                    <img id="thumbPreview" src="" class="hidden w-full h-28 object-cover rounded-lg mb-2">
                    <div id="thumbPlaceholder"><svg class="w-7 h-7 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg><p class="text-gray-400 text-xs">Klik untuk upload</p></div>
                    <input type="file" id="thumbInput" name="thumbnail" accept="image/*" class="hidden" onchange="previewThumb(this,'thumbPreview','thumbPlaceholder')">
                </div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-500"><option value="aktif">Aktif</option><option value="nonaktif">Nonaktif (Draft)</option></select></div>
            <div class="flex gap-3"><button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="flex-1 py-2.5 bg-gray-100 text-gray-600 font-semibold rounded-xl text-sm">Batal</button><button type="submit" class="flex-1 py-2.5 bg-green-700 text-white font-semibold rounded-xl text-sm">Simpan</button></div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEdit" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-5"><h3 class="font-bold text-gray-800">Edit Berita</h3><button onclick="document.getElementById('modalEdit').classList.add('hidden')" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-500">✕</button></div>
        <form method="POST" action="{{ route('admin.edit-berita') }}" enctype="multipart/form-data" class="space-y-4">@csrf
            <input type="hidden" name="id" id="editId">
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Judul</label><input type="text" name="judul" id="editJudul" required class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Isi</label><textarea name="isi" id="editIsi" required rows="5" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea></div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Thumbnail Baru</label>
                <div id="editCurrentThumb" class="hidden mb-2"><img id="editThumbImg" src="" class="w-full h-28 object-cover rounded-xl"><p class="text-xs text-gray-400 mt-1">Thumbnail saat ini</p></div>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-3 text-center cursor-pointer hover:border-blue-400 transition" onclick="document.getElementById('editThumbInput').click()">
                    <img id="editThumbPreview" src="" class="hidden w-full h-24 object-cover rounded-lg mb-2">
                    <div id="editThumbPlaceholder"><p class="text-gray-400 text-xs">Klik untuk ganti thumbnail</p></div>
                    <input type="file" id="editThumbInput" name="thumbnail" accept="image/*" class="hidden" onchange="previewThumb(this,'editThumbPreview','editThumbPlaceholder')">
                </div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" id="editStatus" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"><option value="aktif">Aktif</option><option value="nonaktif">Nonaktif</option></select></div>
            <div class="flex gap-3"><button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')" class="flex-1 py-2.5 bg-gray-100 text-gray-600 font-semibold rounded-xl text-sm">Batal</button><button type="submit" class="flex-1 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm">Update</button></div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
function showEditModal(b){
    document.getElementById('editId').value=b.id;
    document.getElementById('editJudul').value=b.judul;
    document.getElementById('editIsi').value=b.isi;
    document.getElementById('editStatus').value=b.status;
    const tc=document.getElementById('editCurrentThumb');
    if(b.thumbnail){document.getElementById('editThumbImg').src='/'+b.thumbnail;tc.classList.remove('hidden');}else{tc.classList.add('hidden');}
    document.getElementById('editThumbPreview').classList.add('hidden');
    document.getElementById('editThumbPlaceholder').style.display='block';
    document.getElementById('modalEdit').classList.remove('hidden');
}
function previewThumb(input,previewId,placeholderId){
    if(input.files&&input.files[0]){const r=new FileReader();r.onload=e=>{const img=document.getElementById(previewId);img.src=e.target.result;img.classList.remove('hidden');document.getElementById(placeholderId).style.display='none';};r.readAsDataURL(input.files[0]);}
}
@endsection