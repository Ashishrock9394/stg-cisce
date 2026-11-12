@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
@section('content')


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
                                </svg>
                            </span>
                        </a>

                    @elseif(auth()->guard('sstudent')->check())

                        <a href="javascript:history.back()" class="back-button">
                            <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16"> <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
                                </svg>
                            </span> 
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

            <form method="POST" name="saveBMIRecord" id="save_bmi_record_id" action="">
                {{method_field('post')}}
                @csrf
                    
                    
                <input type="hidden" name="skillReportId" value="{{ $skillReportId }}">
                <input type="hidden" name="TestTypeMasterID" value="{{ $TestTypeMasterID }}">
                <input type="hidden" id="SchoolId" name="SchoolId" value="{{ $SchoolId }}">
                <input type="hidden" id="selected_student_id" name="student_id">
                <input type="hidden" id="testtype" name="testtype" value="seniorbmi">
                <div class="row">
                    <div class="col-12">
                        <div class="form">
                            <h2 class="mb-3 mt-4 text-center">Enter Height and Weight Score</h2>
                            <div class="input-group input-group__2 mb-3">
                                <span class="form-control">
                                    <label for="AGE_GENDER_ID" class="form-label">Age/Gender</label>
                            
                                    <input type="text" class="form-control form-control-lg no-cursor" id="AGE_GENDER_ID" placeholder="" disabled="">
                                </span>
                                <span class="form-control">
                                    <label for="heightInput" class="form-label">Height(cm)</label>
                                    <input type="text" name="height" required class="form-control form-control-lg" id="heightInput" placeholder="--">
                                </span>
                                <span class="form-control">
                                    <label for="weightInput" class="form-label"> Weight(kgs)</label>
                                    <input type="text" name="weight" required class="form-control form-control-lg" id="weightInput" placeholder="--">
                                </span>
                            </div>

                        </div>
                    </div>

                </div>


                {{-- footer for submit and reset button --}}
                @php
                    $id = "seniorBmi";
                @endphp
                <x-reset-submit-btn :id="$id"/>
                {{-- footer close --}}

            
            </form>	
            
            
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
    $('#save_bmi_record_id').submit(function(e) {
        e.preventDefault(); // prevent default form submission
        const studentId = document.getElementById('selected_student_id').value;
        if (!studentId) {
            handleResponseMessages( 'warning',  'Add Student', 'Please select the student');
            return;
        }
        submitLoader();
        $.ajax({
            url: '{{ route("bmi.record.submit") }}', // or your route URL
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
				Swal.close();
	       		$('#save_bmi_record_id')[0].reset();
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


// height inputs restrictions

document.getElementById("heightInput").addEventListener("input", function (e) {
    let value = e.target.value;

    value = value.replace(/[^0-9.]/g, '');

    const parts = value.split('.');
    if (parts.length > 2) {
        value = parts[0] + '.' + parts[1];
    }

    let match = value.match(/^(\d{0,3})(\.(\d{0,2})?)?$/);
    if (match) {
        value = match[0];
    } else {
        value = value.slice(0, -1); 
    }

    if (value && parseFloat(value) <= 0) {
        value = '';
        Swal.fire({
            title: '',
            text: 'Height can\'t be 0',
            icon: 'warning'
        });
    }

    e.target.value = value;
});

// weight inputs restrictions

document.getElementById("weightInput").addEventListener("input", function (e) {
    let value = e.target.value;

    value = value.replace(/[^0-9.]/g, '');

    const parts = value.split('.');
    if (parts.length > 2) {
        value = parts[0] + '.' + parts[1];
    }

    let match = value.match(/^(\d{0,3})(\.(\d{0,2})?)?$/);
    if (match) {
        value = match[0];
    } else {
        value = value.slice(0, -1); 
    }
    
    if (value && parseFloat(value) <= 0) {
        value = '';
        Swal.fire({
            title: '',
            text: 'Weight can\'t be 0',
            icon: 'warning'
        });
    }
    e.target.value = value;
});


</script>




@endsection