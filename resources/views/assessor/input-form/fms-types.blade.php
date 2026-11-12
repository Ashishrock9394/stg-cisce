@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
@section('content')


<div class="all-chaptr-cards">
    <div class="container">
        <div class="t-mrg2 mb-5 pb-5">
            <div class="row">
                <div class="col-12">
                    <div class="heading-rw mt-0 mt-md-1 mb-0 p-0">
                        <a href="javascript:history.back()" class="back-button">
                            <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
                                </svg>
                            </span>
                        </a>

                        <h1 class="ml-md-4 mb-0">{{$title}}</h1>
                    </div>
                </div>
            </div>

            {{-- get student list componet --}}
                @php
                $type = "allFmsTest";
                @endphp
                <x-get-student-list :classes="$classes" :type="$type" />

                <form class="row" method="POST" name="fms_types_submit" id="fms_types_submit_id" action="">
                    {{method_field('post')}}
                    @csrf

                    <input type="hidden" name="skillReportId" value="{{ $skillReportId }}" id="skillReportId">
                    <input type="hidden" name="TestTypeMasterID" value="{{ $TestTypeMasterID }}">
                    <input type="hidden" name="SchoolId" id="SchoolId"  value="{{ $SchoolId }}">
                    <input type="hidden" name="student_id" id="selected_student_id" >


                        <div class="col-12">
                            <div class="list-group mb-4">

                                @foreach($skillTypes as $key => $val)
                                <label class="list-group-item pr-3">
                                    <input class="form-check-input me-1" name="description[]" type="checkbox" value="{{ $val->id }}">
                                    {{ $val->description }}
                                </label>
                                @endforeach

                            </div>
                        </div>

                        {{-- footer for submit and reset button --}}
                        @php
                            $id = "fmsTest";
                        @endphp
                        <x-reset-submit-btn :id="$id"/>
                        {{-- footer close --}}
                </form>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
    $('#fms_types_submit_id').submit(function(e) {
        e.preventDefault(); // prevent default form submission
    
        const studentId = document.getElementById('selected_student_id').value;
        if(!studentId){
            handleResponseMessages( 'warning',  'Select Student', 'Please select the student');
            return;
        }

        const checkedCount = $('input[name="description[]"]:checked').length;
        if (checkedCount === 0) {
            handleResponseMessages( 'warning',  'No Observation Selected', 'Please select at least one observation before submitting');
            return;
        }
        submitLoader();
        $.ajax({
            url: '{{ route("fms.types.submit") }}', 
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {	
                Swal.close();	
                $('#fms_types_submit_id')[0].reset();
                		
                if (response.status === 'success') {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success"
                    }).then(() => location.reload());
                }
					
            },
            error: function (xhr) {
                Swal.fire({
                    title: "Error!",
                    text: xhr.responseJSON?.message ?? 'Unexpected error occurred',
                    icon: "error"
                });
            }
        });
    });
});
</script>

@endsection