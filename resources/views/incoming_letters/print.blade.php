<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Print Letter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 20px;
            font-size: 14px;
        }

        .print-header {
            text-align: center;
            margin-bottom: 30px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="print-header">
        <h2>Letter Details</h2>
        <p>{{ config('app.name') }}</p>
    </div>

    <table class="table table-bordered">
        <tr>
            <th>Reference</th>
            <td>{{ $incomingLetter->reference ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Subject</th>
            <td>{{ $incomingLetter->subject ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Sender</th>
            <td>{{ $incomingLetter->company ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Date</th>
            <td>{{ $incomingLetter->date?->format('Y-m-d') ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ ucfirst($incomingLetter->status) ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Attachment</th>
            <td>
                @if($incomingLetter->attachment && file_exists(storage_path('app/public/' . $incomingLetter->attachment)))
                    <a href="{{ asset('storage/' . $incomingLetter->attachment) }}" target="_blank">
                        {{ basename($incomingLetter->attachment) }}
                    </a>
                @else
                    No File
                @endif
            </td>
        </tr>
    </table>

    <div class="no-print mt-3">
        <button onclick="window.print()" class="btn btn-primary">Print</button>
        <a href="{{ route('incoming-letters.index') }}" class="btn btn-secondary">Back</a>
    </div>
</body>

</html>