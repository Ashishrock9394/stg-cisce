@extends('assessor.cwsn.index')
@section('cwsnform')

@php
    $baseName = basename($__path);
    $formName = explode('.', $baseName)[0];
    $formId = $formName . '_form';
@endphp

@push('cwsn-style')
<style>

    @media only screen and (max-width: 600px) {
        .btn {  min-width: 80px; }
        #laps_completed{ width: 100px; }
    }

    @media only screen and (min-width: 600px) {
        .btn { min-width: 170px; }
        #laps_completed{ width: 140px; }
    }

    @media only screen and (min-width: 768px) {
        .btn {  min-width: 170px;}
        #laps_completed{ width: 140px; }
    }

   

    button.btn.btn-secondary.d-flex.align-items-center.justify-content-center.fw-bold.fs-3.user-select-none {
        border: 1px solid orange;
    }

    .btn-danger-stop {
        background-color: #ec0000 !important;
        border-color: #ec0000 !important;
        color: #fff !important;
    }
    
    .hide {
        pointer-events: none;   
        opacity: 0.6;    
        cursor: not-allowed;
    }
</style>
@endpush

@if($TestTypeId == 1043)
  <audio id="whistleSound" src="{{ asset('assets/audio/20m_pacer.mp3') }}" preload="auto"></audio>
@else
  <audio id="whistleSound" src="{{ asset('assets/audio/15-meter-pacer.mp3') }}" preload="auto"></audio>
@endif

<h3 class="mb-3 text-center">Total Completed Laps ({{ $title }})</h3>

<form class="row mt-0" method="POST" name="{{ $TestTypeId }}" id="{{ $TestTypeId }}" action="javascript:void(0);">
    {{ method_field('post') }}
    @csrf

    <input type="hidden" name="skillReportId" value="{{ $skillReportId }}" id="skillReportId">
    <input type="hidden" name="pwd_category_id" value="{{ $pwd_category_id ?? '' }}">
    <input type="hidden" name="TestTypeMasterID" value="{{ $TestTypeMasterID }}">
    <input type="hidden" name="SchoolId" id="SchoolId" value="{{ $SchoolId }}">
    <input type="hidden" name="student_id" id="selected_student_id">
    
    <input type="hidden" name="final_level" id="final_level" value="0">
    <input type="hidden" name="final_shuttle" id="final_shuttle" value="0">

    <div class="card border-0 bg-light col-12 p-2 d-flex flex-column align-items-center justify-content-center text-center">       
        
        <!-- Timer Display (mm:ss:SS) -->
        <div id="timer_display" class="mb-3 fw-bold text-dark" style="font-family: monospace; font-size: 1.5rem;">00:00:00</div>

        <div class="counter-container d-flex justify-content-center align-items-center w-100 mb-3">
            <div class="d-flex align-items-center justify-content-center" style="max-width: 280px; width: 100%;">
                <button type="button" id="minus_btn" onclick="changeLap(-1)" class="btn btn-secondary d-flex align-items-center justify-content-center fw-bold fs-3 user-select-none" style="width: 55px; height: 45px; font-size: 20px;">-</button>

                <input type="number" id="laps_completed" name="laps_completed" value="0" min="0" class="form-control text-center mx-3 fw-bolder fs-3 bg-light" readonly>

                <button type="button" id="plus_btn" onclick="changeLap(1)" class="btn btn-primary d-flex align-items-center justify-content-center fw-bold fs-3 user-select-none" style="width: 55px; height: 45px; font-size: 20px;">+</button>
            </div>
        </div>
        
        <div class="badge bg-light text-dark p-2 border mb-4" style="font-size: 14px;">
            Current State: <span class="fw-bold text-indigo" id="live_status_badge">Ready to Start</span>
        </div>

        <!-- Timer Action Controller -->
        <div class="w-100 d-flex justify-content-center">
            <a href="javascript:void(0)" id="startBtn" class="btn btn-success text-light py-2 w-100 d-flex justify-content-center align-items-center" style="gap: 10px;">
                <i class="bi bi-stopwatch fs-4"></i><span style="color: #fff;">Start Test</span>
            </a>
        </div>
    </div>

    @php $id = $TestTypeId; @endphp
    <x-reset-submit-btn :id="$id"/>
</form>

@push('cwsn-module-script')
<script>


const lapsInput = document.getElementById('laps_completed');
lapsInput.addEventListener('keydown', (e) => {
  if (['-', '+', 'e', 'E', '.'].includes(e.key)) {
    e.preventDefault();
  }
});
lapsInput.addEventListener('input', (e) => {
  let val = parseInt(e.target.value, 10);
  if (isNaN(val) || val < 0) {
    e.target.value = 0;
  }
});


const AUDIO_INTRO_OFFSET = 0;
const pacerMatrix = { 
    20 : {
        1: 7, 2: 8, 3: 8, 4: 9, 5: 9, 6: 10, 7: 10, 8: 11, 9: 11, 10: 11, 
        11: 12, 12: 12, 13: 13, 14: 13, 15: 13, 16: 14, 17: 14, 18: 15, 19: 15, 20: 16, 21: 16 
    },

    15 : { 1: 9, 2: 10, 3: 11, 4: 12, 5: 12, 6: 13, 7: 13, 8: 14, 9: 14, 10: 15, 11: 15, 12: 16, 13: 16, 
        14: 17, 15: 17, 16: 18, 17: 18, 18: 19, 19: 19, 20: 20, 21: 21 
    },
};

const pacerTiming = {
    20: {
        1: 9.00, 2: 8.00, 3: 7.58, 4: 7.20, 5: 6.86, 6: 6.55, 7: 6.26, 8: 6.00, 9: 5.76, 10: 5.54, 
        11: 5.33, 12: 5.14, 13: 4.97, 14: 4.80, 15: 4.65, 16: 4.50, 17: 4.36, 18: 4.24, 19: 4.11, 
        20: 4.00, 21: 3.89 
    },

    15: {
        1: 6.75, 2: 6.00, 3: 5.68, 4: 5.40, 5: 5.14, 6: 4.91, 7: 4.70, 8: 4.50, 9: 4.32, 10: 4.15, 
        11: 4.00, 12: 3.86, 13: 3.72, 14: 3.60, 15: 3.48, 16: 3.38, 17: 3.27, 18: 3.18, 19: 3.09, 
        20: 3.00, 21: 2.92
    }
};

const formName     = parseInt(@json($TestTypeId), 10);
const saveBtn      = document.getElementById(`submit_${formName}`);
const startBtn     = document.getElementById('startBtn');
const lapInput     = document.getElementById('laps_completed');
const timerDisplay = document.getElementById('timer_display');
const minusBtn     = document.getElementById('minus_btn');
const plusBtn      = document.getElementById('plus_btn');
const whistleAudio = document.getElementById('whistleSound');

const distanceMeters = (formName === 1043) ? 20 : 15;


// Generate array of cumulative completion timestamps for each shuttle
function buildShuttleTimestamps(distance) {
    let timestamps = [];
    let cumulativeTime = AUDIO_INTRO_OFFSET;

    for (let lvl = 1; lvl <= 21; lvl++) {
        let shuttlesInLevel = pacerMatrix[distance][lvl];
        let duration = pacerTiming[distance][lvl];

        for (let s = 1; s <= shuttlesInLevel; s++) {
            cumulativeTime += duration;
            timestamps.push(parseFloat(cumulativeTime.toFixed(2)));
        }
    }
    return timestamps;
}

const shuttleTimestamps = buildShuttleTimestamps(distanceMeters);

// Engine State variables
let isRunning = false;
let animationFrameId = null;
let completedLevel = 0;
let currentShuttle = 0;
let totalLapsCount = 0;

window.onload = function () {
    resetUI();
};

document.addEventListener("DOMContentLoaded", function () {
    startBtn.addEventListener("click", function() {
        if (!isRunning) {
            startPacerTest();
        } else { 
            stopPacerTest(false);
        }
    });

    const resetBtn = document.getElementById(`reset_${formName}`);
    if (resetBtn) {
        resetBtn.addEventListener("click", function() {
            resetUI();
        });
    }

    // Auto-stop if audio reaches the natural end
    if (whistleAudio) {
        whistleAudio.addEventListener('ended', function() {
            if (isRunning) {
                stopPacerTest(true);
            }
        });
    }
});

function startPacerTest() {
    isRunning = true;
    totalLapsCount = 0;
    lapInput.value = 0;

    startBtn.innerHTML = '<i class="bi bi-stopwatch fs-4"></i><span>Stop Test</span>';
    startBtn.classList.remove("btn-success");
    startBtn.classList.add("btn-danger-stop");

    minusBtn.classList.add("hide");
    plusBtn.classList.add("hide");
    if (saveBtn) saveBtn.classList.add("hide");

    // Play continuous full PACER audio track
    whistleAudio.currentTime = 0;
    whistleAudio.play().catch(err => {
        console.error("Audio playback error:", err);
    });

    // Start high-precision sync loop
    syncAudioEngine();
}

// Master Audio Sync Loop using requestAnimationFrame
function syncAudioEngine() {
    if (!isRunning) return;

    const currentSec = whistleAudio.currentTime;

    // 1. Calculate how many shuttles are completed based on audio timestamp
    let lapsCompletedSoFar = 0;
    for (let i = 0; i < shuttleTimestamps.length; i++) {
        if (currentSec >= shuttleTimestamps[i]) {
            lapsCompletedSoFar = i + 1;
        } else {
            break;
        }
    }

    // 2. Update state only when a new shuttle is crossed
    if (lapsCompletedSoFar !== totalLapsCount) {
        totalLapsCount = lapsCompletedSoFar;
        lapInput.value = totalLapsCount;
        updateLevelAndShuttle(totalLapsCount);
    }

    // 3. Update timer display from audio clock
    updateTimerFromSeconds(currentSec);

    // 4. Check max level completion
    if (totalLapsCount >= shuttleTimestamps.length) {
        stopPacerTest(true);
        return;
    }

    animationFrameId = requestAnimationFrame(syncAudioEngine);
}

function stopPacerTest(hitMaxCeiling = false) {
    isRunning = false;
    if (animationFrameId) cancelAnimationFrame(animationFrameId);

    if (whistleAudio) {
        whistleAudio.pause();
    }

    startBtn.classList.add("hide");
    minusBtn.classList.remove("hide");
    plusBtn.classList.remove("hide");

    saveBtn.classList.remove("hide");

    /*if (totalLapsCount > 0 && saveBtn) {
        saveBtn.classList.remove("hide");
    }*/

    if (hitMaxCeiling) {
        startBtn.innerHTML = '<span>Max PACER Capacity Met!</span>';
        startBtn.className = "btn btn-warning py-2 w-100 d-flex justify-content-center text-dark";
    } else {
        startBtn.innerHTML = '<span style="color:white">Test Stopped</span>';
        startBtn.className = "btn btn-success py-2 w-100 d-flex justify-content-center disabled";
    }
}

function resetUI() {
    isRunning = false;
    if (animationFrameId) cancelAnimationFrame(animationFrameId);

    if (whistleAudio) {
        whistleAudio.pause();
        whistleAudio.currentTime = 0;
    }

    completedLevel = 0;
    currentShuttle = 0;
    totalLapsCount = 0;

    timerDisplay.textContent = "00:00:00";
    lapInput.value = 0;

    document.getElementById('final_level').value = 0;
    document.getElementById('final_shuttle').value = 0;
    document.getElementById('live_status_badge').textContent = 'Ready to Start';

    startBtn.innerHTML = '<i class="bi bi-stopwatch fs-4"></i><span style="color:white">Start Test</span>';
    startBtn.className = "btn btn-success py-2 w-100 d-flex justify-content-center align-items-center";
    startBtn.classList.remove("hide");

    minusBtn.classList.remove("hide");
    plusBtn.classList.remove("hide");

    if (saveBtn) saveBtn.classList.add("hide");
}

function updateTimerFromSeconds(seconds) {
    let minutes = Math.floor(seconds / 60);
    let secs = Math.floor(seconds % 60);
    let centiseconds = Math.floor((seconds % 1) * 100);

    let minsStr = String(minutes).padStart(2, '0');
    let secsStr = String(secs).padStart(2, '0');
    let milliStr = String(centiseconds).padStart(2, '0');

    timerDisplay.textContent = `${minsStr}:${secsStr}:${milliStr}`;
}

function changeLap(val) {
    let current = parseInt(lapInput.value, 10) || 0;
    current += val;

    if (current >= 0) {
        lapInput.value = current;
        totalLapsCount = current;
        updateLevelAndShuttle(current);

        if (current > 0 && saveBtn && !isRunning) {
            saveBtn.classList.remove("hide");
        }
    }
}

function updateLevelAndShuttle(totalLaps) {
    let doneLevel = 0;
    let extraShuttles = 0;
    let accumulated = 0;

    if (totalLaps > 0) {
        const matrix = pacerMatrix[distanceMeters];

        for (let lvl = 1; lvl <= 21; lvl++) {
            let countInLevel = matrix[lvl];

            if (totalLaps >= accumulated + countInLevel) {
                doneLevel = lvl;
                accumulated += countInLevel;
                extraShuttles = 0;
            } else {
                extraShuttles = totalLaps - accumulated;
                break;
            }
        }
    }

    completedLevel = doneLevel;
    currentShuttle = extraShuttles;

    document.getElementById('final_level').value = completedLevel;
    document.getElementById('final_shuttle').value = currentShuttle;

    const badge = document.getElementById('live_status_badge');
    if (totalLaps === 0) {
        badge.textContent = 'Ready to Start';
    } else if (extraShuttles === 0) {
        badge.textContent = `Level ${completedLevel} Completed`;
    } else {
        badge.textContent = `Level ${completedLevel} Completed, Shuttle ${extraShuttles}`;
    }
}



$(document).ready(function() {
    $(`#${formName}`).submit(function(e) {
        e.preventDefault();

        const studentId = document.getElementById('selected_student_id').value;
        if(!studentId){
            handleResponseMessages('warning', 'Select Student', 'Please select the student');
            return;
        }
        
        const finalMmInput = $('input[name="laps_completed"]').val();
        if (finalMmInput === '' || finalMmInput === null || undefined === finalMmInput) {
            handleResponseMessages('info', '', 'Please enter position of the student');
            return;
        }

        let route = '{{ route("cwsn.types.submit") }}';
        let formData = $(this).serialize();
        SubmitForm(formName, formData, route);
        document.getElementById('live_status_badge').textContent = `Ready to Start`;
    });
});


</script>
@endpush
@endsection