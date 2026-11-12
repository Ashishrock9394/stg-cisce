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
				<x:back-button title="{!! $title !!}" >
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
                    <th>CWSN Students</th>
                    <th>Paid</th>
                    <th>Unpaid</th>
                    <th>Assessments Completed</th>
                    <th>Ongoing</th>
                    <th>Yet to Start</th>
                </tr>
            </thead>
        </table>

        
    </div>
</div>
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
				url: "{{ route('schools.assessement.status') }}",
				data: function(d) {
					d.region = $('#select_region').val();
					d.zone = $('#select_zone').val();
					d.status = $('#status').val();
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
				{ data: 'cwsn_count', name: 'cwsn_count' },
				{ data: 'pymt_count', name: 'pymt_count' },
				{ data: 'unpaid_students', name: 'unpaid_students' },
				{ data: 'completed', name: 'completed' },
				{ data: 'ongoing', name: 'ongoing' },
				{ data: 'yet_to_start', name: 'yet_to_start' },
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

			order: [[0, 'asc']],

			initComplete: function () {
				$('.dt-search input[type="search"]').attr('placeholder', 'Search here...');

				var regions = @json($region);

				let academicYear = @json($academicYear);
				let previousAcademicYear = @json($previousAcademicYear);
				let selectedYear = @json($selectedYear);

				// Academic Year Dropdown
				const $academicYearDropdown = $('<select class="form-control ml-2" name="academic_year" id="academic_year"></select>')
					.append(`
						<option value="${previousAcademicYear}" ${selectedYear == previousAcademicYear ? 'selected' : ''}>${previousAcademicYear}</option>
						<option value="${academicYear}" ${selectedYear == academicYear ? 'selected' : ''}>${academicYear}</option>
					`);

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
					.append($uploadDropdown).append($academicYearDropdown);

				$('<div class="pull-right"></div>')
					.append($filterContainer)
					.appendTo("#schoolStatusTable_wrapper .top");

				// Reload table on filter change
				$regionDropdown.on('change', () => table.ajax.reload());
				$zoneDropdown.on('change', () => table.ajax.reload());
				$uploadDropdown.on('change', () => table.ajax.reload());
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
			return `Schools Assessment List ${now.getDate()}-${now.getMonth()+1}-${now.getFullYear()}`;
		}
	});

</script>

@endsection