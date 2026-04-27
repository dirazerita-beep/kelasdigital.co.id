<?php

if (! function_exists('formatRupiah')) {
    /**
     * Format an amount as Indonesian Rupiah (e.g. 299000 -> "Rp 299.000").
     */
    function formatRupiah($amount): string
    {
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }
}
