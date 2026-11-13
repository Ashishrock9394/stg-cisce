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
                <div class="tab-content cus-radius pb-5 mt-4">
                    <!-- Infrastructure List Tab -->
                
                        <table id="FitnessTestStatus" class="table table-bordered tbl-style display mb-4" style="width:100%">
                            <thead>             
                                <tr>
                                
                                    <th scope="col" >Class</th>
                                    <th scope="col" >Roll No.</th>
                                    <th scope="col" >Students Name</th>
                                    <th scope="col" >Sit and Reach Test</th>
                                    <th scope="col" >600 meter run/walk</th>
                                    <th scope="col" >Push Ups</th>
                                    <th scope="col" >50 mt. dash</th>
                                    <th scope="col" >Partial curl up 30 sec</th>
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





<script >
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
        columns: [
            { data: 'class_name', name : 'class_name'},
            { data: 'rollno', name: 'rollno' },
            { data: 'student_name', name: 'student_name' },
            { data: 'sit_and_reach', name: 'sit_and_reach' },
            { data: 'run_600m', name: 'run_600m' },
            { data: 'pushups', name: 'pushups' },
            { data: 'dash_50m', name: 'dash_50m' },
            { data: 'curlup', name: 'curlup' },
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
        order: [[0, 'asc']],

        initComplete: function () {
            $('.dt-search input[type="search"]').attr('placeholder', 'Search here...');

            /* === CLASS FILTER === */
            var classList = @json($classList);
            const $dropdown = $('<select class="form-control" id="filter_class"></select>');
            classList.forEach(option => {
                const section = option.section ? ` - ${option.section}` : '';
                const displayText = option.name + section ;
                const value = option.class_id + '-' + option.section;
                $dropdown.append(new Option(displayText, value));
            });
            $('<div class="pull-right"></div>').append($dropdown).appendTo("#FitnessTestStatus_wrapper .top").next('.dt-length').addClass("pull-right");
            $dropdown.on('change', function() { table.ajax.reload(); });

            /* === STATUS FILTER === */
            var status = [
                { name: 'All', status: ''},
                { name: 'Complete', status: 'complete'},
                { name: 'Incomplete', status: 'incomplete'},
            ];
            const $dropdown1 = $('<select class="form-control" id="filter_status"></select>');
            status.forEach(option => $dropdown1.append(new Option(option.name, option.status)));
            $('<div class="pull-right"></div>').append($dropdown1).appendTo("#FitnessTestStatus_wrapper .top").next('.dt-length').addClass("pull-right");
            $dropdown1.on('change', function() {
                updateTestCount();
                table.ajax.reload();
            });

            /* === TEST FILTER (Multi-select with Select All) === */
            var testType = [
                { name: 'Sit and Reach', test: 'sit_and_reach' },
                { name: '600 Meter Run/Walk', test: 'run_600m' },
                { name: 'Push Ups', test: 'pushups' },
                { name: '50 mt Dash', test: 'dash_50m' },
                { name: 'Curl Up', test: 'curlup' },
                { name: 'BMI', test: 'bmi' }
            ];
        
            let $dropdown2 = $(`
                <div class="dropdown" style="display:inline-block;">
                    <button class="form-control dropdown-toggle" type="button" data-bs-toggle="dropdown" id="test_count_label">
                        All Tests <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" style="padding:10px; max-height:300px; overflow:auto; min-width:200px;"></ul>
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
                var testColumns = ['sit_and_reach','run_600m','pushups','dash_50m','curlup','bmi','height','weight'];
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
                table.ajax.reload(); // ✅ only once
            });

            $(document).on("change", ".filter_test", debounce(function() {
                updateTestCount();
                toggleColumns();
                table.ajax.reload(); // ✅ debounced
            }, 300));
        },

        language: {
            paginate: { first: "«", previous: "‹", next: "›", last: "»" },
        }
    });

    function getTimestamp() {
        const now = new Date();
        return `Fitness Test Status ${now.getDate()}-${now.getMonth()+1}-${now.getFullYear()}`;
    }
});
</script>




@endsection