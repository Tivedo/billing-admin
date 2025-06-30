<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Exports\BillingExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class BillingController extends Controller
{
    public function index()
    {
        if (!session()->has('user')) {
            return redirect()->route('login');
        }
        $customer = DB::table('customer')->get();
        return view('dashboard', [
            'title' => 'Billing',
            'customers' => $customer,
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
                '),
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
        $file = $request->file('faktur');

        // Nama file unik
        $filename = time() . '_' . $file->getClientOriginalName();

        // Simpan ke folder public/faktur/
        $file->move(public_path('faktur'), $filename);

        // Simpan path relatif ke DB
        $invoice->url_faktur = 'faktur/' . $filename;
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
        $file = $request->file('pph');
        // Nama file unik
        $filename = time() . '_' . $file->getClientOriginalName();
        // Simpan ke folder public/bukti_potong/
        $file->move(public_path('bukti_potong'), $filename);
        // Simpan path relatif ke DB
        $invoice->url_bukti_potong_pph = 'bukti_potong/' . $filename;
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
