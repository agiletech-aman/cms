<!doctype html>
<html>
<body style="font-family: Arial, sans-serif; font-size: 14px; color: #222;">
    <p>Hello,</p>
    <p>The following complaint has been marked as <strong>Resolved</strong>.</p>

    <table cellpadding="6" cellspacing="0" border="1" style="border-collapse: collapse; border-color: #ccc;">
        <tr><td><strong>Complaint ID</strong></td><td>{{ $complaint->complaint_id }}</td></tr>
        <tr><td><strong>Site</strong></td><td>{{ optional($complaint->user)->username ?? optional($complaint->user)->name ?? 'N/A' }}</td></tr>
        <tr><td><strong>Subject</strong></td><td>{{ $complaint->subject }}</td></tr>
        <tr><td><strong>Description</strong></td><td>{{ $complaint->description }}</td></tr>
        <tr><td><strong>Root Cause</strong></td><td>{{ $complaint->root_cause ?? 'N/A' }}</td></tr>
        <tr><td><strong>Engineer</strong></td><td>{{ optional($complaint->engineer)->name ?? 'N/A' }}</td></tr>
        <tr><td><strong>Resolved Date</strong></td><td>{{ $complaint->resolved_date ? \Carbon\Carbon::parse($complaint->resolved_date)->format('d-m-Y H:i:s') : 'N/A' }}</td></tr>
        <tr><td><strong>Resolved By</strong></td><td>{{ $resolvedBy ?? 'N/A' }}</td></tr>
    </table>

    <p>
        If no action is taken, this complaint will be closed automatically after
        {{ config('complaint.auto_close_hours') }} hours.
    </p>
    <p><a href="{{ route('detail.complaint', $complaint->id) }}">View Complaint</a></p>
</body>
</html>
