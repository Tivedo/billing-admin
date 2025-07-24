@extends('layouts.index',['title' => 'Billing (Non Wapu)'])
@section('content')
@include('layouts.navbar')
@include('layouts.sidebar')
<style>
    .btn-group .btn {
        transition: background-color 0.3s, color 0.3s;
    }

    .btn-group .btn:hover {
        background-color: #4791e1;
        color: #fff;
    }

    .btn-group .btn.selected {
        background-color: #004080;
        color: #fff;
        border: 1px solid #003366;
    }
    .table-responsive {
        overflow-x: auto;
        white-space: normal;
    }

    .table {
        min-width: 1000px;
    }

    .table th, .table td {
        font-size: 1rem;
    }
</style>
<section class="section p-3">
    <div class="card" id="card">
        <div class="card-body">
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Tertunggak</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        Rp {{ number_format($stats['totalTertunggak'] ?? 0, 0, ',', '.') }}
                                    </div>
                                    <small class="text-muted">Dari {{ $stats['invoicePendingCount'] ?? 0 }} invoice</small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Invoice Jatuh Tempo</div>
                                    <div class="h5 mb-0 font-weight-bold text-danger">
                                        {{ $stats['invoiceJatuhTempoCount'] ?? 0 }} Invoice
                                    </div>
                                    <small class="text-muted">
                                        Total Rp {{ number_format($stats['invoiceJatuhTempoValue'] ?? 0, 0, ',', '.') }}
                                    </small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pendapatan (Bulan Ini)</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        Rp {{ number_format($stats['pendapatanBulanIni'] ?? 0, 0, ',', '.') }}
                                    </div>
                                    <small class="text-muted">Dari {{ $stats['invoiceLunasBulanIniCount'] ?? 0 }} invoice lunas</small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pelanggan Aktif</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $stats['pelangganAktifCount'] ?? 0 }}
                                    </div>
                                    <small class="text-muted">Memiliki kontrak aktif</small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Baris 2: Grafik dan Ringkasan Lainnya --}}
            <div class="row">
                <div class="col-xl-4 col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Status Dokumen & Administrasi</h6>
                        </div>
                        <div class="card-body">
                            <h4 class="small font-weight-bold">Menunggu Pembayaran <span class="float-right">{{ $stats['invoicePendingCount'] ?? 0 }}</span></h4>
                            <hr>
                            <h4 class="small font-weight-bold">Menunggu Unggahan Bukti Potong <span class="float-right">{{ $stats['invoiceTanpaBuktiPotongCount'] ?? 0 }}</span></h4>
                            <hr>
                            <h4 class="small font-weight-bold">Menunggu Unggahan Faktur Pajak <span class="float-right">{{ $stats['invoiceTanpaFakturCount'] ?? 0 }}</span></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card" id="card">
        <div class="card-header">
            <h5 class="card-title">
                Billing
            </h5>
        </div>
        <div class="d-flex justify-content-end pe-4">
            <div class="mb-4">
                <button  id="btnFilter" class="btn btn-primary" style="margin-right: 0.5rem;">
                    <i class="bi bi-funnel"></i> Filter
                </button>   
                <a href="export-billing" class="btn btn-success">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Export Excel
                </a>     
            </div>  
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table w-100" id="invoice-table">
                    <thead>
                        <tr>
                            <th>No Invoice</th>
                            <th>Customer</th>
                            <th>Tanggal Terbit Invoice</th>
                            <th>Tanggal Jatuh Tempo</th>
                            <th>Nominal Tagihan</th>
                            <th>Status Pembayaran</th>
                            <th>Status Perusahaan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
                <button class="btn btn-primary mt-3" id="action-button" style="display: none">Approve</button>
            </div>
        </div>
        <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" style="width: 45vw;max-width: unset;">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detail Perusahaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 50%"><strong>Identitas Customer</strong></th>
                                <th><strong>No NPWP</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td id="nama_customer"> <br> <button class="btn btn-secondary p-0 px-1">detail</button></td>
                                <td id="npwp"></td>
                            </tr>
                            <tr>
                                <td><strong>Nama Layanan</strong></td>
                                <td><strong>Alamat Perusahaan/NPWP</strong></td>
                            </tr>
                            <tr>
                                <td id="nama_layanan" class="pe-5"></td>
                                <td id="alamat"><br> <button class="btn btn-secondary p-0 px-1">detail</button></td>
                            </tr>
                            <tr>
                                <td><strong></strong></td>
                            </tr>
                            <tr>
                                <td></td>
                            </tr>
                            <tr>
                                <td><strong>Periode</strong></td>
                                <td><strong>Status Perusahaan</strong></td>
                            </tr>
                            <tr>
                                <td id="periode_invoice"></td>
                                <td id="status_perushaan"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="unduhInvoiceModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" style="width: 45vw;max-width: unset;">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Invoice</h5>
                </div>
                <div class="modal-body" id="invoiceBody">
                    file
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="uploadFakturModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" style="width: 45vw;max-width: unset;">
                <div class="modal-content">
                <form action="{{ route('submitFaktur') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Faktur Pajak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <input type="hidden" name="id" id="idInvoice">
                    <input type="file" name="faktur" class="form-control" onchange="previewImage(event)">

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary border-0" style="background-color: #004080" data-bs-dismiss="modal">Upload</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
                </form>
                </div>
            </div>
        </div>
        {{-- <div class="modal fade" id="uploadBuktiBayar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" style="width: 45vw;max-width: unset;">
                <div class="modal-content">
                <form action="{{ route('submitBuktiBayar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Bukti Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <input type="hidden" name="id" id="idBuktiBayar">
                    <input type="file" name="buktiBayar" class="form-control" onchange="previewImage(event)">

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary border-0" style="background-color: #004080" data-bs-dismiss="modal">Upload</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
                </form>
                </div>
            </div>
        </div> --}}
        <div class="modal fade" id="uploadPphModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" style="width: 45vw;max-width: unset;">
                <div class="modal-content">
                <form action="{{ route('submitBuktiPotongPph') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">PPh 23</h5>
                </div>
                <div class="modal-body" id="modalBody">
                    <input type="file" name="pph" id="pph" accept=".pdf" class="form-control" onchange="previewImage(event)">
                    <input type="hidden" name="id" id="idPph">

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary border-0" style="background-color: #004080" data-bs-dismiss="modal">Upload</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
                </form>
                </div>
            </div>
        </div>
        {{-- <div class="modal fade" id="lihatFakturModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" style="width: 45vw;max-width: unset;">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Faktur Pajak</h5>
                </div>
                <div class="modal-body" id="modalBody">
                    file
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div> --}}
        {{-- <div class="modal fade" id="unduhPphModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" style="width: 45vw;max-width: unset;">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Bukti Potong PPH 23</h5>
                </div>
                <div class="modal-body" id="pphBody">
                    file
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div> --}}
        {{-- <div class="modal fade" id="unduhPpnModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" style="width: 45vw;max-width: unset;">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Bukti Potong PPN 11%</h5>
                </div>
                <div class="modal-body" id="ppnBody">
                    file
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div> --}}
        <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filterModalLabel">Filter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="filterForm">
                            <div class="mb-3">
                                <label for="namaCustomer" class="form-label">Customer</label>
                                <select class="js-example-basic-single form-select" id="namaCustomer" name="namaCustomer">
                                    <option value="">Semua Customer</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->nama }}">{{ $customer->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status Pembayaran</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="statusSemua" value="" checked>
                                    <label class="form-check-label" for="statusSemua">Semua</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="statusLunas" value="paid">
                                    <label class="form-check-label" for="statusLunas">Lunas</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="statusBelumLunas" value="unpaid">
                                    <label class="form-check-label" for="statusBelumLunas">Belum Lunas</label>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-4 gap-2">
                                <button type="button" class="btn btn-secondary" id="resetFilter">Reset Filter</button>
                                <button type="button" class="btn btn-primary" id="applyFilter">Terapkan Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#invoice-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('ajaxBilling') }}',
                        data: function(d) {
                            d.namaCustomer = $('#namaCustomer').val();
                            d.status = $('input[name="status"]:checked').val();
                        }
                    },
                    columns: [
                        { data: 'nomor', name: 'invoice.nomor' },
                        { data: 'nama_customer', name: 'customer.nama' },
                        { data: 'tgl_invoice', name: 'invoice.tgl_invoice' },
                        { data: 'tgl_jatuh_tempo', name: 'invoice.tgl_jatuh_tempo' },
                        {
                            data: 'total_harga',
                            name: 'invoice.total_harga',
                            render: $.fn.dataTable.render.number(',', '.', 0, 'Rp ')
                        },
                        { data: 'status', name: 'invoice.status' },
                        { data: 'status_perusahaan', name: 'invoice.status_perusahaan' },
                        { data: 'aksi', name: 'aksi', orderable: false, searchable: false },
                    ]
                });
                $('.js-example-basic-single').select2({
                    placeholder: 'Pilih Customer',
                    width: '100%',
                    theme: 'bootstrap',
                    dropdownParent: $('#filterModal')
                });
                $('.js-example-basic-single').on('select2:open', function(e) {
                    $('.select2-dropdown').css('z-index', 999999999);
                })
                $('#resetFilter').on('click', function() {
                    // Reset radio buttons
                    $('input[name="status"]').prop('checked', false);
                    $('#statusSemua').prop('checked', true); // Set default to "Semua

                    // Reset select dropdown
                    $('#namaCustomer').val(null).trigger('change'); // Reset Select2
                });
                $('#btnFilter').click(function() {
                    $('#filterModal').modal('show');
                });

            $('#applyFilter').on('click', function() {
                $('#invoice-table').DataTable().draw();
                $('#filterModal').modal('hide');
            });
            $(document).on('change', '.action-select', function() {
                var selectedValue = $(this).val();
                if (selectedValue === 'unduh-invoice') {
                    var selectedOption = $(this).find('option:selected');
                    var invoiceUrl = selectedOption.data('invoice-url');
                    //arahin ke tab sebelah buat buka invoice
                    window.location.href = invoiceUrl;
                    $(this).val('');
                }
                if (selectedValue === 'unduh-tanda-terima') {
                    var selectedOption = $(this).find('option:selected');
                    var invoiceUrl = selectedOption.data('invoice-url');
                    window.location.href = "/download-tanda-terima/" + selectedOption.data('nama-file');
                    $(this).val('');
                }
                if (selectedValue === 'invoice-not-found') {
                    var selectedOption = $(this).find('option:selected');
                    $('#invoiceBody').html('invoice tidak ditemukan');
                    $('#unduhInvoiceModal').modal('show');
                    $(this).val('');
                }
                if (selectedValue === 'upload-faktur') {
                    var selectedOption = $(this).find('option:selected');
                    var id = selectedOption.data('id');
                    $('#idInvoice').val(id);
                    $('#uploadFakturModal').modal('show');
                    $(this).val('');
                }
                if (selectedValue === 'upload-pph') {
                    var selectedOption = $(this).find('option:selected');
                    var id = selectedOption.data('id');
                    $('#idPph').val(id);
                    $('#uploadPphModal').modal('show');
                    $(this).val('');
                }
                if (selectedValue === 'lihat-faktur') {
                    var selectedOption = $(this).find('option:selected');
                    var fakturUrl = selectedOption.data('faktur-url');
                    //arahin ke tab sebelah buat buka invoice
                    window.location.href = fakturUrl;
                    $(this).val('');
                }
                if (selectedValue === 'unduh-bukti-pph') {
                    var selectedOption = $(this).find('option:selected');
                    var bupotUrl = selectedOption.data('bupot-url');
                    //arahin ke tab sebelah buat buka invoice
                    window.location.href = bupotUrl;
                    $(this).val('');
                    
                }
                if (selectedValue === 'pph-not-found') {
                    var selectedOption = $(this).find('option:selected');
                    var pphUrl = selectedOption.data('pph-url');
                    $('#pphBody').html('bukti potong PPh tidak ditemukan');
                    $('#unduhPphModal').modal('show');
                    $(this).val('');
                }
                if (selectedValue === 'ppn-not-found') {
                    var selectedOption = $(this).find('option:selected');
                    var ppnUrl = selectedOption.data('ppn-url');
                    $('#pphBody').html('bukti potong PPN tidak ditemukan');
                    $('#unduhPpnModal').modal('show');
                    $(this).val('');
                }
            });

            });
            </script>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    @if($errors->any())
                        @foreach ($errors->all() as $error)
                            toastr.error('{{ $error }}');
                        @endforeach
                    @endif
                    @if(session('success'))
                        toastr.success('{{ session('success') }}');
                    @endif
                })
            </script>
    </div>
    </section>
    @endsection
