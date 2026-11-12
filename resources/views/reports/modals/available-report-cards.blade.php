 <div class="mb-3">
     <strong>Note:</strong>
    Generated reports are retained for <strong>7 days</strong> only. After 7 days, they will be permanently deleted from the system. Please download and store your reports before they expire. 
</div>


<table class="table table-bordered">
    <thead>
        <tr>
            <th>Request Date & Time</th>
            <th>Total Reports Requested</th>
            <th>Reports Generated</th>
            <th>Current Status</th>
            <th>Action</th>
            <th>Available Until</th>
        </tr>
    </thead>
    <tbody>


    @foreach($reports as $report)
        <tr>
            <td>{{ \Carbon\Carbon::parse($report->created_at)->format('d-m-Y h:i A') }}</td>
            <td>{{ $report->total_students }}</td>
            <td>{{ $report->completed_students }} / {{ $report->total_students }}</td>
            <td>
                @if($report->status === 'completed')
                    <span class="badge bg-success" style="color:#ffffff;">Completed</span>
                @elseif($report->status === 'in_progress')
                    <span class="badge bg-warning text-dark" style="color:#ffffff;">Processing</span>
                @elseif($report->status === 'failed')
                    <span class="badge bg-danger">Failed</span>
                 @elseif($report->status === 'pending')
                    <span class="badge bg-danger">Pending</span>
                @else
                    <span class="badge bg-secondary" style="color:#ffffff;">Requested</span>
                @endif
            </td>

            <td>
                @if($report->status === 'completed')
                    <a href="{{ $report->download_path }}" class="btn btn-sm btn-primary"> Ready to Download </a>
                @else
                    <button class="btn btn-sm btn-secondary" disabled>
                        Not Ready
                    </button>
                @endif
            </td>

            <td>
                @if($report->expires_at)
                    @php
                        $expiresAt = \Carbon\Carbon::parse($report->expires_at);
                    @endphp

                    @if(now()->lessThan($expiresAt))
                        <span class="text-success">
                            Available until:
                            <strong>{{ $expiresAt->format('d M Y') }}</strong>
                        </span>
                        <br>
                        <small class="text-muted">
                            Expires in {{ now()->diffInDays($expiresAt) }} day(s)
                        </small>
                    @else
                        <span class="text-danger">
                            Download expired on {{ $expiresAt->format('d M Y') }}
                        </span>
                    @endif
                @else
                    <span class="text-muted">Expiry not available</span>
                @endif
            </td>
            
        </tr>
    @endforeach
    </tbody>
</table>
