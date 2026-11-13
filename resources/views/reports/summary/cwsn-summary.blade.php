@extends('layouts.filldart-app')
@section('title', $title)
@section('content')

<style>
.dropdown.show [aria-expanded="true"] {
    background:#fff;
}
.dropdown .form-control {
    height: 32px !important;
    border-radius: 4px;
    padding: 0 25px 0 10px;
}
.dropdown .form-control.dropdown-toggle::after {
        right: 10px;
    }
.dt-search input[type=search] {
    width: 180px !important;
    border: none;
    font-size:13px;
}
.invalid-age {
    background-color: #ffcccc !important;
    color: #000;
    font-weight: bold;
}

.heading-rw {
    padding: 0 50px;
    display: flex;
    align-content: center;
    align-items: center;
    justify-content: flex-start;
}

/* Style for the top-level parent category headers (Row 1) */
#FitnessTestStatus thead tr:first-child th {
    background-color: #131340; /* A darker, richer shade to anchor the top group */
    color: #ffffff;
    font-size: 13px;
    font-weight: 700;
    text-align: center;
    vertical-align: middle;
    border-right: 2px solid #3b486b; /* Clear column grouping divider */
    border-bottom: 2px solid #ffffff;
    padding: 8px 4px;
}

/* Style for the subheader row (Row 2) */
#FitnessTestStatus thead tr:nth-child(2) th {
    background-color: #2b3652; /* Lighter than the parent row for banded hierarchy */
    color: #e2e8f0;
    font-weight: 600;
    text-align: center;
    vertical-align: top;
    border-right: 1px solid #3b486b;
}

/* Style for the standalone multi-row headers */
#FitnessTestStatus thead tr:first-child th[rowspan="2"] {
    background-color: #2b3652;
    border-right: 1px solid #3b486b;
}

/* Ensure table cells align nicely */
#FitnessTestStatus th, #FitnessTestStatus td {
    vertical-align: middle;
}

</style>

<div class="container-fluid">
    <div class="t-mrg2 mb-5 pb-5 px-4">
        <div class="row">
        
            <div class="col-12 col-md">
                <div class="heading-rw mt-0 mt-md-1 mb-0 p-0 d-flex">                
                    <x:back-button title="{{$title}}" />
                </div>
            </div>

            <div class="responsive mb-5 mt-3 pb-4">
                <div class="tab-content cus-radius pb-5 mt-4">
                    <!-- Infrastructure List Tab -->
                
                    <table id="FitnessTestStatus" class="table table-bordered tbl-style display mb-4" style="width:100%">
                        <thead>
                            <tr>
                                <!-- Non-grouped columns spanning 3 rows vertically -->
                                <th scope="col" rowspan="3">Class</th>
                                <th scope="col" rowspan="3">Roll No.</th>
                                <th scope="col" rowspan="3">Students Name</th>
                                <th scope="col" rowspan="3">Age</th>
                                
                                <!-- Group 1: Aerobic Capacity (4 columns total) -->
                                <th scope="col" colspan="4" class="text-center">Aerobic Capacity</th>
                                
                                <!-- Group 2: Strength and Endurance -->
                                <th scope="col" colspan="11" class="text-center">Strength and Endurance</th>
                                
                                <!-- Group 3: Flexibility -->
                                <th scope="col" colspan="3" class="text-center">Flexibility</th>
                                
                                <!-- Group 4: Body Mass Composition -->
                                <th scope="col" colspan="3" class="text-center">Body Mass Composition</th>
                                
                                <!-- Standalone trailing columns spanning 3 rows vertically -->
                                <th scope="col" rowspan="3">Report</th>
                                <th scope="col" rowspan="3">Action</th>
                            </tr>
                            <tr>
                                <!-- One-mile run/walk parent sub-header spanning VO2 max & Time Taken -->
                                <th scope="col" colspan="2" class="text-center">One-mile run/walk</th>
                                <th scope="col" rowspan="2" class="align-middle">20-m Pacer (laps)</th>
                                <th scope="col" rowspan="2" class="align-middle">Modified 15-m Pacer (laps)</th>

                                <!-- Sub-headers for Strength and Endurance (Spanning 2 rows down) -->
                                <th scope="col" rowspan="2" class="align-middle">Reverse Curl (P|F)</th>
                                <th scope="col" rowspan="2" class="align-middle">40-m push/walk (P|F)</th>
                                <th scope="col" rowspan="2" class="align-middle">Push-ups</th>
                                <th scope="col" rowspan="2" class="align-middle">Seated Push-ups (sec.ms)</th>
                                <th scope="col" rowspan="2" class="align-middle">Pull-ups</th>
                                <th scope="col" rowspan="2" class="align-middle">Modified Pull-ups</th>
                                <th scope="col" rowspan="2" class="align-middle">Dumbbell Press</th>
                                <th scope="col" rowspan="2" class="align-middle">Isometric Push-ups (sec.ms)</th>
                                <th scope="col" rowspan="2" class="align-middle">Curl-ups</th>
                                <th scope="col" rowspan="2" class="align-middle">Modified Curl-ups</th>
                                <th scope="col" rowspan="2" class="align-middle">Trunk Lift (cm)</th>

                                <!-- Sub-headers for Flexibility (Spanning 2 rows down) -->
                                <th scope="col" rowspan="2" class="align-middle">
                                    Shoulder stretch<br>
                                    <span style="font-size: 11px; white-space: nowrap;">Left | Right</span>
                                    <span style="font-size: 11px; white-space: nowrap;">T | F</span>
                                </th>
                                <th scope="col" rowspan="2" class="align-middle">
                                    Back-Saver Sit-and-Reach<br>
                                    <span style="font-size: 11px; white-space: nowrap;">Left | Right</span>
                                    <span style="font-size: 11px; white-space: nowrap;">(cm.mm)</span>
                                </th>
                                <th scope="col" rowspan="2" class="align-middle">
                                    Modified Apley Test<br>
                                    <span style="font-size: 11px; white-space: nowrap;">Left | Right</span>
                                </th>

                                <!-- Sub-headers for Body Mass Composition (Spanning 2 rows down) -->
                                <th scope="col" rowspan="2" class="align-middle">BMI (kg/m²)</th>
                                <th scope="col" rowspan="2" class="align-middle">Height (cm)</th>
                                <th scope="col" rowspan="2" class="align-middle">Weight (kg)</th>
                            </tr>
                            <tr>
                                <!-- Specific sub-fields under One-mile run/walk -->
                                <th scope="col">VO₂ max (mL/kg/min)</th>
                                <th scope="col">Time Taken (mm:ss)</th>
                            </tr>
                        </thead>
                        <tbody> </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
/* Simple debounce utility */
function debounce(fn, delay) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => fn.apply(this, args), delay);
    };
}

$(document).ready(function() {
    var table = $('#FitnessTestStatus').DataTable({
        info: true,
        processing: true,
        serverSide: true,
        searchable: true,
        pageLength: 100,
        lengthMenu: [[100, 200, 300, -1], [100, 200, 300 , "All"]],
        dom: `<"top"lf><"filter-right"B>rt<"bottom"ip><"clear">`,        
        ajax: {
            url: '{{ $ajaxUrl }}',
            type: 'GET',
            data: function(d) {
                d.class = $('#filter_class').val();
                d.term = $('#filter_term').val();         
                d.status = $('#filter_status').val();    
                d.test = $('.filter_test:checked').map(function () {
                    return $(this).val();
                }).get();
            },
            beforeSend: function() {
                Swal.fire({
                    icon:'info',
                    title: 'Loading...',
                    text: 'Please wait while we fetch the data',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
            },
            complete: function() {
                Swal.close();
            }
        },

        order: [ [1, 'asc'] ],

        columns: [
            { data: 'class_name', name : 'class_name'},
            { data: 'rollno', name : 'rollno'},
            { data: 'student_name', name: 'student_name' },            
            { data: 'age', name: 'age' },
            { data: 'oneMile_run_walk', name: 'oneMile_run_walk' },
            { 
                data: 'oneMile_time_taken', 
                name: 'oneMile_time_taken', 
                defaultContent: '---',
                render: function(data, type, row) {
                    if (!data || isNaN(data) || data <= 0) {
                        return '---';
                    }
            
                    let totalSeconds = parseFloat(data) / 1000;
                    let minutes = Math.floor(totalSeconds / 60);
                    let seconds = Math.round(totalSeconds % 60);
                   // return `${minutes} min ${seconds} sec`;
                    
                    return `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
                }
            },
            { data: 'twenty_m_pacer', name: 'twenty_m_pacer' },
            { data: 'fifteen_m_pacer', name: 'fifteen_m_pacer' },
            { data: 'reverse_curl', name: 'reverse_curl' },
            { data: 'push_walk', name: 'push_walk' },
            { data: 'pushup', name: 'pushup' },
            { data: 'seated_pushup', name: 'seated_pushup' },
            { data: 'pullup', name: 'pullup' },
            { data: 'modified_pullup', name: 'modified_pullup' },
            { data: 'dumbbell_press', name: 'dumbbell_press' },
            { data: 'Isometric_pushup', name: 'Isometric_pushup' },
            { data: 'curlup', name: 'curlup' },
            { data: 'modified_curlup', name: 'modified_curlup' },
            { data: 'trunk_lift', name: 'trunk_lift' },
            { data: 'shoulder_stretch', name: 'shoulder_stretch' },
            { data: 'sit_and_reach', name: 'sit_and_reach' },
            { data: 'modified_apley_test', name: 'modified_apley_test' },
            { data: 'cwsn_bmi', name: 'cwsn_bmi' },
            { data: 'height', name: 'height' },
            { data: 'weight', name: 'weight' },
            { data: 'pwd_type_id', name: 'pwd_type_id', visible: false, searchable: false },
            { 
                data: 'view_link', name: 'view_link', orderable: false, searchable: false, className: 'noExport',
                render: function(data, type, row) {
                    return `<a href="${data}" class="btn-link" data-pwd-type-id="${row.pwd_type_id ?? ''}">View</a>`;
                }
            }
        ],

        buttons: [{
            extend: 'collection',
            text: 'Export',
            className: 'exportButton itm-list',
            buttons: [
                {
                    extend: 'excel',
                    title: 'Fitness Test Status',
                    exportOptions: { columns: ':visible:not(.noExport)' },
                    filename: function () { return getTimestamp(); },
                },
            ],
        }],

        initComplete: function () {
            $('.dt-search input[type="search"]').attr('placeholder', 'Student | Roll No.');

            var classList = @json($classList);
            const $dropdown =$('<select class="form-control" id="filter_class"></select>');
            classList.forEach(option => {
                const section = option.section ? ` - ${option.section}` : '';
                const displayText = option.name + section ;
                const value = option.class_id + '-' + option.section;
                $dropdown.append(new Option(displayText, value));
            });
            $('<div class="pull-right"></div>').append($dropdown).appendTo("#FitnessTestStatus_wrapper .top").next('.dt-length').addClass("pull-right");
            $dropdown.on('change', function() { table.ajax.reload(); });
            
            /* === Terms Filter === */
            var terms = @json($terms);
            var selectedTermId = @json($TermMasterId);
            const $termDropdown =$('<select class="form-control" id="filter_term"></select>');
            terms.forEach(option => {
                const displayText =option.academic_year + ' | ' +  option.term_name;
                const value = option.academic_year + '|' + option.id;
                const isSelected = option.id == selectedTermId;
                $termDropdown.append(new Option(displayText, value, isSelected, isSelected));
            });

            $('<div class="pull-right"></div>')
                .append($termDropdown)
                .appendTo("#FitnessTestStatus_wrapper .top")
                .next('.dt-length')
                .addClass("pull-right");
            $termDropdown.on('change', function() { table.ajax.reload(); });

            /* === STATUS FILTER === */
            var status = [
                { name: 'All', status: ''},
                { name: 'Complete', status: 'complete'},
                { name: 'Incomplete', status: 'incomplete'},
            ];
            const $dropdown1 =$('<select class="form-control" id="filter_status"></select>');
            status.forEach(option => $dropdown1.append(new Option(option.name, option.status)));
            $('<div class="pull-right"></div>').append($dropdown1).appendTo("#FitnessTestStatus_wrapper .top").next('.dt-length').addClass("pull-right");
            $dropdown1.on('change', function() {
                updateTestCount();
                table.ajax.reload();
            });

            /* === TEST FILTER (Multi-select with Select All) === */
            var testType = [
                { name: '20-Meter Pacer', test: 'twenty_m_pacer' },
                { name: '15-Meter Pacer', test: 'fifteen_m_pacer' },
                { name: 'One-mile Run/Walk', test: 'oneMile_run_walk' },
                { name: 'Curl-up', test: 'curlup' },
                { name: 'Modified Curl-up', test: 'modified_curlup' },
                { name: 'Push-up', test: 'pushup' },
                { name: 'Modified Pull-up', test: 'modified_pullup' },
                { name: 'Pull-up', test: 'pullup' },
                { name: 'Trunk Lift', test: 'trunk_lift' },
                { name: 'Isometric Push-up', test: 'Isometric_pushup' },
                { name: 'Seated Push-up', test: 'seated_pushup' },
                { name: '40-meter Push/Walk', test: 'push_walk' },
                { name: 'Reverse Curl', test: 'reverse_curl' },
                { name: 'Dumbbell Press', test: 'dumbbell_press' },
                { name: 'Modified Apley Test', test: 'modified_apley_test' },
                { name: 'Sit and Reach Test', test: 'sit_and_reach' },
                { name: 'Shoulder Stretch', test: 'shoulder_stretch' },
                { name: 'Body Mass Index', test: 'cwsn_bmi' }
            ];
        
            let $dropdown2 =$(`
                <div class="dropdown" style="display:inline-block;">
                    <button class="form-control dropdown-toggle" type="button" data-bs-toggle="dropdown" id="test_count_label">
                        All Tests <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" style="padding:8px 0px 0px 12px; max-height:300px; overflow:auto; min-width:200px;"></ul>
                </div>
            `);

            let $list =$dropdown2.find(".dropdown-menu");
            let $selectAll =$(`
                <li>
                    <label style="font-weight:bold; cursor:pointer;">
                        <input type="checkbox" id="select_all_tests"> Select All
                    </label>
                    <hr style="margin:5px 0;">
                </li>
            `);
            $list.append($selectAll);

            testType.forEach(option => {
                let $chk =$(`
                    <li>
                        <label style="white-space:nowrap; cursor:pointer;">
                            <input type="checkbox" class="filter_test" value="${option.test}" checked> ${option.name}
                        </label>
                    </li>
                `);
                $list.append($chk);
            });

            $('<div class="pull-right"></div>').append($dropdown2).appendTo("#FitnessTestStatus_wrapper .top").next('.dt-length').addClass("pull-right");

            $dropdown2.find(".filter_test").prop("checked", true);
            $("#select_all_tests").prop("checked", true);
            updateTestCount();

            /* === FUNCTIONS === */
            function updateTestCount() {
                let selectedCount = $('.filter_test:checked').length;
                if (selectedCount === testType.length) {
                    $("#test_count_label").text("All Tests Selected");
                    $("#select_all_tests").prop("checked", true);
                } else if (selectedCount === 0) {
                    $("#test_count_label").text("0 Tests Selected");
                    $("#select_all_tests").prop("checked", false);
                } else {
                    $("#test_count_label").text(selectedCount + " Tests Selected");
                    $("#select_all_tests").prop("checked", false);
                }
            }

            function toggleColumns() {
                var testColumns = [
                    'twenty_m_pacer',
                    'fifteen_m_pacer',
                    'oneMile_run_walk',
                    'shoulder_stretch',
                    'sit_and_reach',
                    'modified_apley_test',
                    'curlup',
                    'modified_curlup',
                    'dumbbell_press',
                    'pullup',
                    'pushup',
                    'seated_pushup',
                    'trunk_lift',
                    'Isometric_pushup',
                    'reverse_curl',
                    'modified_pullup',
                    'push_walk',
                    'cwsn_bmi',
                    'height',
                    'weight'
                ];
                testColumns.forEach(test => {
                    let colIndex = table.column(test + ':name').index();
                    if (colIndex !== undefined) table.column(colIndex).visible(false);
                });
                let selectedTests = $('.filter_test:checked').map(function(){ return$(this).val(); }).get();
                if (selectedTests.length === 0) {
                    testColumns.forEach(test => {
                        let colIndex = table.column(test + ':name').index();
                        if (colIndex !== undefined) table.column(colIndex).visible(true);
                    });
                } else {
                    selectedTests.forEach(test => {
                        let colIndex = table.column(test + ':name').index();
                        if (colIndex !== undefined) table.column(colIndex).visible(true);
                    });
                    if (selectedTests.includes('cwsn_bmi')) {
                        ['height','weight'].forEach(test => {
                            let colIndex = table.column(test + ':name').index();
                            if (colIndex !== undefined) table.column(colIndex).visible(true);
                        });
                    }
                }
            }

            /* === EVENT HANDLERS === */
            $(document).on("change", "#select_all_tests", function() {
                $(".filter_test").prop("checked", $(this).is(":checked"));
                updateTestCount();
                toggleColumns();
                table.ajax.reload(); 
            });

            $(document).on("change", ".filter_test", debounce(function() {
                updateTestCount();
                toggleColumns();
                table.ajax.reload();
            }, 300));
        },

        language: {
            paginate: { first: "«", previous: "‹", next: "›", last: "»" },
        }
    });

    $('#FitnessTestStatus').on('click', '.btn-link', function(e) {
        e.preventDefault();
        var pwdTypeId = $(this).attr('data-pwd-type-id');
        
        if (!pwdTypeId || pwdTypeId === 'null' || pwdTypeId === 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Disability Category Not Mapped',
                text: 'Please map the student disability category in manage student module.',
                confirmButtonText: 'OK'
            });

            return;
        }

        var url = $(this).attr('href');
        window.open(url, '_blank');
    });

    function getTimestamp() {
        const now = new Date();
        return `Fitness Test Status ${now.getDate()}-${now.getMonth()+1}-${now.getFullYear()}`;
    }
});
</script>
@endsection