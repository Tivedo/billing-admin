<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Exports\BillingExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;

class BillingController extends Controller
{
    public function index()
    {
        if (!session()->has('user')) {
            return redirect()->route('login');
        }
        $customer = DB::table('customer')->get();
        $stats = Invoice::join('order', 'invoice.order_id', '=', 'order.id')
        ->join('customer', 'order.customer_id', '=', 'customer.id')
        ->join('kontrak', 'order.kontrak_id', '=', 'kontrak.id')
        ->join('invoice_item', 'invoice.id', '=', 'invoice_item.invoice_id')
        ->select([
            DB::raw('SUM(invoice_item.nilai_bayar) as total_harga'),
            DB::raw('SUM(CASE WHEN invoice.status != "paid" THEN 1 ELSE 0 END) as invoicePendingCount'),
            DB::raw('SUM(CASE WHEN invoice.status != "paid" THEN invoice_item.nilai_bayar ELSE 0 END) as totalTertunggak'),
            DB::raw('SUM(CASE WHEN invoice.tgl_jatuh_tempo < CURRENT_DATE() AND invoice.status != "paid" THEN 1 ELSE 0 END) as invoiceJatuhTempoCount'),
            DB::raw('SUM(CASE WHEN invoice.tgl_jatuh_tempo < CURRENT_DATE() AND invoice.status != "paid" THEN invoice_item.nilai_bayar ELSE 0 END) as invoiceJatuhTempoValue'),
            DB::raw('SUM(CASE WHEN invoice.status = "paid" AND MONTH(invoice.tgl_invoice) = MONTH(CURRENT_DATE()) AND YEAR(invoice.tgl_invoice) = YEAR(CURRENT_DATE()) THEN 1 ELSE 0 END) as invoiceLunasBulanIniCount'),
            DB::raw('SUM(CASE WHEN invoice.status = "paid" AND MONTH(invoice.tgl_invoice) = MONTH(CURRENT_DATE()) AND YEAR(invoice.tgl_invoice) = YEAR(CURRENT_DATE()) THEN invoice_item.nilai_bayar ELSE 0 END) as pendapatanBulanIni'),
            DB::raw('COUNT(DISTINCT IF(kontrak.status = "active", customer.id, NULL)) as pelangganAktifCount'),
            DB::raw('SUM(CASE WHEN invoice.url_bukti_potong_pph IS NULL THEN 1 ELSE 0 END) as invoiceTanpaBuktiPotongCount'),
            DB::raw('SUM(CASE WHEN invoice.url_faktur IS NULL THEN 1 ELSE 0 END) as invoiceTanpaFakturCount'),
        ])
        ->first();

        return view('dashboard', [
            'title' => 'Billing',
            'customers' => $customer,
            'stats' => $stats,
        ]);
    }
    public function ajaxBilling(Request $request)
    {
        $data = Invoice::join('order', 'invoice.order_id', '=', 'order.id')
            ->join('customer', 'order.customer_id', '=', 'customer.id')
            ->join('invoice_item', 'invoice.id', '=', 'invoice_item.invoice_id')
            ->select(
                'invoice.id as invoice_id',
                'customer.nama as nama_customer',
                'invoice.nomor',
                'invoice.tgl_invoice',
                'invoice.tgl_jatuh_tempo',
                DB::raw('SUM(invoice_item.nilai_bayar) as total_harga'),
                'invoice.status',
                'invoice.url_bukti_potong_pph',
                'invoice.url_invoice',
                'invoice.url_tanda_terima',
                'invoice.url_faktur',
                DB::raw('
                    CASE customer.status_perusahaan
                        WHEN 1 THEN "Wapu"
                        WHEN 2 THEN "Non Wapu tanpa PPh"
                        WHEN 3 THEN "Non Wapu dengan PPh"
                        ELSE "-"
                    END as status_perusahaan
                ')
            )
            ->when($request->namaCustomer, function ($query) use ($request) {
                $query->where('customer.nama', 'like', '%' . $request->namaCustomer . '%');
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('invoice.status', $request->status);
            })
            ->orderBy('invoice.tgl_invoice', 'desc')
            ->groupBy(
                'invoice.id',
                'invoice.nomor',
                'invoice.tgl_invoice',
                'invoice.tgl_jatuh_tempo',
                'invoice.status',
                'invoice.url_bukti_potong_pph',
                'invoice.url_invoice',
                'invoice.url_tanda_terima',
                'customer.status_perusahaan',
                'customer.nama',
                'customer.status_perusahaan',
                'invoice.url_faktur'
            )
            ->get();
        return DataTables::of($data)
        ->addColumn('aksi', function($d) {
            $html = '<select type="button" class="action-select form-control form-control-sm btn btn-secondary btn-sm" style="width: 120px;">
                        <option value="">Aksi</option>';
    
            if (!empty($d->url_invoice)) {
                $html .= '<option value="unduh-invoice" data-invoice-url="'.$d->url_invoice.'">Lihat Invoice</option>';
            }
            if (!empty($d->url_faktur)) {
                $html .= '<option value="lihat-faktur" data-faktur-url="'.asset($d->url_faktur).'">Lihat Faktur</option>';
            }else {
                $html .= '<option value="upload-faktur" data-id="'.$d->invoice_id.'">Upload Faktur</option>';
            }
            if (!empty($d->url_bukti_potong_pph)) {
                $html .= '<option value="unduh-bukti-pph" data-bupot-url="'.asset($d->url_bukti_potong_pph).'">Lihat Bukti Potong PPH 23</option>';
            } elseif ($d->status_perusahaan == 'Non Wapu dengan PPh') {
                $html .= '<option value="upload-pph" data-id="'.$d->invoice_id.'">Upload Bukti Potong PPh 23</option>';
            }
    
            $html .= '</select>';
    
            return $html;
        })
        ->rawColumns(['aksi'])
        ->make(true);
    }
    public function submitFaktur(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:invoice,id',
            'faktur' => 'required|file|mimes:pdf|max:2048',
        ]);
        $invoice = Invoice::findOrFail($request->id);
        if($request->file('faktur')){
            $filePath = $request->file('faktur')->store('uploads', 'public');
        }

        // Simpan path relatif ke DB
        $invoice = Invoice::find($request->id);
        $invoice->url_faktur = $filePath;
        $invoice->save();

        return redirect()->back()->with('success', 'Faktur berhasil diunggah.');
    }
    public function submitBuktiPotongPph(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:invoice,id',
            'pph' => 'required|file|mimes:pdf|max:2048',
        ]);

        $invoice = Invoice::findOrFail($request->id);
        if($request->file('pph')){
            $filePath = $request->file('pph')->store('uploads', 'public');
        }

        $invoice = Invoice::find($request->id);
        $invoice->url_bukti_potong_pph = $filePath;
        $invoice->save();

        return redirect()->back()->with('success', 'Bukti potong PPh 23 berhasil diunggah.');
    }
    public function exportBilling()
    {
        $data = Invoice::join('order', 'invoice.order_id', '=', 'order.id')
            ->join('customer', 'order.customer_id', '=', 'customer.id')
            ->join('invoice_item', 'invoice.id', '=', 'invoice_item.invoice_id')
            ->select(
                'invoice.id as invoice_id',
                'customer.nama as nama_customer',
                'invoice.nomor',
                'invoice.tgl_invoice',
                'invoice.tgl_jatuh_tempo',
                DB::raw('SUM(invoice_item.nilai_bayar) as total_harga'),
                'invoice.status',
                'invoice.url_bukti_potong_pph',
                'invoice.url_invoice',
                DB::raw('
                    CASE customer.status_perusahaan
                        WHEN 1 THEN "Wapu"
                        WHEN 2 THEN "Non Wapu tanpa PPh"
                        WHEN 3 THEN "Non Wapu dengan PPh"
                        ELSE "-"
                    END as status_perusahaan
                ')
            )
            ->orderBy('invoice.tgl_invoice', 'desc')
            ->groupBy(
                'invoice.id',
                'invoice.nomor',
                'invoice.tgl_invoice',
                'invoice.tgl_jatuh_tempo',
                'invoice.status',
                'invoice.url_bukti_potong_pph',
                'invoice.url_invoice',
                'customer.status_perusahaan',
                'customer.nama'
            )
            ->get();

        $data = $data->map(function ($item) {
            return [
                'Nomor Invoice' => $item->nomor,
                'Nama Customer' => $item->nama_customer,
                'Tanggal Invoice' => $item->tgl_invoice,
                'Jatuh Tempo' => $item->tgl_jatuh_tempo,
                'Total' => $item->total_harga,
                'Status' => $item->status,
                'Status Perusahaan' => $item->status_perusahaan
            ];
        });
        $export = new BillingExport($data->toArray());
        return Excel::download($export, 'billing.xlsx');


        return response()->json(['message' => 'Export functionality not implemented yet.']);
    }
}
