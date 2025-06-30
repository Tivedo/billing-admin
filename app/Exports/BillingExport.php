<?php

namespace App\Exports;

use App\Invoice;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class BillingExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $invoices;

    public function __construct(array $invoices)
    {
        $this->invoices = $invoices;
    }

    public function array(): array
    {
        return $this->invoices;
    }
    public function headings(): array
    {
        // Tentukan judul untuk setiap kolom dalam file Excel
        return [
            'Nomor Invoice',
            'Nama Customer',
            'Tanggal Invoice',
            'Jatuh Tempo',
            'Total',
            'Status',
            'Status Perusahaan',
            // Tambahkan kolom lain sesuai kebutuhan
        ];
    }
    public function getColumnAutoSize(): array
    {
        return [
            'A' => true, // Nomor Invoice
            'B' => true, // Nama Customer
            'C' => true, // Tanggal Invoice
            'D' => true, // Jatuh Tempo
            'E' => true, // Total
            'F' => true, // Status
            'G' => true, // Status Perusahaan
            // Tambahkan ukuran kolom lain sesuai kebutuhan
        ];
    }
}
