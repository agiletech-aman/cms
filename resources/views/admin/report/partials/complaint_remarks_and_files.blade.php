@php
    $historyFileColumn = $historyFileColumn ?? 'complaint_image';
    $statusHistory = [
        'in_progress' => ['remarks' => collect(), 'files' => collect()],
        'resolved' => ['remarks' => collect(), 'files' => collect()],
        'closed' => ['remarks' => collect(), 'files' => collect()],
    ];

    foreach ($item->statusUpdates as $statusUpdate) {
        $commentText = \Illuminate\Support\Str::lower($statusUpdate->comment ?? '');
        $statusKey = null;

        if (\Illuminate\Support\Str::contains($commentText, ['inprogress', 'in progress'])) {
            $statusKey = 'in_progress';
        } elseif (\Illuminate\Support\Str::contains($commentText, 'resolved')) {
            $statusKey = 'resolved';
        } elseif (\Illuminate\Support\Str::contains($commentText, ['closed', 'close'])) {
            $statusKey = 'closed';
        }

        if (!$statusKey) {
            continue;
        }

        if (filled($statusUpdate->remark)) {
            $statusHistory[$statusKey]['remarks']->push($statusUpdate->remark);
        }

        $statusHistory[$statusKey]['files'] = $statusHistory[$statusKey]['files']
            ->merge(
                $item->uploadedFiles
                    ->where('comment_id', $statusUpdate->id)
                    ->pluck($historyFileColumn)
                    ->filter(fn ($path) => filled($path))
            )
            ->unique()
            ->values();
    }
@endphp

@foreach(['in_progress', 'resolved', 'closed'] as $statusKey)
    <td>
        @forelse($statusHistory[$statusKey]['remarks'] as $remark)
            {{ $remark }}@if(!$loop->last) | @endif
        @empty
            N/A
        @endforelse
    </td>
    <td>
        @forelse($statusHistory[$statusKey]['files'] as $path)
            @php
                $fileUrl = \Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])
                    ? $path
                    : rtrim(config('app.url'), '/') . '/' . ltrim($path, '/');
            @endphp
            <a href="{{ $fileUrl }}" target="_blank" rel="noopener noreferrer">Complaint File</a>@if(!$loop->last) | @endif
        @empty
            N/A
        @endforelse
    </td>
@endforeach
