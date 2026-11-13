@extends('layouts.filldart-app')
@section('title', $title)
@section('content')

<style>
    table#upload_test_data th.dt-orderable-none .dt-column-order {
	    display: none !important;
	}

    #upload_test_data .badge {
        background-color: #292775;
        border-color: #292775 !important;
        color: #fff;
    }

    .datatable-actions-outside > .all-chaptr-cards {
        flex: 1 1 auto;
        min-width: 0;
    }

    #upload_test_data {
        width: 100%;
        table-layout: auto;
        border-collapse: collapse;
    }
    #upload_test_data th:nth-child(1),
    #upload_test_data td:nth-child(1) {
        width: 5%;
        min-width: 40px;        
        text-align: center;
        white-space: nowrap;
    }
    #upload_test_data th:nth-child(2),
    #upload_test_data td:nth-child(2) {
        width: 25%;
        min-width: 150px;
    }
    #upload_test_data th:nth-child(3),
    #upload_test_data td:nth-child(3) {
        width: 40%;
        min-width: 180px;
    }
    #upload_test_data th:nth-child(4),
    #upload_test_data td:nth-child(4) {
        width: 12%;
        min-width: 120px;
    }

    #upload_test_data th:nth-child(5),
    #upload_test_data td:nth-child(5) {
        width: 12%;
        min-width: 120px;
    }
    #upload_test_data td:nth-child(4),
    #upload_test_data td:nth-child(5) {
        text-align: center;
    }

    #upload_test_data th,
    #upload_test_data td {
        padding: 8px;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }


</style>


<div class="container">
    <div class="t-mrg2 mb-5 pb-5">
        <div class="datatable-actions-outside d-flex justify-content-between align-items-center mb-3">
            <div class="all-chaptr-cards mb-4" style="margin: 0;">
                <div class="row">
                    <x:back-button title="{{$title}}" />
                </div>
            </div>

            <div style="color: #ffffff;">
                <div class="d-flex">
                    @if (!session('is_impersonating'))
                    <a type="button" id="upload_btn" class="btn btn-primary text-light text-center custome-btn-i w-100 mr-2" data-bs-toggle="modal" data-bs-target="#uploadtestdata">
                    <i class="fa-solid fa-upload"></i>Upload Data</a>
                    @endif
                    @if($logs->isNotEmpty())
                    <a type="button" class="btn btn-primary custome-btn-i w-100" data-bs-toggle="modal" data-bs-target="#testHistoryModal">
                        View History
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="alert alert-info d-flex align-items-start gap-2" role="alert" style="border-left: 4px solid #0d6efd;">
            <i class="bi bi-info-circle-fill text-primary"></i>
            <div>
                <strong>&nbsp;&nbsp;Note:</strong>  
                <ul class="mb-0 mt-1">
                    <li><strong>Template Options:</strong> Choose <strong>Incomplete</strong> for students who did not participate in the test, or <strong>All</strong> to include all students.</li>
                    <li><strong>Important:</strong> Do not modify the Excel file name or change any column headings.</li>
                    <li><strong>Enter Scores:</strong> Download the <strong>Sample Data</strong> for each test and enter the scores following the format provided.</li>
                    <li><strong>FMS Test Scores:</strong> Only <strong>"Y"</strong>, <strong>"y"</strong>, or a blank cell are valid entries for FMS tests. Any other characters, numbers, or symbols will be rejected. <em>(Refer to Sample Data)</em>.</li>
                    <li><strong>FMS Test Status:</strong> Select <strong>Attempted</strong> or <strong>Not Attempted</strong> to indicate whether the student participated in the test.</li>
                </ul>
            </div>
        </div>

        <div class="container-fluid p-0">
            <x-data-listing-component
                id="upload_test_data"
                :headers="['Skill Name', 'Test Applicable in Classes', 'Download', 'Download']"
                :columns="[
                    ['data' => 'skill_name', 'name' => 'skill_name', 'orderable' => false],
                    ['data' => 'classType', 'name' => 'classType', 'orderable' => false],
                    ['data' => 'downloadTemplate', 'name' => 'downloadTemplate', 'orderable' => false],
                    ['data' => 'downloadSample', 'name' => 'downloadSample', 'orderable' => false]
                ]"
                ajax-url="{{ route('upload.test.data') }}"
                :enable-export-buttons="false"
                :enableLengthMenu="false"
                :pageLength="100"
                :enable-only-class-filter="false"
                :enable-skill-name-filter="false"
                :selectedOnlyClass="null"
                :enable-test-status-filter="true"
                :status-mode-flag="1"
                :exportButtonText="'Bulk Action'"
                :selectedTestStatus="'incomplete'"
                searchPlaceholder="Search: Skill Name"
                data-class-id=""
                data-section=""
                :export-buttons="[
                    [   
                        'type' => 'custom', 'text' => 'Download selected templates', 'action' => 'allTestTemplates'
                    ]
                ]"
            >
            </x-data-listing-component>

        </div>
    </div>
</div>

{{-- upload test data modal --}}
@include('modals.upload-test-data-modal');
{{-- modal close --}}

{{-- history modal  --}}
@include('modals.test-history-modal');
{{-- modal close --}}

<script>
    $('#uploadtestdata').on('hide.bs.modal', function () {
        document.activeElement.blur();
    });

    // for sample data 
    $('#upload_test_data').on('click', '.download-sample', function () {
        let skillId = $(this).data('id');

        $.ajax({
            url: "{{route('test.sample')}}",
            type: 'GET',
            data: { skillId: skillId },
            xhrFields: {
                responseType: 'blob'
            },
            success: function (blob, status, xhr) {
                let filename = 'sample.xlsx';
                let disposition = xhr.getResponseHeader('Content-Disposition');
                if (disposition && disposition.indexOf('filename=') !== -1) {
                    filename = disposition.split('filename=')[1].replace(/"/g, '');
                }
                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            },
            error: function (xhr) {
                let msg = 'Unable to download the file. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire({
                    title: 'Download Failed!',
                    text: msg,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false
                });
            }
        });
    });


    // Global scope
    function allTestTemplates(e, dt, node, config) {

        var selectedSkills = [];
        $('#upload_test_data input[type="checkbox"]:checked').each(function() {
            var dataId = $(this).closest('tr').find('.download-template').data('id');
            if (dataId !== undefined) {
                selectedSkills.push(dataId);
            }
        });

        downloadTemplates(selectedSkills);
    }


    $('#upload_test_data').on('click', '.download-template', function() {
        let skillId = $(this).data('id');
        downloadTemplates([skillId]);
    });

    function downloadTemplates(skillIds) {

        let schoolId = @json($SchoolId);
        let status = $('#filter-test-status').val();

        if (!skillIds || skillIds.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'No Data Selected',
                text: 'Please select at least one Skill.',
                allowOutsideClick: false,
                showConfirmButton: true
            });
            return;
        }

        submitLoader();

        $.ajax({
            url: "{{ route('test.templete') }}",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify({
                _token: "{{ csrf_token() }}",
                skillIds: skillIds,
                schoolId: schoolId,
                status: status
            }),
            xhrFields: { responseType: "blob" },
            success: function(response, status, xhr) {
                let disposition = xhr.getResponseHeader('Content-Disposition');
                let filename = (skillIds.length > 1) ? 'skill_templates.zip' : 'test_score_template.xlsx';

                if (disposition && disposition.indexOf('filename=') !== -1) {
                    const matches = disposition.match(/filename="?([^";]+)"?/);
                    if (matches && matches[1]) {
                        filename = matches[1];
                    }
                }

                const blobType = (skillIds.length > 1) ? 'application/zip' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                const blob = new Blob([response], { type: blobType });
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                Swal.close();
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error
                });
            }
        });
    }


    $(document).on('click', '#upload_btn', function() {
		$('#import_msg').text('');
		$('#error_msg').hide();
	});

    $('#uploadTestFile').on('submit', function (event) {
        event.preventDefault();
        let fileInput = $(this).find('input[type="file"]')[0];

        if (!fileInput.files.length) {
            Swal.fire({
                icon: 'warning',
                title: 'No File Selected',
                text: 'Please select a file before uploading.',
                allowOutsideClick: false
            });
            return;
        }
        let formData = new FormData(this);
        formData.append('event', 'preview');

        sendImportRequest(formData);
    });

    function sendImportRequest(formData) {

        Swal.fire({
            title: 'Processing...',
            text: 'Please wait...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: "{{ route('import.test.data') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,

            success: function (response) {
                Swal.close();
                if (response.error) {
                    Swal.fire({
                        icon: 'error',
                        title: response.title || 'Error',
                        html: response.summary || 'Something went wrong',
                        allowOutsideClick: false
                    });
                    return;
                }

                if (response.step === 'preview') {
                    Swal.fire({
                        title: 'Preview',
                        icon: 'info',
                        html: `
                            <p>You are importing test data of <b>${response.total_students}</b> Students</p>
                        `,
                        showCancelButton: true,
                        allowOutsideClick: false,
                        confirmButtonText: 'Import',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let formData = new FormData($('#uploadTestFile')[0]);
                            formData.append('event', 'import');
                            sendImportRequest(formData);
                        }
                    });
                    return;
                }
                if (response.step === 'confirm_override') {
                    Swal.fire({
                        title: 'Existing Data Found',
                        icon: 'warning',
                        html: `
                            <p>Total Students: <b>${response.total_students}</b></p>
                            <p><b>${response.existing_students}</b> students' test data has already been submitted.</p>
                            <br>
                        `,
                        showCancelButton: true,
                        showDenyButton: false,
                        width: 550,
                        confirmButtonText: 'Continue',
                        // denyButtonText: 'Override',
                        cancelButtonText: 'Cancel',
                        allowOutsideClick: false,
                        reverseButtons: true
                    }).then((result) => {

                        let formData = new FormData($('#uploadTestFile')[0]);

                        if (result.isConfirmed) {
                            formData.append('event', 'skip');
                            sendImportRequest(formData);
                        }

                        if (result.isDenied) {
                            formData.append('event', 'override');
                            sendImportRequest(formData);
                        }
                    });

                    return;
                }
                Swal.fire({
                    icon: response.icon || 'success',
                    title: response.title || 'Success',
                    html: response.summary || 'Import has been queued successfully. Please check the import history for the status.',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false
                }).then(() => {
                    $('#uploadtestdata').modal('hide');
                    location.reload();
                });
            },

            error: function () {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Unexpected Error',
                    text: 'Something went wrong. Please try again.',
                    allowOutsideClick: false
                });
            }
        });
    }

</script>

@endsection
