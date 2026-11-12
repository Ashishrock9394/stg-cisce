@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
@section('content')

<style>
   
    .hide{
        pointer-events: none;   
        opacity: 0.6;    
        cursor: not-allowed;
    }

</style>

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
                            </svg></span> 
                    </a>

                    @elseif(auth()->guard('sstudent')->check())

                    <a href="javascript:history.back()" class="back-button">
                        <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
                            </svg></span> 
                    </a>
                    
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
        
            <form class="row" method="POST" name="saveFlamingoRecord" id="save_partial_curl_up_record_id" action="">
                    {{method_field('post')}}
                    @csrf
                    
                    <input type="hidden" name="skillReportId" value="{{ $skillReportId }}">
                    <input type="hidden" name="TestTypeMasterID" value="{{ $TestTypeMasterID }}">
                    <input type="hidden" id="SchoolId" name="SchoolId" value="{{ $SchoolId }}">
                    <input type="hidden" id="selected_student_id" name="student_id">
                    
                    <div class="col-12"> 
                        <div class="form mb-4">
                            <h2 class="mb-3 mt-4 text-center">Enter Partial Curl up 30 sec Score</h2>
                            <div class="input-group mb-3 text-center">
                                <span class="form-control single-input">
                                    <label for="count_total_number_id" class="form-label">Counts</label>
                                    <input type="text" name="count_total_number" class="form-control form-control-lg text-center" id="count_total_number_id" placeholder="--">
                                </span>
                            </div>
                            <div  id="timer" class="mt-0 mb-3 text-center">00:00:00</div>
                            <div class="actions"><a href="javascript:void(0)" id="startBtn" class="btn btn-success py-2 w-100 d-flex justify-content-center" style="gap: 10px;"><i class="bi bi-stopwatch"></i><span>Start Timer</span></a></div>
                        </div>
                    </div>
                            
                    {{-- footer for submit and reset button --}}
                    @php
                        $id = "strength";
                    @endphp
                    <x-reset-submit-btn :id="$id"/>
                    {{-- footer close --}}
            </form>		
                
            
            
            
        </div>
    </div>
</div>


<script>
    const saveBtn = document.getElementById("submit_strength");
    let timerInterval;
    const countInput = document.getElementById("count_total_number_id");
    const timerDisplay = document.getElementById("timer");
    const startBtn = document.getElementById("startBtn");

     window.onload = function() {
        saveBtn.classList.add("hide");

    }
document.addEventListener("DOMContentLoaded", function () {
    let timeLeft = 30000; // 30 seconds in milliseconds
    const resetBtn = document.getElementById("reset_strength");
    countInput.disabled = true;

    function startTimer() {
        clearInterval(timerInterval);
        startBtn.innerHTML = "Test is Running!";
        startBtn.classList.add("hide");

        let endTime = Date.now() + 30000; // current time + 30 sec

        timerInterval = setInterval(() => {
            let msLeft = endTime - Date.now();
            if (msLeft <= 0) {
                clearInterval(timerInterval);
                updateTimerDisplay(0);
                countInput.disabled = false;                
                startBtn.textContent = "Test Completed!";
                saveBtn.classList.remove("hide");
            } else {
                updateTimerDisplay(msLeft);
            }
        }, 10); // update every 10ms for smooth milliseconds
    }
    startBtn.addEventListener("click", startTimer);
    saveBtn.classList.add("hide");

    updateTimerDisplay(timeLeft);
});

    function updateTimerDisplay(ms) {
        let totalSeconds = Math.floor(ms / 1000);
        let milliseconds = ms % 1000;
        let mins = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
        let secs = String(totalSeconds % 60).padStart(2, '0');
        let millis = String(milliseconds).padStart(3, '0');
        timerDisplay.textContent = `00:${secs}:${millis}`;
    }
</script>


<script>
$(document).ready(function() {
    $('#save_partial_curl_up_record_id').submit(function(e) {
        e.preventDefault();
        const studentId = document.getElementById('selected_student_id').value;
        const curlUpCount = document.getElementById('count_total_number_id')?.value;

        if (!studentId) {
            handleResponseMessages( 'warning',  'Add Student', 'Please select the student');
            return;
        }
        if (curlUpCount === null || curlUpCount === undefined || curlUpCount === ""){
            handleResponseMessages( 'warning',  'Add curl-up count', 'Please add curl-up count');
            return;
        }
        submitLoader();
        $.ajax({
            url: '{{ route("partial.curl.up.record.submit") }}', // or your route URL
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
				Swal.close();
				$('#save_partial_curl_up_record_id')[0].reset();
                handleResponseMessages( 'success',  '', response.message, {
                    confirmText: 'OK',
                    onConfirm: function () {
                        location.reload();
                    }
                });				
            },

            error: function(xhr) {
                Swal.close();
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
    });
});

document.getElementById("count_total_number_id").addEventListener("input", function (e) {
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