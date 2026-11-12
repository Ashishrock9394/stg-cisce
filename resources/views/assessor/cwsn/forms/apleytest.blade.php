@extends('assessor.cwsn.index')
@section('cwsnform')

<style>
/* Custom styling overrides to make standard radio listings look like professional select cards */
.style-radio-card {
    border-radius: 10px;
    border: 1.5px solid #deebd5;
    transition: all 0.2s ease-in-out;
    cursor: pointer;
}
.style-radio-card input[type="radio"] {
    transform: scale(1.2);
    vertical-align: middle;
}

.list-group-item {
  padding: 0.75rem 1rem !important;
}

h4.text-uppercase {
    color: #292775 !important;
}

h4.text-uppercase {
    color: #292775 !important;
}

</style>


<h2 class="text-center mb-3">{{ $title }} Score</h2>
<form method="POST" name="saveApleyRecord" name="{{ $TestTypeId }}" id="{{ $TestTypeId }}" action="javascript:void(0);">
    {{ method_field('post') }}
    @csrf
    
    <!-- Core Assessment Meta Tags -->
    <input type="hidden" name="skillReportId" value="{{ $skillReportId }}" id="skillReportId">
    <input type="hidden" name="TestTypeMasterID" value="{{ $TestTypeMasterID }}">
    <input type="hidden" name="SchoolId" id="SchoolId" value="{{ $SchoolId }}">
    <input type="hidden" name="student_id" id="selected_student_id">
    
    <!-- Dynamic Outputs -->
    <input type="hidden" name="result" id="result" value="0" readonly>
    <input type="hidden" name="aplay_test" id="aplay_test" value="">

    <div class="row ">
        <!-- ==================== Left ARM PROGRESSION ==================== -->
        <div class="col-12 col-md-6 px-2 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-0">
                    <h4 class="font-weight-bold text-success text-center mb-1 text-uppercase" style="font-size: 1.1rem;">Left Arm Reach</h4>
                    <p class="small text-muted text-center mb-1">Reaching towards Right side landmarks</p>
                    
                    <div class="d-flex flex-column gap-2">                       
                                               
                        <label class="list-group-item pr-3">
                            <input type="radio" name="left_apley_level" value="3" onchange="syncApleyPayload()">
                            <strong class="ml-2">Score 3:</strong> Touches superior medial angle of opposite scapula
                        </label>
                        <label class="list-group-item pr-3">
                            <input type="radio" name="left_apley_level" value="2" onchange="syncApleyPayload()">
                            <strong class="ml-2">Score 2:</strong> Touches the top of the head
                        </label>
                         <label class="list-group-item pr-3">
                            <input type="radio" name="left_apley_level" value="1" onchange="syncApleyPayload()">
                            <strong class="ml-2">Score 1:</strong> Touches the mouth cleanly
                        </label>
                        <label class="list-group-item pr-3">
                            <input type="radio" name="left_apley_level" value="0" onchange="syncApleyPayload()" checked>
                            <strong class="ml-2">Score 0:</strong> Unable to touch the mouth
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <!-- ==================== Right ARM PROGRESSION ==================== -->
        <div class="col-12 col-md-6 px-2 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-0">
                    <h4 class="font-weight-bold text-primary text-center mb-1 text-uppercase" style="font-size: 1.1rem;">Right Arm Reach</h4>
                    <p class="small text-muted text-center mb-1">Reaching towards Left side landmarks</p>
                    
                    <div class="d-flex flex-column gap-2">
                        <label class="list-group-item pr-3">
                            <input type="radio" name="right_apley_level" value="3" onchange="syncApleyPayload()">
                            <strong class="ml-2">Score 3:</strong> Touches superior medial angle of opposite scapula
                        </label>
                        <label class="list-group-item pr-3">
                            <input type="radio" name="right_apley_level" value="2" onchange="syncApleyPayload()">
                            <strong class="ml-2">Score 2:</strong> Touches the top of the head
                        </label>
                        <label class="list-group-item pr-3">
                            <input type="radio" name="right_apley_level" value="1" onchange="syncApleyPayload()">
                            <strong class="ml-2">Score 1:</strong> Touches the mouth cleanly
                        </label>
                        <label class="list-group-item pr-3">
                            <input type="radio" name="right_apley_level" value="0" onchange="syncApleyPayload()" checked>
                            <strong class="ml-2">Score 0:</strong> Unable to touch the mouth
                        </label>
                    </div>
                </div>
            </div>
        </div>        
    </div>

    <x-reset-submit-btn :id="$TestTypeId"/>
</form>



<script>

const formName = @json($TestTypeId);

function syncApleyPayload() {
    let rightVal = document.querySelector('input[name="right_apley_level"]:checked')?.value || '0';
    let leftVal = document.querySelector('input[name="left_apley_level"]:checked')?.value || '0';

    document.getElementById('aplay_test').value = `L: ${leftVal} | R: ${rightVal}`;
    document.getElementById('result').value = parseInt(leftVal) + parseInt(rightVal);
}



$(document).ready(function() {
    syncApleyPayload();

    $(document).ready(function() {

        $(`#${formName}`).submit(function(e) {
            e.preventDefault();

            const studentId = document.getElementById('selected_student_id').value;
            if(!studentId){
                handleResponseMessages( 'warning',  'Select Student', 'Please select the student');
                return;
            }
            
            const aplay_test_result = $('input[name="aplay_test"]').val();

            if (aplay_test_result === '' || aplay_test_result === null || undefined === aplay_test_result) {
                handleResponseMessages('info', '', 'Please enter position of the student');
                return;
            }

            let route = '{{ route("cwsn.types.submit") }}';
            let formData =  $(this).serialize();
            SubmitForm(formName, formData, route);
            document.getElementById('live_status_badge').textContent = `Level 1, Shuttle 0`;
        });
    });
});
</script>
@endsection