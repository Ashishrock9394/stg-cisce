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
            
            
            <form class="row" method="POST" name="savePlateTappingRecord" id="save_plate_tapping_record_id" action="">
                {{method_field('post')}}
                @csrf
                    
            <input type="hidden" name="skillReportId" value="{{ $skillReportId }}">
            <input type="hidden" name="TestTypeMasterID" value="{{ $TestTypeMasterID }}">
            <input type="hidden" id="SchoolId" name="SchoolId" value="{{ $SchoolId }}">
            <input type="hidden" id="selected_student_id" name="student_id">
            <input type="hidden" id="total_milisecond_id" name="total_miliseconds">
        
        
                <div class="col-12">
                    <div class="form">
                        <h2 class="mb-3 mt-4 text-center">{{$title}} Score</h2>
                        <div class="input-group input-group__2 mb-3">
                            <span class="form-control">
                                <label for="minuteId" class="form-label">min</label>
                                <input type="number" name="minute" class="form-control form-control-lg" id="minuteId" placeholder="00" disabled>
                            </span>
                            <span class="form-control">
                                <label for="secondId" class="form-label">sec</label>
                                <input type="number" name="second" class="form-control form-control-lg" id="secondId" placeholder="00" disabled>
                            </span>
                            <span class="form-control">
                                <label for="milisecondId" class="form-label">msec</label>
                                <input type="number" name="mili_second" class="form-control form-control-lg" id="milisecondId" placeholder="00" disabled>
                            </span>
                        </div>
                        <div class="actions">
                            <a href="#a"  id="startTimerBtn" class="btn btn-success py-2 w-100 d-flex justify-content-center" style="gap: 10px;">
                            <i class="bi bi-stopwatch"></i><span id="timerLabel">Start Timer</span>
                            </a>
                        </div>
                    </div>
                </div>
        
            {{-- footer for submit and reset button --}}
            @php
                $id = "plateTapping";
            @endphp
            <x-reset-submit-btn :id="$id"/>
            {{-- footer close --}}	
        </form>	
            
        </div>
    </div>
</div>

<script>
    const saveBtn = document.getElementById("submit_plateTapping");
    window.onload = function() {
        saveBtn.classList.add("hide");

    }
$(document).ready(function() {


    
    $('#save_plate_tapping_record_id').submit(function(e) {
        e.preventDefault(); // prevent default form submission
        const studentId = document.getElementById('selected_student_id').value;
        
        if (!studentId) {
            handleResponseMessages( 'warning',  'Select Student', "Please select the student");
            return;
        }

        if(milisecondInput.value<1){
            handleResponseMessages( 'warning',  'Start Test', "Please Start Test");
            return;
        }        
        submitLoader();
        $.ajax({
            url: '{{ route("plate.tapping.record.submit") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
				Swal.close();
                $('#save_plate_tapping_record_id')[0].reset();                
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
</script>


<script>
    let minute = 0;
    let second = 0;
    let milisecond = 0;
    let interval = null;
    let isRunning = false;
	
    const minuteInput = document.getElementById('minuteId');
    const secondInput = document.getElementById('secondId');
    const milisecondInput = document.getElementById('milisecondId');
    const startTimerBtn = document.getElementById('startTimerBtn');
    const timerLabel = document.getElementById('timerLabel');

    function updateDisplay() {
        minuteInput.value = String(minute).padStart(2, '0');
        secondInput.value = String(second).padStart(2, '0');
        milisecondInput.value = String(milisecond).padStart(3, '0');
    }

    function startTimer() {
        interval = setInterval(() => {
            milisecond += 10;

            if (milisecond >= 1000) {
                milisecond = 0;
                second++;
            }

            if (second >= 60) {
                second = 0;
                minute++;
            }

            updateDisplay();
        }, 10);

        isRunning = true;
        timerLabel.textContent = "STOP Timer";
    }

    function stopTimer() {
        clearInterval(interval);
        isRunning = false;
        startTimerBtn.classList.add("hide");
        saveBtn.classList.remove("hide");


        timerLabel.textContent = "Start Timer";
		
		// 👉 Calculate total time in milliseconds
		const totalMilliseconds = (minute * 60 * 1000) + (second * 1000) + milisecond;
		
		// 👉 Optional: store in a hidden input if submitting to Laravel
        document.getElementById('total_milisecond_id').value = totalMilliseconds;
    }
	
    startTimerBtn.addEventListener('click', function (e) {
        e.preventDefault();
        if (isRunning) {
            stopTimer();
        } else {
            startTimer();
        }
    });
</script>




@endsection