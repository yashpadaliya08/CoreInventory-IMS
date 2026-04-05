<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Picqer\Barcode\BarcodeGeneratorPNG;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class BarcodeController extends Controller
{
    /**
     * Show the label print page for a product.
     * Contains both barcode (SVG) and QR code (SVG) inline.
     */
    public function labels(Product $product)
    {
        // Generate barcode SVG inline
        $barcodeGenerator = new BarcodeGeneratorSVG();
        $barcodeSvg = $barcodeGenerator->getBarcode(
            $product->sku,
            BarcodeGeneratorSVG::TYPE_CODE_128,
            2,   // widthFactor
            80   // height in px
        );

        // Generate QR code SVG inline
        $qrSvg = $this->generateQrSvg($product);

        return view('products.labels', compact('product', 'barcodeSvg', 'qrSvg'));
    }

    /**
     * Serve a raw PNG barcode image for a product.
     * Usage: <img src="{{ route('products.barcode', $product) }}">
     */
    public function barcodePng(Product $product)
    {
        $generator = new BarcodeGeneratorPNG();
        $png = $generator->getBarcode(
            $product->sku,
            BarcodeGeneratorPNG::TYPE_CODE_128,
            2,
            80
        );

        return response($png, 200)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Serve a raw SVG QR code for a product.
     * Usage: <img src="{{ route('products.qrcode', $product) }}">
     */
    public function qrcodeSvg(Product $product)
    {
        $svg = $this->generateQrSvg($product);

        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    // ── Private Helpers ──────────────────────────────────────

    /**
     * Build QR code SVG string. The QR encodes a JSON payload with
     * product name, SKU, and unit of measure for scanner compatibility.
     */
    private function generateQrSvg(Product $product): string
    {
        $payload = json_encode([
            'sku'  => $product->sku,
            'name' => $product->name,
            'uom'  => $product->unit_of_measure,
        ]);

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        return $writer->writeString($payload);
    }
}
