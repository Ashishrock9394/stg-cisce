
{{-- history modal  --}}
<div class="modal fade" id="emailHistoryModal" tabindex="-1" role="dialog" aria-labelledby="emailHistoryModalTitle" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-xl modal-dialog-centered">
        <div class="modal-content">            
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Email Updation History</h5>
                <button type="button" class="close btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ol>
                    <li>
                        The email update may take up to 24 hours. Please be patient. Once the update is successful, the school will receive a confirmation email.
                    </li>
                    <li>
                        <strong>Note:</strong> Email Updation history is retained for <strong>7 days</strong> only.  
                    After 7 days, all Email Updation history will be permanently deleted from the system.
                    </li>
                </ol>
                <!-- AJAX content loads here -->
                @if($logs->isEmpty())
                    <p class="text-center">No Email Updation history found</p>
                @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Total Students</th>
                                <th>Email Updated</th>
                                <th>Failed</th>
                                <th>Status</th>
                                <th>Notification Sent</th>
                                <th>Uploaded File</th>
                                <th>Updated At</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($logs as $index => $log)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $log->total_students }}</td>
                                    <td>{{ $log->success_count }}</td>
                                    <td>{{ $log->failed_count }}</td>
                                    <td>
                                        @if($log->status === 'completed')
                                            <span class="badge bg-success" style="color:#ffffff;">Completed</span>
                                        @elseif($log->status === 'processing')
                                            <span class="badge bg-warning text-dark" style="color:#ffffff;">Processing</span>
                                        @elseif($log->status === 'pending')
                                            <span class="badge bg-info text-dark" style="color:#ffffff;">Queued</span>
                                        @else
                                            <span class="badge bg-danger" style="color:#ffffff;">Failed</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($log->notification_sent == '1')
                                            <span class="badge rounded-pill px-3 py-2 bg-success" style="color:#ffffff;">
                                                <i class="fas fa-check-circle me-1"></i> Sent
                                            </span>
                                        @else
                                            <span class="badge rounded-pill px-3 py-2 bg-warning text-dark">
                                                <i class="fas fa-spinner fa-spin me-1"></i> Processing
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->file_path)
                                        <a href="{{ route('download.uploaded.emailfile', $log->id) }}" class="btn btn-sm btn-primary">View</a>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $log->updated_at 
                                            ? \Carbon\Carbon::parse($log->updated_at)->format('d M Y h:i A') 
                                            : (\Carbon\Carbon::parse($log->updated_at)->format('d M Y h:i A') ?? '-') 
                                        }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
           
        </div>
    </div>
</div>
{{-- modal close --}}
