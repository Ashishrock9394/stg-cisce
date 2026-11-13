@extends('layouts.filldart-app')
@section('title', $title)
@section('content')

<style>

	#studentTableRecords th:nth-child(1),
	#studentTableRecords td:nth-child(1) {
		width: 4% !important;
		min-width: 30px !important;
	}

	#studentTableRecords th:nth-child(2),
	#studentTableRecords td:nth-child(2) {
		width: 4% !important;
		min-width: 30px !important;
	}

	#studentTableRecords th:nth-child(3),
	#studentTableRecords td:nth-child(3) {
		width: 10% !important;
		min-width: 60px !important;
	}

	#studentTableRecords th:nth-child(4),
	#studentTableRecords td:nth-child(4) {
		width: 8% !important;
		min-width: 60px !important;
	}

	#studentTableRecords th:nth-child(5),
	#studentTableRecords td:nth-child(5) {
		width: 8% !important;
		min-width: 60px !important;
	}

	#studentTableRecords th:nth-child(6),
	#studentTableRecords td:nth-child(6) {
		width: 25% !important;
		min-width: 150px !important;
	}

	#studentTableRecords th:nth-child(7),
	#studentTableRecords td:nth-child(7) {
		width: 10% !important;
		min-width: 60px !important;
	}
	#studentTableRecords th:nth-child(8),
	#studentTableRecords td:nth-child(8) {
		width: 30% !important;
		min-width: 150px !important;
	}
	table#studentTableRecords th.dt-orderable-none .dt-column-order {
	    display: none !important;
	}
	.dt-button-collection.dropdown-menu{
		top: 35px !important;
	}
    .student-email-card{
        border: none;
        border-radius: 16px;
        overflow: hidden;
    }

    .drop-zone{
        border: 2px dashed #d6dce5;
        border-radius: 16px;
        padding: 30px 20px;
        text-align: center;
        background: #fafcff;
        transition: all .3s ease;
        cursor: pointer;
    }

    .drop-zone:hover,
    .drop-zone.dragover{
        border-color: #ff8a00;
        background: #fff8f0;
    }

    .drop-zone i{
        font-size: 55px;
        color: #ff8a00;
        margin-bottom: 15px;
    }

    .upload-title{
        font-size: 22px;
        font-weight: 600;
        color: #1f2d5a;
    }

    .upload-subtitle{
        color: #6c757d;
        font-size: 13px;
        font-weight: 400;
    }

    .file-selected{
        color: #198754;
    }

    .instructions-card{
        background: linear-gradient(135deg,#0ea5b5,#1499d3);
        border-radius: 16px;
        color: white;
        height: 100%;
    }

    .instructions-body{
        padding: 25px;
    }

    .instructions-body ul{
        padding-left: 20px;
    }

    .instructions-body li{
        margin-bottom: 15px;
        line-height: 1.6;
    }

    .upload-btn{
        background: #ff8a00;
        border: none;
        border-radius: 10px;
        padding: 12px 30px;
        font-weight: 600;
    }

    .upload-btn:hover{
        background: #e87900;
    }
</style>
<div class="container">
    <div class="t-mrg2 mb-5 pb-5">
        <div class="datatable-actions-outside d-flex justify-content-between align-items-center mb-3">
            <div class="all-chaptr-cards mb-4" style="margin: 0;">
                <div class="row">
                    <x:back-button title="{!! $title !!}" />
                </div>
            </div>

            <div style="color: #ffffff;">
                <div class="d-flex"> 
					@if (!session('is_impersonating'))                                      
                    <a type="button" class="btn btn-primary text-light text-center custome-btn-i w-100 mr-2" data-bs-toggle="modal" data-bs-target="#uploadEmailModal">
                    	<i class="fa-solid fa-upload"></i>Upload
                    </a>  
					@endif  
					{{-- <a type="button" id="downloadTemplate" class="btn btn-primary text-light text-center custome-btn-i w-100 mr-2">
                    	<i class="fa-solid fa-download"></i>Download Template
                    </a> --}}    
					@if($logs->isNotEmpty())
                    <a type="button" class="btn btn-primary custome-btn-i w-100" data-bs-toggle="modal" data-bs-target="#emailHistoryModal">
                        View History
                    </a>
					@endif

                </div>
            </div>
        </div>
		

		<div class="container-fluid p-0">
			<div class="responsive m-0 mt-4 pt-2" id="record_table">

				<table id="studentTableRecords" class="table table-bordered tbl-style" >
					<thead>
						<tr>
							<th scope="col"><input type="checkbox" id="selectAll"></th>
							<th scope="col">#</th>
							<th scope="col">Class</th>
							<th scope="col">Section </th>
							<th scope="col">Roll No. </th>
							<th scope="col">Student Name</th>
							<th scope="col">Admission No.</th>
							<th scope="col">Email</th>
						</tr>
					</thead>
					<tbody> </tbody>
				</table>

			</div>
		</div>              
    </div>
</div>

<!-- upload Email Modal -->
<div class="modal fade" id="uploadEmailModal" tabindex="-1" role="dialog" aria-labelledby="uploadEmailModalTitle" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">            
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><i class="fa fa-envelope-open-text mr-2"></i>Upload Parent's Email Template</h5>
                <button type="button" class="close btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
				<div class="alert alert-info d-flex align-items-start gap-2" role="alert" style="border-left: 4px solid #0d6efd;">
					<div>
						<h3><i class="bi bi-info-circle-fill text-primary"></i>  Instructions for Bulk Updating Parent Email Addresses</h3>
						<div class="instructions">
							<ol style="padding-left: 10px;">
								<li>
									Download the <strong>Email Template</strong> using the <strong>Download Template</strong> button. <br> 1.a Select <strong>All Students</strong> to download the complete list or <strong>Selected Students</strong> to download only the records of the students you have selected.	
										
								</li>

								<li>
									Do not modify the structure, column names, or format of the downloaded <strong>Email Template</strong>.
								</li>

								<li>
									Do not add, delete, or rearrange student records in the template. The number of student records must remain unchanged.
								</li>

								<li>
									Update email addresses only in the <strong>Email ID</strong> column for the students whose email addresses need to be changed.
								</li>
								<li>
									Ensure that all email addresses entered are valid and correctly formatted before uploading the file. 
								</li>
								<li>
									The email update may take up to 24 hours. Please be patient. Once the update is successful, the school will receive a confirmation email.
								</li>
							</ol>
							<strong>NOTE : </strong> Upload only the original <strong>Email Template</strong> downloaded from the system. Files with modified structures or any other formats will not be accepted.
						</div> 
					</div>
				</div>
				<div class="row align-items-stretch">
					<div class="col-lg-12">
						<div class="student-email-card">
							<div class="upload-section">
								<form id="studentEmailUploadForm">
									<div class="drop-zone" id="studentEmailDropZone">
										<i class="fa fa-cloud-upload-alt"></i>
										<h5 class="mb-2">
											Drag & Drop Excel File
										</h5>
										<p class="text-muted mb-0">
											or click to browse your file
										</p>
										<input type="file" id="studentEmailFileInput" name="student_email_file" hidden>
										<div id="studentEmailFileName" class="file-selected d-none"></div>
									</div>
									<div class="text-right mt-4">
										<button type="submit" class="btn upload-btn text-white">
											<i class="fa fa-upload mr-2"></i>
											Upload
										</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
            </div>
           
        </div>
    </div>
</div>


@include('modals.students-email-history')

<script>

    $(document).ready(function() {

		var table = $('#studentTableRecords').DataTable({

			dom: `<"top"lf><"filter-right"B>rt<"bottom"ip><"clear">`,
			lengthChange: true,
			lengthMenu: [
				[20, 40, 60, 80, 100, -1],
				[20, 40, 60, 80, 100, 'All']
			],
			pageLength: 100,
			info: true,
			responsive: true,
			processing: true,
			serverSide: true,
			ajax: {
				url: "{{ route('student.email') }}",
				data: function(d) {
					d.class_id = $('#select_class').val();
					d.section_id = $('#select_section').val();
            	}
			},
			columns: [
				{	data: 'checkbox', name: 'checkbox' },
				{
	                data: null,
	                name: 'serial_no',
	                orderable: false,
	                searchable: false,
	                render: function (data, type, row, meta) {
	                    return meta.row + meta.settings._iDisplayStart + 1;
	                }
	            },
				{	data: 'class_id', name: 'class_id' },
				{  data: 'section_id', name: 'section_id'	},
				{	data: 'rollno', name: 'rollno' },
				{  data: 'student_name', name: 'student_name' },
	            {  data: 'admissionnumber', name: 'admissionnumber' },
	            {  data: 'email_id', name: 'email_id' },
			],
					

			columnDefs: [
		        { targets: 0, orderable: false, searchable: false, className: 'no-sort text-center dt-' }
		    ],

			"initComplete": function() {
            $('.dt-search input[type="search"]').attr('placeholder', 'Search here...');

			var classList = @json($classList);

			const $classDropdown = $('<select class="form-control" id="select_class"></select>');
			const $sectionDropdown = $('<select class="form-control" id="select_section"><option value="">Select Section</option></select>');

			const classMap = {};

			classList.forEach(item => {
				if (!classMap[item.class_id]) {
					classMap[item.class_id] = item.name;
					$classDropdown.append(new Option(item.name, item.class_id));
				}
			});

			$classDropdown.on('change', function () {
				const selectedClassId = $(this).val();

				// reset section dropdown
				$sectionDropdown.empty().append('<option value="">Select Section</option>');

				classList.forEach(item => {
					if (item.class_id == selectedClassId && item.section) {
						$sectionDropdown.append(new Option(item.section, item.section));
					}
				});

				table.ajax.reload();
			});

			$sectionDropdown.on('change', function () {
				table.ajax.reload();
			});
			const $filterWrapper = $('<div class="pull-right d-flex" style="gap:10px;"></div>');

			$filterWrapper
				.append($classDropdown)
				.append($sectionDropdown)
				.appendTo("#studentTableRecords_wrapper .top");

			$("#studentTableRecords_wrapper .top .dt-length").addClass("pull-right");
        },
			buttons: [{
				extend: 'collection',
				text: 'Download Template',
				className: 'exportButton',
				buttons: [
					{
						text: 'All Students',
						action: function ( e, dt, node, config ) {							
							downloadAllStudents();
						}
					},
					{
						text: 'Selected Students',
						action: function ( e, dt, node, config ) {							
							downloadSelected();
						}
					}
				],
			}]
		});


		table.on('draw', function () {
	     
	        $('#select-all').off('click').on('click', function () {
	            var isChecked = $(this).is(':checked');
	            $('.row-checkbox').prop('checked', isChecked);
	        });

	        $('.row-checkbox').on('change', function () {
	            var total = $('.row-checkbox').length;
	            var checked = $('.row-checkbox:checked').length;
	            $('#select-all').prop('checked', total === checked);
	        });
	    });

		table.select.style('api');

		$('#selectAll').on('click', function () {
			var checked = this.checked;

			$('#studentTableRecords tbody input.row-select:not(:disabled)')
				.prop('checked', checked);

			if (checked) {
				table.rows().select();
			} else {
				table.rows().deselect();
			}
		});

	});

	function downloadSelected() {
		var studentIds = [];
		$('#studentTableRecords tbody input.row-select:checked').each(function () {
			studentIds.push($(this).data('id'));
		});

		if (studentIds.length === 0) {
			Swal.fire({
				icon: 'warning',
				title: 'No students selected',
				text: 'Please select at least one student'
			});
			return;
		}
		downloadTemplate(studentIds)

	}

	function downloadAllStudents() {
		Swal.fire({
			icon: 'info',
			title: 'Email Template',
			text: 'Download Email Template for all Students',
			showCancelButton: true,
			confirmButtonText: 'OK',
			cancelButtonText: 'Cancel',
			reverseButtons: true
		}).then((result) => {
			if (result.isConfirmed) {
				downloadTemplate([]);			}
		});
	}

	function downloadTemplate(studentIds = []){

		Swal.fire({
			icon: 'info',
			title: "Please wait",
			html: "Generating student Email Template...",
			allowOutsideClick: false,
			didOpen: () => {
				Swal.showLoading();
			}
		});

		$.ajax({
			url: "{{ route('download.email.template') }}",
			method: "POST",
			data: JSON.stringify({
				_token: "{{ csrf_token() }}",
				studentIds: studentIds
			}),
			contentType: "application/json",
			xhrFields: {
				responseType: "blob"
			},
			success: function (response, status, xhr) {
				Swal.close();
				let disposition = xhr.getResponseHeader('Content-Disposition');
				let filename = "email_template.xlsx";

				if (disposition && disposition.indexOf('filename=') !== -1) {
					filename = disposition
						.split('filename=')[1]
						.replace(/['"]/g, '');
				}
				let blob = new Blob([response], {
					type: xhr.getResponseHeader('Content-Type')
				});
				let link = document.createElement('a');
				link.href = window.URL.createObjectURL(blob);
				link.download = filename;
				document.body.appendChild(link);
				link.click();
				document.body.removeChild(link);
			},
			error: function (xhr) {
				Swal.close();
				let msg = "Download failed";
				if (xhr.responseJSON && xhr.responseJSON.message) {
					msg = xhr.responseJSON.message;
				}
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: msg
				});
			}
		});
	}

    const studentEmailDropZone = document.getElementById('studentEmailDropZone');
    const studentEmailFileInput = document.getElementById('studentEmailFileInput');
    const studentEmailFileName = document.getElementById('studentEmailFileName');

    const allowedFileExtensions = ['xlsx'];

    function isValidStudentEmailExcel(file) {
        const extension = file.name.split('.').pop().toLowerCase();
        return allowedFileExtensions.includes(extension);
    }

    function showInvalidStudentEmailFileAlert() {
        Swal.fire({
            icon: 'error',
            title: 'Invalid File',
            text: 'Please upload a valid Excel file (.xlsx).',
            confirmButtonColor: '#3085d6'
        });
    }

    // Click Upload Area
    studentEmailDropZone.addEventListener('click', () => {
        studentEmailFileInput.click();
    });

    // Browse File
    studentEmailFileInput.addEventListener('change', () => {
        const file = studentEmailFileInput.files[0];

        if (!file){
			studentEmailFileName.textContent = '';
			studentEmailFileName.classList.add('d-none');
			return;
		}
		studentEmailFileName.classList.remove('d-none');

        if (!isValidStudentEmailExcel(file)) {
            studentEmailFileInput.value = '';
            studentEmailFileName.classList.add('d-none');
            showInvalidStudentEmailFileAlert();
            return;
        }

        studentEmailFileName.textContent = file.name;
    });

    // Drag Over
    studentEmailDropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        studentEmailDropZone.classList.add('dragover');
    });

    // Drag Leave
    studentEmailDropZone.addEventListener('dragleave', () => {
        studentEmailDropZone.classList.remove('dragover');
    });

    // Drop File
    studentEmailDropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        studentEmailDropZone.classList.remove('dragover');

        const file = e.dataTransfer.files[0];

        if (!file) return;

        if (!isValidStudentEmailExcel(file)) {
            showInvalidStudentEmailFileAlert();
            return;
        }

        studentEmailFileInput.files = e.dataTransfer.files;

        studentEmailFileName.textContent = file.name;
        studentEmailFileName.classList.remove('d-none');
    });

    $('#studentEmailUploadForm').on('submit', function (e) {

        e.preventDefault();

        let file = studentEmailFileInput.files[0];

        if (!file) {
            Swal.fire({
                icon: 'warning',
                title: 'No File Selected',
                text: 'Please select an Excel file first.'
            });
            return;
        }

        let formData = new FormData();
        formData.append('file', file);
        formData.append('_token', "{{ csrf_token() }}");

        Swal.fire({
            title: 'Uploading...',
            text: 'Please wait while we validate the file',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "{{ route('upload.student.emails') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,

            success: function (response) {

                Swal.close();

                // Validation errors found
                if (response.validation_error) {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Failed',
                        html: `
                            <div style="text-align:center">
                                <p>
                                    <strong>${response.error_count}</strong> invalid email(s) found in ${response.totalRows} rows.
                                </p>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Download Error File',
                        cancelButtonText: 'Back',
                        allowOutsideClick: false
                    }).then((result) => {

                        if (result.isConfirmed) {
                            window.location.href = response.download_url;
                        }
                    });

                    return;
                }

                // Success
                Swal.fire({
					icon: 'success',
					title: 'Success',
					text: response.message || 'Student emails imported successfully.',
					allowOutsideClick: false
				}).then(() => {
					$('#uploadEmailModal').modal('hide');
                    $('#studentEmailUploadForm')[0].reset();
                    $('#studentEmailFileName').addClass('d-none').text('');
					location.reload();
                });
            },

            error: function (xhr) {

                Swal.close();

                let message = 'Upload failed';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                    allowOutsideClick: false
                });
            }
        });
    });
</script>

@endsection