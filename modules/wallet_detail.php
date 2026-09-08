<?php require_once __DIR__.'/../config/config.php';$id=(int)($_GET['id']??1);$s=db()->prepare("SELECT * FROM wallets WHERE id=? AND company_id=?");$s->execute([$id,current_company_id()]);$w=$s->fetch();if(!$w){flash('error','Dompet tidak ditemukan.');redirect('wallets.php');}$q=db()->prepare("SELECT * FROM transactions WHERE wallet_id=? AND company_id=? ORDER BY transaction_date DESC,id DESC LIMIT 30");$q->execute([$id,current_company_id()]);$rows=$q->fetchAll();$active='wallets';$title='Dompet';require __DIR__.'/../includes/header.php';?>
<div class="section-head">
  <div><h1><?=e($w['name'])?></h1><p class="muted">Detail mutasi transaksi dompet 30 terakhir.</p></div>
  <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
    <a class="btn ghost sm" href="wallets.php">← Kembali ke Daftar Dompet</a>
    <a class="btn sm" href="transaction.php?type=income">+ Pemasukan</a>
    <a class="btn danger sm" href="transaction.php?type=expense">+ Pengeluaran</a>
  </div>
</div>
<div class="grid"><div class="card stat"><div class="label">SALDO</div><div class="value"><?=rupiah((float)$w['balance'])?></div></div><div class="card stat"><div class="label">TIPE</div><div class="value"><?=e($w['type'])?></div></div></div>
<div class="section table-wrap"><table class="table"><tr><th>Tanggal</th><th>Keterangan</th><th>Jenis</th><th class="right">Nominal</th></tr><?php foreach($rows as $r):?><tr><td><?=e($r['transaction_date'])?></td><td><?=e($r['description'])?></td><td><span class="badge <?=e($r['type'])==='income'?'success':'danger'?>"><?=e($r['type'])==='income'?'Pemasukan':'Pengeluaran'?></span></td><td class="right"><?=rupiah((float)$r['amount'])?></td></tr><?php endforeach;?><?php if(!$rows):?><tr><td colspan="4" class="empty">Belum ada transaksi di dompet ini.</td></tr><?php endif;?></table></div><?php require __DIR__.'/../includes/footer.php';?>