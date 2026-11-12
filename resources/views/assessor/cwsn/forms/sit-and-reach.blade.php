@extends('assessor.cwsn.index')
@section('cwsnform')

<style>
    h4.text-uppercase {
        color: #292775 !important;
    }
</style>

<h2 class="text-center pb-2">{{ $title }} Score</h2>

<!-- Wrap everything inside a single master form submission to capture both scores simultaneously -->
<form method="POST" name="{{ $TestTypeId }}" id="{{ $TestTypeId }}" action="javascript:void(0);">
    @csrf
    
    <input type="hidden" name="skillReportId" value="{{ $skillReportId }}" id="skillReportId">
    <input type="hidden" name="TestTypeMasterID" value="{{ $TestTypeMasterID }}">
    <input type="hidden" name="SchoolId" id="SchoolId" value="{{ $SchoolId }}">
    <input type="hidden" name="student_id" id="selected_student_id">
    
    <!-- Distinct fields containing the calculated final float values (in cm) sent to backend database rows -->
    <input type="hidden" name="score_left" id="score_left" value="">
    <input type="hidden" name="score_right" id="score_right" value="">

    <div class="row mx-n2">
        <!-- ==================== LEFT LEG COLUMN ==================== -->
        <div class="col-12 col-md-6 px-2 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <h4 class="text-center text-primary text-uppercase font-weight-bold" style="font-size: 1.1rem;">Left Leg Evaluation</h4>
                    
                    <div class="row mt-2 justify-content-center">
                        <!-- Left Final Position Grid -->
                        <div class="col-8">
                            <h5 class="mb-2 text-center text-muted font-weight-bold" style="font-size:1.0rem;">Final Position</h5>
                            <div class="row no-gutters">
                                <div class="col-6 px-1">
                                    <label for="left_final_cm" class="small font-weight-bold text-muted mb-1 d-block text-center">Cms</label>
                                    <input type="text" name="left_final_cm" onkeyup="calculateLegScore('left')" class="form-control text-center font-weight-bold" id="left_final_cm" placeholder="00" inputmode="numeric" style="font-size: 1.25rem;">
                                </div>
                                <div class="col-6 px-1">
                                    <label for="left_final_mm" class="small font-weight-bold text-muted mb-1 d-block text-center">mm</label>
                                    <input type="text" name="left_final_mm" onkeyup="calculateLegScore('left')" class="form-control text-center font-weight-bold" id="left_final_mm" placeholder="0" inputmode="numeric" style="font-size: 1.25rem;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <span id="left_net_score_container" class="badge badge-light p-2 w-100 border text-secondary shadow-sm" style="display:none; font-size: 0.9rem; border-radius: 8px;">
                            Net Score: <strong id="left_final_result" class="text-dark">0.0 cm</strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== RIGHT LEG COLUMN ==================== -->
        <div class="col-12 col-md-6 px-2 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <h4 class="text-center font-weight-bold text-success text-uppercase" style="font-size: 1.1rem;">Right Leg Evaluation</h4>
                    
                    <div class="row mt-2 justify-content-center">
                        <!-- Right Final Position Grid -->
                        <div class="col-8">
                            <h5 class="mb-2 text-center text-muted font-weight-bold" style="font-size:1.0rem;">Final Position</h5>
                            <div class="row no-gutters">
                                <div class="col-6 px-1">
                                    <label for="right_final_cm" class="small font-weight-bold text-muted mb-1 d-block text-center">Cms</label>
                                    <input type="text" name="right_final_cm" onkeyup="calculateLegScore('right')" class="form-control text-center font-weight-bold" id="right_final_cm" placeholder="00" inputmode="numeric" style="font-size: 1.25rem;">
                                </div>
                                <div class="col-6 px-1">
                                    <label for="right_final_mm" class="small font-weight-bold text-muted mb-1 d-block text-center">mm</label>
                                    <input type="text" name="right_final_mm" onkeyup="calculateLegScore('right')" class="form-control text-center font-weight-bold" id="right_final_mm" placeholder="0" inputmode="numeric" style="font-size: 1.25rem;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <span id="right_net_score_container" class="badge badge-light p-2 w-100 border text-secondary shadow-sm" style="display:none; font-size: 0.9rem; border-radius: 8px;">
                            Net Score: <strong id="right_final_result" class="text-dark">0.0 cm</strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @php $id = $TestTypeId; @endphp
    <x-reset-submit-btn :id="$id"/>
</form>

<script>
// Helper converts entry pairs into pure millimeters
function getTotalInMm(cm, mm) {
    return (parseInt(cm) || 0) * 10 + (parseInt(mm) || 0);
}

// Side-specific calculation isolates DOM lookups cleanly
function calculateLegScore(side) {
    let finalCm = document.getElementById(`${side}_final_cm`).value;
    let finalMm = document.getElementById(`${side}_final_mm`).value;

    // Hide indicator if both fields are completely empty
    if (finalCm === '' && finalMm === '') {
        document.getElementById(`${side}_net_score_container`).style.display = "none";
        document.getElementById(`score_${side}`).value = "";
        return 0;
    }

    let totalMm = getTotalInMm(finalCm, finalMm);

    document.getElementById(`${side}_net_score_container`).style.display = "block";
    
    let displayCm = Math.floor(totalMm / 10);
    let displayMm = totalMm % 10;
    document.getElementById(`${side}_final_result`).innerHTML = `${displayCm} cm, ${displayMm} mm`;
    
    // Convert total millimeters to standard float centimeters for backend processing
    let finalFloatCm = (totalMm / 10).toFixed(1); 
    document.getElementById(`score_${side}`).value = finalFloatCm;

    return totalMm;
}

// Setup input sizing filters to constrain input ranges
const inputConfigs = [
    { id: 'left_final_cm', size: 2 },
    { id: 'right_final_cm', size: 2 },
    { id: 'left_final_mm', size: 1 },
    { id: 'right_final_mm', size: 1 }
];

inputConfigs.forEach(config => {
    let element = document.getElementById(config.id);
    if(element) {
        element.addEventListener("input", function (e) {
            let val = e.target.value.replace(/[^0-9]/g, '');    
            if (val.length > config.size) {
                val = val.slice(0, config.size); 
            }
            e.target.value = val;
        });
    }
});

$(document).ready(function() {
    const formId = @json($TestTypeId);
    const formSelector = $(`#${formId}`);

    // Standard Reset Trigger Handler Setup
    $(`#reset_${formId}`).on('click', function(e) {
        e.preventDefault();
        formSelector[0].reset();
        $(`#left_net_score_container, #${formId} #right_net_score_container`).hide();
        document.getElementById('score_left').value = '';
        document.getElementById('score_right').value = '';      
    });

    formSelector.submit(function(e) {
        e.preventDefault();
        
        const studentId = document.getElementById('selected_student_id').value;
        const scoreLeft = document.getElementById('score_left').value;
        const scoreRight = document.getElementById('score_right').value;
        
        if (!studentId) {
            handleResponseMessages('info', 'Select Student', 'Please select a student from the listing array first.');
            return;
        }

        // Validate that calculations for both sides are completed before continuing
        if (scoreLeft === '' || scoreRight === '') {
            handleResponseMessages('info', 'Incomplete Form', 'Please complete valid numeric scoring metrics for both Left and Right leg groups.');
            return;
        }

        let route = '{{ route("cwsn.types.submit") }}';
        let formData = $(this).serialize();
        SubmitForm(formId, formData, route);
    });
});
</script>
@endsection