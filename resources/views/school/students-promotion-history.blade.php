@extends('layouts.filldart-app') @section('title', $title) @section('content')

@php
    $userId = Auth::user()->id;
    $schoolsId = DB::table('school_reference')
        ->where('school_user_id', $userId)
        ->where('status', 1)
        ->value('school_id');
    $schoolCode = DB::table('schools')->where('id', $schoolsId)->value('school_code');
@endphp


<style type="text/css">
    .dt-container .top .filter-right {
        order: 2;
        margin-right: 8%;
        margin-top: 5px;
    }

    #select_action {
        margin-right: 15px !important;
    }

    #record_table {
        width: 100%;
        overflow-x: auto;
        overflow-y: auto;
        display: block;
    }

    #studentsPromotionHistory thead th {
        position: sticky;
        top: 0;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
    }

    table#studentsPromotionHistory th.dt-orderable-none .dt-column-order {
        display: none !important;
    }
</style>

<div class="container">
    <div class="t-mrg2 pb-5">
        {{-- Header --}}
        <div class="all-chaptr-cards mb-4" style="margin: 0;">
            <div class="row">
                <x:back-button title="{{ $title }}">
                    <x-slot name="actionsOutside">
				        <div>				       
							<!-- <button ></button> -->
						</div>
				    </x-slot>
                </x:back-button>
            </div>
        </div>

        <div class="responsive m-0 mt-4 mb-4 pt-2" id="record_table">

                <table id="studentsPromotionHistory" class="table table-bordered tbl-style">
                    <thead>

                        <tr data-dt-order="disable">
                                                       
                            <th colspan="3" class="text-center dt-orderable-none" style="background: #2c2d78; color:white; width:330px;"> Student Information </th>
                            <!-- Current Year -->
                            <th colspan="3" class="text-center dt-orderable-none" style="background: #2c2d78; color:white; width:330px;"> Academic Year : 2026-2027 </th>
                            <!-- Previous Year -->
                            <th colspan="3" class="text-center dt-orderable-none" style="background: #2c2d78; color:white; width:330px;"> Academic Year : 2025-2026</th>
                            <th rowspan="2" class="dt-orderable-none" style="width: 8%; text-align: center;">Restore Previous Class</th>
                        </tr>

                        <tr>
                            <th style="width:50px; min-width:50px;">#</th>
                            <th style="width:110px; min-width:110px;">Admission No.</th>
                            <th style="width:160px; min-width:160px;">Student Name</th>
                            <!-- Current -->
                            <th style="width:110px; min-width:110px;">Class</th>
                            <th style="width:110px; min-width:110px;">Section</th>
                            <th style="width:60px; min-width:60px;">Roll No.</th>
                            <!-- Previous -->
                            <th style="width:110px; min-width:110px;">Class</th>
                            <th style="width:110px; min-width:110px;">Section</th>
                            <th style="width:60px; min-width:60px;">Roll No.</th>
                        </tr>

                    </thead>

                    <tbody> </tbody>
                </table>

            </div>
    </div>
</div>





<script>
    $(document).ready(function() {

        var table = $('#studentsPromotionHistory').DataTable({

            dom: `<"top"lf><"filter-right">rt><"clear">`,

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
                url: "{{ route('students.promotion.history') }}",
                data: function(d) {
                    d.class_id = $('#select_class').val();
                    d.section_id = $('#select_section').val();
                    d.status = $('#select_status').val();
                }
            },
            columns: [
                {
                    data: null,
                    name: 'serial_no',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                { data: 'admissionnumber', name: 'admissionnumber' },
                { data: 'student_name', name: 'student_name' },

                //    current year
                { data: 'class_id', name: 'class_id' },
                { data: 'section_id', name: 'section_id' },
                { data: 'rollno', name: 'rollno' },
                { data: 'prev_class', name: 'prev_class' },
                { data: 'prev_section', name: 'prev_section' },
                { data: 'prev_rollno', name: 'prev_rollno' },
                { data: 'action', name: 'action' },

            ],


            "initComplete": function() {
                $('.dt-search input[type="search"]').attr('placeholder', 'Search here...');


                var classList = @json($classList);

                const $classDropdown = $(
                '<select class="form-control" id="select_class"></select>');
                const $sectionDropdown = $(
                    '<select class="form-control" id="select_section"><option value="">Select Section</option></select>'
                    );

                const classMap = {};

                classList.forEach(item => {
                    if (!classMap[item.class_id]) {
                        classMap[item.class_id] = item.name;
                        $classDropdown.append(new Option(item.name, item.class_id));
                    }
                });

                $classDropdown.on('change', function() {
                    const selectedClassId = $(this).val();

                    // reset section dropdown
                    $sectionDropdown.empty().append(
                        '<option value="">Select Section</option>');

                    classList.forEach(item => {
                        if (item.class_id == selectedClassId && item.section) {
                            $sectionDropdown.append(new Option(item.section, item
                                .section));
                        }
                    });

                    table.ajax.reload();
                });

                $sectionDropdown.on('change', function() {
                    table.ajax.reload();
                });
                const $filterWrapper = $('<div class="pull-right d-flex" style="gap:10px;"></div>');

                $filterWrapper
                    .append($classDropdown)
                    .append($sectionDropdown)
                    .appendTo("#studentsPromotionHistory_wrapper .top");

                $("#studentsPromotionHistory_wrapper .top .dt-length").addClass("pull-right");
            },
        });

        table.select.style('api');

    });

    $(document).on('click', '.revert-student', function () {

        let studentId = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You want to revert this student!",
            icon: 'warning',
            allowOutsideClick: false,
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#ff8000',
            cancelButtonColor: '#9da3a9',
            confirmButtonText: 'Yes, Revert',
            cancelButtonText: 'Cancel',
        }).then((result) => {

            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('revert.promotion') }}",
                    type: "POST",
                    data: {
                        student_id: studentId,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function () {
                        $('.revert-student').prop('disabled', true);
                    },
                    success: function (response) {
                        $('.revert-student').prop('disabled', false);
                        if (response.success === true) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,                                
                                allowOutsideClick: false,
                                showConfirmButton: true,
                            });

                            $('#studentsPromotionHistory')
                                .DataTable()
                                .ajax.reload(null, false);

                        } else {

                            Swal.fire({
                                icon: 'warning',
                                title: 'Failed',
                                text: response.message
                            });
                        }
                    },
                    error: function (xhr) {

                        $('.revert-student').prop('disabled', false);

                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!'
                        });

                        console.log(xhr.responseText);
                    }
                });
            }
        });
    });

</script>
@endsection
