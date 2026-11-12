@extends('assessor.cwsn.index')

@section('cwsnform')

@push('cwsn-style')
<style>
    .all-chaptr-cards .card {
        min-height: 0px !important;
    }
    button#stopwatch_btn.btn-danger {
        background-color: #ff0000;
        color: #fff !important;
    }


</style>
@endpush

<h2 class="mb-4 text-center">{{ $title }} Score</h2>

<form class="form row" method="POST" name="{{ $TestTypeId }}" id="{{ $TestTypeId }}" action="javascript:void(0);">
    {{ method_field('POST') }}
    @csrf     
    
    <input type="hidden" name="skillReportId" value="{{ $skillReportId }}">
    <input type="hidden" name="TestTypeMasterID" value="{{ $TestTypeMasterID }}">
    <input type="hidden" id="SchoolId" name="SchoolId" value="{{ $SchoolId }}">
    <input type="hidden" id="selected_student_id" name="student_id">
    <input type="hidden" name="timeTaken" id="timeTaken">

    <!-- Hidden state management for backend submission -->
    <input type="hidden" name="assessment_result" id="assessment_result" value="">
    <input type="hidden" name="heart_rate_status" id="heart_rate_status" value="">
    <input type="hidden" name="mobility_mode" id="mobility_mode" value="">

    <!-- 1. MOBILITY MODE SELECTION -->
    <div class="card border-0 bg-light p-3 mb-3 text-center shadow-sm" style="border-radius: 12px;">
        <h4 class="font-weight-bold text-uppercase mb-3" style="font-size: 1rem;">1. Select Mobility Type</h4>
        <div class="btn-group w-100" role="group">
            <button type="button" id="btn_mode_legs" class="btn btn-outline-secondary py-2 fw-bold w-50" style="border-top-left-radius: 10px; border-bottom-left-radius: 10px;"> Propelled by Leg
            </button>
            <button type="button" id="btn_mode_arms" class="btn btn-outline-secondary py-2 fw-bold w-50" style="border-top-right-radius: 10px; border-bottom-right-radius: 10px;"> Propelled by Arms
            </button>
        </div>
    </div>

    <!-- 2. COUNTUP & PULSE ASSESSMENT CONTAINER -->
    <div class="card border-0 bg-light text-center shadow-sm p-3 mb-3" style="border-radius: 12px;">

        <div class="input-group mb-3 text-center">
            <span class="form-control single-input">
                <label for="pulse_rate" class="form-label pulse_count_info">Post-Test 10-Sec Pulse Count</label>
                <div class="w-100">
                    <input type="text" id="pulse_rate" name="pulse_rate" class="form-control form-control-lg text-center fw-bold mx-auto" placeholder="00" min="1" max="99" maxlength="2" pattern="[0-9]{1,2}" oninput="if(this.value.length > 2) this.value = this.value.slice(0, 2);" disabled>
                </div>
            </span>
        </div>

        <!-- Stopwatch Display -->
        <div id="stopwatch_display" class="text-center mt-2 mb-3 fw-bold" style="font-family: monospace; font-size: 1.7rem;">00:00</div>    
        
        <div class="actions mb-3">
            <button id="stopwatch_btn" data-state="start" class="btn btn-success py-3 mx-auto w-100 d-flex justify-content-center align-items-center fw-bold gap-2" style="border-radius: 12px;">
                <i class="bi bi-play-fill" id="timer_icon"></i>
                <span id="timer_btn_text">Start Timer</span>
            </button>
        </div>
    </div>

    <!-- 3. ACTION LAYER -->
    <div>
        <footer class="container-fluid position-fixed bg-white p-0" style="bottom: 0; left: 0; right: 0; z-index: 100; border-top: 1px solid #e9ecef;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="action-bar py-3 p-0 d-flex justify-content-between">
                            <button type="button" id="reset_{{$TestTypeId}}" class="btn py-2 px-5 btn-outline-secondary">Reset</button>  
                            <button type="submit" id="submit_{{$TestTypeId}}" class="btn py-2 px-5 btn-primary disabled" disabled>Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

</form>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const formId = @json($TestTypeId);
    
    // Hidden state inputs
    const hiddenResult = document.getElementById('assessment_result');
    const hiddenTimeTaken = document.getElementById('timeTaken');
    const hiddenHr = document.getElementById('heart_rate_status');
    const hiddenMobilityMode = document.getElementById('mobility_mode');
    const pulseInput = document.getElementById('pulse_rate');
    
    // UI Elements
    const btnModeLegs = document.getElementById('btn_mode_legs');
    const btnModeArms = document.getElementById('btn_mode_arms');
    const pulseThresholdHint = document.getElementById('pulse_threshold_hint');

    const submitBtn = document.getElementById(`submit_${formId}`);

    if (submitBtn) {
        submitBtn.classList.add("disabled");
        $(submitBtn).css("background-color", "#ff7800");
    }

    const timerBtn = document.getElementById('stopwatch_btn');
    const timerDisplay = document.getElementById('stopwatch_display');
    const timerIcon = document.getElementById('timer_icon');
    const timerBtnText = document.getElementById('timer_btn_text');

    let timerInterval = null;
    let startTime = 0;
    let elapsedMs = 0;
    
    // PASS/FAIL threshold set to exactly 60 seconds
    const PASS_CUTOFF_MS = 60000; 
    const AUTO_STOP_MS = 60000;   // Hard stop at 60:00

    let selectedMode = null; // 'legs' or 'arms'

    // Helper to toggle submit button state
    function toggleSubmitButton(enable) {
        if (!submitBtn) return;
        submitBtn.disabled = !enable;
        if (enable) {
            submitBtn.classList.remove('disabled');
        } else {
            submitBtn.classList.add('disabled');
            $(submitBtn).css("background-color", "#ff7800");
        }
    }

    // Helper to toggle pulse rate input field
    function togglePulseInput(enable) {
        if (!pulseInput) return;
        pulseInput.disabled = !enable;
    }

    // --- Mode Selection Logic ---
    function setMobilityMode(mode) {
        selectedMode = mode;
        hiddenMobilityMode.value = mode;
        
        if (mode === 'legs') {
            btnModeLegs.className = "btn btn-primary py-2 fw-bold w-50";
            btnModeArms.className = "btn btn-outline-secondary py-2 fw-bold w-50";
        } else {
            btnModeLegs.className = "btn btn-outline-secondary py-2 fw-bold w-50";
            btnModeArms.className = "btn btn-primary py-2 fw-bold w-50";
        }
    }

    btnModeLegs.addEventListener('click', () => setMobilityMode('legs'));
    btnModeArms.addEventListener('click', () => setMobilityMode('arms'));

    function clearSelectionStates() {
        hiddenResult.value = "";
        hiddenHr.value = "";
        pulseInput.value = "";
    }

    // --- Format Countup Time (00:00, 60:00, etc.) ---
    function formatCountUpTime(ms) {
        let totalSeconds = ms / 1000;
        let seconds = Math.floor(totalSeconds);
        let hundredths = Math.floor((totalSeconds % 1) * 100);
        
        let displaySecs = seconds < 10 ? '0' + seconds : seconds;
        let displayMs = hundredths < 10 ? '0' + hundredths : hundredths;
        
        return displaySecs + ':' + displayMs;
    }

    function resetTimerUI() {
        clearInterval(timerInterval);
        elapsedMs = 0;
        hiddenTimeTaken.value = "";
        timerDisplay.innerText = "00:00";
        timerDisplay.className = "mt-2 mb-3 text-center fw-bold";
        
        // Re-enable and reset button back to "Start Timer"
        timerBtn.disabled = false;
        timerBtn.setAttribute('data-state', 'start');
        timerBtnText.innerText = "Start Timer";
        timerIcon.className = "bi bi-play-fill";
        timerBtn.className = "btn btn-success py-3 mx-auto w-100 d-flex justify-content-center align-items-center fw-bold gap-2";

        // Disable Save button & Pulse Input on reset
        toggleSubmitButton(false);
        togglePulseInput(false);
    }

    // --- Timer Controls & Logic ---
    timerBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        const currentState = timerBtn.getAttribute('data-state');

        // State 1: User clicks "Start Timer"
        if (currentState === 'start') {
            if (!selectedMode) {
                if (typeof handleResponseMessages === 'function') {
                    handleResponseMessages('warning', 'Select Mobility Type', 'Please select Walk/Legs or Wheelchair/Arms before starting the timer.');
                } else {
                    alert('Please select Walk/Legs or Wheelchair/Arms before starting the timer.');
                }
                return;
            }

            // Disable Save button and Pulse Input while timer is running
            toggleSubmitButton(false);
            togglePulseInput(false);

            // Transition to Running state
            timerBtn.setAttribute('data-state', 'running');
            timerBtnText.innerText = "Stop Timer";
            timerIcon.className = "bi bi-pause-fill";
            timerBtn.className = "btn btn-danger py-3 mx-auto w-100 d-flex justify-content-center align-items-center fw-bold gap-2";
            
            startTime = performance.now() - elapsedMs;
            
            timerInterval = setInterval(() => {
                elapsedMs = performance.now() - startTime;
                
                // If timer reaches 60.00s -> Hard Failure Stop
                if (elapsedMs >= AUTO_STOP_MS) {
                    clearInterval(timerInterval);
                    elapsedMs = AUTO_STOP_MS;
                    hiddenTimeTaken.value = elapsedMs;

                    timerDisplay.innerText = "60:00";
                    timerDisplay.className = "mt-2 mb-3 text-center fw-bold text-danger";
                    
                    timerBtn.setAttribute('data-state', 'failed');
                    timerBtnText.innerHTML = "&nbsp; Time's Up!";
                    timerIcon.className = "bi bi-hourglass-bottom";
                    timerBtn.className = "btn btn-secondary py-3 mx-auto w-100 d-flex justify-content-center align-items-center fw-bold gap-2";
                    timerBtn.disabled = true; // Lock button
                    
                    // Mark test as failed
                    hiddenResult.value = "0"; 
                    hiddenHr.value = "0";

                    $('.pulse_count_info').hide();

                    pulseInput.value = "Failed !";
                    pulseInput.style.color = "red"; 
                    pulseInput.style.setProperty('padding', '.375rem .75rem', 'important');               
                    pulseInput.classList.add('outcome_failed');

                    // Enable Save button & Pulse Input when timer auto-stops
                    toggleSubmitButton(true);
                    togglePulseInput(false);
                } else {
                    timerDisplay.innerText = formatCountUpTime(elapsedMs);
                }
            }, 10);
        } 
        // State 2: User stops the timer manually
        else if (currentState === 'running') {
            clearInterval(timerInterval);
            hiddenTimeTaken.value = Math.round(elapsedMs);
            
            // Enable Pulse Input field when timer is stopped
            togglePulseInput(true);

            // Check if stopped within allowable 60-second window (<= 60.00s)
            if (elapsedMs <= PASS_CUTOFF_MS) {
                timerBtn.setAttribute('data-state', 'completed');
                timerBtnText.innerHTML = "&nbsp; Test Completed";
                timerIcon.className = "bi bi-check-circle-fill";
                timerBtn.className = "btn btn-success py-3 mx-auto w-100 d-flex justify-content-center align-items-center fw-bold gap-2";
                
                hiddenResult.value = "1";
                pulseInput.focus();
            } else {
                // Stopped past 60.00s manually
                timerBtn.setAttribute('data-state', 'failed');
                timerBtnText.innerText = "Test Failed";
                timerIcon.className = "bi bi-x-circle-fill";
                timerBtn.className = "btn btn-secondary py-3 mx-auto w-100 d-flex justify-content-center align-items-center fw-bold gap-2";
                
                hiddenResult.value = "0";
                hiddenHr.value = "0";
            }

            timerBtn.disabled = true; // Lock button until Reset

            // Enable Save button when test is manually stopped
            toggleSubmitButton(true);
        }
    });

    pulseInput.addEventListener('keydown', function(e) {
        if (['e', 'E', '+', '-', '.'].includes(e.key)) {
            e.preventDefault();
        }
    });

    pulseInput.addEventListener('input', function() {
        if (this.value.length > 2) {
            this.value = this.value.slice(0, 2);
        }
    });

    // Reset button handler
    document.getElementById(`reset_${formId}`).addEventListener('click', function() {
        resetTimerUI();
        clearSelectionStates();
        selectedMode = null;
        hiddenMobilityMode.value = "";
        btnModeLegs.className = "btn btn-outline-secondary py-2 fw-bold w-50";
        btnModeArms.className = "btn btn-outline-secondary py-2 fw-bold w-50";
    });

    $(`#${formId}`).submit(function(e) {
        e.preventDefault();

        const studentId = document.getElementById('selected_student_id').value;
        const assessment = hiddenResult.value;

        if (!studentId) {
            handleResponseMessages('warning', 'Select Student', 'Please select a student.');
            return;
        }

        if (!selectedMode) {
            handleResponseMessages('warning', 'Select Mobility Type', 'Please select the mobility mode (Legs or Arms).');
            return;
        }

        if (assessment === '') {
            handleResponseMessages('info', 'Incomplete Test', 'Please start and stop the timer assessment.');
            return;
        }

        // Validate pulse entry ONLY IF the student passed the test
        if (assessment === "1") {
            const valStr = pulseInput.value.trim();
            const val = parseInt(valStr, 10);

            if (!valStr || isNaN(val) || val <= 0 || valStr.length > 2) {
                if (typeof handleResponseMessages === 'function') {
                    handleResponseMessages('warning', 'Invalid Pulse Entry', 'Please enter a valid 1 to 2-digit pulse count.');
                } else {
                    alert('Please enter a valid 1 to 2-digit pulse count.');
                }
                pulseInput.focus();
                return;
            }

            // Evaluate heart_rate_status automatically
            const maxThreshold = (selectedMode === 'legs') ? 20 : 19;
            const isAcceptable = val <= maxThreshold;
            hiddenHr.value = isAcceptable ? "1" : "0";
        }

        let route = '{{ route("cwsn.types.submit") }}';
        let formData = $(this).serialize();
        SubmitForm(formId, formData, route);
        
        const badge = document.getElementById('live_status_badge');
        if (badge) {
            badge.textContent = `Level 1, Shuttle 0`;
        }
    });

});
</script>

@endsection