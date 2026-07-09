<?php
//biaya jasa
function hitung_biaya_jasa($total_harga) {
    if ($total_harga <= 10000000) {
        return $total_harga * 0.01; // 1%
    } else {
        return $total_harga * 0.02; // 2%
    }
}

//diskon voucher
function hitung_diskon_voucher($total_harga, $voucher_code) {
    $voucher_code = strtoupper($voucher_code);
    $diskon_persen = 0;

    if ($voucher_code === 'PROMO2025') {
        $diskon_persen = 0.10; // 10%
    } elseif ($voucher_code === 'PROMO2026') {
        $diskon_persen = 0.15; // 15%
    } elseif ($voucher_code === 'AKHIRTAHUN') {
        $diskon_persen = 0.25; // 25%
    } else {
        $diskon_persen = 0;    // kode tidak valid
    }

    return $total_harga * $diskon_persen;
}

//free mouse
function hitung_free_mouse($total_harga) {
    if ($total_harga >= 15000000) {
        return 150000; // free mouse 150 ribu
    }
    return 0;
}