@extends('assessor.cwsn.index')
@section('cwsnform')

<style>
    .btn-primary.disabled, .btn-primary:disabled {
        color: #fff;
        background-color: #ff8000;
        border-color: #ff8000;
        cursor: not-allowed;
    }

    .btn-theme-primary:disabled {
        background-color: #cccccc !important;
        border-color: #cccccc !important;
        color: #ff8000 !important;
        cursor: not-allowed;
    }

     .btn-theme-primary {
        background-color: #4da3ff !important;
        border-color: #4da3ff !important;
        color: #ffffff !important;
    }
    .btn-theme-primary:hover:not(:disabled) {
        background-color: #378ce6 !important;
        border-color: #378ce6 !important;
    }
    .btn-theme-primary:disabled {
        background-color: #cccccc !important;
        border-color: #cccccc !important;
        color: #666666 !important;
        cursor: not-allowed;
        opacity: 0.65;
    }

    #btn-timer-control.paused {
        background-color: red;
        color: white;
        border: none;
        border-radius: 10px;
    }
</style>

<h2 class="mb-3 text-center">{{ $title }} Score </h2>

<form class="row bg-white mt-4" method="POST" name="{{ $TestTypeId }}" id="{{ $TestTypeId }}" action="javascript:void(0);">
    {{ method_field('post') }}
    @csrf

    <input type="hidden" name="skillReportId" value="{{ $skillReportId }}" id="skillReportId">
    <input type="hidden" name="TestTypeMasterID" value="{{ $TestTypeMasterID }}">
    <input type="hidden" id="SchoolId" name="SchoolId" value="{{ $SchoolId }}">
    <input type="hidden" id="selected_student_id" name="student_id">
    <input type="hidden" name="modified_pushup" id="score_measurement" value="">

    <div class="col-12">
       <div class="form row mb-4">  
            <div class="card-body bg-light text-center">

                {{--
                <div class="input-group mb-3 text-center">
                    <span class="form-control single-input">
                        <label for="count_total_number_id" class="form-label">Counts</label>
                        <input type="text" name="count_total_number" class="form-control form-control-lg text-center" id="count_total_number_id" placeholder="--">
                    </span>
                </div>
                --}}

                <!-- Centered Digital Timer Display Circle -->
                <div class="d-flex justify-content-center align-items-center">
                    <div class="d-flex flex-column justify-content-center align-items-center text-dark" id="timer-display-box">
                        <!-- Updated default placeholder text to match SS:mm layout -->
                        <span id="stopwatch_display" class="font-weight-bold m-2" style="font-size: 2.4rem; font-family: monospace; line-height: 1;">00:00</span>                         
                    </div>
                </div>

                <button type="button" id="btn-timer-control" class="btn btn-success w-100 d-flex justify-content-center mt-2" style="gap: 8px; border-radius: 8px;">
                    <i class="bi bi-stopwatch"></i><span id="timer-btn-text">Start Timer</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Sticky Bottom Layout Form Controls -->
    <div class="col-12">
        <footer class="container-fluid position-fixed bg-white shadow-lg border-top p-0" style="bottom: 0; left: 0; right: 0; z-index: 100;">
            <div class="container py-3">
                <div class="d-flex justify-content-between align-items-center px-2">
                    <button type="button" id="reset_{{ $TestTypeId }}" class="btn py-2.5 px-5 font-weight-bold btn-outline-secondary" style="border-radius: 8px; min-width: 140px;">Reset</button>   
                    <button type="submit" id="submit_{{ $TestTypeId }}" class="btn py-2.5 px-5 font-weight-bold btn-primary" style="border-radius: 8px; min-width: 140px;" disabled>Save</button>
                </div>
            </div>
        </footer>
    </div>
</form>

<script>

    const countInput = document.getElementById("count_total_number_id");

    document.addEventListener("DOMContentLoaded", function () {

    // countInput.disabled = true;

    const testtype = `{{ $title }}`;
    const formName = @json($TestTypeId);

    let animationFrame = null;
    let startTime = null;
    let elapsedTime = 0;
    let isRunning = false;

    const stopwatchDisplay = document.getElementById("stopwatch_display");

    const timerBox = document.getElementById("timer-display-box");
    const btnControl = document.getElementById("btn-timer-control");
    const btnReset = document.getElementById(`reset_${formName}`);
    const btnSubmit = document.getElementById(`submit_${formName}`);
    const hiddenScoreInput = document.getElementById("score_measurement");

    if (btnSubmit) {
        btnSubmit.disabled = true;
    }

    // New helper to parse milliseconds into a strict SS:mm (Seconds:Milliseconds) output
    function formatTime(ms) {
        let totalSeconds = Math.floor(ms / 1000);
        let milliseconds = Math.floor((ms % 1000) / 10); // Extract 2 digit milliseconds

        let secondsStr = totalSeconds < 10 ? '0' + totalSeconds : totalSeconds;
        let milliStr = milliseconds < 10 ? '0' + milliseconds : milliseconds;

        return `${secondsStr}:${milliStr}`;
    }

    btnControl.addEventListener("click", function () {
        if (!isRunning) {
            startTimer();
        } else {
            stopTimer(false, testtype);
        }
    });

    btnReset.addEventListener("click", function (e) {
        e.preventDefault();
        resetTimer();
    });

    function startTimer() {
        isRunning = true;
        startTime = performance.now() - elapsedTime;

        document.getElementById("timer-btn-text").textContent = "Stop";
        btnControl.classList.remove("btn-success");
        btnControl.classList.add("paused");
        timerBox.style.borderColor = "#28a745"; 

        if (btnSubmit) {
            btnSubmit.disabled = true; 
            btnSubmit.classList.remove("btn-theme-primary");
            btnSubmit.classList.add("btn-primary");
        }

        updateTimer();
    }

    function updateTimer() {
        if (!isRunning) return;

        elapsedTime = performance.now() - startTime;
        let seconds = elapsedTime / 1000;

        if (testtype == 'Isometric Push-up') {
            if (seconds >= 40) {
                elapsedTime = 40000;
                let formatted = formatTime(elapsedTime);
                stopwatchDisplay.innerText = formatted;
                hiddenScoreInput.value = formatted; 
                stopTimer(true, testtype);
                return;
            }
        }

        if (testtype == 'Seated Push-up') {
            if (seconds >= 20) {
                elapsedTime = 20000;
                let formatted = formatTime(elapsedTime);
                stopwatchDisplay.innerText = formatted;
                hiddenScoreInput.value = formatted; 
                stopTimer(true, testtype);
                return;
            }
        }

        let formatted = formatTime(elapsedTime);
        stopwatchDisplay.innerText = formatted;
        hiddenScoreInput.value = formatted; 
        animationFrame = requestAnimationFrame(updateTimer);
    }

    function stopTimer(maxReached = false, testtype) {
        let maxtimeMs = Infinity;
        if (testtype == 'Isometric Push-up') {
            maxtimeMs = 40000;
        }
        if (testtype == 'Seated Push-up') {
            maxtimeMs = 20000;
        }

        isRunning = false;
        if (animationFrame) {
            cancelAnimationFrame(animationFrame);
            animationFrame = null;
        }

        const finalScoreMs = Math.min(maxtimeMs, elapsedTime);
        const formattedScore = formatTime(finalScoreMs);

        stopwatchDisplay.innerText = formattedScore;
        hiddenScoreInput.value = formattedScore;

        document.getElementById("timer-btn-text").textContent = "Start Timer";
        btnControl.classList.remove("paused");
        btnControl.classList.add("btn-success");
        btnControl.disabled = true;
        if (maxReached) {
            document.getElementById("timer-btn-text").textContent = "Test Completed!";
            timerBox.style.borderColor = "#ffc107"; 
        } else {
            timerBox.style.borderColor = "#dc3545"; 
        }

        if (btnSubmit && finalScoreMs > 0) {
            btnSubmit.disabled = false;
            btnSubmit.classList.remove("btn-theme-primary");
            btnSubmit.classList.add("btn-primary");
        }
    }

    function resetTimer() {
        if (animationFrame) {
            cancelAnimationFrame(animationFrame);
            animationFrame = null;
        }
        
        isRunning = false;
        elapsedTime = 0;
        startTime = null;

        stopwatchDisplay.innerText = "00:00";
        timerBox.style.borderColor = "#4da3ff";
        hiddenScoreInput.value = "";

        btnControl.disabled = false;
        document.getElementById("timer-btn-text").textContent = "Start Timer";
        btnControl.classList.remove("paused");
        btnControl.classList.add("btn-success");

        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.classList.remove("btn-theme-primary");
            btnSubmit.classList.add("btn-primary");
        }
    }

    $(document).ready(function() {
        const formName = @json($TestTypeId);

        $(`#${formName}`).submit(function(e) {
            e.preventDefault();

            const studentId = document.getElementById('selected_student_id').value;
            if (!studentId) {
                handleResponseMessages('warning', 'Select Student', 'Please select the student');
                return;
            }            
            const finalMmInput = $('input[name="modified_pushup"]').val();
            if (finalMmInput === '' || finalMmInput === null || undefined === finalMmInput) {
                handleResponseMessages('info', '', 'Please enter position of the student');
                return;
            }

            let route = '{{ route("cwsn.types.submit") }}';
            let formData = $(this).serialize();
            SubmitForm(formName, formData, route);
        });
    });
});
</script>

@endsection