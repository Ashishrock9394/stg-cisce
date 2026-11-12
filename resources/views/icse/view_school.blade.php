@extends('layouts.filldart-app')
@section('title', $title) <!-- Or your master layout -->

@section('content')
<div class="container mt-4">


    <div class="col">
        <a href="{{ url()->previous() }}" class="back-button" style="left: 0px;">
            <span class="arrow">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
                </svg>
            </span>
        </a>
        <div class="heading-rw mt-0" style=" margin-left: -18px;">
            <h1>{{$title}}</h1>
        </div>
    </div>


<div class="card mb-4 mt-5">
    <div class="card-body">
        <h4 class="mb-4">{{ $school->school_name }} ({{ $school->school_code }})</h4>

        <div class="row">
            <div class="col-md-6 mb-3">
                <strong>Principal:</strong> {{ $school->school_principal }}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Region:</strong> {{ $school->region }}
            </div>
            <div class="col-md-6 mb-3">
                <strong>State:</strong> {{ $school->state }}
            </div>
            <div class="col-md-6 mb-3">
                <strong>District:</strong> {{ $school->district }}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Address:</strong> {{ $school->address }}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Students Uploaded ({{$academicYear}}):</strong> Active:-{{ $studentCount->total_students }}, CWSN:- {{ $studentCount->cwsn_students }}
            </div>
        </div>
    </div>
</div>


    <h4>Uploaded Students</h4>

    <div class="responsive mb-5">
        <div class="tab-content cus-radius p-0" id="itemTabsContent">
            <!-- Infrastructure List Tab -->
            <div id="school-list" role="tabpanel" aria-labelledby="infrastructure-tab">
                <table id="studentsTable" class="table table-bordered tbl-style display mb-4" style="width:100%">
                    <thead>             
                        <tr>
                            <th>#</th>
                            <th>Class</th>
                            <th>Section</th>
                            <th>Roll No</th>
                            <th>Name</th>
                            <th>Admission Number</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Birth Date</th>
                            <th>Status</th>
                            <th>ApaarId</th>
                            <!-- <th>Passport</th> -->
                        </tr>
                    </thead>
                    <tbody> </tbody>
                </table>
            </div>
        </div>
    </div>

</div>


<script>

    $(document).ready(function() {
        var table = $('#studentsTable').DataTable({
            info: true,
            processing: true,
            serverSide: true,
            searchable: true,
            pageLength: 100, // default rows per page
            lengthMenu: [[20, 40, 100, 200, -1], [20, 40, 100, 200, "All"]],
            dom: `<"top"lf><"filter-right"B>rt<"bottom"ip><"clear">`,
            
            ajax: {
                url: '{{ route("cicse.school.view", $school->id) }}',
                data: function(d) {
                    d.academic_year = $('#academic_year').val();
                },
                beforeSend: function () {
                    $('#datatable-loader').show();
                },
                complete: function () {
                    $('#datatable-loader').hide();
                }
            },

            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'display_classname', name: 'display_classname' },
                { data: 'section', name: 'section' },
                { data: 'rollno', name: 'rollno' },
                { data: 'student_name', name: 'student_name' },
                { data: 'student_uid', name: 'student_uid' },
                { data: 'email_id', name: 'email_id' },
                { data: 'gender', name: 'gender' },
                { data: 'dob', name: 'dob' },
                { data: 'status', name: 'status' },
                { data: 'apaarId', name: 'apaarId' },
            {{-- { data: 'aadhaarId', name: 'aadhaarId' },
                { data: 'passport', name: 'passport' },  --}}

            ],

            buttons: [{
                extend: 'collection',
                text: 'Export',
                className: 'exportButton itm-list',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'CICSE School List',
                        filename: function () {
                            return getTimestamp();
                        },
                    },
                ],
            }],
            order: [[0, 'asc']],

            initComplete: function () {
                $('.dt-search input[type="search"]').attr('placeholder', 'Search here...');

                let academicYear = @json($academicYear);
                let previousAcademicYear = @json($previousAcademicYear);
                let selectedYear = @json($selectedYear);

                // Academic Year Dropdown
                const $academicYearDropdown = $('<select class="form-control ml-2" name="academic_year" id="academic_year"></select>')
                    .append(`
                        <option value="${previousAcademicYear}" ${selectedYear == previousAcademicYear ? 'selected' : ''}>${previousAcademicYear}</option>
                        <option value="${academicYear}" ${selectedYear == academicYear ? 'selected' : ''}>${academicYear}</option>
                    `);

                const $filterContainer = $('<div class="d-flex"  style="column-gap: 6px;"></div>').append($academicYearDropdown);
                $('<div class="pull-right"></div>').append($filterContainer).appendTo("#studentsTable_wrapper .top");
                $academicYearDropdown.on('change', () => table.ajax.reload());
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
            return `CICSE School List ${now.getDate()}-${now.getMonth()+1}-${now.getFullYear()}`;
        }
    });

</script>
@endsection
