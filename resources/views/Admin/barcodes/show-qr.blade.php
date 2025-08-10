<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code: {{ $barcode->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f5f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .qr-card { max-width: 500px; }
    </style>
</head>
<body>
    <div class="card shadow-lg qr-card">
        <div class="card-header text-center">
            <h4 class="card-title mb-0">{{ $barcode->name }}</h4>
        </div>
        <div class="card-body d-flex flex-column align-items-center justify-content-center p-4">
            <div class="p-3 border rounded">
                {!! QrCode::size(350)->generate($barcode->value) !!}
            </div>
            <code class="mt-3 text-muted">{{ $barcode->value }}</code>
        </div>
        <div class="card-footer text-center">
            <a href="{{ route('admin.barcodes.download-qr', $barcode->id) }}" class="btn btn-primary">
                Unduh QR Code (PNG)
            </a>
        </div>
    </div>
</body>
</html>