<?php
require_once __DIR__.'/../config/config.php';
ensure_sales_columns();
$pdo=db(); $cid=current_company_id();
$day=$_GET['date']??date('Y-m-d');
$deposit=$pdo->prepare("SELECT COUNT(*) total,COALESCE(SUM(total_weight),0) weight,COALESCE(SUM(total_amount),0) amount FROM deposits WHERE company_id=? AND deposit_date=? AND status='posted'");
$deposit->execute([$cid,$day]);$deposit=$deposit->fetch();
$sale=$pdo->prepare("SELECT COUNT(*) total,COALESCE(SUM(total_weight),0) weight,COALESCE(SUM(total_amount),0) amount,COALESCE(SUM(CASE WHEN status='pending' THEN total_amount ELSE 0 END),0) pending FROM sales WHERE company_id=? AND sale_date=?");
$sale->execute([$cid,$day]);$sale=$sale->fetch();
$withdrawal=$pdo->prepare("SELECT COUNT(*) total,COALESCE(SUM(amount),0) amount FROM withdrawals WHERE company_id=? AND withdrawal_date=? AND status='posted'");
$withdrawal->execute([$cid,$day]);$withdrawal=$withdrawal->fetch();
$transaction=$pdo->prepare("SELECT type,COUNT(*) total,COALESCE(SUM(amount),0) amount FROM transactions WHERE company_id=? AND transaction_date=? GROUP BY type");
$transaction->execute([$cid,$day]);$other=['income'=>['total'=>0,'amount'=>0],'expense'=>['total'=>0,'amount'=>0]];foreach($transaction->fetchAll() as $row){$other[$row['type']]=['total'=>(int)$row['total'],'amount'=>(float)$row['amount']];}
$net=(float)$sale['amount']-(float)$withdrawal['amount']-(float)$other['expense']['amount']+(float)$other['income']['amount'];
$activity=[];
foreach([
 ['Setoran',(int)$deposit['total'],(float)$deposit['amount'],'success'],
 ['Penjualan',(int)$sale['total'],(float)$sale['amount'],'success'],
 ['Penarikan',(int)$withdrawal['total'],(float)$withdrawal['amount'],'warning'],
 ['Pemasukan lain',(int)$other['income']['total'],(float)$other['income']['amount'],'success'],
 ['Pembiayaan / biaya',(int)$other['expense']['total'],(float)$other['expense']['amount'],'danger']
] as $item){$activity[]=$item;}
$active='report_daily';$title='Laporan Harian';require __DIR__.'/../includes/header.php';?>
<div class="section-head"><div><h1>Laporan Harian</h1><p class="muted">Ringkasan aktivitas operasional dan pergerakan kas untuk satu tanggal.</p></div><div class="toolbar"><div style="display:flex;gap:8px"><button class="btn ghost sm" onclick="exportToExcel('daily-table', 'laporan-harian-<?=date('Y-m-d')?>')">📊 Excel</button><button class="btn ghost sm" onclick="exportToPDF('daily-table', 'laporan-harian-<?=date('Y-m-d')?>', 'Laporan Harian')">📄 PDF</button><button class="btn ghost sm" onclick="printReport()">🖨️ Print</button><a class="btn ghost sm" href="../index.php">← Kembali</a></div><a class="btn sm" href="transaction.php?type=expense">+ Pembiayaan</a></div></div>
<div class="card"><form class="report-filter" method="get"><label class="muted">Tanggal <input type="date" name="date" value="<?=e($day)?>"></label><button class="btn sm">Tampilkan</button><a class="btn ghost sm" href="report_daily.php">Hari Ini</a></form></div>
<div class="grid section"><div class="card stat income-card"><div class="label">Setoran</div><div class="value income"><?=rupiah((float)$deposit['amount'])?></div><small class="muted"><?=number_format((int)$deposit['total'])?> transaksi · <?=number_format((float)$deposit['weight'],2,',','.')?> kg</small></div><div class="card stat income-card"><div class="label">Penjualan</div><div class="value income"><?=rupiah((float)$sale['amount'])?></div><small class="muted"><?=number_format((int)$sale['total'])?> transaksi · <?=number_format((float)$sale['weight'],2,',','.')?> kg</small></div><div class="card stat expense-card"><div class="label">Penarikan</div><div class="value expense"><?=rupiah((float)$withdrawal['amount'])?></div><small class="muted"><?=number_format((int)$withdrawal['total'])?> transaksi</small></div><div class="card stat <?=($net>=0?'income-card':'expense-card')?>"><div class="label">Pergerakan Bersih</div><div class="value <?=($net>=0?'income':'expense')?>"><?=rupiah($net)?></div><small class="muted">Penjualan + pemasukan lain - penarikan - biaya</small></div></div>
<div class="section"><div class="section-head"><h2>Aktivitas per kategori</h2><span class="badge sage"><?=e(date('d M Y',strtotime($day)))?></span></div><div class="table-wrap"><table class="table" id="daily-table"><tr><th>Aktivitas</th><th class="right">Transaksi</th><th class="right">Nilai</th><th>Status</th></tr><?php foreach($activity as $item):?><tr><td><b><?=e($item[0])?></b></td><td class="right"><?=number_format($item[1])?></td><td class="right <?=($item[3]==='danger'||$item[3]==='warning')?'expense':'income'?>"><?=rupiah($item[2])?></td><td><span class="badge <?=$item[3]?>"><?=$item[1]?'Tercatat':'Tidak ada'?></span></td></tr><?php endforeach;?></table></div></div>
<div class="section grid"><div class="card"><span class="eyebrow">Piutang penjualan</span><h2><?=rupiah((float)$sale['pending'])?></h2><p class="muted">Nilai penjualan hari ini yang masih berstatus belum lunas.</p></div><div class="card"><span class="eyebrow">Catatan operasional</span><h2><?=number_format((int)$other['expense']['total'])?> biaya</h2><p class="muted">Gunakan menu Pembiayaan / Biaya untuk mencatat transportasi, peralatan, dan biaya rutin.</p></div></div>
<?php require __DIR__.'/../includes/footer.php';?>