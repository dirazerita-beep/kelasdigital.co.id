<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat - {{ $product->title }}</title>
    <style>
        @page { margin: 25px; }
        body {
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            color: #1f2937;
        }
        .frame-outer {
            border: 6px solid #1d4ed8;
            padding: 12px;
        }
        .frame-inner {
            border: 1px solid #1d4ed8;
            padding: 35px 60px;
            text-align: center;
        }
        .brand {
            font-size: 28px;
            font-weight: bold;
            color: #1d4ed8;
            letter-spacing: 2px;
        }
        .brand .dot {
            color: #1f2937;
        }
        .title {
            margin-top: 20px;
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #1f2937;
        }
        .subtitle {
            margin-top: 18px;
            font-size: 14px;
            color: #4b5563;
            letter-spacing: 1px;
        }
        .name {
            margin-top: 14px;
            font-size: 32px;
            font-weight: bold;
            color: #1d4ed8;
            font-style: italic;
        }
        .completion {
            margin-top: 16px;
            font-size: 13px;
            color: #4b5563;
        }
        .course {
            margin-top: 8px;
            font-size: 22px;
            font-weight: bold;
            color: #111827;
        }
        .footer {
            margin-top: 50px;
            width: 100%;
            font-size: 12px;
            color: #4b5563;
        }
        .footer-cell {
            display: inline-block;
            width: 30%;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-line {
            margin: 0 auto 4px;
            width: 70%;
            border-bottom: 1px solid #1f2937;
            height: 40px;
        }
        .signature-name {
            font-weight: bold;
            color: #1f2937;
        }
        .signature-role {
            color: #6b7280;
            font-size: 11px;
            margin-top: 2px;
        }
        .date-cell {
            margin-top: 50px;
            text-align: center;
            font-size: 13px;
            color: #4b5563;
        }
    </style>
</head>
<body>
    <div class="frame-outer">
        <div class="frame-inner">
            <div class="brand">KelasDigital<span class="dot">.</span></div>
            <div class="title">SERTIFIKAT PENYELESAIAN</div>
            <div class="subtitle">Diberikan kepada:</div>
            <div class="name">{{ $user->name }}</div>
            <div class="completion">Telah berhasil menyelesaikan kursus:</div>
            <div class="course">{{ $product->title }}</div>
            <div class="date-cell">Diselesaikan pada {{ $completedDate }}</div>
            <table style="width:100%; margin-top:60px; border-collapse:collapse;">
                <tr>
                    <td style="width:33%;"></td>
                    <td style="width:33%;"></td>
                    <td style="width:34%; text-align:center;">
                        <div class="signature-line"></div>
                        <div class="signature-name">Admin KelasDigital</div>
                        <div class="signature-role">Penanggung Jawab Program</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
