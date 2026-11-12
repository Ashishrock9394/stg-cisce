@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
@section('content')

<style>
  .preview-img {
    max-width: 100px;
    height: auto;
  }
  #schoolStatusTable thead th {
		position: sticky;
		top: 0;
		box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
	}
    #schoolStatusTable tbody td {
		position: sticky;
		color: #000;
	}

    table#schoolStatusTable th.dt-orderable-none .dt-column-order {
	    display: none !important;
	}
	.swal2-container {
		z-index: 99999 !important;
	}
</style>
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

<div class="container">
    <div class="t-mrg2 pb-5">
        {{-- Header --}}
        <div class="all-chaptr-cards mb-4" style="margin: 0;">
            <div class="row">
                <x:back-button title="{{ $title }}">
                    <x-slot name="actionsOutside">
				        <div >				        	
				        	<button id="showFormBtn" class="btn btn-success" style="display: {{ $newReg->isEmpty() ? 'none' : 'block' }};"><i class="fa-solid fa-plus"></i> Register School</button>
							<button id="showTableBtn" class="btn btn-secondary" style="display:none;">← Back to List</button>
						</div>
				    </x-slot>
                </x:back-button>
            </div>
        </div>
		<div id="schoolTableContainer" style="display: {{ $newReg->isEmpty() ? 'none' : 'block' }};">
			<table id="schoolStatusTable" class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>#</th>
						<th>Code</th>
						<th>School Name</th>
						<th>Principal</th>
						<th>Region</th>
						<th>Zone</th>
						<th>State</th>
						<th>District</th>
						<th scope="col" width="18%">Action</th>
					</tr>
				</thead>
			</table>
		</div>
		<div class="filter-bx" id="schoolFormContainer" style="display: {{ $newReg->isEmpty() ? 'block' : 'none' }};">
			<h5 class="card-title text-center mb-0">School Details</h5> 
			<form id="addSchoolForm" method="post" action="{{ route('add-school-record') }}" enctype="multipart/form-data">
				@csrf

				<!-- School Details -->
				<h5 class="mb-3 text-primary">🏫 School Details</h5>
				<div class="form-row">
					<div class="form-group col-md-2">
						<label for="schoolCode">School Code (U-DISE Code) <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="schoolCode" name="school_code" placeholder="Enter School Code" required>
						<span class="text-danger error-text school_code_error"></span>
					</div>
					<div class="form-group col-md-4">
						<label for="schoolName">School Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="schoolName" name="school_name" placeholder="Enter School Name" required>
						<span class="text-danger error-text school_name_error"></span>
					</div>
					<div class="form-group col-md-4">
						<label for="schoolEmail">School Email <span class="text-danger">*</span></label>
						<input type="email" class="form-control" id="schoolEmail" name="school_email" placeholder="school@example.com" required>
						<span class="text-danger error-text school_email_error"></span>
					</div>    
					<div class="form-group col-md-2">
						<label for="shift">Shift <span class="text-danger">*</span></label>
						<select class="form-control" id="shift" name="shift" required>
							<option value="">-- Select Shift --</option>
							<option value="Day">Morning</option>
							<option value="Day/Residential">Residential</option>
							
						</select>
						<span class="text-danger error-text shift_error"></span>
					</div>                        
				</div>

				<!-- Principal Details -->
				<h5 class="mb-3 mt-4 text-primary">👨‍🏫 Principal Details</h5>
				<div class="form-row">
					<div class="form-group col-md-4">
						<label for="principalName">Principal Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="principalName" name="principal_name" placeholder="Enter Principal Name" required>
						<span class="text-danger error-text principal_name_error"></span>
					</div>
					<div class="form-group col-md-4">
						<label for="principalEmail">Principal Email <span class="text-danger">*</span></label>
						<input type="email" class="form-control" id="principalEmail" name="principal_email" placeholder="principal@example.com" required>
						<span class="text-danger error-text principal_email_error"></span>
					</div>
					<div class="form-group col-md-2">
						<label for="position">Position <span class="text-danger">*</span></label>
						<select class="form-control" name="position" id="position" required>
							<option value="Principal">Principal</option>
							{{-- <option value="HM">HM</option> --}}
						</select>
						<span class="text-danger error-text position_error"></span>
					</div>
					<div class="form-group col-md-2">
						<label for="gender">gender <span class="text-danger">*</span></label>
						<select class="form-control" name="gender" id="gender" required>
							<option value="Male">Male</option>
							<option value="Female">Female</option>
						</select>
						<span class="text-danger error-text gender_error"></span>
					</div>
				</div>

				<!-- Location Details -->
				<h5 class="mb-3 mt-4 text-primary">📍 Location Details</h5>
				<div class="form-row">
					<div class="form-group col-md-3">
						<label for="region">Region Name</label>
						<select class="form-control" id="region" name="region" required>
							<option value="">-- Select Region --</option>
							@foreach($regions as $region)
								<option value="{{ $region->name }}|{{ $region->id }}">
									{{ $region->name }}
								</option>
							@endforeach
						</select>
						<span class="text-danger error-text region_error"></span>
					</div>
                    <div class="form-group col-md-3">
						<label for="zone">Zone</label>
						<select class="form-control" id="zone" name="zone" required>
							 <option value="">-- Select Zone --</option>
                        </select>
						<span class="text-danger error-text district_error"></span>
					</div>		
					<div class="form-group col-md-3">
						<label for="state">State</label>
						<select class="form-control" id="state" name="state" required>
							<option value="">-- Select State --</option>
							@foreach($states as $state)
								<option value="{{ $state->id }}|{{ $state->name }}">{{ $state->name }}</option>

							@endforeach
						</select>
						<span class="text-danger error-text state_error"></span>
					</div>
					<div class="form-group col-md-3">
						<label for="district">District</label>
						<select class="form-control" id="district" name="district" required>
							<option value="">-- Select District --</option>
						</select>
						<span class="text-danger error-text district_error"></span>
					</div>																									
					
					<div class="form-group col-md-4">
						<label for="city">City</label>
						<input type="text" class="form-control" id="city" name="city" placeholder="Enter City">
					</div>
					<div class="form-group col-md-4">
						<label for="address">Address</label>
						<input type="text" class="form-control" id="address" name="address" placeholder="Enter Address">
					</div>
					<div class="form-group col-md-3">
						<label for="pincode">Pincode</label>
						<input type="text" class="form-control" id="pincode" name="pincode" inputmode="numeric" maxlength="6" pattern="\d{6}" placeholder="Enter Pincode">
					</div>
					
				</div>

				<!-- Submit Button -->
				<div class="text-center mt-4">
					<button type="submit" class="btn btn-primary px-4 py-2">Submit Registration</button>
				</div>
			</form>
		</div>
    </div>
</div>


<!-- aprroval modal -->
<div class="modal fade" id="approveSchoolModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Approve School</h5>
				<button type="button" class="close" data-dismiss="modal">
				<span>&times;</span>
				</button>
			</div>
			<form id="approveSchoolForm">
				<div class="modal-body">

					<input type="hidden" id="apr_school_id" name="school_id">
					<input type="hidden" name="action_type" id="action_type" value="approve">

					<h5 class="mb-3 text-primary">🏫 School Details</h5>

					<div class="form-row">

						<div class="form-group col-md-2">
							<label>School Code (U-DISE Code)</label>
							<input type="text" class="form-control" id="apr_school_code" name="school_code">
						</div>

						<div class="form-group col-md-4">
							<label>School Name</label>
							<input type="text" class="form-control" id="apr_school_name" name="school_name">
						</div>

						<div class="form-group col-md-4">
							<label>School Email</label>
							<input type="email" class="form-control" id="apr_school_email" name="school_email">
						</div>

						<div class="form-group col-md-2">
							<label>Shift</label>
							<select class="form-control" id="apr_school_shift" name="school_shift">
								<option value="">-- Select Shift --</option>
								<option value="Day">Morning</option>
								<option value="Day/Residential">Residential</option>
							</select>
						</div>

					</div>

					<!-- Principal -->
					<h5 class="mb-3 mt-4 text-primary">👨‍🏫 Principal Details</h5>

					<div class="form-row">

						<div class="form-group col-md-4">
							<label>Principal Name</label>
							<input type="text" class="form-control" id="apr_principal_name" name="principal_name">
						</div>

						<div class="form-group col-md-4">
							<label>Principal Email</label>
							<input type="email" class="form-control" id="apr_principal_email" name="principal_email">
						</div>

						<div class="form-group col-md-2">
							<label>Position</label>
							<select class="form-control" id="apr_position" name="position">
								<option value="Principal">Principal</option>
							</select>
						</div>

						<div class="form-group col-md-2">
							<label>Gender</label>
							<select class="form-control" id="apr_gender" name="gender">
								<option value="Male">Male</option>
								<option value="Female">Female</option>
							</select>
						</div>

					</div>

					<!-- Location -->
					<h5 class="mb-3 mt-4 text-primary">📍 Location Details</h5>

					<div class="form-row">

						<div class="form-group col-md-3">
							<label>Region</label>
							<select class="form-control" id="apr_region" name="region" required>
								<option value="">-- Select Region --</option>
								@foreach($regions as $region)
									<option value="{{ $region->name }}">
										{{ $region->name }}
									</option>
								@endforeach
							</select>
						</div>

						<div class="form-group col-md-3">
							<label>State</label>
							<select class="form-control" id="apr_state" name="state" required>
								<option value="">-- Select State --</option>
								@foreach($states as $state)
									<option value="{{ $state->id }}|{{ $state->name }}">
										{{ $state->name }}
									</option>
								@endforeach
							</select>
						</div>

						<div class="form-group col-md-3">
							<label>District</label>
							<select class="form-control" id="apr_district" name="district" required>
								<option value="">-- Select District --</option>
								@foreach($district as $district)
									<option value="{{ $district->id }}|{{ $district->name }}">
										{{ $district->name }}
									</option>
								@endforeach
							</select>
						</div>

						<div class="form-group col-md-3">
							<label>Pincode</label>
							<input type="text" class="form-control" id="apr_pincode" name="pincode">
						</div>

						<div class="form-group col-md-4">
							<label>Zone Name</label>
							<input type="text" class="form-control" id="apr_zonename" name="zonename">
						</div>

						<div class="form-group col-md-4">
							<label>City</label>
							<input type="text" class="form-control" id="apr_city" name="city">
						</div>

						<div class="form-group col-md-4">
							<label>Address</label>
							<input type="text" class="form-control" id="apr_address" name="address">
						</div>

					</div>

				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" id="rejectBtn" class="btn btn-danger">Reject</button>
					<button type="submit" id="approveBtn" class="btn btn-primary">Approve</button>
				</div>
			</form>
		</div>
	</div>
</div>


<script>
	$(document).ready(function () {
		$('#showFormBtn').click(function () {
			$('#schoolTableContainer').hide();
			$('#schoolFormContainer').show();

			$('#showFormBtn').hide();
			$('#showTableBtn').show();
		});

		$('#showTableBtn').click(function () {
			$('#schoolFormContainer').hide();
			$('#schoolTableContainer').show();

			$('#showTableBtn').hide();
			$('#showFormBtn').show();
		});

		$("#region").on("change", function () {
			let regionValue = $(this).val();
			const selectZone = $('#zone');
        	selectZone.empty().append('<option value="">Select Zone</option>');
			if(regionValue != ''){
				let regionArr = regionValue.split('|');
				let regionId = regionArr[1];
				$.ajax({
					url: "{{ route('school.getZones') }}",
					type: "POST",
					data: {
						regionId: regionId,
						_token: "{{ csrf_token() }}"
					},
                	dataType: 'json',
					success: function(response) {
						$.each(response, function(index, zones) {
							selectZone.append(
								$('<option>', {
									value: zones.name,
									text: zones.name
								})
							);
						});
					},
					error: function(xhr, status, error) {
						console.error("AJAX Error:", xhr.responseText);
					}
				});
			}
		});

		$("#state").on("change", function () {
			loadDistrict($(this).val(), "#district");
		});

		$("#apr_state").on("change", function () {
			loadDistrict($(this).val(), "#apr_district");
		});


		function loadDistrict(stateId, target) {

			$(target).html('<option value="">-- Select District --</option>');

			if (stateId) {
				$.ajax({
					url: "get-districts/" + stateId,
					type: "GET",
					dataType: "json",
					success: function (data) {

						$.each(data, function (id, name) {
							$(target).append(
								'<option value="'+ id +'|'+ name +'">'+ name +'</option>'
							);
						});

					},
					error: function (xhr) {
						console.log(xhr.responseText);
					}
				});
			}
		}
	});

	$(document).ready(function() {

		// Attach CSRF token to all AJAX requests
		$.ajaxSetup({
			headers: {
				"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
			}
		});

		$("#addSchoolForm").on("submit", function(e) {
			e.preventDefault();

			$(".error-text").text(""); 
			$.ajax({
				url: "{{ route('add-school-record') }}",
				type: "POST",
				data: new FormData(this),
				contentType: false,
				processData: false,
				dataType: "json",

				success: function(response) {
					if (response.status === 0) {
						$.each(response.errors, function(key, val) {
							$("." + key + "_error").text(val[0]);
						});
					} else {
						Swal.fire({
							icon: "success",
							title: "Success",
							text: response.message,
							timer: 2000,
							showConfirmButton: false
						});
						$("#addSchoolForm")[0].reset();
					}
				},

				error: function(xhr) {
					console.log("Error Response:", xhr.responseText);
					Swal.fire({
						icon: "error",
						title: "Error",
						text: xhr.responseJSON?.message || "Something went wrong! Please try again."
					});
				}
			});
		});
	});

	$(document).ready(function() {
		var table = $('#schoolStatusTable').DataTable({
			info: true,
			processing: true,
			serverSide: true,
			searching: true,
			pageLength: 100,
			lengthMenu: [[100, 200, 300, -1], [100, 200, 300 , "All"]],
			dom: `<"top"lf><"filter-right"B>rt<"bottom"ip><"clear">`,

			ajax: {
				url: "{{ route('cicse.addschool') }}",
				data: function(d) {
					d.status = $('#status').val();
				},
				beforeSend: function () {
					$('#datatable-loader').show();
				},
				complete: function () {
					$('#datatable-loader').hide();
				}
			},

			columns: [
				{
					data: null,
					orderable: false,
					searchable: false,
					render: function (data, type, row, meta) {
						return meta.row + meta.settings._iDisplayStart + 1;
					}
				},
				{ data: 'school_code', name: 'school_code' },
				{ data: 'school_name', name: 'school_name' },
				{ data: 'principal_name', name: 'principal_name'},
				{ data: 'region', name: 'region'},
				{ data: 'zonename', name: 'zonename'},
				{ data: 'state', name: 'state'},
				{ data: 'district', name: 'district'},
				{
					data: null,
					orderable: false,
					searchable: false,
					className: 'text-center',
					render: function(data, type, row) {
						if (row.status == 0) {
							return `
								<button type="button"
									class="btn btn-sm btn-primary approve-btn"
									data-id="${row.school_id}">
									<i class="fas fa-eye"></i> Pending
								</button>
							`;
						} else if (row.status == 2) {
							return `
								<button type="button"
									class="btn btn-sm btn-danger"
									disabled>
									<i class="fa-regular fa-circle-xmark"></i> Rejected
								</button>
							`;
						}

						return `
							<span class="badge bg-secondary">Unknown</span>
						`;
					}
				}
			],

			order: [[1, 'asc']],

			buttons: [{
				extend: 'collection',
				text: 'Export',
				className: 'exportButton itm-list',
				buttons: [{
					extend: 'excel',
					title: 'Schools List',
					filename: function () {
						return getTimestamp();
					},
				}],
			}],

			initComplete: function () {
				$('.dt-search input[type="search"]').attr('placeholder', 'Search here...');

				const $statusDropdown = $('<select class="form-control ml-2" id="status"></select>')
					.append(`
						<option value="">All</option>
						<option value="0">Pending</option>
						<option value="2">Rejected</option>
					`);

				const $filterContainer = $('<div class="d-flex gap-2"></div>')
					.append($statusDropdown);

				$('<div class="pull-right"></div>')
					.append($filterContainer)
					.appendTo("#schoolStatusTable_wrapper .top");
				$statusDropdown.on('change', () => table.ajax.reload());
			},

			language: {
				paginate: {
					first: "«",
					previous: "‹",
					next: "›",
					last: "»"
				}
			}
		});

		function getTimestamp() {
			const now = new Date();
			return `Pending Schools ${now.getDate()}-${now.getMonth()+1}-${now.getFullYear()}`;
		}
	});


	$('#schoolStatusTable').on('click', '.approve-btn', function () {

		let table = $('#schoolStatusTable').DataTable();
		let row = table.row($(this).closest('tr')).data();
		let schoolId = row.school_id;

		$.ajax({
			url: "{{ route('cicse.addschool') }}",
			type: 'GET',
			data: { schoolId: schoolId },
			success: function(res) {

				console.log(res);

				let data = res.data[0];

				$('#approveSchoolForm')[0].reset();

				$('#apr_school_id').val(data.school_id);
				$('#apr_school_code').val(data.school_code);
				$('#apr_school_name').val(data.school_name);
				$('#apr_school_email').val(data.school_email);
				$('#apr_school_shift').val(data.school_shift);
				$('#apr_principal_name').val(data.principal_name);
				$('#apr_principal_email').val(data.email);
				$('#apr_position').val(data.position);
				$('#apr_gender').val(data.gender);
				$('#apr_pincode').val(data.pincode);
				$('#apr_zonename').val(data.zonename);
				$('#apr_city').val(data.city);
				$('#apr_address').val(data.address);
				$('#apr_region').val(data.region);
				$('#apr_state').val(data.state_id + '|' + data.state);
				$('#apr_district').val(data.district_id + '|' + data.district);
				$('#approveSchoolModal').modal('show');
			}
		});

		
	});


	// School approval 
	let actionType = "approve";	
	$("#approveBtn").on("click", function () {
		$("#action_type").val("approve");
		let formData = new FormData($("#approveSchoolForm")[0]);
		submitApproveForm(formData, "approve");
	});

	$("#rejectBtn").on("click", function () {
		$('#approveSchoolModal').modal('hide');
		setTimeout(() => {
			Swal.fire({
				title: "Reject School",
				input: "textarea",
				inputPlaceholder: "Enter reject reason",
				icon: "warning",
				showCancelButton: true,
				confirmButtonText: "Reject",
				cancelButtonText: "Cancel",

				inputValidator: (value) => {
					if (!value) {
						return "Reject reason is required!";
					}
				}
			}).then((result) => {
				if (result.isConfirmed) {
					let reason = result.value;
					$("#action_type").val("reject");
					let formData = new FormData($("#approveSchoolForm")[0]);
					formData.append("reject_reason", reason);

					submitApproveForm(formData, "reject");
				} else {
					$('#approveSchoolModal').modal('show');
				}
			});
		}, 300);

	});

	
	function submitApproveForm(formData, actionType) {
		$.ajax({
			url: "{{ route('cicse.approveSchool') }}",
			type: "POST",
			data: formData,
			contentType: false,
			processData: false,

			beforeSend: function () {
				$("#approveBtn, #rejectBtn").prop("disabled", true);
			},

			success: function(res) {

				$("#approveBtn, #rejectBtn").prop("disabled", false);

				if (res.status === 1) {
					let title = actionType === "approve" ? "Approved!" : "Rejected!";
					Swal.fire({
						icon: "success",
						title: title,
						text: res.message,
						showConfirmButton: true,
						AllowOutsideClick: false,
					});
					$("#approveSchoolModal").modal("hide");
					$('#schoolStatusTable').DataTable().ajax.reload(null, false);

				} else {

					let msg = res.message || "Something went wrong";
					if (res.errors) {
						msg = Object.values(res.errors).join("\n");
					}
					Swal.fire({
						icon: "error",
						title: "Error",
						text: msg
					});
				}
			},

			error: function(xhr) {

				$("#approveBtn, #rejectBtn").prop("disabled", false);

				Swal.fire({
					icon: "error",
					title: "Server Error",
					text: xhr.responseJSON?.message || "Something went wrong"
				});
			}
		});
	}

</script>


@endsection