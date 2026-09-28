<!DOCTYPE html>
<html>
<head>
    <style>
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: sans-serif; background-size: cover; @if($templateDataUri) background-image: url('{{ $templateDataUri }}'); @endif }
        .absolute-element { position: absolute; }
    </style>
</head>
<body>
    <div class="absolute-element" style="left: {{ $certificate->pos_number_x }}px; top: {{ $certificate->pos_number_y }}px;">No: {{ $certificate->certificate_number }}</div>
    <div class="absolute-element" style="left: {{ $certificate->pos_name_x }}px; top: {{ $certificate->pos_name_y }}px; font-size: 24px; font-weight: bold;">{{ $certificate->recipient_name }}</div>
    <div class="absolute-element" style="left: {{ $certificate->pos_qr_x }}px; top: {{ $certificate->pos_qr_y }}px;"><img src="data:image/svg+xml;base64,{{ $qrCode }}" width="80"></div>
</body>
</html>
