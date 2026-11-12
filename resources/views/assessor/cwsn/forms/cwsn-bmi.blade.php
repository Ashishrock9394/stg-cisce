@extends('assessor.cwsn.index')
@section('cwsnform')

<style>
    .input-group__2 span.form-control:first-child input,
    .input-group__2 span.form-control:last-child input {
        border-radius: 0rem !important;
    }
    .input-group.input-group__2 span.form-control {
        padding: 0 !important;
        border-radius: 0px !important;
    }

    div:where(.swal2-icon).swal2-error [class^=swal2-x-mark-line][class$=left] {
        left: 0.2935em;
        transform: rotate(45deg);
    }

    div:where(.swal2-icon).swal2-error [class^=swal2-x-mark-line] {
        display: block;
        position: absolute;
        top: 1.4em;
        width: 2.375em;
        height: .3125em;
        left: .18rem;
        border-radius: .125em;
        background-color: #f27474;
    }

</style>

<h2 class="mb-4 mt-4 text-center">Enter Height and Weight Score</h2>

<form class="row form" method="POST" name="{{ $TestTypeId }}" id="{{ $TestTypeId }}" action="javascript:void(0);">
    {{ method_field('post') }}
    @csrf
    
    <input type="hidden" name="skillReportId" value="{{ $skillReportId }}">
    <input type="hidden" name="TestTypeMasterID" value="{{ $TestTypeMasterID }}">
    <input type="hidden" id="SchoolId" name="SchoolId" value="{{ $SchoolId }}">
    <input type="hidden" id="selected_student_id" name="student_id">

    <input type="hidden" name="anthropo_ht_id" id="anthropo_ht_id" value="">
    <input type="hidden" name="anthropo_wt_id" id="anthropo_wt_id" value="">


    <!-- MAIN ROW: Core Input Group -->
    <div class="form-body-wrapper mb-3">
        <div class="input-group input-group__2">
            
            <!-- Age / Gender Column -->
            <span class="form-control">
                <label for="AGE_GENDER_ID" class="form-label">Age/Gender</label>
                <input type="text" class="form-control form-control-lg no-cursor" id="AGE_GENDER_ID" disabled>
            </span>
            
            <!-- Standard Height Cell -->
            <span class="form-control" id="standard-height-cell">
                <label for="heightInput" class="form-label">Height (cm)</label>
                <input type="text" name="height" class="form-control form-control-lg decimal-input" id="heightInput" placeholder="--" maxlength="6">
            </span>
            
            <!-- Segmented Height Fields (Uses d-none by default instead of display:none) -->
            <span class="form-control d-none" id="segmented-fields">         
                <span class="form-control">
                    <label class="form-label">Floor to Knee (cm)</label>
                    <input type="text" name="segment_floor_to_knee" step="0.1" class="form-control form-control-lg decimal-input segment-calc"  placeholder="--">
                </span>
                
                <span class="form-control">
                    <label class="form-label">Knee to Hip (cm)</label>
                    <input type="text" name="segment_knee_to_hip" step="0.1" class="form-control form-control-lg decimal-input segment-calc" placeholder="--">
                </span>
                
                <span class="form-control">
                    <label class="form-label">Hip to Head (cm)</label>
                    <input type="text" name="segment_hip_to_head" step="0.1" class="form-control form-control-lg decimal-input segment-calc" placeholder="--">
                </span>               
            </span>

            
            <!-- Standard Weight Cell -->
            <span class="form-control" id="standard-weight-cell">
                <label for="weightInput" class="form-label">Weight (kgs)</label>
                <input type="text" name="weight" class="form-control form-control-lg allow-decimal decimal-input" id="weightInput" placeholder="--" maxlength="7">
            </span>

            <!-- Amputation Weight Cell (Fixed duplicate ID to amputationWeightInput) -->
            <span class="form-control" id="amputation-fields" style="display: none;">
                <label for="amputationWeightInput" class="form-label">Raw Measured Weight (kg)</label>
                <input type="text" name="amputation_raw_weight" step="0.1" class="form-control form-control-lg decimal-input" id="amputationWeightInput" placeholder="--" maxlength="7">
            </span>

            <!-- WHEELCHAIR METRICS SECTION CONTAINER -->
            <span class="form-control d-none" id="wheelchair-fields">         
                <span class="form-control">
                    <label class="form-label">Total Weight with Chair (kg)</label>
                    <input type="text" name="total_combined_weight" step="0.1" class="form-control form-control-lg decimal-input wheelchair-calc" placeholder="--">
                </span>
                
                <span class="form-control">
                    <label class="form-label">Wheelchair weight (kg)</label>
                    <input type="text" name="wheelchair_tare_weight" step="0.1" class="form-control form-control-lg decimal-input wheelchair-calc" placeholder="--">
                </span>
            </span>

            <!-- TOTAL SEGMENTED HEIGHT -->
           
        </div>


        <div class="d-flex bd-highlight justify-content-center ">
            <div id="total_broprot_height" style="display:none; text-align: center;" class="p-2 flex-grow-1 bd-highlight">
                <b>Total Height:</b> <span id="final_result_id"></span>
            </div>

            <div id="total_broprot_weight" style="display:none; text-align: center;" class="p-2 bd-highlight">
                <b>Total Weight:</b> <span id="total_wheelchair_net_weight"></span>               
            </div>
            <span id="weight_error_message" style="color: red; display: none;" class="p-2 bd-highlight"></span>
        </div>

      

    </div>

    @php $id = "bmi"; @endphp
    <x-reset-submit-btn :id="$id"/>
</form> 

<script>

    const formId = @json($TestTypeId);
    let pwd_category_id = parseInt(@json($pwd_category_id ?? 0), 10);
    
    $(document).ready(function() {
        handleCategoryChange(pwd_category_id);

        $(document).on('input', '.decimal-input', function () {
	        let value = $(this).val();
	        value = value.replace(/[^0-9.]/g, '');
	        value = value.replace(/(\..*?)\..*/g, '$1');
	        let parts = value.split('.');

	        // Allow max 3 digits before decimal
	        parts[0] = parts[0].substring(0, 3);

	        // Allow max 2 digits after decimal
	        if (parts.length > 1) {
	            parts[1] = parts[1].substring(0, 2);
	            value = parts[0] + '.' + parts[1];
	        } else {
	            value = parts[0];
	        }

	        $(this).val(value);
	    });

       // Dynamic Calculation: Segmented Height Total
        $(document).on('input', '.segment-calc', function() {
            let kneeToFloor = parseFloat($('input[name="segment_floor_to_knee"]').val()) || 0;
            let kneeToHip   = parseFloat($('input[name="segment_knee_to_hip"]').val()) || 0;
            let hipToHead   = parseFloat($('input[name="segment_hip_to_head"]').val()) || 0;

          
            let total = kneeToFloor + kneeToHip + hipToHead;
            let formattedTotal = total > 0 ? total.toFixed(2) : '';

            $('#final_result_id').text(formattedTotal);

            console.log('total' + total)
            if (total > 0) {
                $('#total_broprot_height').show();
            } else {
                $('#total_broprot_height').hide();
            }
        });

        // Dynamic Calculation: Wheelchair Net Weight
        $(document).on('input', '.wheelchair-calc', function() {
            
            let totalWithChair = parseFloat($('input[name="total_combined_weight"]').val()) || 0;
            let chairWeight    = parseFloat($('input[name="wheelchair_tare_weight"]').val()) || 0;

            let netWeight = totalWithChair - chairWeight;
            if (totalWithChair > 0 && chairWeight > 0) {
                if (netWeight < 0) {
                    $('#weight_error_message').text('Error: Total combined weight cannot be less than the wheelchair weight.').show();
                    $('#total_wheelchair_net_weight').text('');
                    $('#total_broprot_weight').hide();
                    return; 
                }
            }

            // Hide error message if validation passes
            $('#weight_error_message').hide();

            let final_weight = netWeight > 0 ? netWeight.toFixed(2) : '' ;

            $('#total_wheelchair_net_weight').text(final_weight);

            if (final_weight > 0) {
                $('#total_broprot_weight').show();
            } else {
                $('#total_broprot_weight').hide();
            }
        });



	    $(`#${formId}`).on('submit', function(e) {
	    	e.preventDefault();

	    	const studentId = document.getElementById('selected_student_id').value;
	    	if (!studentId) {
	            handleResponseMessages( 'warning',  'Add Student', 'Please select the student');
	            return;
	        }
	        

            $('.is-invalid').removeClass('is-invalid');
            let isValid = true;
            let errorMsg = '';

            // Helper to check if element is visible and active in UI
            function isVisible(selector) {
                const $el = $(selector);
                return $el.is(':visible') && !$el.hasClass('d-none') && !$el.closest('.d-none').length;
            }

            // 1. Validate Standard Height
            if (isVisible('#heightInput')) {
                let val = parseFloat($('#heightInput').val());
                if (isNaN(val) || val < 30 || val > 250) {
                    isValid = false;
                    errorMsg = 'Please enter a valid height between 30 cm and 250 cm.';
                    $('#heightInput').addClass('is-invalid').focus();
                }
            }

            // 2. Validate Segmented Height Inputs
            if (isValid && isVisible('#segmented-fields')) {
                $('#segmented-fields input[type="text"]').each(function() {
                    let val = parseFloat($(this).val());
                    let label = $(this).prev('label').text();
                    if (isNaN(val) || val <= 0 || val > 150) {
                        isValid = false;
                        errorMsg = `Please enter a valid value for ${label}.`;
                        $(this).addClass('is-invalid').focus();
                        return false; // Break loop
                    }
                });
            }

            // 3. Validate Standard Weight
            if (isValid && isVisible('#weightInput')) {
                let val = parseFloat($('#weightInput').val());
                if (isNaN(val) || val < 2 || val > 300) {
                    isValid = false;
                    errorMsg = 'Please enter a valid weight between 2 kg and 300 kg.';
                    $('#weightInput').addClass('is-invalid').focus();
                }
            }

            // 4. Validate Amputation Raw Weight
            if (isValid && isVisible('#amputationWeightInput')) {
                let val = parseFloat($('#amputationWeightInput').val());
                if (isNaN(val) || val < 2 || val > 300) {
                    isValid = false;
                    errorMsg = 'Please enter a valid raw measured weight.';
                    $('#amputationWeightInput').addClass('is-invalid').focus();
                }
            }

            // 5. Validate Wheelchair Metrics
            if (isValid && isVisible('#wheelchair-fields')) {
                let $totalInput = $('input[name="total_combined_weight"]');
                let $tareInput  = $('input[name="wheelchair_tare_weight"]');
                let totalWeight = parseFloat($totalInput.val());
                let tareWeight  = parseFloat($tareInput.val());

                if (isNaN(totalWeight) || totalWeight <= 0) {
                    isValid = false;
                    errorMsg = 'Please enter a valid Total Weight with Chair.';
                    $totalInput.addClass('is-invalid').focus();
                } else if (isNaN(tareWeight) || tareWeight <= 0) {
                    isValid = false;
                    errorMsg = 'Please enter a valid Wheelchair Weight.';
                    $tareInput.addClass('is-invalid').focus();
                } else if (tareWeight >= totalWeight) {
                    isValid = false;
                    errorMsg = 'Wheelchair weight cannot be greater than or equal to total combined weight.';
                    $tareInput.addClass('is-invalid').focus();
                }
            }

            // Prevent form submit if invalid
            if (!isValid) {
                e.preventDefault();

                Swal.fire({
					title: "Error!",
					text: errorMsg,
					icon: "error"
				});
                // alert(errorMsg);
                
                return false;
            }

            let route = '{{ route("cwsn.types.submit") }}';
            let formData =  $(this).serialize();
            SubmitForm(formId, formData, route);
        });
    });

    
    function handleCategoryChange(pwd_category_id) {

        if (pwd_category_id === 3) {
            handleAdaptationChange();    
        } else {
            $('#adaptation_type').val('none');
            handleAdaptationChange();
        }
    }

    function handleAdaptationChange() {

    	$(`#${formId}`)[0].reset();
  
        let anthropo_ht_id = parseInt($('#anthropo_ht_id').val(), 10) || 0; 
        let anthropo_wt_id = parseInt($('#anthropo_wt_id').val(), 10) || 0;  

        // jQuery Selectors
        const $standardHeight  = $('#standard-height-cell');
        const $standardWeight  = $('#standard-weight-cell');
        const $segmentedPanel  = $('#segmented-fields');
        const $amputationPanel = $('#amputation-fields');
        const $adaptationRow   = $('#cwsn-adaptation-row');
        const $wheelchairPanel = $('#wheelchair-fields');

        // 1. Reset all elements to standard state
        $standardHeight.show();
        $standardWeight.show();
        
        // Hide custom panels cleanly
        $segmentedPanel.addClass('d-none').removeClass('d-flex');
        $wheelchairPanel.addClass('d-none').removeClass('d-flex');
        $amputationPanel.hide();
        $wheelchairPanel.hide();
        $adaptationRow.hide();


        // 2. Height Adaptation Logic
        if (anthropo_ht_id === 3) { // Segmented Height
            $standardHeight.hide();
            $segmentedPanel.removeClass('d-none').addClass('d-flex');
        }

        if([5,6,7].includes(anthropo_wt_id)){
        	$standardHeight.show();
        	$standardWeight.hide();
            $segmentedPanel.addClass('d-none').removeClass('d-flex');
            $amputationPanel.show();
        }

        if(anthropo_ht_id == 3 && [5,6,7].includes(anthropo_wt_id)){
        	$standardHeight.hide();
        	$segmentedPanel.removeClass('d-none').addClass('d-flex');
        }

        if(anthropo_wt_id == 8){
            $standardWeight.hide();
            $wheelchairPanel.removeClass('d-none').addClass('d-flex');
        }

        if(anthropo_ht_id == 3 && anthropo_wt_id == 8){
        	$standardWeight.hide();
        	$wheelchairPanel.removeClass('d-none').addClass('d-flex');
        }
    }

</script>
@endsection