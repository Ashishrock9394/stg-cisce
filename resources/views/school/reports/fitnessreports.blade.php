@extends('layouts.filldart-app') @section('title', $title) @section('content')

@php
	$userId = Auth::user()->id; 
	$schoolsId = DB::table('school_reference')->where('school_user_id',$userId)->where('status', 1)->value('school_id'); 
	$schoolCode = DB::table('schools')->where('id',$schoolsId)->value('school_code'); 
@endphp


<style type="text/css">
	#studentReportsTable.table thead th:first-child {
	    width: 40px;
	}

	table#studentReportsTable thead tr th:nth-child(6) {
	    width: 7%;
	}
	table#studentReportsTable thead tr th:nth-child(2) {
	    width: 3%;
	}
	table#studentReportsTable thead tr th:nth-child(2) {
	    width: 1%;
	}
	table#studentReportsTable thead tr th:nth-child(9) {
	    width: 11%;
	}
	table#studentReportsTable th.dt-orderable-none .dt-column-order {
	    display: none !important;
	}

	.dt-container .top .filter-right {
	    order: 2;
	    margin-right: 8%;
	    margin-top: 5px;
	}

</style>

<div class="container">
	<div class="t-mrg2 mb-5 pb-5">
		<!-- <div class="t-mrg2 mb-5 pb-4"> -->
		<div class=" all-chaptr-cards" style="margin: 0;">
			<!-- Success message -->
			<form method="POST" name="view-trainer-report" id="reportform" action="javascript(0);">
				<div class="row">
					<div class="col">
						<a href="#a" onclick="history.back()" class="back-button">
							<span class="arrow">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
									<path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
								</svg>
							</span>
						</a>
						<div class="heading-rw mt-2 mb-0 pt-0">
							<h1>{{$title}}</h1>
						</div>
					</div>
				</div>
			</form>
		</div>

		<div class="container-fluid p-0">
			<div class="responsive m-0 mt-4 pt-2" id="record_table">
				<table id="studentReportsTable" class="table table-bordered tbl-style" >
					<thead>
						<tr>
							<th scope="col"><input type="checkbox" id="select-all"></th>
							<th scope="col">#</th>
							<th scope="col" >Class</th>
							<th scope="col">Section </th>
							<th scope="col" >Roll No </th>
							<th scope="col" >Student Name</th>
							<th scope="col" >Admission No.</th>
							<th scope="col" >Gender</th>
							<th scope="col" >Birth Date</th>
							<th scope="col" >Email</th>
							<th scope="col" >Status</th>
							<th scope="col">Report</th>
						</tr>
					</thead>
					<tbody> </tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection


@push('scripts')
<script>

	/*** Manipulate the Table with the Student Details.	* */
	$(document).ready(function() {

		var table = $('#studentReportsTable').DataTable({
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
				url: "{{ route('fitness.report') }}",
				data: function(d) {
	                d.class_id = $('#select_class').val();
	                d.test_status = $('#test_status').val();
	            },

	            beforeSend: function() {
				    Swal.fire({
				      icon:'info',
				      title: 'Loading data...',
				      text: 'Please wait while we fetch the report.',
				      allowOutsideClick: false,
				      didOpen: () => {
				        Swal.showLoading();
				      }
				    });
				  },
				  complete: function() {
				    Swal.close();
				  }

			},
			columns: [
				{	
					targets: 0,
		            data: null,
		            name: 'checkbox',
		            orderable: false,
		            searchable: false,className: 'no-sort text-center',
		            render: function(data, type, row) {
				        return '<input type="checkbox" class="row-checkbox" value="' + row.student_id + '">';
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
				{	data: 'class_id', name: 'class_id' },
				{  data: 'section', name: 'section'	},
				{	data: 'rollno', name: 'rollno' },
				{  data: 'student_name', name: 'student_name' },
            	{  data: 'admissionnumber', name: 'admissionnumber' },
				{ 	data: 'gender', name: 'gender' },
				{	data: 'dob', name: 'dob' },
				{	data: 'email_id', name: 'email_id' },
				{ data:'testStatus', name:'testStatus',searchable:false, sorting:false},
				{ data:'viewReport', name:'viewReport',searchable:false, sorting:false}
			],

			columnDefs: [
		        { targets: 0, orderable: false, className: 'no-sort' }
		    ],



			"initComplete": function() {
            $('.dt-search input[type="search"]').attr('placeholder', 'Search here...');

	
			var classList = @json($classList);
            const $dropdown = $('<select class="form-control" id="select_class"></select>');

            classList.forEach(option => {
                const section = option.section ? ` - ${option.section}` : '';
                const displayText = option.name + section ;
                const value = option.class_id + '-' + option.section;
                $dropdown.append(new Option(displayText, value));
            });

            $('<div class="pull-right"></div>').append($dropdown).appendTo("#studentReportsTable_wrapper .top").next('.dt-length').addClass("pull-right");
            $dropdown.on('change', function() {
               table.ajax.reload();
            });


            const $uploadDropdown = $('<select class="form-control" id="test_status"></select>')
                .append(`<option value="">Test Status</option>
                         <option value="complete">Complete</option>
                         <option value="incomplete">InComplete</option>`);
            const $filterContainer = $('<div class="d-flex"></div>').append($uploadDropdown);
            $('<div class="pull-right"></div>').append($filterContainer).appendTo("#studentReportsTable_wrapper .top");
            $uploadDropdown.on('change', () => table.ajax.reload());    
        },


		buttons: [{
			extend: 'collection',
			text: 'Export',
			className: 'exportButton',
			
			buttons: [
				{
					text: 'Generate Report Cards',
					action: function ( e, dt, node, config ) {							
                generateICards();
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

	
		function generateICards() {
	    Swal.fire({
		        icon: "info",
		        title: "Assessments in Process",
		        text: "This feature is coming soon.",
		    });
		}



		function generateICards_bk() {

			Swal.fire({
				icon: 'info',
				title: 'Processing...',
				text: 'Please wait while while we generate I-Cards.',
				allowOutsideClick: false,
				showConfirmButton: false,
				didOpen: () => {  Swal.showLoading(); }
		   });

         var studentIds = [];
		   var data = table.rows().data();
		   data.each(function(rowData) {
		      studentIds.push(rowData.student_id); 
		   });

         $.ajax({
		        url: "{{ route('generatecard') }}",
		        method: 'POST',
		        data: {
		            _token: '{{ csrf_token() }}',
		             student_ids: studentIds,
		        },
		        xhrFields: {
		            responseType: 'blob'
		        },
		         success: function(data) {
		        		Swal.close();
		            const blob = new Blob([data], { type: 'application/pdf' });
		            const link = document.createElement('a');
		            link.href = window.URL.createObjectURL(blob);
		            link.download = 'students-i-cards.pdf';
		            link.click();
		        },

		        error: function() {
		            alert('Error generating i-Cards. Please try again.');
		        }
		   });
      }
	});


</script>
@endpush

