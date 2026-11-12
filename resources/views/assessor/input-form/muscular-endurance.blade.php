@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
@section('content')

<div class="all-chaptr-cards">
    <div class="container">
        <div class="t-mrg2 mb-5 pb-5">
            <div class="row">
                <div class="col-12">
                    <div class="heading-rw mt-0 mt-md-1 mb-0 p-0">
                    @if(auth()->guard('web')->check())

                    <a href="javascript:history.back()" class="back-button">
                        <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
                            </svg></span> </a>

                    @elseif(auth()->guard('sstudent')->check())

                    <a href="javascript:history.back()" class="back-button">
                        <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
                            </svg></span> </a>
                    @endif

                        <h1 class="ml-md-4 mb-0">{{$title}}</h1>
                    </div>
                </div>
            </div>
            
            {{-- get student list componet --}}
            @php
            $type = "fitnessTest";
            @endphp

            <x-get-student-list :classes="$classes" :type="$type"  />
            
        <form class="row" method="POST" name="savePushUpRecord" id="save_push_up_id" action="">
            {{method_field('post')}}
            @csrf	
            
            
                        <input type="hidden" name="skillReportId" value="{{ $skillReportId }}">
                        <input type="hidden" name="TestTypeMasterID" value="{{ $TestTypeMasterID }}">
                        <input type="hidden" id="SchoolId" name="SchoolId" value="{{ $SchoolId }}">
                        <input type="hidden" id="selected_student_id" name="student_id">
            
            <div class="col-12">
                <div class="form mb-4">
                    <h3 class="mb-3 mt-4 text-center">Enter Push Ups/ Modified Push Ups Score</h3>
                    <div class="input-group mb-3 text-center">
                        <span class="form-control single-input">
                            <label for="pushUpCount" class="form-label">Counts</label>
                            <input type="text" name="total_push_up" class="form-control form-control-lg text-center" id="pushUpCount" placeholder="00">
                        </span>
                    </div>
                    <div id="timer" class="mt-0 mb-3 text-center">00:00:00</div>
                    
                    <div class="actions"><a  id="startPauseBtn" href="#a" class="btn btn-success py-2 w-100 d-flex justify-content-center" style="gap: 10px;"><i class="bi bi-stopwatch"></i><span>Start Timer</span></a></div>
            
            </div>
            </div> 

            {{-- footer for submit and reset button --}}
            @php
                $id = "pushups";
            @endphp
            <x-reset-submit-btn :id="$id"/>
            {{-- footer close --}}

        </form>
            
        </div>
    </div>
</div>

<script>
    let startTime = 0;
    let elapsed = 0;
    let timerInterval = null;
    let running = false;

    const display = document.getElementById("timer");
    const pushUpInput = document.getElementById("pushUpCount");
    const startPauseBtn = document.getElementById("startPauseBtn");

    function updateTimer() {
        const now = Date.now();
        let time = now - startTime + elapsed;

        let minutes = Math.floor(time / 60000);
        let seconds = Math.floor((time % 60000) / 1000);
        let milliseconds = Math.floor((time % 1000) / 10);

        minutes = minutes < 10 ? '0' + minutes : minutes;
        seconds = seconds < 10 ? '0' + seconds : seconds;
        milliseconds = milliseconds < 10 ? '0' + milliseconds : milliseconds;

        display.innerHTML = `${minutes}:${seconds}:${milliseconds}`;
    }

    startPauseBtn.addEventListener("click", function () {
        if (!running) {
            // Start or Resume
            startTime = Date.now();
            timerInterval = setInterval(updateTimer, 10);
            startPauseBtn.textContent = "Pause Timer";
            running = true;
        } else {
            // Pause
            clearInterval(timerInterval);
            elapsed += Date.now() - startTime;
            startPauseBtn.textContent = "Start Timer";
            running = false;
        }
    });

</script>

<script>
$(document).ready(function() {
    $('#save_push_up_id').submit(async function(e) { 
        e.preventDefault(); // prevent default form submission

        const studentId = document.getElementById('selected_student_id').value;
        const pushUps = pushUpInput.value;

        if(!studentId){

            handleResponseMessages( 'warning',  'Select Student', 'Please select the student');
            return;
        }
        
        if (pushUps === "") {
            handleResponseMessages( 'warning',  'Push up empty', 'Please enter the push ups count');
            return;
        }
        
        await Swal.fire({
            title: 'Confirm Submission',
            text: `You did ${pushUps} push-ups in ${display.innerHTML}!`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, submit',
            cancelButtonText: 'Cancel'
            }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Please wait...',
                    text: 'Processing your request',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

            $.ajax({
                url: '{{ route("push.up.record.submit") }}', // or your route URL
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    
                    $('#save_push_up_id')[0].reset();
                    handleResponseMessages( 'success',  '', response.message, {
                        confirmText: 'OK',
                        onConfirm: function () {
                            location.reload();
                        }
                    }); 
                        
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorHtml = '<ul>';
                        $.each(errors, function(key, value) {
                            errorHtml += '<li>' + value[0] + '</li>';
                        });
                        errorHtml += '</ul>';
                        $('#response').html('<div style="color:red;">' + errorHtml + '</div>');
                        
                        Swal.fire({
                            title: "error!",
                            text: response.message,
                            icon: "error"
                        });
                        
                    }
                });
            }else{
                return;
            }
        });
        
    });
});


document.getElementById("pushUpCount").addEventListener("input", function (e) {
    let value = e.target.value;
    value = value.replace(/[^0-9]/g, '');    
    let match = value.match(/^(\d{0,3})?$/);
    if (match) {
        value = match[0];
    } else {
        value = value.slice(0, -1); 
    }
    
    if (value && parseFloat(value) < 0) {
        value = '';
    }
    e.target.value = value;
});
</script>
@endsection