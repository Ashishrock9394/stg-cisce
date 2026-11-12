@extends('assessor.cwsn.index')
@section('cwsnform')

@php 
    $isTimerTest = ($TestTypeId == '1021' || $TestTypeId == '1023');
@endphp

<form class="row" method="POST" name="{{ $TestTypeId }}" id="{{ $TestTypeId }}" action="javascript:void(0);">
    {{ method_field('post') }}
    @csrf     
    
    <input type="hidden" name="skillReportId" value="{{ $skillReportId }}">
    <input type="hidden" name="TestTypeMasterID" value="{{ $TestTypeMasterID }}">
    <input type="hidden" id="SchoolId" name="SchoolId" value="{{ $SchoolId }}">
    <input type="hidden" id="selected_student_id" name="student_id">
    
    <div class="col-12">
        <div class="row form mb-4">
            <h3 class="mb-3 text-center">{{ $title }} Score</h3>  
            <div class="input-group mb-3 text-center">
                <span class="form-control single-input">
                    <label for="pushUpCount" class="form-label">Counts</label>
                    {{-- Input disabled by default ONLY if it is a timer-based test --}}
                    <input type="text" name="total_push_up" class="form-control form-control-lg text-center" id="pushUpCount" placeholder="00" {{ $isTimerTest ? 'disabled' : '' }}>
                </span>
            </div>

            @if($isTimerTest)
                <div id="timer" class="mt-0 mb-3 text-center" style="font-family: monospace; font-size: 1.5rem;">00:00:00</div>
                <div class="actions">
                    <a id="startPauseBtn" href="javascript:void(0);" class="btn btn-success py-2 w-100 d-flex justify-content-center" style="gap: 10px;">
                        <i class="bi bi-stopwatch"></i><span>Start Timer</span>
                    </a>
                </div>
            @endif
        </div>
    </div> 
    @php $id = "pushups"; @endphp
    <x-reset-submit-btn :id="$TestTypeId"/>

</form>

<script>
const TestTypeId = parseInt(@json($TestTypeId));
const isTimerTest = @json($isTimerTest);

const saveBtn = document.getElementById(`submit_${TestTypeId}`);
const display = document.getElementById("timer");
const pushUpInput = document.getElementById("pushUpCount");
const startPauseBtn = document.getElementById("startPauseBtn");

let startTime = 0;
let elapsed = 0;
let timerInterval = null;
let running = false;

let beepInterval = null;
let audioCtx = null;

let cadenceInterval = null;
let currentRep = 0;
const CADENCE_MAX_REPS = 50;

const synth = window.speechSynthesis;

// Lock save button initially for timer tests
if (isTimerTest && saveBtn) {
    saveBtn.classList.add("disabled");
    $(saveBtn).css("background-color", "#ff7800");
}

function autoStopTest() {
    if (!running || !isTimerTest) return;

    // Halt stopwatch timer and interval loops
    clearInterval(timerInterval);
    if (TestTypeId === 1021) {
        clearInterval(cadenceInterval);
        cadenceInterval = null;
    } else if (TestTypeId === 1023) {
        stopAndResetBeep();
    }

    if (pushUpInput) {
        pushUpInput.disabled = false;
        pushUpInput.focus();
    }

    elapsed += Date.now() - startTime;
    
    if (startPauseBtn) {
        startPauseBtn.innerHTML = '<i class="bi bi-stopwatch"></i><span>Test Completed!</span>';
        startPauseBtn.classList.add("disabled");
        startPauseBtn.style.pointerEvents = "none";
    }

    if (saveBtn) {
        saveBtn.classList.remove("disabled");
    }

    running = false;
}

// Cadence logic (TestTypeId 1021): Plays spoken number every 4 seconds, caps at 50
function playCadence() {
    currentRep++;
    
    if (pushUpInput) {
        pushUpInput.value = currentRep;
    }

    const phrase = `${currentRep}`;
    const utterance = new SpeechSynthesisUtterance(phrase);
    utterance.rate = 1.0;  
    utterance.pitch = 1.0; 
    utterance.lang = 'en-US';

    synth.speak(utterance);

    // Stop timer and interval loop immediately at count 50 without cancelling audio
    if (currentRep >= CADENCE_MAX_REPS) {
        autoStopTest();
    }
}

function stopAndResetCadence() {
    clearInterval(cadenceInterval);
    cadenceInterval = null;
    if (synth.speaking || synth.pending) {
        synth.cancel();
    }
}

// Beep logic (TestTypeId 1023): Plays audio tone every 3 seconds (no rep limit)
function playBeep() {
    currentRep++;

    if (pushUpInput) {
        pushUpInput.value = currentRep;
    }

    if (audioCtx) {
        if (audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
        
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();

        osc.type = 'square';
        osc.frequency.setValueAtTime(1100, audioCtx.currentTime);

        const duration = 0.8; 
        gain.gain.setValueAtTime(0.8, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.00001, audioCtx.currentTime + duration); 

        osc.connect(gain);
        gain.connect(audioCtx.destination);

        osc.start();
        osc.stop(audioCtx.currentTime + 0.15);
    }
}

function stopAndResetBeep() {
    clearInterval(beepInterval);
    beepInterval = null;
}

function stopAllTimers() {
    if (!isTimerTest) return;
    clearInterval(timerInterval);
    stopAndResetBeep();
    stopAndResetCadence();
}

function updateTimer() {
    if (!display) return;
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

function resetTest() {
    currentRep = 0;
    if (isTimerTest) {
        stopAllTimers();

        startTime = 0;
        elapsed = 0;
        running = false;

        if (display) {
            display.innerHTML = "00:00:00";
        }

        if (pushUpInput) {
            pushUpInput.value = "";
            pushUpInput.disabled = true;
        }

        if (startPauseBtn) {
            startPauseBtn.innerHTML = '<i class="bi bi-stopwatch"></i><span>Start Timer</span>';
            startPauseBtn.classList.remove("disabled");
            startPauseBtn.style.pointerEvents = "auto";
            startPauseBtn.style.opacity = "1";
        }

        if (saveBtn) {
            saveBtn.classList.add("disabled");
        }
    } else {
        if (pushUpInput) {
            pushUpInput.value = "";
        }
    }
}

// Timer event listener
if (isTimerTest && startPauseBtn) {
    startPauseBtn.addEventListener("click", function () {
        if (!running) {
            if (!audioCtx) {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }

            currentRep = 0;

            if (pushUpInput) {
                pushUpInput.disabled = true;
                pushUpInput.value = ""; 
            }

            startTime = Date.now();
            timerInterval = setInterval(updateTimer, 10);

            if (TestTypeId === 1021) {
                cadenceInterval = setInterval(playCadence, 4000); // 1st count at 00:04:00
            } else if (TestTypeId === 1023) {
                beepInterval = setInterval(playBeep, 3000);    // 1st count at 00:03:00
            }

            startPauseBtn.innerHTML = '<i class="bi bi-stopwatch"></i><span>Stop Timer</span>';
            if (saveBtn) {
                saveBtn.classList.add("disabled");
            }
            running = true;
        } else {
            autoStopTest();
        }
    });   
}

// Numeric input sanitizer
if (pushUpInput) {
    pushUpInput.addEventListener("input", function (e) {
        let value = e.target.value.replace(/[^0-9]/g, ''); 
        let match = value.match(/^(\d{0,3})?$/);
        value = match ? match[0] : value.slice(0, -1);
        
        let numVal = parseInt(value, 10);
        
        // Capped at 50 ONLY if it's Cadence (1021)
        if (TestTypeId === 1021 && !isNaN(numVal) && numVal > CADENCE_MAX_REPS) {
            value = CADENCE_MAX_REPS.toString();
        }

        e.target.value = value;
    });
}

// Form submit and reset handlers
$(document).ready(function() {
    const formName = @json($TestTypeId);

    $(`#${formName}`).submit(function(e) {
        e.preventDefault();

        const studentId = document.getElementById('selected_student_id').value;
        const pushUps = pushUpInput.value;

        if(!studentId){
            handleResponseMessages('warning', 'Select Student', 'Please select the student');
            return;
        }
        
        if (pushUps === "") {
            handleResponseMessages('warning', 'Push up empty', 'Please enter the push ups count');
            return;
        }

        if (isTimerTest) {
            stopAllTimers();
        }

        let route = '{{ route("cwsn.types.submit") }}';
        let formData = $(this).serialize();
        SubmitForm(formName, formData, route);
    });

    const resetBtn = document.getElementById(`reset_${formName}`);
    if (resetBtn) {
        resetBtn.addEventListener("click", resetTest);
    }
});
</script>
@endsection