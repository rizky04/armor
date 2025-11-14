{{-- <!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Stiker Ganti Oli</title>
    <style>
        @page {
            size: 8cm 5cm; /* ukuran stiker */
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 10px;
            width: 8cm;
            height: 5cm;
            box-sizing: border-box;
        }
        .stiker {
            border: 2px dashed #333;
            border-radius: 6px;
            padding: 6px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .header {
            text-align: center;
            font-weight: bold;
            font-size: 13px;
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
            padding-bottom: 3px;
        }
        .info {
            line-height: 1.3;
        }
        .footer {
            text-align: center;
            font-size: 11px;
            border-top: 1px dashed #000;
            margin-top: 4px;
            padding-top: 3px;
        }
        .bold { font-weight: bold; }
    </style>
</head>
<body onload="window.print();">
    <div class="stiker">
        <div class="header">
            BENGKEL ARMOR MOTOR<br>
            <small>GANTI OLI & SERVICE</small>
        </div>

        <div class="info">
            <div><span class="bold">No. Polisi:</span> {{ $oilService->service->vehicle->license_plate ?? '-' }}</div>
            <div><span class="bold">Tanggal:</span> {{ \Carbon\Carbon::parse($oilService->service_date)->format('d/m/Y') }}</div>
            <div><span class="bold">KM Sekarang:</span> {{ number_format($oilService->km_service) }}</div>
            <div><span class="bold">KM Berikutnya:</span> {{ number_format($oilService->km_service_next) }}</div>
            <div><span class="bold">Oli:</span> {{ $oilService->oil_name }}</div>
        </div>

        <div class="footer">
            Datang Kembali: {{ $oilService->next_service_date ? \Carbon\Carbon::parse($oilService->next_service_date)->format('d/m/Y') : '-' }}
        </div>
    </div>
</body>
</html> --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Cetak Stiker Ganti Oli</title>
  <style>
    @page {
      size: 80mm auto;
      margin: 0;
    }

    body {
      font-family: "Arial", sans-serif;
      width: 80mm;
      margin: 0 auto;
      font-size: 12px;
      padding: 4px;
      color: #000;
    }

    .stiker {
      border: 1px solid #000;
      padding: 6px;
      width: 100%;
    }

    .header {
      text-align: center;
      font-weight: bold;
      font-size: 14px;
      border-bottom: 1px solid #000;
      margin-bottom: 4px;
      padding-bottom: 2px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 2px;
    }

    td {
      font-size: 11px;
      padding: 2px 0;
    }

    .label {
      width: 50%;
      vertical-align: top;
    }

    .value {
      text-align: right;
      font-weight: bold;
    }

    .footer {
      border-top: 1px solid #000;
      margin-top: 4px;
      padding-top: 4px;
      text-align: center;
      font-size: 10px;
    }

    @media print {
      button {
        display: none;
      }
      body {
        margin: 0;
      }
    }
  </style>
</head>
<body>
  <div class="stiker">
    <div class="header">
      BENGKEL ARMOR MOTOR
    </div>

    <table>
      <tr>
        <td class="label">Tanggal Service</td>
        <td class="value">{{ $oilService->service_date ?? '-' }}</td>
      </tr>
      <tr>
        <td class="label">Tanggal Service Berikutnya</td>
        <td class="value">{{ $oilService->next_service_date ?? '-' }}</td>
      </tr>
      <tr>
        <td class="label">KM Service</td>
        <td class="value">{{ $oilService->km_service ?? '-' }}</td>
      </tr>
      <tr>
        <td class="label">KM Service Berikutnya</td>
        <td class="value">{{ $oilService->km_service_next ?? '-' }}</td>
      </tr>
      <tr>
        <td class="label">Jenis Oli yang Digunakan</td>
        <td class="value">{{ $oilService->oil_name ?? '-' }}</td>
      </tr>
    </table>

    <div class="footer">
      Jl. Raya Nyorondung No. 96, Pamorah, Bangkalan<br>
      Telp: 0878 - 4513 - 3640
    </div>
  </div>

  <button onclick="window.print()">🖨️ Cetak Stiker</button>
</body>
</html>

