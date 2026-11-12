@extends('assessor.cwsn.index')
@section('cwsnform')

<form class="row" method="POST" name="{{ $TestTypeId }}" id="{{ $TestTypeId }}" action="javascript:void(0);">
    {{ method_field('POST') }}
    @csrf
    
    <input type="hidden" name="skillReportId" value="{{ $skillReportId }}" id="skillReportId">
    <input type="hidden" name="TestTypeMasterID" value="{{ $TestTypeMasterID }}">
    <input type="hidden" name="SchoolId" id="SchoolId" value="{{ $SchoolId }}">
    <input type="hidden" name="student_id" id="selected_student_id">
    <input type="hidden" name="trunk_lift" id="result" readonly>
    
    <div class="col-12">
        <div class="form">
            <h2 class="mb-2 mt-4 text-center">{{ $title }} Test Score</h2>
        </div>
    </div>
    
    <div class="col-12 col-md-6">
        <div class="form">
            <h3 class="mb-2 mt-4 text-left label">Trial 1</h3>
            <div class="input-group input-group__2 mb-3">
                <span class="form-control">
                    <label for="initial_cm_id" class="form-label">Cms</label>
                    <input type="text" name="initial_cm" class="form-control form-control-lg score-input" id="initial_cm_id" placeholder="00">
                </span>
                <span class="form-control">
                    <label for="initial_mm_id" class="form-label">mm</label>
                    <input type="text" name="initial_mm" class="form-control form-control-lg score-input" id="initial_mm_id" placeholder="00">
                </span>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <div class="form">
            <h3 class="mb-2 mt-4 text-left label">Trial 2</h3>
            <div class="input-group input-group__2 mb-3">
                <span class="form-control">
                    <label for="final_cm_id" class="form-label">Cms</label>
                    <input type="text" name="final_cm" class="form-control form-control-lg score-input" id="final_cm_id" placeholder="00">
                </span>
                <span class="form-control">
                    <label for="final_mm_id" class="form-label">mm</label>
                    <input type="text" name="final_mm" class="form-control form-control-lg score-input" id="final_mm_id" placeholder="00">
                </span>
            </div>
            <div>        
                <span id="best_score" style="display:none"><b>Best Score:</b> <span id="final_result_id"></span></span>
            </div>
        </div>
    </div>

    <x-reset-submit-btn :id="$TestTypeId"/>
</form>

<script>
function getTotalInMm(cm, mm) {
    return (parseInt(cm, 10) || 0) * 10 + (parseInt(mm, 10) || 0);
}

function calculateScore() {
    let initialCm = document.getElementById("initial_cm_id").value;
    let initialMm = document.getElementById("initial_mm_id").value;
    let finalCm = document.getElementById("final_cm_id").value;
    let finalMm = document.getElementById("final_mm_id").value;

    // Return safely if no values have been provided yet
    if (!initialCm && !initialMm && !finalCm && !finalMm) {
        document.getElementById("best_score").style.display = "none";
        document.getElementById("result").value = "";
        return 0;
    }

    let initialTotalMm = getTotalInMm(initialCm, initialMm);
    let finalTotalMm = getTotalInMm(finalCm, finalMm);

    // Dynamic max comparison
    let maxTotalMm = Math.max(initialTotalMm, finalTotalMm);

    let resultCm = Math.floor(maxTotalMm / 10);
    let resultMm = maxTotalMm % 10;

    document.getElementById("best_score").style.display = "block";
    document.getElementById("result").value = maxTotalMm;
    document.getElementById("final_result_id").innerHTML = `${resultCm}cm, ${resultMm}mm`;

    return maxTotalMm;
}

// FIXED: Added partnerMmId to parameter definition
function validateCmInput(element, partnerMmId) {
    let value = element.value.replace(/[^0-9]/g, ''); 
    
    if (value.length > 2) {
        value = value.slice(0, 2);
    }
    
    // Max cap fixed to 30 cm
    if (value !== '' && parseInt(value, 10) > 30) {
        value = '30';
    }
    
    element.value = value;

    if (value === '30') {
        const mmElement = document.getElementById(partnerMmId);
        if (mmElement && mmElement.value !== '' && mmElement.value !== '0') {
            mmElement.value = '0';
        }
    }
}

function validateMmInput(element, partnerCmId) {
    let value = element.value.replace(/[^0-9]/g, ''); 
    
    if (value.length > 1) {
        value = value.slice(0, 1);
    }

    const cmValue = document.getElementById(partnerCmId).value;
    if (cmValue === '30' && value !== '' && value !== '0') {
        value = '0';
    }

    element.value = value;
}


$(document).ready(function() {
    const formName = String(@json($TestTypeId));

    // Handle initial inputs tracking
    $(document).on('input', '#initial_cm_id', function() {
        validateCmInput(this, "initial_mm_id"); // FIXED: Passed partner ID here
        calculateScore();
    });
    $(document).on('input', '#initial_mm_id', function() {
        validateMmInput(this, "initial_cm_id");
        calculateScore();
    });

    // Handle final inputs tracking
    $(document).on('input', '#final_cm_id', function() {
        validateCmInput(this, "final_mm_id"); // FIXED: Passed partner ID here
        calculateScore();
    });
    $(document).on('input', '#final_mm_id', function() {
        validateMmInput(this, "final_cm_id");
        calculateScore();
    });
    

    // Handle Form Form Submissions
    $(`#${formName}`).submit(function(e) {
        e.preventDefault();

        const studentId = document.getElementById('selected_student_id').value;
        const initialCm = $('#initial_cm_id').val().trim();
        const initialMm = $('#initial_mm_id').val().trim();
        const finalCm = $('#final_cm_id').val().trim();
        const finalMm = $('#final_mm_id').val().trim();

        if (!studentId) {
            handleResponseMessages('info', 'Select Student', 'Please select the student');
            return;
        }

        const initialProvided = initialCm !== '' || initialMm !== '';
        const finalProvided = finalCm !== '' || finalMm !== '';

        if (!initialProvided && !finalProvided) {
            handleResponseMessages('info', '', 'Please enter at least one trial measurement position.');
            return;
        }

        let route = '{{ route("cwsn.types.submit") }}';
        let formData = $(this).serialize();
        
        SubmitForm(formName, formData, route);
    });
});


</script>
@endsection