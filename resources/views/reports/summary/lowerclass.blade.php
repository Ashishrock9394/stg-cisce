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
</style>

<div class="container-fluid">
    <div class="t-mrg2 mb-5 pb-5 px-4">
        <div class="row">
            <div class="col">
                <a href="#a" onclick="history.back()" class="back-button" style="left: 0px;">
                    <span class="arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
                        </svg>
                    </span>
                </a>
                <div class="heading-rw mt-2" style=" margin-left: -18px;">
                    <h1>{{$title}}</h1>
                </div>
            </div>

            <div class="responsive mb-5 mt-3 pb-4">
                <div class="tab-content cus-radius pb-5 mt-4 ">
                    <!-- Infrastructure List Tab -->
                
                        <table id="LowerClassFitnessStatus" class="table table-bordered tbl-style display mb-4" style="width:100%">
                            <thead>             
                                <tr>
                                
                                    <th scope="col" >Class</th>
                                    <th scope="col" >Roll No.</th>
                                    <th scope="col" >Students Name</th>
                                    <th scope="col" >Age</th>
                                    <th scope="col" >Running</th>
                                    <th scope="col" >Hopping</th>
                                    <th scope="col" >Jumping & Landing</th>
                                    <th scope="col" >Skipping</th>
                                    <th scope="col" >Dodging</th>
                                    <th scope="col" >One-Foot Balance</th>
                                    <th scope="col" >Beam Walk</th>
                                    <th scope="col" >Catching & Receiving Bounce Ball</th>
                                    <th scope="col" >Catching Small Ball With Two Hands</th>
                                    <th scope="col" >Under Arm Throw</th>
                                    <th scope="col" >Over Arm Throw</th>
                                    <th scope="col" >Striking drop & hit forward</th>
                                    <th scope="col" >Dribbling With Hands</th>
                                    <th scope="col" >Dribbling With Feet</th>
                                    <th scope="col" >Kicking Stationary Ball</th>

                                    <th scope="col" >Flamingo Balance Test</th>
                                    <th scope="col" >Plate Tapping</th>

                                    <th scope="col" >Body Mass Index</th>
                                    <th scope="col" >Height</th>
                                    <th scope="col" >Weight</th>
                                    <th scope="col" >Report</th>

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
/* Debounce utility */
function debounce(fn, delay) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => fn.apply(this, args), delay);
    };
}

$(document).ready(function() {
    var table = $('#LowerClassFitnessStatus').DataTable({
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
            complete: function() { Swal.close(); }
        },

       

        columns: [
            { data: 'class_name', name : 'class_name'},
            { data: 'rollno', name : 'rollno'},
            { data: 'student_name', name: 'student_name' },
            { data: 'age', name: 'age' },
            { data: 'running', name: 'running' },
            { data: 'hopping', name: 'hopping' },
            { data: 'jumping_landing', name: 'jumping_landing' },
            { data: 'skipping', name: 'skipping' },
            { data: 'dodging', name: 'dodging' },
            { data: 'one_foot_balance', name: 'one_foot_balance' },
            { data: 'beam_walk', name: 'beam_walk' },
            { data: 'catching_receiving_bounce', name: 'catching_receiving_bounce' },
            { data: 'catching_small_ball', name: 'catching_small_ball' },
            { data: 'under_arm_throw', name: 'under_arm_throw' },
            { data: 'over_arm_throw', name: 'over_arm_throw' },
            { data: 'striking_drop_hit', name: 'striking_drop_hit' },
            { data: 'dribbling_hands', name: 'dribbling_hands' },
            { data: 'dribbling_feet', name: 'dribbling_feet' },
            { data: 'kicking_ball', name: 'kicking_ball' },
            { data: 'flamingo_balance', name: 'flamingo_balance' },
            { data: 'plate_tapping', name: 'plate_tapping' },
            { data: 'bmi', name: 'bmi' },
            { data: 'height', name: 'height' },
            { data: 'weight', name: 'weight' },
            { 
                data: 'view_link', name: 'view_link', orderable: false, searchable: false, className: 'noExport',
                render: function(data) {
                    return `<a href="${data}" class="btn-link" target="_blank">View</a>`;
                }
            }
        ],

        createdRow: function(row, data, dataIndex) {
            if (data.invalid_age == 1) {
                $(row).addClass('invalid-age');
                $(row).find('a.btn-link').attr('data-invalid', 1);
            }
        },


        buttons: [{
            extend: 'collection',
            text: 'Export',
            className: 'exportButton itm-list',
            buttons: [{
                extend: 'excel',
                title: 'Fitness Test Status',
                exportOptions: { columns: ':visible:not(.noExport)' },
                filename: function () { return getTimestamp(); },
            }],
        }],

       

        initComplete: function () {
            $('.dt-search input[type="search"]').attr('placeholder', 'Student | Roll No.');

            /* === CLASS FILTER === */
            var classList = @json($classList);
            const $dropdown = $('<select class="form-control" id="filter_class"></select>');
            classList.forEach(option => {
                const section = option.section ? ` - ${option.section}` : '';
                const displayText = option.name + section ;
                const value = option.class_id + '-' + option.section;
                $dropdown.append(new Option(displayText, value));
            });
            $('<div class="pull-right"></div>').append($dropdown).appendTo("#LowerClassFitnessStatus_wrapper .top").next('.dt-length').addClass("pull-right");
            $dropdown.on('change', function() { table.ajax.reload(); });

            /* === Terms Filter === */
            var terms = @json($terms);
            var selectedTermId = @json($TermMasterId);
            const $termDropdown = $('<select class="form-control" id="filter_term"></select>');
            terms.forEach(option => {
                const displayText =option.academic_year + ' | ' +  option.term_name;
                const value = option.academic_year + '|' + option.id;
                const isSelected = option.id == selectedTermId;
                $termDropdown.append(new Option(displayText, value, isSelected, isSelected));
            });

            $('<div class="pull-right"></div>')
                .append($termDropdown)
                .appendTo("#LowerClassFitnessStatus_wrapper .top")
                .next('.dt-length')
                .addClass("pull-right");
            $termDropdown.on('change', function() { table.ajax.reload(); });

            /* === STATUS FILTER === */
            var status = [
                { name: 'All', status: '' },
                { name: 'Complete', status: 'complete' },
                { name: 'Incomplete', status: 'incomplete' },
               
            ];
            const $dropdown1 = $('<select class="form-control" id="filter_status"></select>');
            status.forEach(option => $dropdown1.append(new Option(option.name, option.status)));
            $('<div class="pull-right"></div>').append($dropdown1).appendTo("#LowerClassFitnessStatus_wrapper .top").next('.dt-length').addClass("pull-right");
            $dropdown1.on('change', function() {
                updateTestCount();
                table.ajax.reload();
            });

            /* === TEST FILTER (Multi-select with Select All) === */
            var testType = [
                { name: 'Running', test: 'running' },
                { name: 'Hopping', test: 'hopping' },
                { name: 'Jumping & Landing', test: 'jumping_landing' },
                { name: 'Skipping', test: 'skipping' },
                { name: 'One-Foot Balance', test: 'one_foot_balance' },
                { name: 'Dodging', test: 'dodging' },
                { name: 'Beam Walk', test: 'beam_walk' },
                { name: 'Catching & Receiving Bounce Ball', test: 'catching_receiving_bounce' },
                { name: 'Catching Small Ball With Two Hands', test: 'catching_small_ball' },
                { name: 'Under Arm Throw', test: 'under_arm_throw' },
                { name: 'Over Arm Throw', test: 'over_arm_throw' },
                { name: 'Striking drop & hit forward', test: 'striking_drop_hit' },
                { name: 'Dribbling With Hands', test: 'dribbling_hands' },
                { name: 'Dribbling With Feet', test: 'dribbling_feet' },
                { name: 'Kicking Stationary Ball', test: 'kicking_ball' },
                { name: 'Flamingo Balance Test', test: 'flamingo_balance' },
                { name: 'Plate Tapping', test: 'plate_tapping' },                
                { name: 'Body Mass Index', test: 'bmi' }
            ];
        
            let $dropdown2 = $(`
                <div class="dropdown" style="display:inline-block;">
                    <button class="form-control dropdown-toggle" type="button" data-toggle="dropdown" id="test_count_label">
                        All Tests <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" style="padding:10px; max-height:300px; overflow:auto; min-width:260px;"></ul>
                </div>
            `);

            let $list = $dropdown2.find(".dropdown-menu");
            let $selectAll = $(`
                <li>
                    <label style="font-weight:bold; cursor:pointer;">
                        <input type="checkbox" id="select_all_tests"> Select All
                    </label>
                    <hr style="margin:5px 0;">
                </li>
            `);
            $list.append($selectAll);

            testType.forEach(option => {
                let $chk = $(`
                    <li>
                        <label style="white-space:nowrap; cursor:pointer;">
                            <input type="checkbox" class="filter_test" value="${option.test}" checked> ${option.name}
                        </label>
                    </li>
                `);
                $list.append($chk);
            });

            $('<div class="pull-right"></div>').append($dropdown2).appendTo("#LowerClassFitnessStatus_wrapper .top").next('.dt-length').addClass("pull-right");

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
                    'running','hopping','jumping_landing','skipping','dodging','one_foot_balance','beam_walk',
                    'catching_receiving_bounce','catching_small_ball','under_arm_throw','over_arm_throw',
                    'striking_drop_hit','dribbling_hands','dribbling_feet','kicking_ball','flamingo_balance',
                    'plate_tapping','bmi','height','weight'
                ];
                testColumns.forEach(test => {
                    let colIndex = table.column(test + ':name').index();
                    if (colIndex !== undefined) table.column(colIndex).visible(false);
                });
                let selectedTests = $('.filter_test:checked').map(function(){ return $(this).val(); }).get();
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
                    if (selectedTests.includes('bmi')) {
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
                table.ajax.reload(); //  single reload
            });

            $(document).on("change", ".filter_test", debounce(function() {
                updateTestCount();
                toggleColumns();
                table.ajax.reload(); // debounced reload
            }, 300));
        },

        language: {
            paginate: { first: "«", previous: "‹", next: "›", last: "»" },
        }
    });

    $('#LowerClassFitnessStatus').on('click', '.btn-link', function(e) {
        e.preventDefault();
        var isInvalid = $(this).attr('data-invalid');

        if (isInvalid == 1) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Age',
                text: 'The student does not meet the class-wise age requirement. You may view or download the report from the Assessment Reports module.',
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