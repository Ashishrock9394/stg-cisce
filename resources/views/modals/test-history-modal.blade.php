{{-- history modal  --}}
<div class="modal fade" id="testHistoryModal" tabindex="-1" role="dialog" aria-labelledby="testHistoryModalTitle" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-xl modal-dialog-centered">
        <div class="modal-content">
            
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel1">Upload History</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            </div>

        <div class="modal-body">
            <p>
                <strong>Note:</strong> Upload history is retained for <strong>7 days</strong> only.  
                After 7 days, all upload history will be permanently deleted from the system.  
                Please ensure that the uploaded file and the uploaded data are correct.
            </p>
        </div>

        @if($logs->isNotEmpty())   
        <div class="row m-1">
            <div class="col">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th width="3%">#</th>
                            <th width="12%">Uploaded By</th>
                            <th width="15%">Upload Time</th>
                            <th width="8%">Status</th>
                            <th width="27%">Message</th>
                            <th width="15%">Completed At</th>
                            <th width="10%">Uploaded File</th>
                            <th width="10%">Error File</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $index => $log)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $log->user->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y h:i A') }}</td>
                            <td>
                                @if($log->status === 'completed')
                                    <span class="badge bg-success" style="color:#ffffff;">Completed</span>
                                @elseif($log->status === 'processing')
                                    <span class="badge bg-warning text-dark" style="color:#ffffff;">Processing</span>
                                @elseif($log->status === 'queued')
                                    <span class="badge bg-info text-dark" style="color:#ffffff;">Queued</span>
                                @else
                                    <span class="badge bg-danger" style="color:#ffffff;">Failed</span>
                                @endif
                            </td>
                            <td>{!! nl2br(e($log->message)) !!}</td>
                            <td>{{ $log->completed_at ? \Carbon\Carbon::parse($log->completed_at)->format('d M Y h:i A') : '-' }}</td>
                            
                                <td style="text-align: center;">
                                @if($log->file_path)
                                    <a href="{{ route('download.testuploadedfile', $log->id) }}" class="btn btn-sm btn-primary">View</a>
                                @endif
                            </td>

                            <td style="text-align: center;">
                                    @if($log->error_file)
                                        <a href="{{ route('download.testerrorfile', $log->id) }}" class="btn btn-sm btn-primary">View</a>
                                    @endif                      
                            </td>
                            </tr>

                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
{{-- modal close --}}