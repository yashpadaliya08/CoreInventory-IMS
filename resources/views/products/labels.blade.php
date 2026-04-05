@extends('layouts.app')

@push('styles')
<style>
    .page-header { margin-bottom: 32px; }
    .glass-card { 
        background: rgba(255, 255, 255, 0.6); 
        backdrop-filter: blur(16px); 
        border: 1px solid rgba(255,255,255,0.8); 
        border-radius: var(--radius-lg); 
        padding: 32px; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.02); 
        margin-bottom: 24px; 
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .section-title { 
        font-family: 'Outfit'; 
        font-weight: 700; 
        color: var(--text-main); 
        font-size: 1.25rem; 
        display: flex; 
        align-items: center; 
        gap: 8px; 
        margin-bottom: 24px; 
        padding-bottom: 12px; 
        border-bottom: 2px solid rgba(0,0,0,0.03); 
    }
    
    .label-box {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .sku-text {
        font-family: monospace;
        font-size: 1.5rem;
        font-weight: 700;
        margin-top: 16px;
        color: var(--text-main);
        letter-spacing: 2px;
    }

    .product-name-text {
        font-size: 0.9rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    .btn-gradient-primary {
        background: linear-gradient(135deg, var(--primary), var(--accent));
        color: white;
        border: none;
    }
    .btn-gradient-primary:hover {
        background: linear-gradient(135deg, var(--primary-hover), var(--primary));
        color: white;
        opacity: 0.95;
    }

    .btn-gradient-secondary {
        background: linear-gradient(135deg, var(--secondary), #f472b6);
        color: white;
        border: none;
    }
    .btn-gradient-secondary:hover {
        background: linear-gradient(135deg, #db2777, var(--secondary));
        color: white;
        opacity: 0.95;
    }

    .mini-label {
        border: 1px dashed rgba(0,0,0,0.2);
        padding: 16px;
        border-radius: 8px;
        background: white;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .mini-label-header {
        text-align: center;
        margin-bottom: 12px;
    }

    .mini-label-name {
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--text-main);
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .mini-label-sku {
        font-family: monospace;
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    .mini-label-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    .mini-barcode img { /* For inline SVGs scale if needed */
        max-width: 100%;
        height: auto;
    }

    .mini-qr svg {
        width: 80px;
        height: 80px;
    }
    
    /* PRINT STYLES */
    @media print {
        body {
            background: #fff !important;
            color: #000 !important;
        }
        .sidebar, .page-header, .btn, form, nav, footer {
            display: none !important;
        }
        .main-content {
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
        }
        .glass-card {
            background: none !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .section-title, .description-text {
            display: none !important;
        }
        .row {
            margin: 0 !important;
        }
        /* Only show the bulk sheet on print by default if we print the full page */
        #bulk-print-section {
            display: block !important;
        }
        .mini-label {
            border: 1px solid #000 !important;
            page-break-inside: avoid;
            margin-bottom: 10px;
        }
    }
</style>
@endpush

@section('content')
<div class="fade-in">
    <!-- Non-printable Header -->
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3 d-print-none">
        <div>
            <a href="{{ route('products.show', $product) }}" class="text-muted text-decoration-none fw-bold" style="font-size: 0.85rem;">
                <i data-feather="arrow-left" style="width: 14px;"></i> Back to Product
            </a>
            <div class="mt-2">
                <h2 class="m-0" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">Label Generator — {{ $product->name }}</h2>
                <div class="text-muted fw-bold mt-1" style="font-size: 0.95rem;">Product SKU: {{ $product->sku }}</div>
            </div>
        </div>
    </div>

    <!-- Individual Labels (Side-by-side) -->
    <div class="row g-4 mb-4 d-print-none">
        <!-- BARCODE CARD -->
        <div class="col-lg-6">
            <div class="glass-card">
                <h6 class="section-title"><i data-feather="align-justify" style="color: var(--primary);"></i> 1D Barcode Label</h6>
                
                <div class="label-box" id="barcode-container">
                    <div class="mb-3">
                        {!! $barcodeSvg !!}
                    </div>
                    <div class="sku-text">{{ $product->sku }}</div>
                    <div class="product-name-text">{{ $product->name }}</div>
                </div>

                <div class="mt-4 text-center">
                    <button onclick="printIndividual('barcode-container')" class="btn btn-gradient-primary d-inline-flex align-items-center gap-2 fw-bold px-4 py-2" style="border-radius: 10px;">
                        <i data-feather="printer" style="width: 18px;"></i> Print Barcode
                    </button>
                </div>
            </div>
        </div>

        <!-- QR CODE CARD -->
        <div class="col-lg-6">
            <div class="glass-card">
                <h6 class="section-title"><i data-feather="grid" style="color: var(--secondary);"></i> 2D QR Code Label</h6>
                
                <div class="label-box" id="qrcode-container">
                    <div style="width: 200px; height: 200px; margin: 0 auto;">
                        {!! $qrSvg !!}
                    </div>
                    <div class="text-muted mt-3 mb-1" style="font-size: 0.8rem;">Scan to identify product</div>
                    <div class="fw-bold" style="color: var(--text-main); font-size: 1.1rem;">{{ $product->name }}</div>
                    <div class="sku-text" style="font-size: 1rem; margin-top: 4px;">{{ $product->sku }}</div>
                </div>

                <div class="mt-4 text-center">
                    <button onclick="printIndividual('qrcode-container')" class="btn btn-gradient-secondary d-inline-flex align-items-center gap-2 fw-bold px-4 py-2" style="border-radius: 10px;">
                        <i data-feather="printer" style="width: 18px;"></i> Print QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- BULK SHEET (Full Width) -->
    <div id="bulk-print-section" class="row g-4">
        <div class="col-12">
            <div class="glass-card">
                <h6 class="section-title d-print-none"><i data-feather="layers" style="color: var(--accent);"></i> Bulk Label Sheet — Print All</h6>
                <p class="text-muted description-text d-print-none">The sheet below is optimized for A4 printing. Each label contains both the barcode and QR code side by side.</p>

                <div class="row g-3">
                    @for($i = 0; $i < 6; $i++)
                    <div class="col-md-6 col-12">
                        <div class="mini-label">
                            <div class="mini-label-header">
                                <div class="mini-label-name">{{ $product->name }}</div>
                                <div class="mini-label-sku">{{ $product->sku }}</div>
                            </div>
                            <div class="mini-label-body">
                                <div class="mini-barcode flex-grow-1 text-center">
                                    {!! $barcodeSvg !!}
                                </div>
                                <div class="mini-qr">
                                    {!! $qrSvg !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>

                <div class="mt-5 text-center d-print-none">
                    <button onclick="window.print()" class="btn btn-dark d-inline-flex align-items-center gap-2 fw-bold px-5 py-3" style="border-radius: 12px; font-size: 1.1rem; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                        <i data-feather="printer"></i> Print Full Sheet
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function printIndividual(containerId) {
        const content = document.getElementById(containerId).innerHTML;
        const printWindow = window.open('', '_blank', 'width=600,height=600');
        
        printWindow.document.write(`
            <html>
                <head>
                    <title>Print Label</title>
                    <style>
                        body { 
                            font-family: sans-serif; 
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            height: 100vh;
                            margin: 0;
                            background: white;
                        }
                        .label-container {
                            border: 1px dashed #ccc;
                            padding: 30px;
                            text-align: center;
                            border-radius: 8px;
                        }
                        .sku-text {
                            font-family: monospace;
                            font-size: 1.5rem;
                            font-weight: 700;
                            margin-top: 16px;
                            letter-spacing: 2px;
                        }
                        .product-name-text, .text-muted {
                            font-size: 0.9rem;
                            color: #666;
                            margin-top: 8px;
                        }
                        .fw-bold { font-weight: bold; }
                        svg { max-width: 100%; height: auto; }
                    </style>
                </head>
                <body>
                    <div class="label-container">
                        ${content}
                    </div>
                    <script>
                        window.onload = function() {
                            window.print();
                            setTimeout(function() { window.close(); }, 500);
                        }
                    <\/script>
                </body>
            </html>
        `);
        printWindow.document.close();
    }
</script>
@endpush
