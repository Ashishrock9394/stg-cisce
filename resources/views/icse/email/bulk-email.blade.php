@extends('layouts.filldart-app')
@section('title', $title)
@section('content')
<style>
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

</style>

<div class="container">
    <div class="t-mrg2 mb-5 pb-5">
        <div class="all-chaptr-cards mb-4" style="margin: 0;">
			<div class="row">
				<x:back-button title="{{$title}}" >
				</x:back-button>
			</div>
		</div>

        <table id="schoolStatusTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>School Code</th>
                    <th>School Name</th>
                    <th>Region</th>
                    <th width="6%">Zone</th>
                    <th>Total Students</th>
                    <th>Assessments Completed</th>
                    <th>Ongoing</th>
                    <th>Yet to Start</th>
                    <th>Sent Email</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>

        
    </div>
</div>
@include('reports.modals.check-sent-reports')

<script>
	$(document).ready(function() {
		var table = $('#schoolStatusTable').DataTable({
			info: true,
			processing: true,
			serverSide: true,
			searchable: true,
			pageLength: 100, // default rows per page
			lengthMenu: [[100, 200, 300, -1], [100, 200, 300 , "All"]],
			dom: `<"top"lf><"filter-right"B>rt<"bottom"ip><"clear">`,
			
			ajax: {
				url: "{{ route('send.report.view') }}",
				data: function(d) {
					d.region = $('#select_region').val();
					d.zone = $('#select_zone').val();
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
					name: 'serial_no',
					orderable: false,
					searchable: false,
					render: function (data, type, row, meta) {
						return meta.row + meta.settings._iDisplayStart + 1;
					}
				},
				{ data: 'school_code', name: 'school_code' },
				{ data: 'school_name', name: 'school_name' },
				{ data: 'region', name: 'region' },
				{ data: 'zonename', name: 'zonename' },
				{ data: 'registered_students', name: 'registered_students' },
				{ data: 'completed', name: 'completed' },
				{ data: 'ongoing', name: 'ongoing' },
				{ data: 'yet_to_start', name: 'yet_to_start' },
				{
				    data: 'email_sent',
				    name: 'email_sent',
				    orderable: false,
				    searchable: false,
				    className: 'text-center',
				    render: function (data, type, row) {
				        return (data === null || data === undefined) ? 0 : data;
				    }
				},
                {
                    data: null,
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function (data, type, row) {
                        return `
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-sm btn-primary mr-1 validate-email" data-id="${row.school_id}">
                                    Validate
                                </button>

                                <button type="button" class="btn btn-sm btn-success history" data-id="${row.school_id}">
                                    History
                                </button>
                            </div>
                        `;
                    }
                },

			],

			buttons: [{
				extend: 'collection',
				text: 'Export',
				className: 'exportButton itm-list',
				buttons: [
					{
						extend: 'excel',
						title: 'Schools Assessment List',
						filename: function () {
							return getTimestamp();
						},
					},
				],
			}],

			order: [[6, 'desc']],

			initComplete: function () {
				$('.dt-search input[type="search"]').attr('placeholder', 'Search here...');

				var regions = @json($region);
				// Region Dropdown
				const $regionDropdown = $('<select class="form-control" id="select_region"></select>')
					.append('<option value="">All Regions</option>');

					regions.forEach(region => {
					$regionDropdown.append(`<option value="${region.name}|${region.id}">${region.name}</option>`);
				});

				var zones = @json($zones);
                const $zoneDropdown = $('<select class="form-control ml-2" id="select_zone"></select>')
                    .append('<option value="">All Zones</option>');
				
				$regionDropdown.on('change', function () {
					let selectedValue = $(this).val();
					let regionData = selectedValue.split('|');
					let regionId = regionData[1];                
					$zoneDropdown.html('<option value="">All Zones</option>');
					if (regionId !== '') {
						let filteredZones = zones.filter(zone => 
							zone.region_id == regionId
						);
						filteredZones.forEach(zone => {
							$zoneDropdown.append(
								`<option value="${zone.name}">${zone.name}</option>`
							);
						});
					}
				});

				const $uploadDropdown = $('<select class="form-control ml-2" id="status"></select>')
					.append(`<option value="">All Data</option>
							<option value="completed">Completed</option>
							<option value="ongoing">Ongoing</option>
							<option value="yet_to_start">Yet To Start</option>`);

				const $filterContainer = $('<div class="d-flex gap-2"></div>')
					.append($regionDropdown).append($zoneDropdown)
					.append($uploadDropdown);

				$('<div class="pull-right"></div>')
					.append($filterContainer)
					.appendTo("#schoolStatusTable_wrapper .top");

				// Reload table on filter change
				$regionDropdown.on('change', () => table.ajax.reload());
				$zoneDropdown.on('change', () => table.ajax.reload());
				$uploadDropdown.on('change', () => table.ajax.reload());
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
			return `Schools Assessment List ${now.getDate()}-${now.getMonth()+1}-${now.getFullYear()}`;
		}

        // sent email history        
        
        $(document).on('click', '.history', function () {
            const schoolId = $(this).data('id');

            $('#availableDownloadsModal').modal('show');

            $.ajax({
                url: "{{ route('fitness.report.sent') }}",
                type: "GET",
                data: {
                    school_id: schoolId
                },
                success: function (data) {
                    document.getElementById('downloadsContent').innerHTML = data.html;
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching history:', error);
                }
            });
        });



        // validate email 
        $(document).on('click', '.validate-email', function () {
            let button = $(this);
            let schoolId = button.data('id');
            let valid_student_ids = [];

            Swal.fire({
                title: 'Validating Emails...',
                text: 'Please wait while we validate student email addresses.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ route('validate.email') }}",
                type: "POST",

                data: {
                    _token: "{{ csrf_token() }}",
                    school_id: schoolId
                },

                success: function (response) {
                    Swal.close();
                    valid_student_ids = response.validStudentIds || [];
                    Swal.fire({
                        title: 'Email Validation Completed',
                        html: `
                            <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 20px;">

                                <div>
                                    All Students: <strong>${response.total}</strong>
                                    <a href="javascript:void(0)" class="student-list-view ml-3"
                                    data-type="all" style="color: #3085d6;">
                                        View
                                    </a>
                                </div>

                                <div>
                                    Valid Emails: <strong>${response.valid}</strong>
                                    <a href="javascript:void(0)" class="student-list-view ml-3"
                                    data-type="valid" style="color: #198754;">
                                        View
                                    </a>
                                </div>

                                <div>
                                    Invalid Emails: <strong>${response.invalid}</strong>
                                    <a href="javascript:void(0)" class="student-list-view ml-3"
                                    data-type="invalid" style="color: #dc3545;">
                                        View
                                    </a>
                                </div>

                            </div>
                        `,
                        icon: response.invalid === 0 ? 'success' : 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Send Report',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        allowOutsideClick: false,

                        didOpen: () => {
                            document.querySelectorAll('.student-list-view').forEach(link => {
                                link.addEventListener('click', function (event) {
                                    event.preventDefault();

                                    const type = this.dataset.type;
                                    let students_ids = [];

                                    if (type === 'all') {
                                        students_ids = response.allStudentIds || [];
                                        title = 'All';
                                    } else if (type === 'valid') {
                                        students_ids = response.validStudentIds || [];
                                        title = 'Valid';
                                    } else if (type === 'invalid') {
                                        students_ids = response.invalidStudentIds || [];
                                        title = 'Invalid';
                                    }
                                    exportInvalidEmails(students_ids, title);
                                });
                            });
                        }
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            sendReportsEmail(schoolId, valid_student_ids)

                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            console.log('Email validation cancelled');
                        }
                    });
                },
                error: function (xhr) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Failed',
                        text: xhr.responseJSON?.message ||
                            'Something went wrong while validating emails.'
                    });

                },

                complete: function () {
                    button.prop('disabled', false);
                    button.text('Validate');
                }
            });

        });


        function exportInvalidEmails(students_ids, title) {
            if (!students_ids || students_ids.length === 0) {
                return;
            }

            Swal.fire({
				title: "Downloading",
				text: "Please wait...",
				allowOutsideClick: false,
				allowEscapeKey: false,
				didOpen: () => {
					Swal.showLoading();
				}
			});

			$.ajax({
				url: "{{ route('download.invalid.emails') }}",
				type: "POST",
				contentType: "application/json; charset=UTF-8",
                data: JSON.stringify({
                    student_ids: students_ids,
                    title: title,
                    _token: "{{ csrf_token() }}"
                }),
				xhrFields: {
                    responseType: 'blob'
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                },
                success: function (blob, status, xhr) {
                    Swal.close();
                    
                    // Verify that the response is a Blob
                    if (!(blob instanceof Blob)) {
                        Swal.fire({
                            icon: "error",
                            title: "Download Failed",
                            text: "Server returned an unexpected response."
                        });
                        console.error("Unexpected response:", blob);
                        return;
                    }

                    // Check if server returned JSON instead of Excel
                    if (blob.type.includes("application/json") ||
                        blob.type.includes("text/html")) {

                        blob.text().then(function (text) {
                            let message = "Unable to download the Excel file.";

                            try {
                                const response = JSON.parse(text);
                                message = response.message || message;
                            } catch (e) {
                                console.error("Server error response:", text);
                            }

                            Swal.fire({
                                icon: "error",
                                title: "Download Failed",
                                text: message
                            });
                        });

                        return;
                    }

                    let filename = "Email_validation.xlsx";
                    let disposition = xhr.getResponseHeader("Content-Disposition");

                    if (disposition) {
                        const match = disposition.match(
                            /filename\*?=(?:UTF-8''|")?([^";\n]*)/i
                        );

                        if (match && match[1]) {
                            filename = decodeURIComponent(match[1].replace(/"$/, ""));
                        }
                    }

                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement("a");

                    link.href = url;
                    link.download = filename;

                    document.body.appendChild(link);
                    link.click();
                    link.remove();

                    window.URL.revokeObjectURL(url);

                    Swal.fire({
                        icon: "success",
                        title: "Complete!",
                        text: "Email Validation File downloaded successfully."
                    });
                },

                error: function (xhr, status, error) {
                    Swal.close();

                    let message = "Unable to download the Excel file.";

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            message = response.message || message;
                        } catch (e) {
                            console.error("Server response:", xhr.responseText);
                        }
                    }

                    console.error("Download error:", {
                        status: xhr.status,
                        error: error,
                        response: xhr.responseText
                    });

                    Swal.fire({
                        icon: "error",
                        title: "Download Failed",
                        text: message
                    });
                }
			});
        }

        function sendReportsEmail(schoolId, studentIds) {
            const action = "email";

            Swal.fire({
                title: 'Are you sure?',
                html: `
                        <p>Do you want to send report cards via email?</p>
                        <small style="color: #6c757d; display: block; margin-top: 10px;">
                            Note: Fitness reports will be sent only for students who have
                            completed their fitness test and have valid parent email IDs.
                        </small>
                    `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Send',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                allowOutsideClick: false
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }
                Swal.fire({
                    title: 'Processing',
                    text: 'Please wait, your request is being processed.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute('content')
                    },
                    url: "{{ route('send.bulk.emails') }}",
                    method: 'POST',
                    contentType: "application/json",
                    data: JSON.stringify({
                        _token: "{{ csrf_token() }}",
                        schoolId: schoolId,
                        studentIds: studentIds,
                        action: action
                    }),

                    success: function(data) {
                        Swal.close();

                        if (data.status && data.message) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Request Submitted Successfully',
                                text: data.message,
                                showCancelButton: false,
                                allowOutsideClick: false,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Notice',
                                text: data.message || 'The request could not be processed.'
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
            });
        }
        
	});

</script>

@endsection