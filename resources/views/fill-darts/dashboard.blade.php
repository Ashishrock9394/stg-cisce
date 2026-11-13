@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
@section('content')
	
<style>
.marquee-container {
    width: 100%;
    overflow: hidden;
    background-color: #ff8000;
    color: #fff;
    padding: 4px 0;
    font-weight: 500;
}

.marquee {
    display: inline-block;
    white-space: nowrap;
    will-change: transform;
    animation: scroll-left 60s linear infinite;
}

.marquee span {
    display: inline-block;
    padding-right: 100%;
}

/* Animation */
@keyframes scroll-left {
    0% {
        transform: translateX(100%);
    }
    100% {
        transform: translateX(-100%);
    }
}

/* Pause on hover */
.marquee-container:hover .marquee {
    animation-play-state: paused;
}
</style>

@if(Auth::user()->role_id == '4')
	<div class="marquee-container">
		<div class="marquee">
		<span><strong>Important Notice: </strong>Update the student’s Date of Birth and Parent’s Email ID via Edit Student in the Manage Student module. The assessment report will be sent to the updated email address. Ignore if already updated.</span>
		</div>
	</div>
@elseif(Auth::user()->role_id == '3')
	<div class="marquee-container">
		<div class="marquee">
		<span><strong>Important Notice: </strong>Kindly verify and update your email ID and other details in the Edit Profile section.</span>
		</div>
	</div>
@endif
<div class="container">
    <div class="t-mrg2">

        <div class="row text-center justify-content-md-center">
            <div class="col-12 col-md-12 col-lg-12 col-xl-10">
                <div class="form-row" style="justify-content: center;">

                	<!-- Trainers Dashboard  -->
                    @if($hasSchools && Auth::user()->role_id == '3')

	                    <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
	                        <a href="{{ route('all-test') }}" class="box"><div><img class="img-fluid" alt="" src="{{ asset('assets/icons/taketest.svg') }}"></div><span>Take Test</span></a>
	                    </div>                   
	                   
						<div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
							<a href="{{ route('lowerclasstestsummary') }}" class="box"><div>
							<img class="img-fluid" alt="" src="{{asset('assets/icons/lc_test-status.svg') }}"></div><span>Fitness Test Status <br> (Class 1 to 3)</span></a>
						</div>

						<div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
							<a href="{{ route('higherclasstestsummary') }}" class="box"><div>
							<img class="img-fluid" alt="" src="{{asset('assets/icons/hc_test-status.svg') }}"></div><span>
							Fitness Test Status <br> (Class 4 to 12)</span></a>
						</div>


                        <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
                            <a href="{{ route ('trainer.cwsn.status')}}" class="box"><div>
                            <img class="img-fluid" alt="" src="{{asset('assets/icons/lc_test-status.svg') }}"></div><span>CWSN Test Status <br> (Class 5 to 12)</span></a>
                        </div>						
					
						<div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
							<a href="{{ route('training.resources') }}" class="box"><div>
							<img class="img-fluid" alt="" src="{{asset('assets/icons/trainer-manual2.svg') }}" target="_blank"></div><span>
								Training Resources</span>
							</a>
						</div>
						
                        					
					@endif


					<!-- School Dashboard -->
                    @if(Auth::user()->role_id == '4')

	                    <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
	                        <a href="{{ route('managestudent') }}" class="box"><div><img class="img-fluid" alt="" src="{{asset('assets/icons/manage-stds.svg') }}"></div><span>Manage Students</span></a>
	                    </div>

						<div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
							<a href="{{ route('student.email') }}" class="box"><div>
							<img class="img-fluid" alt="" src="{{asset('assets/icons/manage-stds-email.svg') }}"></div><span>Parent's Email Update </span></a>
						</div>

	                    <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
	                        <a href="{{ route('mapping.trainer') }}" class="box"><div><img class="img-fluid" alt="" src="{{asset('assets/icons/MAP-Students.svg') }}"></div><span>Manage Trainers</span></a>
	                    </div>
	                  

						@php
							$schoolIds = \App\Models\ActiveSchool::where('status', 'active')->pluck('school_user_id')->toArray();
						@endphp
						<div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
							<a href="{{ route('upload.test.data') }}" class="box"><div><img class="img-fluid" alt="" src="{{asset('assets/icons/taketest.svg') }}"></div><span>Upload Test Data</span></a>
							<!-- <a href="javascript:void(0)" class="box notAvailable"><div><img class="img-fluid" alt="" src="{{asset('assets/icons/taketest.svg') }}"></div><span>Upload Test Data</span></a> -->
						</div> 
						
						<div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
								<a href="{{ route('lowerclasstestsummary') }}" class="box"><div>
							<img class="img-fluid" alt="" src="{{asset('assets/icons/lc_test-status.svg') }}"></div><span>Fitness Test Status <br> (Class 1 to 3)</span></a>
						</div>

						<div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
							<a href="{{ route('higherclasstestsummary') }}" class="box"><div>
							<img class="img-fluid" alt="" src="{{asset('assets/icons/hc_test-status.svg') }}"></div><span>
							Fitness Test Status <br> (Class 4 to 12)  </span></a>
						</div>	


						<div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
							<a href="{{ route ('trainer.cwsn.status')}}" class="box"><div>
							<img class="img-fluid" alt="" src="{{asset('assets/icons/hc_test-status.svg') }}"></div><span>CWSN Test Status <br> (Class 5 to 12)</span></a>
						</div>	


						<div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
							<a href="{{ route('fitness.report') }}" class="box"><div>
							<img class="img-fluid" alt="" src="{{asset('assets/icons/fms-report.svg') }}"></div><span>Assessment Report </span></a>
						</div>
						@if (!session('is_impersonating'))
						<div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
							<a href="{{ route('view.school.admin') }}" class="box"><div>
							<img class="img-fluid" alt="" src="{{asset('assets/icons/viewer.svg') }}"></div><span>Delegate Portal Access </span></a>
						</div>		
						@endif				
					
						<div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
							<a href="{{ route('training.resources') }}" class="box"><div>
							<img class="img-fluid" alt="" src="{{asset('assets/icons/trainer-manual2.svg') }}" target="_blank"></div><span>
								Training Resources</span>
							</a>
						</div>
						
							
						{{-- @if(Auth::user()->id == '3251' || Auth::user()->id == '3139')	--}}							
						{{-- @endif --}}
                    @endif

					@if(Auth::user()->role_id == '8')
					    @forelse($dashboardModules as $module)
							<div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
								<a href="{{ route($module->route_name) }}" class="box"><div>
								<img class="img-fluid" alt="{{ $module->name }}" src="{{ asset('assets/icons/'.$module->icon) }}"></div><span>{{ $module->name }} </span></a>
							</div>
					    @empty
					        <div class="alert alert-warning text-center mt-4 fixed-bottom">
					            <h6>No Modules Assigned</h6>
					            <p>Please contact your school administration to have your modules assigned.</p>
					        </div>
					    @endforelse
					@endif
					

					{{--
					<div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
						<a href="{{ route('test_videos') }}" class="box"><div>
						<img class="img-fluid" alt="" src="{{asset('assets/icons/test-demo.svg') }}"></div><span>
						Battery of Tests</span></a>
					</div>
					
					<div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
						<a href="{{ route('getactive') }}" class="box"><div><img class="img-fluid" alt="" src="{{asset('assets/icons/Get-Active.svg') }}"></div><span>Get Active</span></a>
					</div>

                    <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
						<a href="{{ route('training_manual') }}" class="box"><div>
						<img class="img-fluid" alt="" src="{{asset('assets/icons/trainer-manual2.svg') }}" target="_blank"></div><span>
							Training Manual</span>
						</a>
					</div>
					

					<div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
						<a href="{{ route('contact_me') }}" class="box"><div>
						<img class="img-fluid" alt="" src="{{asset('assets/icons/contact-us.png') }}"></div><span>Contact Us </span></a>
					</div>
					--}}
                </div>
            </div>
        </div>
    </div>
</div>

@if(!$hasSchools && Auth::user()->role_id == 3)
  <div class="alert alert-warning text-center mt-4 fixed-bottom" >
    <h4>No School Assigned</h4>
    <p>Please contact your school administrator to map your account.</p>
  </div>
@endif


@if($wrongDobStudents->count() > 0 && Auth::user()->role_id == 4)
	<div class="modal fade" id="dobIssueModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="dobIssueLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				
				<div class="modal-header">
					<h5 class="modal-title">Update or Confirm the Date of Birth</h5>
					<button type="button" class="close btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>

				<div class="modal-body">
					<p class="text-danger mb-2">
						<b>Action to be taken:</b><br>
						<b>a. </b>The identified student(s) do not meet the usual age range for their respective class(es). Please double-check their official document. <br>
						<b>b. </b>Confirm their date of birth (if DOB is correct) or update their date of birth (if there is an issue). <br>
						<b>c. </b>Click on "Confirm/Update" button. 
					</p>
					<div style="max-height:400px; overflow:auto;">
						<table class="table table-bordered text-dark">
							<thead>
								<tr>
									<th>#</th>
									<th>Name</th>
									<th>Admission Number</th>
									<th>Class</th>
									<th>Section</th>
									<th>Roll No.</th>
									<th>Age</th>
									<th>Ideal Age Range</th>
									<th width="15%">DOB to be checked</th>
									<!-- <th>Issue</th> -->
									<th>Action</th>
									
								</tr>
							</thead>
							<tbody>
								@foreach($wrongDobStudents as $student)
									<tr>
										<td>{{ $loop->iteration }}</td>
										<td>{{ $student->name }}</td>
										<td>{{ $student->student_uid }}</td>
										<td>{{ $student->class_id }}</td>
										<td>{{ $student->section_id }}</td>
										<td>{{ $student->rollno }}</td>
										<td class="text-danger">{{ $student->age }}</td>
										<td class="text-info">{{ $student->min_age }} years to {{ $student->max_age }} years</td>
										<td class="align-middler">
											<div class="d-flex align-items-center">
												<input type="date"
													class="form-control form-control-sm dob-input mr-2"
													style="width: 140px; color:red;"
													data-id="{{ $student->id }}"
													value="{{ $student->dob }}">																								
											</div>
										</td>
										<!-- <td class="text-danger">{{ $student->issue }}</td> -->
										<td>
											<button class="btn btn-sm btn-success save-dob"
													style="height: 40px; line-height: 1;"
													data-id="{{ $student->id }}">
												Confirm/Update
											</button>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
						Close
					</button>
				</div>
			</div>
		</div>
	</div>

	<script>
		$(document).ready(function () {
			$('#dobIssueModal').modal('show');
		});

		$(document).on('click', '.save-dob', function () {
			let id = $(this).data('id');
			let row = $(this).closest('tr');
			let date = row.find('.dob-input').val();
			if (!date) {
				Swal.fire({
					icon: 'warning',
					title: 'Missing DOB',
					text: 'Please select DOB before updating.',
					allowOutsideClick: false,
					showConfirmButton: true
				});
				return;
			}
			$.post("{{ route('upadatedob') }}", {
				_token: "{{ csrf_token() }}",
				student_id: id,
				new_date: date
			}, function (res) {

				if (res.status === 'success') {

					Swal.fire({
						icon: 'success',
						title: 'Updated',
						text: res.message,
						allowOutsideClick: false,
						showConfirmButton: true
					}).then((result) => {
						if (result.isConfirmed) {
							location.reload();
						}
					});

				}

				else if (res.status === 'age_warning') {

					Swal.fire({
						icon: 'warning',
						title: 'Age Warning',
						text: res.message,
						allowOutsideClick: false,
						showCancelButton: true,
						confirmButtonText: 'Yes, Update',
						cancelButtonText: 'Cancel'
					}).then((result) => {

						if (result.isConfirmed) {
							$.post("{{ route('upadatedob') }}", {
								_token: "{{ csrf_token() }}",
								student_id: id,
								new_date: date,
								force_update: true
							}, function (r2) {

								Swal.fire({
									icon: 'success',
									title: 'Updated',
									text: r2.message,
									allowOutsideClick: false,
									showConfirmButton: true
								}).then((result) => {
									if (result.isConfirmed) {
										location.reload();
									}
								});
							});
						}
					});
				}

				else {
					Swal.fire({
						icon: 'error',
						title: 'Failed',
						text: res.message,
						allowOutsideClick: false,
						showConfirmButton: true
					});
				}

			});

		});
	</script>

@endif

<!-- School Selection Modal -->
@if($hasSchools)

    @if(Auth::user()->role_id == 3 && !Session::get('SelectSchoolId'))
	<div class="modal fade" id="exampleModal" tabindex="-1" data-backdrop='static' role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered model-md" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="exampleModalLabel">Choose your school</h5>
		      </div>  
			  
		     <div class="modal-body">
			   <form method="get" name="trainer_select_school" id="trainer_select_school_id" action="{{ route('filldart.dashboard') }}">
					<select name="select_school_id" required class="form-select form-select-lg form-control mx-0 w-100" aria-label=".form-select-lg example">
						<option value="">Select </option>
						  @foreach($SchoolTrainers as $key => $val)
							<option value="{{ $val->id }}">{{ $val->school_name }}</option>
						  @endforeach
					</select>
					<div class="modal-footer p-0 mt-4 border-0">
						<div class="col-auto p-0">
							<button type="submit" class="btn btn-primary px-4">Save</button>
						</div>
					</div>
				</form>
		      </div>	
		  </div>
		</div>
	</div>
	<script>
      $(window).on('load', function() {
          $('#exampleModal').modal('show');
      });

    </script>
  @endif
@endif

<script>
	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.notAvailable').forEach(function (element) {
			element.addEventListener('click', function (e) {
				e.preventDefault();

				Swal.fire({
				    icon: 'info',
				    iconColor: '#3085d6',
				    title: 'Session Preparation in Progress',
				    html: `
				        <p style="font-size: 1.1em;">Preparation for the <strong>CISCE 2026-27 session</strong> is currently underway.</p>
				        <p>This module will be accessible shortly. We appreciate your patience.</p>
				    `,
				    confirmButtonText: 'Ok',
				    confirmButtonColor: '#3085d6',
				    allowOutsideClick: false,
				});
			});
		});

	});
</script>


@endsection