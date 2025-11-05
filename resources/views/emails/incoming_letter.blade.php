<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $subjectLine }}</title>
</head>

<body>
    <h2>📩 Incoming Letter Details</h2>

    <p><strong>Reference:</strong> {{ $letter->reference }}</p>
    <p><strong>Subject:</strong> {{ $letter->subject }}</p>
    <p><strong>Company:</strong> {{ $letter->company }}</p>
    <p><strong>Date:</strong> {{ $letter->date }}</p>
    <p><strong>Status:</strong> {{ ucfirst($letter->status) }}</p>

    <hr>
    <h3>Message</h3>
    <p>{{ $messageBody }}</p>

    @if ($letter->attachment)
        <p><em>Attachment included with this email.</em></p>
    @endif
</body>

</html>