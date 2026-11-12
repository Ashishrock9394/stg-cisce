@extends('layouts.filldart-app')
@section('title', $title)
@section('content')

<style >
    .table-loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.7);
        z-index: 999;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    /* Clean Bootstrap/CSS Spinner */
    .custom-spinner {
        width: 3rem;
        height: 3rem;
        border: 0.25em solid #e3e6f0;
        border-right-color: #4e73df; /* Matches standard primary blue themes */
        border-radius: 50%;
        animation: spin-animation 0.75s linear infinite;
    }

    
	#SchoolListTable th:nth-child(1),
	#SchoolListTable td:nth-child(1) {
		width: 4% !important;
		min-width: 30px !important;
	}

	#SchoolListTable th:nth-child(2),
	#SchoolListTable td:nth-child(2) {
		width: 10% !important;
		min-width: 50px !important;
	}

	#SchoolListTable th:nth-child(3),
	#SchoolListTable td:nth-child(3) {
		width: 15% !important;
		min-width: 180px !important;
	}

	#SchoolListTable th:nth-child(4),
	#SchoolListTable td:nth-child(4) {
		width: 12% !important;
		min-width: 120px !important;
	}

	#SchoolListTable th:nth-child(5),
	#SchoolListTable td:nth-child(5) {
		width: 10% !important;
		min-width: 120px !important;
	}

	#SchoolListTable th:nth-child(6),
	#SchoolListTable td:nth-child(6) {
		width: 13% !important;
		min-width: 50px !important;
	}

	#SchoolListTable th:nth-child(7),
	#SchoolListTable td:nth-child(7) {
		width: 8% !important;
		min-width: 80px !important;
	}

	#SchoolListTable th:nth-child(8),
	#SchoolListTable td:nth-child(8) {
		width: 8% !important;
		min-width: 100px !important;
	}

	#SchoolListTable th:nth-child(9),
	#SchoolListTable td:nth-child(9) {
		width: 8% !important;
		min-width: 80px !important;
	}

	#SchoolListTable th:nth-child(10),
	#SchoolListTable td:nth-child(10) {
		width: 8% !important;
		min-width: 100px !important;
	}

	#SchoolListTable th:nth-child(11),
	#SchoolListTable td:nth-child(11) {
		width: 8% !important;
		min-width: 85px !important;
	}

	#SchoolListTable th:nth-child(12),
	#SchoolListTable td:nth-child(12) {
		width: 10% !important;
		min-width: 75px !important;
	}
    
	#SchoolListTable th:first-child,
	#SchoolListTable td:first-child,
	#SchoolListTable th:nth-child(2),
	#SchoolListTable td:nth-child(2),
	#SchoolListTable td:last-child {
		text-align: center;
	}

	#SchoolListTable th,
	#SchoolListTable td {
		white-space: normal !important;
		word-wrap: break-word;
		word-break: break-word;
	}

    table#SchoolListTable th.dt-orderable-none .dt-column-order {
	    display: none !important;
	}

    @keyframes spin-animation {
        to { transform: rotate(360deg); }
    }

    /* Keep parent container relative so absolute loader matches boundaries */
    .tab-content {
        position: relative;
    }
</style>


<div class="container px-0">
    <div class="t-mrg2 mb-5 pb-5">
        <div class="all-chaptr-cards mb-4" style="margin: 0;">
			<div class="row">
				<x:back-button title="{{$title}}" >
				</x:back-button>
			</div>
		</div>

        <div class="responsive mb-0">
		    <div class="tab-content cus-radius pb-5" id="itemTabsContent">
		        <div id="school-list" role="tabpanel" aria-labelledby="infrastructure-tab">
		            <table id="SchoolListTable" class="table table-bordered tbl-style display mb-4" style="width:100%">
		                <thead>             
        		              <tr>
                                <th>#</th>
                                <th>School Code</th>
                                <th>School Name</th>
                                <th>Principal</th>
                                <th>Region</th>
                                <th>Zone</th>
                                <th>State</th>
                                <th>City</th>
                                <th>District</th>
                                <th >Address</th>
                                <th>All Students</th>
                                @if(Auth::user()->role_id == 9)
                                <th>Action</th>
                                @endif
                                <!-- @if(Auth::user()->role_id == 2)
                                @else
                                <th>Edit School</th>
                                @endif -->
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
$(document).ready(function() {
    var table = $('#SchoolListTable').DataTable({
        info: true,
        processing: true,
        serverSide: true,
        searchable: true,
        pageLength: 100, // default rows per page
        lengthMenu: [[100, 200, 300, -1], [100, 200, 300 , "All"]],
        dom: `<"top"lf><"filter-right"B>rt<"bottom"ip><"clear">`,
        
        ajax: {
            url: "{{ route('cicse.schoollist') }}",
            data: function(d) {
                d.region = $('#select_region').val();
                d.zone = $('#select_zone').val();
                d.academic_year = $('#academic_year').val();
                d.uploaded_status = $('#select_upload_status').val();
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
            { data: 'school_principal', name: 'school_principal' },
            { data: 'region', name: 'region' },
            { data: 'zonename', name: 'zonename' },
            { data: 'state', name: 'state' },
            { data: 'city', name: 'city' },
            { data: 'district', name: 'district' },
            { data: 'address', name: 'address' },
            {
                data: 'view_link',
                title: 'All Students',
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    if (row.student_count > 0) {
                        return `<div>
                                <strong>${row.student_count}</strong> 
                                <a href="${row.view_link}" target="_blank">View</a>
                            </div>`;
                    } else {
                        return `<span class="text-muted">No record found</span>`;
                    }
                }
            },
            @if(Auth::user()->role_id == 9)
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                defaultContent: '',
                render: function(data, type, row) {
                    return `
                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm btn-primary mr-1 schoolProfile" data-id="${row.id}" title="Edit ${row.school_name} Details">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-primary loginAsSchool" 
                                    data-id="${row.id}" 
                                    title="Login as ${row.school_name}">
                                <i class="fas fa-sign-in-alt"></i>
                            </button>
                            
                        </div>
                    `;
                }
            },
            @endif
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

            var regions = @json($region);
            const $regionDropdown = $('<select class="form-control" id="select_region"></select>')
                .append('<option value="">All Regions</option>');

            regions.forEach(region => {
                $regionDropdown.append(`<option value="${region.name}|${region.id}">${region.name}</option>`);
            });

            var zones = @json($zones);
            const $zoneDropdown = $('<select class="form-control" id="select_zone"></select>')
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

            const $uploadDropdown = $('<select class="form-control ml-2" id="select_upload_status"></select>')
                .append(`<option value="">All Data</option>
                         <option value="uploaded" selected>Uploaded</option>
                         <option value="not_uploaded">Not Uploaded</option>`);

            const $filterContainer = $('<div class="d-flex"  style="column-gap: 6px;"></div>').append($regionDropdown).append($zoneDropdown).append($uploadDropdown).append($academicYearDropdown);
            $('<div class="pull-right"></div>').append($filterContainer).appendTo("#SchoolListTable_wrapper .top");
              $academicYearDropdown.on('change', () => table.ajax.reload());
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
        return `CICSE School List ${now.getDate()}-${now.getMonth()+1}-${now.getFullYear()}`;
    }
});

    $('#SchoolListTable').on('click', '.schoolProfile', function () {
        const schoolId = $(this).data('id');
        console.log(schoolId);
        window.location.href = "{{ route('cisce.update.profile', '') }}/" + schoolId;
    });
    
     // login as school

    $(document).on('click', '.loginAsSchool', function () {

        let schoolId = $(this).data('id');

        $.ajax({
            url: "{{ route('login.as.school') }}",
            type: "POST",
            dataType: "json",
            data: {
                schoolId: schoolId,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {

                console.log('Response:', response);

                if (response.success === true) {

                    if (response.redirect_url) {
                        // window.location.href = response.redirect_url;
                        window.location.replace(response.redirect_url);
                    } else {
                        alert('Redirect URL not found.');
                    }

                } else {

                    alert(response.message || 'Login failed.');

                }
            },
            error: function(xhr, status, error) {

                console.log('XHR:', xhr);
                console.log('Status:', status);
                console.log('Error:', error);
                console.log('Response Text:', xhr.responseText);

                alert('Something went wrong. Check browser console.');
            }
        });

    });
</script>

@endsection