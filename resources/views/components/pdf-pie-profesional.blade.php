@props(['config'])

<div style="
    margin-top: 18px;
    padding-top: 8px;
    border-top: 1px solid #e5e7eb;
    text-align: right;
    font-family: DejaVu Sans, Arial, sans-serif;
">

    <span style="font-size:7px; color:#9ca3af;">
        Documento generado electrónicamente · 
        {{ now()->setTimezone('America/Bogota')->format('d/m/Y H:i') }}
    </span>

</div>