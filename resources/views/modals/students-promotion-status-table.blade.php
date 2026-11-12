@if($logs->isEmpty())
    <p class="text-center">No promotion history found</p>
@else
<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Total Students</th>
                <th>Promoted</th>
                <th>Transferred</th>
                <th>Status</th>
                <th>Updated At</th>
            </tr>
        </thead>

        <tbody>
            @foreach($logs as $index => $log)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $log->total_students }}</td>
                    <td>{{ $log->promoted_students }}</td>
                    <td>{{ $log->transferred_students }}</td>

                    <td>
                        @if($log->status === 'promoted')
                            <span class="badge bg-success" style="color:#ffffff;">Completed</span>
                        @elseif($log->status === 'processing')
                            <span class="badge bg-warning text-dark" style="color:#ffffff;">Processing</span>
                        @elseif($log->status === 'pending')
                            <span class="badge bg-info text-dark" style="color:#ffffff;">Queued</span>
                        @else
                            <span class="badge bg-danger" style="color:#ffffff;">Failed</span>
                        @endif
                    </td>

                    <td>
                        {{ $log->completed_at 
                            ? \Carbon\Carbon::parse($log->completed_at)->format('d M Y h:i A') 
                            : (\Carbon\Carbon::parse($log->updated_at)->format('d M Y h:i A') ?? '-') 
                        }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif