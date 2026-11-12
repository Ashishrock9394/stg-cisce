 <div class="mb-3">
     <strong>Note:</strong>
    Generated reports are retained for <strong>7 days</strong> only. After 7 days, they will be permanently deleted from the system. Please download and store your reports before they expire. 
</div>


<table class="table table-bordered">
    <thead>
        <tr>
            <th>Request Date & Time</th>
            <th>Total Reports Requested</th>
            <th>Reports Sent</th>
            <th>Current Status</th>
        </tr>
    </thead>
    <tbody>


    @foreach($reports as $report)
        <tr>
            <td>{{ \Carbon\Carbon::parse($report->created_at)->timezone('Asia/Kolkata')->format('d-m-Y h:i A') }}</td>
            <td>{{ $report->total_students }}</td>
            <td>{{ $report->completed_students }} / {{ $report->total_students }}</td>
            <td>
                @if($report->status === 'sent')
                    <span class="badge bg-success" style="color:#ffffff;">Sent</span>
                @elseif($report->status === 'processing')
                    <span class="badge bg-warning text-dark" style="color:#ffffff;">Processing</span>
                @elseif($report->status === 'failed')
                    <span class="badge bg-danger">Failed</span>
                 @elseif($report->status === 'pending')
                    <span class="badge bg-danger">Pending</span>
                @else
                    <span class="badge bg-secondary" style="color:#ffffff;">Requested</span>
                @endif
            </td>
            
        </tr>
    @endforeach
    </tbody>
</table>
