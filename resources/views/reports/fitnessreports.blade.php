@extends('layouts.filldart-app') @section('title', $title) @section('content')


<div class="container">
	<div class="t-mrg2 mb-5 pb-5">

		<div class="all-chaptr-cards mb-4" style="margin: 0;">
			<div class="row">
				<x:back-button title="{{$title}}" >
					<x-slot name="actionsOutside">
				        <div >				        	
				        	<button class="btn btn-sm btn-primary" onclick="openAvailableDownloads()">Available Download</button>
				        	@include('reports.modals.check-downloads')		
							@if(Auth::user()->id == 3257)	        	
				        	<button class="btn btn-sm btn-primary" onclick="checkSentReportStatus()">Check Email Status</button>
				        	@include('reports.modals.check-sent-reports') @endif
				        </div>
				    </x-slot>
				</x:back-button>
			</div>
		</div>


		<div class="alert alert-info d-flex align-items-start gap-2" role="alert" style="border-left: 4px solid #0d6efd;">
		    <i class="bi bi-info-circle-fill text-primary"></i>
		    <div>
		        <strong>&nbsp;&nbsp;Note:</strong>  
		        <ul class="mb-0 mt-1">
		            <li> Reports can only be generated for students who have <strong>completed all fitness tests</strong>.</li>
		            <li> If a student’s report is <strong>already requested</strong> or <strong>currently being processed</strong>, the checkbox will be disabled until it’s complete.</li>

		            <li>
					  Students with <strong>incomplete test data</strong> will be skipped during bulk report card generation.
					  Please complete all tests or download the report manually.
					</li>
		            
		            <li>Generated reports are retained for <strong>7 days</strong> only. After 7 days, they will be permanently deleted from the system. Please download and store your reports before they expire.				  
					</li>
		        </ul>
		    </div>
		</div>


		<div class="container-fluid p-0">
			@php
				$exportButtons = [
					[
						'type' => 'custom',
						'text' => 'Request Report Cards',
						'action' => 'generateFitnessReportCard'
					],
				];

				if (in_array(Auth::user()->id, [3251])){
					$exportButtons[] = [
						'type' => 'custom',
						'text' => 'Send Report Cards',
						'action' => 'sendFitnessReportCard'
					];
				}
			@endphp
            <x-data-listing-component
                id="student-skill-reports-table"
                :headers="['Class','Section','Roll No.','Student Name', 'Admission No.','Gender','Birth Date','Status','Test Status','Report','Download']"
            	:columns="[			       
			        ['data' => 'display_classname', 'name' => 'display_classname', 'orderable' => true],
			        ['data' => 'section_id',        'name' => 'section_id',        'orderable' => true],
			        ['data' => 'rollno',            'name' => 'rollno',            'orderable' => true],
			        ['data' => 'student_name',      'name' => 'student_name',      'orderable' => true, 'searchable' => true],
			        ['data' => 'admission_number',  'name' => 'admission_number',  'orderable' => true],
			        ['data' => 'gender',            'name' => 'gender',            'orderable' => true],
			        ['data' => 'dob',               'name' => 'students.dob',      'orderable' => false],
			        ['data' => 'status',            'name' => 'students.status',   'orderable' => false],
			        ['data' => 'testStatus',        'name' => 'test_status',       'orderable' => false],
			        ['data' => 'viewReport',        'name' => 'id',                'orderable' => false],
			        ['data' => 'downloadReport',    'name' => 'id', 			   'orderable' => false],
			    ]"

                ajax-url="{{ route('fitness.report') }}"
                :enable-export-buttons="false"
                :enableLengthMenu="true"
       			:pageLength="100"                
                :enable-class-filter="false"
                :enable-class-section-filter="true"
                :enable-school-terms-filter="true"
				:exportButtonText="'Bulk Action'"
                :enable-test-status-filter="true"
                :enable-student-category-filter="true"
                :enable-status-filter="true"
				:selectedStatus="'active'"
                searchPlaceholder="Students Name | Admission"
                :export-buttons="$exportButtons"
            >
            </x-data-listing-component>
        </div>       
	</div>
</div>
@endsection


@push('scripts')
<script>
	const currentTermId = @json($TermMasterId);

	function generateFitnessReportCard(e, dt, node, config, selectedIds) {

		let termId = document.getElementById('filter-school-terms').value;
		const action = "download";

		// Swal.fire({
		// 	icon: 'info',
		// 	iconColor: '#3085d6',
		// 	title: 'Session Preparation in Progress',
		// 	html: `
		// 		<p style="font-size: 1.1em;">Preparation for the <strong>CISCE 2026-27 session</strong> is currently underway.</p>
		// 		<p>This module will be accessible shortly. We appreciate your patience.</p>
		// 	`,
		// 	confirmButtonText: 'Ok',
		// 	confirmButtonColor: '#3085d6',
		// 	allowOutsideClick: false,
		// });
		// return; 


		if (!selectedIds || selectedIds.length === 0) {   		

		    Swal.fire({
		        icon: 'info',
		        title: 'No Data Selected',
		        text: 'Please select at least one student.',
		        allowOutsideClick: false,
		        showConfirmButton: true
		    });
		    return; 
		}

		Swal.fire({
	        title: 'Action Required',
	        text: "Do you want to generate the report cards?",
	        icon: 'warning',
	        showCancelButton: true,
	        confirmButtonText: 'Yes, generate it!',
	        cancelButtonText: 'Cancel',
	        reverseButtons: true
	    }).then((result) => {

	        if (result.isConfirmed) {   
				Swal.fire({
					icon: 'info',
					title: 'Generating Report',
					text: 'Your request is being submitted. Please wait...',
					allowOutsideClick: false,
					didOpen: () => {
						Swal.showLoading();
					}
				});     
	            
	         	$.ajax({
		    	    headers: {
		                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
		            },
		            url: "{{ route('generate.reportcards') }}",
		            method: 'POST',            
		            contentType: "application/json",            
		            data: JSON.stringify({
		                _token: "{{ csrf_token() }}",
		                student_ids: selectedIds,
						termId: termId,
						action: action
		            }), 
			        
			        success: function(data) {
						Swal.close();
			        	if(data.status && data.message){
			        		Swal.fire({
						        icon: 'success',
						        title: "Request Submitted successfully",
						        text: data.message,
						        showCancelButton: false,
						        allowOutsideClick: false,
						        confirmButtonText: 'Ok',
							}).then((result) => {
						        if (result.isConfirmed) {
						            window.location.reload();
						        }
						    });
			        	}
			        },

			        error: function(err) {
	                    Swal.close();
	                    Swal.fire({
	                        icon: 'error',
	                        title: 'Error',
	                        text: 'Something went wrong while generating the report cards.'
	                    });
	                }
			    });

	        } else if (result.dismiss === Swal.DismissReason.cancel) {
	           
	            Swal.fire({
	                icon: 'info',
	                title: 'Cancelled',
	                text: 'Report card generation was cancelled.'
	            });
	        }
	    });

	}

	function openAvailableDownloads() {
	    $('#availableDownloadsModal').modal('show');

	    fetch("{{ route('fitness.report.available') }}")
	        .then(res => res.json())
	        .then(data => {
	            document.getElementById('downloadsContent').innerHTML = data.html;	
	        });
	}

	function checkSentReportStatus() {
	    $('#availableDownloadsModal').modal('show');

	    fetch("{{ route('fitness.report.sent') }}")
	        .then(res => res.json())
	        .then(data => {
	            document.getElementById('downloadsContent').innerHTML = data.html;	
	        });
	}

	function sendFitnessReportCard(e, dt, node, config, selectedIds) {

		let termId = document.getElementById('filter-school-terms').value;
		const action = "email";

		if (!selectedIds || selectedIds.length === 0) {   	

		    Swal.fire({
		        icon: 'info',
		        title: 'No Data Selected',
		        text: 'Please select at least one student.',
		        allowOutsideClick: false,
		        showConfirmButton: true
		    });
		    return; 
		}

		Swal.fire({
	        title: 'Action Required',
	        text: "Do you want to Sent the report cards to parent's email address?",
	        icon: 'warning',
	        showCancelButton: true,
	        confirmButtonText: 'Yes, Sent it!',
	        cancelButtonText: 'Cancel',
	        reverseButtons: true
	    }).then((result) => {
	        if (result.isConfirmed) {  				  
	            Swal.fire({
					icon: 'info',
					title: 'Sending Report',
					text: 'Your request is being submitted. Please wait...',
					allowOutsideClick: false,
					didOpen: () => {
						Swal.showLoading();
					}
				});
	         	$.ajax({
		    	    headers: {
		                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
		            },
		            url: "{{ route('sent.reportcards') }}",
		            method: 'POST',            
		            contentType: "application/json",            
		            data: JSON.stringify({
		                _token: "{{ csrf_token() }}",
		                student_ids: selectedIds,
						termId: termId,
						action: action
		            }), 
			        
			        success: function(data) {
						Swal.close();
			        	if(data.status && data.message){
			        		Swal.fire({
						        icon: 'success',
						        title: "Request Submitted successfully",
						        text: data.message,
						        showCancelButton: false,
						        allowOutsideClick: false,
						        confirmButtonText: 'Ok',
							}).then((result) => {
						        if (result.isConfirmed) {
						            window.location.reload();
						        }
						    });
			        	}
			        },

			        error: function(err) {
	                    Swal.close();
	                    Swal.fire({
	                        icon: 'error',
	                        title: 'Error',
	                        text: 'Something went wrong while generating the report cards.'
	                    });
	                }
			    });

	        } else if (result.dismiss === Swal.DismissReason.cancel) {
	           
	            Swal.fire({
	                icon: 'info',
	                title: 'Cancelled',
	                text: 'Report card generation was cancelled.'
	            });
	        }
	    });

	}
	
</script>
@endpush

