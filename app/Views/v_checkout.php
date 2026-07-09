<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<?php 
  helper('promo_helper'); 

  $v_jasa   = hitung_biaya_jasa($total ?? 0); 
  $v_mouse  = hitung_free_mouse($total ?? 0); 
  $v_diskon = 0; 
  $grand_total_awal = (($total ?? 0) + $v_jasa) - $v_diskon - $v_mouse; 
?>
<div class="row">
    <div class="col-lg-6">
        <?= form_open('transaksi/checkout', 'class="row g-3"') ?>

        <?= form_hidden('username', session()->get('username')) ?>

        <?= form_input([
            'type' => 'hidden', 
            'name' => 'total_harga', 
            'id' => 'total_harga']) ?>

        <div class="col-12">
            <?= form_label('Nama', 'nama', ['class' => 'form-label']) ?>
            <?= form_input([
                'name'     => 'nama',
                'id'       => 'nama',
                'class'    => 'form-control',
                'value'    => session()->get('username'),
                'readonly' => true]) ?>
        </div>
        <div class="col-12">
            <?= form_label('Alamat', 'alamat', ['class' => 'form-label']) ?>
            <?= form_input([
                'name'  => 'alamat',
                'id'    => 'alamat',
                'class' => 'form-control']) ?>
        </div> 
        <div class="col-12"> 
            <?= form_label('Kelurahan', 'kelurahan', ['class' => 'form-label']) ?>
            <?= form_dropdown('kelurahan', [], '', ['id' => 'kelurahan', 'class' => 'form-control']) ?></div>
        <div class="col-12"> 
            <?= form_label('Layanan', 'layanan', ['class' => 'form-label']) ?> 
            <?= form_dropdown('layanan', [], '', ['id' => 'layanan', 'class' => 'form-control']) ?></div>
        <div class="col-12">
            <?= form_label('Ongkir', 'ongkir', ['class' => 'form-label']) ?>
            <?= form_input([
                'name'     => 'ongkir',
                'id'       => 'ongkir',
                'class'    => 'form-control',
                'readonly' => true]) ?>
        </div>
        <div class="mb-3">
            <label class="form-label">Kode Voucher</label>
            <div class="input-group">
                <input type="text" name="kode_voucher" id="kode_voucher" class="form-control" placeholder="Contoh: AKHIRTAHUN">
                <button class="btn btn-secondary" type="button" id="btn_cek_voucher">Cek Kode</button>
            </div>
            <small class="text-muted">Tersedia: PROMO2025 (10%), PROMO2026 (15%), AKHIRTAHUN (25%)</small>
        </div>
        <div class="col-12">
            <?= form_submit(
                'submit',
                'Buat Pesanan',
                ['class' => 'btn btn-primary']) ?>
        </div>

        <?= form_close() ?> 
</div>

    
<div class="col-lg-6">
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Nama</th>
                <th scope="col">Harga</th>
                <th scope="col">Jumlah</th>
                <th scope="col">Sub Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)) : ?>
                <?php foreach ($items as $index => $item) : ?>
                    <tr>
                        <td><?= $item['name'] ?></td>
                        <td><?= $item['price'] ?></td>
                        <td><?= $item['qty'] ?></td>
                        <td><?= number_to_currency($item['price'] * $item['qty'], 'IDR') ?></td>
                    </tr>
                <?php endforeach; ?>
                
                <tr>
                    <td colspan="2"></td>
                    <td><strong>Subtotal</strong></td>
                    <td><?= number_to_currency($total, 'IDR'); ?></td>
                </tr>
                <tr class="text-danger">
                    <td colspan="2"></td>
                    <td>Diskon Voucher</td>
                    <td><span id="diskon_view">-IDR 0 (0%)</span></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Biaya Jasa</td>
                    <td><?= number_to_currency($v_jasa ?? 0, 'IDR'); ?></td>
                </tr>
                <tr class="text-success baris-mouse">
                    <td colspan="2"></td>
                    <td>Free Mouse</td>
                    <td>-<?= number_to_currency($v_mouse ?? 0, 'IDR'); ?></td>
                </tr>
                <tr class="fw-bold border-top">
                    <td colspan="2"></td>
                    <td>Grand Total</td>
                    <td><span id="total"><?= number_to_currency($grand_total_awal ?? 0, 'IDR'); ?></span></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('script') ?>
<script>
$(document).ready(function() {
    let ongkir = 0;
    let subtotal = <?= $total ?>;
    hitungTotal();

	$('#kelurahan').select2({
	    placeholder: 'Cari daerah tujuan',
	    minimumInputLength: 3, 
        ajax: {
            url: '<?= site_url('ajax/destinations') ?>',
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return {
                    q: params.term
                };
            },
            processResults: function(data) {
                return data;
            },
            cache: true
        }
	});

    $('#kelurahan').on('change', function() {
        let id_kelurahan = $(this).val();
        
        $('#layanan').empty();
        ongkir = 0;
        hitungTotal();

        $.ajax({
            url: "<?= site_url('ajax/costs') ?>",
            dataType: "json",
            data: {
                destination: id_kelurahan
            },
            success: function(data) {
                data.forEach(function(item) {
                    $('#layanan').append(
                        `<option value="${item.cost}">${item.description} (${item.service}) : estimasi ${item.etd}</option>`
                    );
                });
                $('#layanan').trigger('change');
            }
        });
    });

    $('#btn_cek_voucher').on('click', function() {
        console.log("Tombol berhasil diklik!");
        console.log("Isi input voucher:", $('#kode_voucher').val()); 
        hitungTotal();
    });

    $('#layanan').on('change', function() {
        // ambil  biaya ongkir
        let hargaOngkir = $(this).val();
        
        // show biaya ongkir
        $('#ongkir').val(hargaOngkir);
        
        //perhitungan total
        hitungTotal();
    });
});

function hitungTotal() {
    let total = <?= $total ?? 0; ?>;
    let jasa  = <?= $v_jasa ?? 0; ?>;
    let mouse = <?= $v_mouse ?? 0; ?>;
    let nilaiOngkir = parseInt($('#ongkir').val()) || 0;
    
    let voucher = $('#kode_voucher').val().trim().toUpperCase();
    let diskonPersen = 0;
    let teksPersen = "(0%)";

    if (voucher === 'PROMO2025') {
        diskonPersen = 0.10;
        teksPersen = "(10%)";
    } else if (voucher === 'PROMO2026') {
        diskonPersen = 0.15;
        teksPersen = "(15%)";
    } else if (voucher === 'AKHIRTAHUN') {
        diskonPersen = 0.25;
        teksPersen = "(25%)";
    }

    let diskonVoucher = total * diskonPersen;
    let totalAkhir = (total + jasa) - diskonVoucher - mouse + nilaiOngkir;

    $('#diskon_view').text('-IDR ' + diskonVoucher.toLocaleString('id-ID') + ' ' + teksPersen);
    $('#total').text('IDR ' + totalAkhir.toLocaleString('id-ID'));
    $('#total_harga').val(totalAkhir);
}
</script>
<?= $this->endSection() ?>