@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
@section('content')

<style>
  .preview-img {
    max-width: 100px;
    height: auto;
  }
  #superAdminStatusTable thead th {
		position: sticky;
		top: 0;
		box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
	}
    #superAdminStatusTable tbody td {
		position: sticky;
		color: #000;
	}

    table#superAdminStatusTable th.dt-orderable-none .dt-column-order {
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
    <div class="t-mrg2 mb-5 pb-5">
		{{-- Header --}}
		<div class="all-chaptr-cards mb-4" style="margin: 0;">
			<div class="row">
				<x:back-button title="{{$title}}" >
					<x-slot name="actionsOutside">
				        <div >				        	
				        	<button id="showFormBtn" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Define Roles</button>
				        	<button class="btn btn-sm btn-primary" onclick="downloadCredentials();"><i class="fas fa-download"></i> Download Credentials</button>
							<button id="showTableBtn" class="btn btn-dark btn-sm" style="display:none;"><i class="fas fa-arrow-left"></i> Back</button>
						</div>
				    </x-slot>
				</x:back-button>
			</div>
		</div>
		<div id="superAdminTable">
			<table id="superAdminStatusTable" class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>#</th>
						<th>Name</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Role</th>
						<th>Region</th>
						<th>Zone</th>
						<th>Gender</th>
						<!-- <th>Status</th> -->
						<th>Action</th>
					</tr>
				</thead>
			</table>
		</div>

		<!-- Add Super Admin Form  -->
		<div class="filter-bx" id="superadminFormContainer" style="display:none;">
			<form id="addSuperAdmin" method="post" action="{{ route('add.superadmin') }}" enctype="multipart/form-data" autocomplete="off">
				@csrf
				<!-- Super Admin Details -->
				<h5 class="mb-3 mt-4 text-primary">👨‍🏫 Admin Details</h5>
				<div class="form-row">
					<div class="form-group col-md-3">
						<label for="designation">Role <span class="text-danger">*</span></label>
						<select class="form-control" name="designation" id="designation" required>
							<option value="">Select Role</option>
							<option value="2">Super Admin</option>
							<option value="5">Regional Head</option>
							<option value="6">Zonal Head</option>
						</select>
						<span class="text-danger error-text designation_error"></span>
					</div>
					<div class="form-group col-md-3">
						<label for="superadmin_name">Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="superadmin_name" name="superadmin_name" placeholder="Enter Name" required>
						<span class="text-danger error-text superadmin_name_error"></span>
					</div>
					<div class="form-group col-md-3">
						<label for="superadminEmail">Email <span class="text-danger">*</span></label>
						<input type="email" class="form-control" id="superadminEmail" name="superadmin_email" placeholder="superadmin@example.com" autocomplete="new-email" required>
						<span class="text-danger error-text superadmin_email_error"></span>
					</div>
					<div class="form-group col-md-3">
						<label for="superadminphone">Contact <span class="text-danger">*</span></label>
						<input type="tel" class="form-control" id="superadminphone" name="superadmin_phone" placeholder="9876543210" required>
						<span class="text-danger error-text superadmin_phone_error"></span>
					</div>
					<div class="form-group col-md-3">
						<label for="gender">Gender <span class="text-danger">*</span></label>
						<select class="form-control" name="gender" id="gender" required>
							<option value="">Select Gender</option>
							<option value="Male">Male</option>
							<option value="Female">Female</option>
						</select>
						<span class="text-danger error-text gender_error"></span>
					</div>
					<div class="form-group col-md-3" id="regionGroup">
						<label for="region">Region <span class="text-danger">*</span></label>
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
                    <div class="form-group col-md-3" id="zoneGroup">
						<label for="zone">Zone <span class="text-danger">*</span></label>
						<select class="form-control" id="zone" name="zone" required>
							 <option value="">-- Select Zone --</option>
                        </select>
						<span class="text-danger error-text district_error"></span>
					</div>	

					
					<!-- <div class="form-group col-md-4">
						<label for="password">Password <span class="text-danger">*</span></label>
						<input type="password" class="form-control" id="password" name="password" autocomplete="new-password" required>
						<span class="text-danger error-text password_error"></span>
					</div>
					<div class="form-group col-md-4">
						<label for="password_confirmation">Confirm Password<span class="text-danger">*</span></label>
						<input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required>
						<span class="text-danger error-text password_confirmation_error"></span>
					</div> -->
				</div>
				<!-- Submit Button -->
				<div class="text-center mt-4">
					<button type="submit" class="btn btn-primary px-4 py-2">Submit</button>
				</div>
			</form>
		</div>
    </div>
</div>

 <!-- Edit Super Admin Modal -->
<div class="modal fade bd-example-modal-lg" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Edit Super Admin</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

            <form id="editSuperAdmin">
                @csrf
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Name</label>
                            <input type="text" class="form-control" name="superadmin_name" id="edit_name">
                            <span class="text-danger error-text superadmin_name_error"></span>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <input type="email" class="form-control" name="superadmin_email" id="edit_email">
                            <span class="text-danger error-text superadmin_email_error"></span>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Phone</label>
                            <input type="text" class="form-control" name="superadmin_phone" id="edit_phone">
                            <span class="text-danger error-text superadmin_phone_error"></span>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Gender</label>
                            <select class="form-control" name="gender" id="edit_gender">
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                            <span class="text-danger error-text gender_error"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"> Close </button>
                    <button type="submit" class="btn btn-primary"> Update </button>
                </div>

            </form>

        </div>
    </div>
</div>


<script>
	$(document).ready(function() {
		var table = $('#superAdminStatusTable').DataTable({
			info: true,
			processing: true,
			serverSide: true,
			searching: true,
			pageLength: 100,
			lengthMenu: [[100, 200, 300, -1], [100, 200, 300 , "All"]],
			dom: `<"top"lf><"filter-right"B>rt<"bottom"ip><"clear">`,

			ajax: {
				url: "{{ route('view.superadmin') }}",
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
				{ data: 'superadmin_name', name: 'superadmin_name' },
				{ data: 'email', name: 'email' },
				{ data: 'phone', name: 'phone'},
				{ data: 'designation', name: 'designation'},
				{ data: 'region_id', name: 'region_id'},
				{ data: 'zone_id', name: 'zone_id'},
				{ data: 'gender', name: 'gender'},
				// { data: 'status', name: 'status'},
				{
					data: null,
					orderable: false,
					searchable: false,
					className: 'text-center',
					defaultContent: '',
					render: function(data, type, row) {
						return ` 
							<div class="d-flex align-items-center">
								<button class="btn btn-sm btn-primary mr-1 userProfile" 
										data-id="${row.id}">
										<i class="fas fa-edit"></i>
								</button>

								<button class="btn btn-sm btn-danger deleteUser" 
										data-id="${row.id}">
										<i class="fas fa-trash"></i>
								</button>
							</div>
						`;
					}
				},
				
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
						<option value="1">Active</option>
						<option value="2">Inactive</option>
					`);

				const $filterContainer = $('<div class="d-flex gap-2"></div>')
					.append($statusDropdown);

				$('<div class="pull-right"></div>')
					.append($filterContainer)
					.appendTo("#superAdminStatusTable_wrapper .top");
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

		$('#showFormBtn').click(function () {
			$('#superAdminTable').hide();
			$('#superadminFormContainer').show();

			$('#showFormBtn').hide();
			$('#showTableBtn').show();
		});

		$('#showTableBtn').click(function () {
			$('#superadminFormContainer').hide();
			$('#superAdminTable').show();

			$('#showTableBtn').hide();
			$('#showFormBtn').show();
		});

		// Role-based Region / Zone visibility
		$("#designation").on("change", function () {
			let role = $(this).val();

			// Reset first
			$("#regionGroup").show();
			$("#zoneGroup").show();

			$("#region").prop("required", true);
			$("#zone").prop("required", true);

			if (role == "2") {
				// Super Admin
				$("#regionGroup").hide();
				$("#zoneGroup").hide();

				$("#region").prop("required", false).val("");
				$("#zone").prop("required", false).val("");

			} else if (role == "5") {
				// Regional Head
				$("#zoneGroup").hide();

				$("#region").prop("required", true);
				$("#zone").prop("required", false).val("");

			} else if (role == "6") {
				// Zonal Head
				$("#regionGroup").show();
				$("#zoneGroup").show();

				$("#region").prop("required", true);
				$("#zone").prop("required", true);
			}
		});


		// zone dropdown 

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
									value: zones.name + '|' + zones.id,
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

		// Add super admin 

		$("#addSuperAdmin").on("submit", function(e) {
			e.preventDefault();

			$(".error-text").text(""); 
			$.ajax({
				url: "{{ route('add.superadmin') }}",
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
							showConfirmButton: true,
							allowOutsideClick: false
						}).then(() => {
							$("#addSuperAdmin")[0].reset();
							location.reload();
						});
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

		// edit super admin 
		$(document).on('click', '.userProfile', function () {
			let id = $(this).data('id');
			$.ajax({
				url: "{{ route('get.superadmin') }}",
				type: "get",
				data: {
					id: id,
					_token: "{{ csrf_token() }}"
				},
				dataType: "json",
				success: function (response) {
					if (response.status == 1) {
						$("#edit_id").val(response.data.id);
						$("#edit_name").val(response.data.name);
						$("#edit_email").val(response.data.email);
						$("#edit_phone").val(response.data.phone);
						$("#edit_gender").val(response.data.gender);

						$("#editModal").modal('show');

					} else {
						Swal.fire({
							icon: "error",
							title: "Error",
							text: response.message
						});

					}
				},
				error: function (xhr) {
					Swal.fire({
						icon: "error",
						title: "Error",
						text: xhr.responseJSON?.message || "Something went wrong!"
					});
				}
			});
		});

		// update super admin profile 

		$("#editSuperAdmin").on("submit", function (e) {
			e.preventDefault();
			$(".error-text").text("");
			
			$.ajax({
				url: "{{ route('update.superadmin') }}",
				type: "POST",
				data: new FormData(this),
				contentType: false,
				processData: false,
				dataType: "json",
				success: function (response) {
					if (response.status == 0) {
						$.each(response.errors, function (key, val) {
							$("." + key + "_error").text(val[0]);
						});
					} else {
						$("#editModal").modal("hide");
						Swal.fire({
							icon: "success",
							title: "Success",
							text: response.message,
							showConfirmButton: true,
							allowOutsideClick: false
						});
						$('#superAdminStatusTable')
							.DataTable()
							.ajax.reload(null, false);
					}
				},
				error: function (xhr) {
					Swal.fire({
						icon: "error",
						title: "Error",
						text: xhr.responseJSON?.message || "Something went wrong!"
					});
				}
			});
		});


		// delete super admin 
		$(document).on('click', '.deleteUser', function() {
			let userId = $(this).data('id');
			Swal.fire({
				title: "Are you sure?",
				text: "You won't be able to revert this!",
				icon: "warning",
				showCancelButton: true,
				confirmButtonColor: "#d33",
				cancelButtonColor: "#919191",
				confirmButtonText: "Yes, delete it!",				
				allowOutsideClick: false,			
				reverseButtons: true,
			}).then((result) => {
				if (result.isConfirmed) {
					$.ajax({
						url: "{{ route('delete.superadmin') }}",
						type: "POST",
						data: {
							id: userId,
							_token: "{{ csrf_token() }}"
						},

						success: function(response) {
							if (response.status == 1) {
								Swal.fire({
									icon: 'success',
									title: 'Deleted!',
									text: response.message,
									showConfirmButton: true,
									allowOutsideClick: false
								}).then(() => {
									$('#superAdminStatusTable')
										.DataTable()
										.ajax.reload(null, false);
								});
							} else {
								Swal.fire({
									icon: 'error',
									title: 'Error!',
									text: response.message,
									showConfirmButton: true,
									allowOutsideClick: false
								});
							}
						},

						error: function(xhr) {
							Swal.fire({
								icon: 'error',
								title: 'Error!',
								text: xhr.responseJSON?.message || 'Something went wrong!',
								showConfirmButton: true,
								allowOutsideClick: false
							});
						}
					});
				}
			});
		});


		function getTimestamp() {
			const now = new Date();
			return `Pending Schools ${now.getDate()}-${now.getMonth()+1}-${now.getFullYear()}`;
		}
	});

	function downloadCredentials() {
		Swal.fire({
			title: "Download",
			text: "Are you sure you want to download the admins' login credentials?",
			icon: "question",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#919191",
			confirmButtonText: "Yes, Download",
			cancelButtonText: "Cancel",
			allowOutsideClick: false,			
			reverseButtons: true,
		}).then((result) => {

			if (!result.isConfirmed) {
				return;
			}

			Swal.fire({
				title: "Generating File...",
				text: "Please wait while credentials are being prepared.",
				allowOutsideClick: false,
				allowEscapeKey: false,
				didOpen: () => {
					Swal.showLoading();
				}
			});

			$.ajax({
				url: "{{ route('export.credentials') }}",
				type: "POST",
				data: {
					_token: "{{ csrf_token() }}"
				},
				xhrFields: {
					responseType: 'blob'
				},

				success: function (blob, status, xhr) {

					Swal.close();

					let filename = "credentials.xlsx";

					const disposition = xhr.getResponseHeader(
						'Content-Disposition'
					);

					if (disposition && disposition.indexOf('filename=') !== -1) {
						filename = disposition
							.split('filename=')[1]
							.replace(/"/g, '');
					}

					const url = window.URL.createObjectURL(blob);
					const a = document.createElement('a');

					a.href = url;
					a.download = filename;

					document.body.appendChild(a);
					a.click();

					a.remove();
					window.URL.revokeObjectURL(url);

					Swal.fire({
						icon: "success",
						title: "Download Complete!",
						text: "Admin Credentials downloaded successfully."
					});
				},

				error: function (xhr) {

					Swal.close();

					let errorMessage =
						"An error occurred while generating file.";

					try {
						const response = JSON.parse(xhr.responseText);
						errorMessage = response.message || errorMessage;
					} catch (e) {}

					Swal.fire({
						icon: 'error',
						title: 'Download Failed',
						text: errorMessage
					});
				}
			});
		});
	}


</script>


@endsection