@extends('layouts.filldart-app')
@section('title', $title)

@section('content')
<style>

    .dt-container .top .filter-right {
        order: 2;
        margin-right: 1%;
        margin-top: 5px;
    }
    #studentsArchiveTable thead th {
        position: sticky;
        top: 0;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
    }
    #studentsArchiveTable tbody td {
        position: sticky;
        color: #000;
    }

    table#studentsArchiveTable th.dt-orderable-none .dt-column-order {
        display: none !important;
    }

    h2#swal2-title {
        font-size: 18px !important;
    }

    div#swal2-html-container {
        font-size: 15px;
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
        </div>

        @if(session('warning_uid'))
            <div class="alert alert-success">
               {{ session('warning_uid')['message'] }}
            </div>
        @endif


        {{-- Target Datatable Component --}}
        <div class="table-responsive p-0">
            <table id="studentsArchiveTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th scope="col"><input type="checkbox" id="select-all"></th>
                        <th>#</th>
                        <th>School</th>
                        <th>Class</th>
                        <th>Section</th>
                        <th>Name</th>
                        <th>Admission No</th>
                        <th>Deleted At</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
        
            </table>
        </div>
     </div>
</div>


<script>
$(document).ready(function() {
    var table = $('#studentsArchiveTable').DataTable({
        info: true,
        processing: true,
        serverSide: true,
        searchable: true,
        pageLength: 100, // default rows per page
        lengthMenu: [[100, 200, 300, -1], [100, 200, 300 , "All"]],
        dom: `<"top"lf><"filter-right">rt<"bottom"ip><"clear">`,
        ajax: "{{ url()->current() }}",

        columns: [
            {    
                 targets: 0,
                 data: null,
                 name: 'checkbox',
                 orderable: false,
                 searchable: false,
                 className: 'no-sort checkbox',
                 defaultContent: '',
                 render: function(data, type, row) {
                     return `<input type="checkbox" name="checkbox" class="row-select" data-id="${row.student_id}">`;
                 }
                },
                

            {
                data: null,
                name: 'serial_no',
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {  data : 'school_code' },
            { data: 'class_name' },
            { data: 'section' },
            { data: 'name' },
            { data: 'student_uid'},
            { data: 'deleted_at' },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    return `
                        <form action="${row.restore_url}" method="POST" onsubmit="return confirm('Are you sure you want to restore this student back to the active registry?');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success mb-0 py-1 px-3">
                                <i class="fas fa-undo-alt me-1"></i> Restore
                            </button>
                        </form>
                    `;
                }
            }
        ],

    
        "initComplete": function() {
            $('.dt-search input[type="search"]').attr('placeholder', 'Search here...');
            const $dropdown2 = $('<select class="form-control" id="select_action"></select>');

            var status = [
                { name: 'Bulk Action', status: '',},
                { name: 'Restore', status: 'restore'},
            ];

            status.forEach(option => {
                const section = option.status ? ` - ${option.status}` : '';
                const displayText = option.name;
                const value = option.status;
                $dropdown2.append(new Option(displayText, value));
            });
            
            $('<div class="filter-right"></div>').append($dropdown2).appendTo("#studentsArchiveTable_wrapper .top").next('.dt-length').addClass("pull-right");


            $dropdown2.on('change', function() {

                const action = $(this).val();
                var selectedIds = [];
                $('#studentsArchiveTable tbody input.row-select:checked').each(function() {
                    selectedIds.push($(this).data('id'));
                });
                if (selectedIds.length === 0) {
                Swal.fire({
                        icon: 'warning',
                        title: 'No students selected',
                        text: 'Please select at least one student.'
                    });
                    $('#select_action').val('');
                    return;
                }

                Swal.fire({

                    icon: 'warning',
                    title: `Are you sure you want to ${action} ${selectedIds.length} selected students?`,
                    text: "You won't be able to revert this!",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Yes, proceed!",
                    cancelButtonText: "Cancel",
                    allowOutsideClick: false,
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon : 'info',
                            title: 'Processing...',
                            text: 'Please wait while we perform the action.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                       
                        $.ajax({
                            url: "{{ route('restore.students.data') }}",
                            method: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                action: action,
                                schoolid:@json($schoolId),
                                ids: selectedIds
                            },
                            success: function (response) {
                                Swal.close();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message,
                                    confirmButtonText: 'OK',
                                    allowOutsideClick: false
                                }).then((result) => {

                                    if (result.isConfirmed) {
                                        $('#studentsArchiveTable').DataTable().ajax.reload();
                                        $('#select_action').val('');
                                    }

                                });
                            },
                            error: function (xhr) {
                                Swal.close();

                                let message = 'Something went wrong.';

                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Failed',
                                    text: message,
                                    confirmButtonText: 'OK',
                                    allowOutsideClick: false
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $('#studentsArchiveTable').DataTable().ajax.reload(null, false);
                                        $('#select_action').val('');
                                    }
                                });
                            }
                        });

                    } else {
                        $('#select_action').val('');
                    }
                });
            });


        }

    });

    $('#select-all').on('click', function() {
        var isChecked = $(this).is(':checked');
        $('.row-select').prop('checked', isChecked);
    });

    $('#studentsArchiveTable tbody').on('change', '.row-select', function() {
        var totalCheckboxes = $('.row-select').length;
        var checkedCheckboxes = $('.row-select:checked').length;
        $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
    });

    table.on('draw', function () {
        $('#select-all').prop('checked', false);
    });

});
</script>
@endsection