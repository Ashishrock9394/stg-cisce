@extends('assessor.cwsn.index')
@section('cwsnform')

<style>
    .hide {
        pointer-events: none;   
        opacity: 0.6;    
        cursor: not-allowed;
    }
    .btn-danger-stop {
        background-color: #ff0000 !important;
        color: #fff !important;
    }
</style>

<form class="row" method="POST" name="{{ $TestTypeId }}" id="{{ $TestTypeId }}" action="javascript:void(0);">
    {{ method_field('post') }}
    @csrf
    
    <input type="hidden" name="skillReportId" value="{{ $skillReportId }}">
    <input type="hidden" name="TestTypeMasterID" value="{{ $TestTypeMasterID }}">
    <input type="hidden" id="SchoolId" name="SchoolId" value="{{ $SchoolId }}">
    <input type="hidden" id="selected_student_id" name="student_id">
    
    <div class="col-12"> 
        <div class="row form mb-4">
            <h2 class="mb-3 text-center">{{ $title }} Score</h2>
            <div class="input-group mb-3 text-center">
                <span class="form-control single-input">
                    <label for="count_total_number_id" class="form-label">Counts (Max 75)</label>
                    <input type="text" name="count_total_number" class="form-control form-control-lg text-center" id="count_total_number_id" placeholder="00" disabled>
                </span>
            </div>
            
            <div id="timer" class="mt-0 mb-3 text-center" style="font-family: monospace; font-size: 1.5rem;">00:00:00</div>
            <div class="actions">
                <a href="javascript:void(0)" id="startBtn" class="btn btn-success py-2 w-100 d-flex justify-content-center" style="gap: 10px;">
                    <i class="bi bi-stopwatch"></i><span>Start Timer</span>
                </a>
            </div>
        </div>
    </div>
            
    <x-reset-submit-btn :id="$TestTypeId"/>
</form>

<script>
let TestTypeId = @json($TestTypeId);
const saveBtn = document.getElementById(`submit_${TestTypeId}`);
let timerInterval;
const countInput = document.getElementById("count_total_number_id");
const timerDisplay = document.getElementById("timer");
const startBtn = document.getElementById("startBtn");

let audioCtx = null;
let isRunning = false; 
let startTime = null;
let maxTimeMs = 75 * 3 * 1000; 
let currentRepCount = 0; 

function playBeep() {
    try {
        if (!audioCtx) {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
        const oscillator = audioCtx.createOscillator();
        const gainNode = audioCtx.createGain();

        oscillator.type = 'square'; 
        oscillator.frequency.value = 1100; 
        gainNode.gain.setValueAtTime(0.2, audioCtx.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.2);
        oscillator.connect(gainNode);
        gainNode.connect(audioCtx.destination);
        oscillator.start();
        oscillator.stop(audioCtx.currentTime + 0.2);
    } catch (e) {
        console.error("Audio dynamic beep initialization failed: ", e);
    }
}

window.onload = function() { 
    if(saveBtn) saveBtn.classList.add("hide"); 
}

document.addEventListener("DOMContentLoaded", function () {
    let lastBeepTimeIndex = 0;

    startBtn.addEventListener("click", function() {
        if (!isRunning) {
            isRunning = true;
            countInput.disabled = true; 

            if (window.AudioContext || window.webkitAudioContext) {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }

            clearInterval(timerInterval);            
            startBtn.innerHTML = '<i class="bi bi-stopwatch"></i><span>Stop Timer</span>';
            startBtn.classList.remove("btn-success");
            startBtn.classList.add("btn-danger-stop");

            startTime = Date.now();
            lastBeepTimeIndex = 0;
            currentRepCount = 0; 
            countInput.value = currentRepCount; // Starts at 0, updates on 3rd second

            timerInterval = setInterval(() => {
                let msElapsed = Date.now() - startTime;
                
                if (msElapsed >= maxTimeMs) {
                    currentRepCount = 75;
                    countInput.value = currentRepCount;
                    stopTest(true); 
                } else {
                    updateTimerDisplay(msElapsed);
                    
                    let currentSecondBlock = Math.floor(msElapsed / 3000);
                    // Fires sound and increments counter at 3s, 6s, 9s, etc.
                    if (currentSecondBlock > lastBeepTimeIndex) {
                        playBeep();
                        lastBeepTimeIndex = currentSecondBlock;
                        currentRepCount++; 
                        countInput.value = currentRepCount;
                    }
                }
            }, 30); 

        } else {
            stopTest(false);
        }
    });

    function stopTest(hitMaxCeiling = false) {
        isRunning = false;
        clearInterval(timerInterval);
        
        startBtn.classList.add("hide"); 
        countInput.disabled = false; 

        countInput.value = currentRepCount;

        if (saveBtn) saveBtn.classList.remove("hide"); 

        if (hitMaxCeiling) {
            updateTimerDisplay(maxTimeMs);
            startBtn.innerHTML = '<span>Max 75 Reps Completed!</span>';
            startBtn.className = "btn btn-warning py-2 w-100 d-flex justify-content-center text-dark";
        } else {
            startBtn.innerHTML = '<span>Test Stopped</span>';
            startBtn.className = "btn btn-success py-2 w-100 d-flex justify-content-center disabled";
        }
    }

    const resetBtn = document.getElementById(`reset_${TestTypeId}`);
    if (resetBtn) {
        resetBtn.addEventListener("click", function() {
            isRunning = false;
            clearInterval(timerInterval);
            currentRepCount = 0;
            timerDisplay.textContent = "00:00:00"; 
            
            startBtn.innerHTML = '<i class="bi bi-stopwatch"></i><span>Start Timer</span>';
            startBtn.className = "btn btn-success py-2 w-100 d-flex justify-content-center";
            startBtn.classList.remove("hide");
            countInput.value = "";
            countInput.disabled = true; 
            if(saveBtn) saveBtn.classList.add("hide");
        });
    }
});

function updateTimerDisplay(ms) {
    let totalSeconds = Math.floor(ms / 1000);
    
    let minutes = Math.floor(totalSeconds / 60);
    let seconds = totalSeconds % 60;
    let centiseconds = Math.floor((ms % 1000) / 10); 
    
    let minsStr = String(minutes).padStart(2, '0');
    let secsStr = String(seconds).padStart(2, '0');
    let milliStr = String(centiseconds).padStart(2, '0');
    
    timerDisplay.textContent = `${minsStr}:${secsStr}:${milliStr}`;
}

document.getElementById("count_total_number_id").addEventListener("input", function (e) {
    let value = e.target.value.replace(/[^0-9]/g, '');  
    
    if (value !== '') {
        let numValue = parseInt(value, 10);
        if (numValue > 75) {
            value = '75'; 
        }
    }
    
    e.target.value = value;
    if (value !== '' && saveBtn && !isRunning) {
        saveBtn.classList.remove("hide"); 
    }
});

$(document).ready(function() {
    const formName = @json($TestTypeId);

    $(`#${formName}`).submit(function(e) {
        e.preventDefault();

        const studentId = document.getElementById('selected_student_id').value;
        const curlUpCount = document.getElementById('count_total_number_id')?.value;

        if (!studentId) {
            handleResponseMessages('warning', 'Add Student', 'Please select the student');
            return;
        }
        if (curlUpCount === null || curlUpCount === undefined || curlUpCount === "") {
            handleResponseMessages('warning', 'Add curl-up count', 'Please add curl-up count');
            return;
        }

        let route = '{{ route("cwsn.types.submit") }}';
        let formData = $(this).serialize();
        SubmitForm(formName, formData, route);
    });
});
</script>
@endsection