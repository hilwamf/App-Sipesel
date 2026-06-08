<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pembayaran - {{ $namaBulan[$bulan] ?? '' }} {{ $tahun }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; padding: 20px; }
        .header { text-align:center; border-bottom: 2px solid #166534; padding-bottom:12px; margin-bottom:16px; }
        .header h1 { font-size:20px; color:#166534; font-weight:bold; }
        .header p { color:#555; font-size:11px; margin-top:2px; }
        .meta { display:flex; justify-content:space-between; margin-bottom:16px; font-size:11px; }
        .stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; margin-bottom:16px; }
        .stat-box { border:1px solid #e5e7eb; border-radius:6px; padding:8px; text-align:center; }
        .stat-box .val { font-size:15px; font-weight:bold; color:#166534; }
        .stat-box .lbl { font-size:10px; color:#888; margin-top:2px; }
        table { width:100%; border-collapse:collapse; margin-bottom:16px; }
        th { background:#166534; color:white; padding:7px 8px; text-align:left; font-size:11px; }
        td { padding:6px 8px; border-bottom:1px solid #f3f4f6; font-size:11px; }
        tr:nth-child(even) td { background:#f9fafb; }
        .badge { padding:2px 8px; border-radius:9999px; font-size:10px; font-weight:bold; }
        .approved { background:#dcfce7; color:#166534; }
        .pending  { background:#fef9c3; color:#854d0e; }
        .rejected { background:#fee2e2; color:#991b1b; }
        .section-title { font-size:13px; font-weight:bold; color:#166534; margin:16px 0 8px; border-left:3px solid #166534; padding-left:8px; }
        .footer { text-align:center; font-size:10px; color:#9ca3af; margin-top:24px; border-top:1px solid #e5e7eb; padding-top:8px; }
        @media print { button { display:none; } body { padding:10px; } }
    </style>
</head>
<body>
<div class="header">
    <h1>SIPESEL — Laporan Pembayaran Pajak</h1>
    <p>Pasar Wadungasri, Sidoarjo &nbsp;|&nbsp; Periode: {{ $namaBulan[$bulan] ?? '' }} {{ $tahun }}</p>
</div>

<div class="meta">
    <span>Dicetak: {{ now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}</span>
    <button onclick="window.print()" style="background:#166534;color:white;border:none;padding:5px 14px;border-radius:6px;cursor:pointer;font-size:11px;">🖨 Cetak</button>
</div>

<div class="stat-grid">
    <div class="stat-box"><div class="val">{{ $totalPedagang }}</div><div class="lbl">Total Pedagang</div></div>
    <div class="stat-box"><div class="val" style="color:#166534;">{{ $sudahBayar }}</div><div class="lbl">Sudah Bayar</div></div>
    <div class="stat-box"><div class="val" style="color:#dc2626;">{{ $belumBayar }}</div><div class="lbl">Belum Bayar</div></div>
    <div class="stat-box"><div class="val">{{ $kepatuhan }}%</div><div class="lbl">Kepatuhan</div></div>
</div>

<div class="stat-grid">
    <div class="stat-box" style="grid-column:span 2"><div class="val">Rp {{ number_format($ringkasan['total_masuk'],0,',','.') }}</div><div class="lbl">Total Pendapatan</div></div>
    <div class="stat-box"><div class="val">{{ $ringkasan['jml_approved'] }}</div><div class="lbl">Approved</div></div>
    <div class="stat-box"><div class="val">{{ $ringkasan['jml_pending'] }}</div><div class="lbl">Pending</div></div>
</div>

<div class="section-title">Rekap Per Pedagang</div>
<table>
    <thead><tr>
        <th>No</th><th>Nama Pedagang</th><th>Username</th><th>Kios</th>
        <th>Jumlah Bayar</th><th>Total Nominal</th><th>Status</th>
    </tr></thead>
    <tbody>
        @foreach($rekapPedagang as $i => $p)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $p['nama'] }}</td>
            <td>{{ $p['username'] }}</td>
            <td>{{ $p['no_kios'] ?? '-' }}</td>
            <td>{{ $p['jml_bayar'] }}x</td>
            <td>Rp {{ number_format($p['bayar'],0,',','.') }}</td>
            <td><span class="badge {{ $p['jml_bayar']>0?'approved':'rejected' }}">{{ $p['jml_bayar']>0?'✓ Sudah':'✗ Belum' }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="section-title">Detail Transaksi</div>
<table>
    <thead><tr>
        <th>No</th><th>Nama</th><th>Kios</th><th>Jenis</th><th>Metode</th><th>Tanggal</th><th>Nominal</th><th>Status</th>
    </tr></thead>
    <tbody>
        @foreach($transaksi as $i => $t)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $t->user->nama ?? '-' }}</td>
            <td>{{ $t->nomor_kios }}</td>
            <td>{{ $t->jenis_pajak }}</td>
            <td>{{ $t->metode_pembayaran }}</td>
            <td>{{ $t->tanggal->format('d/m/Y') }}</td>
            <td>Rp {{ number_format($t->nominal,0,',','.') }}</td>
            <td><span class="badge {{ $t->status }}">{{ ucfirst($t->status) }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">SIPESEL — Sistem Informasi Pembayaran Pajak Pasar Wadungasri &copy; {{ date('Y') }}</div>
</body>
</html>