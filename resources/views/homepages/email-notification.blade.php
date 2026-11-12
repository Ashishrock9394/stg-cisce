@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
@section('content')


<style>
    .warmup-card {
        background: #ffffff !important;
        border-radius: 16px !important;
        padding: 40px !important;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06) !important;
        border: 1px solid #e2e8f0 !important;
    }

    .warmup-card h2 {
        color: #0f172a !important;
        font-weight: 700 !important;
        font-size: 24px !important;
        margin-top: 0;
        margin-bottom: 28px;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 12px;
    }

    .form-group {
        margin-bottom: 24px !important;
    }

    .form-group label {
        display: block !important;
        color: #334155 !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        margin-bottom: 8px !important;
    }

    /* High-quality Input Fields */
    .form-control-custom {
        width: 100% !important;
        padding: 12px 16px !important;
        border-radius: 8px !important;
        border: 1px solid #cbd5e1 !important;
        background-color: #ffffff !important;
        color: #1e293b !important;
        font-size: 15px !important;
        box-sizing: border-box !important;
        transition: all 0.2s ease;
    }

    .form-control-custom:focus {
        outline: none !important;
        border-color: #ff8000 !important;
        box-shadow: 0 0 0 3px rgba(255, 128, 0, 0.12) !important;
    }

    /* Clean Binary Toggle Options (All vs Custom) */
    .toggle-container {
        display: flex !important;
        gap: 12px !important;
        margin-top: 8px !important;
    }

    .toggle-btn {
        background-color: #ffffff !important;
        color: #64748b !important;
        border: 1px solid #cbd5e1 !important;
        padding: 10px 24px !important;
        border-radius: 8px !important;
        cursor: pointer !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        transition: all 0.2s ease;
        flex: 1;
        text-align: center;
    }

    .toggle-btn:hover {
        background-color: #f8fafc !important;
        border-color: #94a3b8 !important;
    }

    .toggle-btn.active {
        background-color: #ff8000 !important;
        color: #ffffff !important;
        border-color: #ff8000 !important;
    }

    /* Dynamic Count Box Wrapper */
    #count-input-box {
        display: none;
        margin-top: 16px;
    }

    .file-upload-box {
        border: 2px dashed #cbd5e1 !important;
        padding: 16px !important;
        border-radius: 8px !important;
        background: #f8fafc !important;
        transition: border-color 0.2s;
    }

    .file-upload-box:hover {
        border-color: #94a3b8 !important;
    }

    .btn-send {
        width: 100% !important;
        background-color: #ff8000 !important;
        color: #ffffff !important;
        border: none !important;
        padding: 14px !important;
        border-radius: 8px !important;
        font-size: 16px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        box-shadow: 0 4px 12px rgba(255, 128, 0, 0.2) !important;
        transition: all 0.2s ease;
    }

    .btn-send:hover {
        background-color: #e67300 !important;
        box-shadow: 0 6px 16px rgba(255, 128, 0, 0.3) !important;
    }

    /* AJAX Response Alerts Layout */
    .alert-msg {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 500;
        display: none;
    }

    .alert-success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .alert-danger {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
</style>

<div class="container">
    <div class="t-mrg2 pb-5">
        {{-- Header --}}
        <div class="all-chaptr-cards mb-4" style="margin: 0;">
            <div class="row">
                <x:back-button title="{{ $title }}">
                </x:back-button>
            </div>
        </div>

        <div class="warmup-card mb-4">
            <h2>Warmup Email Configuration</h2>        
                        
            <form id="warmup-email-form" enctype="multipart/form-data">
                @csrf   
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="user_type">User Type</label>
                        <select name="user_type" id="user_type" class="form-control-custom" required>
                            <option value="" disabled selected>Select User Type</option>
                            <option value="4">School</option>
                            <option value="3">Trainer</option>
                            <option value="0">Parent</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Send To</label>                    
                        <input type="hidden" name="send_to" id="send_to" value="all">        
                        <div class="toggle-container">
                            <button type="button" class="toggle-btn active" onclick="handleSendToToggle('all')">All</button>
                            <button type="button" class="toggle-btn" onclick="handleSendToToggle('custom')">Custom</button>
                        </div>      
                        <div id="count-input-box">
                            <label for="count">Target Record Count</label>
                            <input type="number" id="count" name="count" class="form-control-custom"
                                placeholder="Enter target limit count (e.g. 50)">
                        </div>
                    </div>
                </div>     
                <div class="form-row">    
                    <div class="form-group col-md-12">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" class="form-control-custom"
                            placeholder="Enter email subject header" required>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" class="form-control-custom" rows="5"
                            placeholder="Write your email body message content here..." required></textarea>
                    </div>        
                    <div class="form-group col-md-12">
                        <label for="attachment">Attachment (Max 512KB)</label>
                        <div class="file-upload-box">
                            <input type="file" id="attachment" name="attachment" accept=".jpg,.png,.pdf,.doc,.docx">
                            <span id="attachmentError" style="color:red; font-size:14px;"></span>
                        </div>
                    </div>    
                                    
                </div>        
                                    
                <button type="submit" class="btn-send" id="submit-btn">TRANSMIT WARMUP EMAILS</button>
            </form>
        </div>

    </div>
</div>


<script>
    
    function handleSendToToggle(targetMode) {
        $('#send_to').val(targetMode);

        $('.toggle-btn').removeClass('active');
        event.currentTarget.classList.add('active');

        if (targetMode === 'custom') {
            $('#count-input-box').slideDown(200);
            $('#count').prop('required', true); 
        } else {
            $('#count-input-box').slideUp(180);
            $('#count').prop('required', false).val(''); 
        }
    }
    
    document.getElementById('attachment').addEventListener('change', function () {
        const file = this.files[0];
        const maxSize = 512 * 1024;
        const errorSpan = document.getElementById('attachmentError');

        errorSpan.textContent = "";

        if (file && file.size > maxSize) {
            errorSpan.textContent = "File size must not exceed 512 KB.";
            this.value = "";
        }
    });

    $(document).ready(function () {

        $('#warmup-email-form').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('send.notification.email') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#submit-btn')
                        .prop('disabled', false)
                        .text('TRANSMIT WARMUP EMAILS');

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        confirmButtonColor: '#ff8000'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                },
                error: function (xhr) {
                    $('#submit-btn')
                        .prop('disabled', false)
                        .text('TRANSMIT WARMUP EMAILS');
                    let message = 'Something went wrong. Please try again.';

                    if (xhr.status === 422) {

                        let errors = xhr.responseJSON.errors;
                        message = Object.values(errors).flat().join('<br>');

                    } else if (xhr.responseJSON) {

                        message = xhr.responseJSON.message || message;
                        if (xhr.responseJSON.error) {
                            message += '<br><br><small>' + xhr.responseJSON.error + '</small>';
                        }
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: message,
                        confirmButtonColor: '#d33'
                    });
                }
            });

        });

});
</script>

@endsection