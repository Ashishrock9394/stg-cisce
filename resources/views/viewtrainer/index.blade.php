@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
@section('content')

<style>
   td.dt-type-numeric {
      text-align: center;
      text-align: left !important;
   }
</style>
<div class="container">
   <div class="t-mrg2">
      <div class=" all-chaptr-cards">
         <!-- Success message -->
         <form method="POST" name="view-trainer-report" id="reportform" action="javascript(0);">
            <div class="row">
               <div class="col">
                  <a href="{{ route('filldart.dashboard') }}" class="back-button">
                     <span class="arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                           <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
                        </svg>
                     </span>
                  </a>
                  <div class="heading-rw mt-0 mb-0 pt-0">
                     <h1>{{$title}}</h1>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-12">
                  <div id="activity_from_div" class="sports-filtr overlay">
                     <div class="form-row mt-4">
                        <div class="col-12 mt-2 mb-4">
                           <div class="row">
                              <div class="col-12 col-md-auto">
                                 <div class="btn-group btn-group-toggle py-0" data-toggle="buttons" id="chksubmit_button">
                                    <label class="btn w-50 btn-secondary active">
                                       <input type="radio" name="options_bytype" value="bytrainer" checked> By Trainer
                                    </label>
                                    <label class="btn w-50 btn-secondary">
                                       <input type="radio" name="options_bytype" value="byclass"> By Class
                                    </label>
                                 </div>
                              </div>
                              <div class="col-12 col-md text-right pt-sm-0 pt-4">
                                 <div style="margin-right:0px">
                                    <label class="">
                                       <a class="btn btn-outline-secondary d-flex justify-content-center align-items-center" style="gap: 5px; width: auto; min-width: auto;" href="{{ route('modify.trainer.record') }}"><span class="d-flex justify-content-center align-items-center"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                             </svg></span>Edit</a>
                                    </label>
                                 </div>
                              </div>
                           </div>
                        </div>


                        <div class="col-12">
                           <div class="form-row">
                              <div class="col-12 col-md-10 col-lg-10 col-xl">
                                 <div id="form-fields" class="form-row">

                                    <div class="col-12 col-sm-6 col-md-3 mb-3" id="by-trainer">
                                       <label for="Class">By Trainer</label><br>
                                       <select class="form-control mx-0 w-100" name="trainer_id" id="trainer_id">
                                          <option value="">All Trainer</option>
                                          @foreach($trainerList as $trainer)
                                          <option value="{{ $trainer->trainer_id }}">{{ $trainer->name }}</option>
                                          @endforeach
                                       </select>
                                    </div>
                                    <div class="col-6 col-sm-6 col-md-3 mb-3" id="from-date">
                                       <label for="skillarea">From Date</label><br>
                                       <input type="date" class="form-control mx-0 w-100" value="" id="from_date_id" name="from_date">
                                    </div>
                                    <div class="col-6 col-sm-6 col-md-3 mb-3" id="last-date">
                                       <label for="sports">To Date</label><br>
                                       <input type="date" class="form-control selctopt" value="" id="to_date_id" name="to_date">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-3 mb-3" id="by-class">
                                       <label for="Period">By Class</label><br>
                                       <select class="form-control mx-0 w-100" name="custom_class_id" id="custom_class_id">
                                          <option value="">All Class</option>
                                          @foreach($classList as $class)
                                          <option value="{{ $class->class_id }}-{{ $class->id }} ">{{ $class->name }}-{{ $class->section }} </option>
                                          @endforeach
                                       </select>
                                    </div>

                                 </div>
                              </div>
                              <div class="col-12 col-sm-12 col-md-2 col-lg-2 col-xl-auto pt-0">
                                 <button type="submit" name="filldata" style="min-width: 100%;" id="activity_fillter" value="filldatasubmit"
                                    class="btn btn-primary d-block w-100 px-4 submit_btn"><i class="fa fa-filter" aria-hidden="true"></i> View
                                 </button>
                                 <!-- <a  class="btn btn-primary mt-1" href="">Reset</a>  -->
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <!---->
         </form>
         <p id="load_more_button"></p>

         <div class="container-fluid p-0">
            <div class="filter-bx1 mt-4 mb-5 pb-5">
               <div class="responsive" id="record_table" style="overflow: hidden;">
                  <table id="reporttable" class="table table-sm table-striped tbl-style" calspan="0" rowspan="0">
                     <thead>
                        <tr>
                           <th scope="col">Trainer Name</th>
                           <th scope="col" width="8%">Date</th>
                           <th scope="col">Period</th>
                           <th scope="col">Class</th>
                           <th scope="col">Skill Area </th>
                           <th scope="col">Skill / Sports </th>
                           <th scope="col">Technique </th>
                           <th scope="col">Activity </th>
                           <th scope="col">Present</th>
                           <th scope="col">Absent</th>
                        </tr>
                     </thead>
                     <tbody> </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>


<!-- The Activity Modal -->
<div class="modal" id="activityDetailId">
   <div class="modal-dialog modal-lg modal-xl modal-dialog-scrollable">
      <div class="modal-content">

         <!-- Modal Header -->
         <div class="modal-header">
            <!-- <h2>Activity Info</h2> -->
            <h3 class="modal-title" id="model-title-id"></h3>
         </div>

         <button type="button" class="close" data-dismiss="modal">×</button>
         <div id="modal-image-id"></div>

         <!-- Modal body -->
         <div class="modal-body pt-0 pb-4 px-4 mt-3">

            <div class="activity-details mb-4">
               <div class="act__video">
                  <iframe id="youtubeurl_id" src="https://www.youtube.com/embed/QUTYxwTsbiM?si=KHp-2Z1yYZFHCzJS" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
               </div>
               <div class="img-act">
                  <img id="modal-image" src="" alt="Activity Image" class="img-fluid mb-3">
               </div>
               <div class="f-row">
                  <p id="is-activity-happend"></p>
                  <div class="std-info mb-4">

                     <p>Class: <span id="model-cls-sec-id"></span></p>
                     <p>Skill Area: <span id="model-skill-area-id">Skill Area</span></p>
                     <p>Skill/ Sports: <span id="model-sports-id">Sports</span></p>
                     <p>Techniques: <span id="model-technique-id">Sudden Change of direction</span></p>
                  </div>

                  <div class="break-line pt-3 pb-2 my-3" id="learning_outcomes_parent_id">
                     <h4>Learning Outcomes</h4>
                     <p id="learning_outcomes_id" class="l-cum"></p>
                  </div>
               </div>

            </div>
            <div class="description break-line" id="model-description-parent_id">
               <h4>Description</h4>
               <p id="model-description-id" class="des-txt"></p>
            </div>

            <div class="break-line pt-3 pb-2 my-3" id="change_it_parent_id">
               <h4>Variation</h4>
               <p id="change_it_id"></p>
            </div>

            <div class="break-line pt-3 pb-2 my-3" id="coaching_parent_id">
               <h4>Coaching/Teaching Tips</h4>
               <p id="coaching_id"></p>
            </div>

            <div class="break-line pt-3 pb-2 my-3" id="equipment_parent_id">
               <h4>Equipment</h4>
               <p id="equipment_id"></p>
            </div>


         </div>


      </div>
   </div>
</div>
<!-- End The Model -->



<script>
   /**
    * 06-08-2024
    * Load and manipulate Activites records into datatable.
    * */
   $(document).ready(function() {

      loadDataTable('bytrainer');

      function loadDataTable(condition) {

         if ($.fn.DataTable.isDataTable('#reporttable')) {
            $('#reporttable').DataTable().destroy();
         }
         $('#reporttable').empty();
         var columnsConfig;
         var headersHtml = '';

         if (condition === 'bytrainer') {
            columnsConfig = [{
                  data: 'name',
                  name: 'name'
               },
               {
                  data: 'date',
                  name: 'date'
               },
               {
                  data: 'period',
                  name: 'period'
               },
               {
                  data: 'classandsec',
                  name: 'classandsec'
               },
               {
                  data: 'skillareas',
                  name: 'skillareas'
               },
               {
                  data: 'sports',
                  name: 'sports'
               },
               {
                  data: 'techniques',
                  name: 'techniques'
               },
               {
                  data: 'title',
                  name: 'title'
               },
               {
                  data: 'present_count',
                  name: 'present_count'
               },
               {
                  data: 'absent_count',
                  name: 'absent_count'
               }
            ];

            headersHtml = `
                <tr>
                    <th width="12%">Name</th>
                    <th width="8%">Date</th>
                    <th width="5%">Period</th>
                    <th width="11%">Class</th>
                    <th>Skill Area</th>
                    <th>Sports</th>
                    <th>Techniques</th>
                    <th>Title</th>
                    <th width="3%">Present</th>
                    <th width="3%">Absent</th>
                </tr>
            `;

         } else {

            columnsConfig = [{
                  data: 'classandsec',
                  name: 'classandsec'
               },
               {
                  data: 'date',
                  name: 'date'
               },
               {
                  data: 'period',
                  name: 'period'
               },
               {
                  data: 'skillareas',
                  name: 'skillareas'
               },
               {
                  data: 'sports',
                  name: 'sports'
               },
               {
                  data: 'techniques',
                  name: 'techniques'
               },
               {
                  data: 'title',
                  name: 'title'
               },
               {
                  data: 'name',
                  name: 'name'
               },
               {
                  data: 'present_count',
                  name: 'present_count'
               },
               {
                  data: 'absent_count',
                  name: 'absent_count'
               }
            ];

            headersHtml = `
                <tr>
                    <th width="11%">Class</th>
                    <th width="8%">Date</th>
                    <th width="3%">Period</th>
                    <th>Skill Area</th>
                    <th>Sports</th>
                    <th>Techniques</th>
                    <th>Title</th>
                    <th width="12%">Name</th>
                    <th width="3%">Present</th>
                    <th width="3%">Absent</th>
                </tr>
            `;
         }


         $('#reporttable').append(`
            <thead>
                ${headersHtml}
            </thead>
            <tbody>
                <!-- Data rows will be filled by DataTables -->
            </tbody>
        `);

         var route = "{{ route('view-trainer') }}";
         var method = 'GET';
         loadData(route, method, serializedData = '', columnsConfig);
      }

      $("input[name='options_bytype']").change(function() {
         loadDataTable($(this).val());
      });
   });


   /**
    * 06-08-2024
    * Sorting Trainer and Class
    * */
   $(document).ready(function() {
      reorderFields(["by-trainer", "from-date", "last-date", "by-class"]);
      $("input[name='options_bytype']").change(function() {
         var selectedOption = $(this).val();

         if ($(this).val() === "bytrainer") {
            reorderFields(["by-trainer", "from-date", "last-date", "by-class"]);
         } else if ($(this).val() === "byclass") {
            reorderFields(["by-class", "from-date", "last-date", "by-trainer"]);
         }
      });

      function reorderFields(order) {
         var formFields = $("#form-fields");
         var fields = order.map(function(id) {
            return $("#" + id).detach();
         });

         fields.forEach(function(field) {
            formFields.append(field);
         });
      }
   });


   /**
    * 06-08-2024
    * Filter Activities Record using ajax and maniplate the response in datatable.
    * */
   $(document).on('submit', 'form#reportform', function(e) {
      e.preventDefault();

      var $table = $('#reporttable');
      if ($.fn.DataTable.isDataTable($table)) {
         $table.DataTable().destroy();
      }

      var form = $(this)[0];
      var formData = new FormData(form);

      var serializedData = [];
      formData.forEach(function(value, key) {
         serializedData.push(encodeURIComponent(key) + '=' + encodeURIComponent(value));
      });
      serializedData = serializedData.join('&');

      var condition = $("input[name=options_bytype]:checked").val()
      var columnsConfig;

      if (condition === 'bytrainer') {
         columnsConfig = [{
               data: 'name',
               name: 'name'
            },
            {
               data: 'date',
               name: 'date'
            },
            {
               data: 'period',
               name: 'period'
            },
            {
               data: 'classandsec',
               name: 'classandsec'
            },
            {
               data: 'skillareas',
               name: 'skillareas'
            },
            {
               data: 'sports',
               name: 'sports'
            },
            {
               data: 'techniques',
               name: 'techniques'
            },
            {
               data: 'title',
               name: 'title'
            },
            {
               data: 'present_count',
               name: 'present_count'
            },
            {
               data: 'absent_count',
               name: 'absent_count'
            }
         ];

      } else {
         columnsConfig = [{
               data: 'classandsec',
               name: 'classandsec'
            },
            {
               data: 'date',
               name: 'date'
            },
            {
               data: 'period',
               name: 'period'
            },
            {
               data: 'skillareas',
               name: 'skillareas'
            },
            {
               data: 'sports',
               name: 'sports'
            },
            {
               data: 'techniques',
               name: 'techniques'
            },
            {
               data: 'title',
               name: 'title'
            },
            {
               data: 'name',
               name: 'name'
            },
            {
               data: 'present_count',
               name: 'present_count'
            },
            {
               data: 'absent_count',
               name: 'absent_count'
            }
         ];
      }

      var route = "{{ route('getTrainerReport') }}";
      var method = "POST";
      loadData(route, method, serializedData, columnsConfig);
   });


   /**
    * 06-08-2024
    * Loading Datatable.
    * */
   function loadData(route, method, serializedData = '', columnsConfig) {

      $('#reporttable').DataTable({

         "dom": `<"top"lf><"filter-right"B>rt<"bottom"ip><"clear">`,
         "lengthChange": true,
         "lengthMenu": [
            [20, 40, 60, 80, 100, -1],
            [20, 40, 60, 80, 100, 'All']
         ],
         "pageLength": 100,
         "info": true,
         "responsive": true,
         "processing": true,
         "serverSide": false,
         "order": [],
         "ajax": {
            "headers": {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            "type": method,
            "url": route,
            "data": function(d) {
               return serializedData;
            },
            "dataType": 'json',
            "dataSrc": "data",
            "error": function(xhr, error, thrown) {
               console.error('Error fetching data:', error);
            }
         },

         "columns": columnsConfig,
         "buttons": [{
            extend: 'collection',
            text: 'Export',
            className: 'exportButton',
            buttons: [{
                  extend: 'excelHtml5',
                  text: 'Excel',
                  customize: function(xlsx) {
                     var sheet = xlsx.xl.worksheets['sheet1.xml'];

                     $(sheet).find('row c[r^="D"]').each(function() {
                        var cell = $(this);
                        var value = cell.text();
                        if (value) {
                           var date = new Date(value);
                           if (!isNaN(date.getTime())) {
                              var day = ('0' + date.getDate()).slice(-2);
                              var month = ('0' + (date.getMonth() + 1)).slice(-2);
                              var year = date.getFullYear();
                              cell.text(day + '-' + month + '-' + year);
                           }
                        }
                     });
                  }
               },


               {
                  extend: 'pdfHtml5',
                  text: 'PDF',
                  customize: function(doc) {
                     doc.content.forEach(function(item) {
                        if (item.table && item.table.body) {
                           item.table.body.forEach(function(row) {
                              row.forEach(function(cell, index) {
                                 if (cell.text && index === 2) {
                                    var date = new Date(cell.text);
                                    if (!isNaN(date.getTime())) {
                                       var day = ('0' + date.getDate()).slice(-2);
                                       var month = ('0' + (date.getMonth() + 1)).slice(-2);
                                       var year = date.getFullYear();
                                       cell.text = day + '-' + month + '-' + year;
                                    }
                                 }
                              });
                           });
                        }
                     });
                  }
               }
            ],
         }]
      });
   }


   function modelContent(ActivityId, skillarea, sports, technique, ClassSectionName, TickMark = '') {


      jQuery.ajax({
         url: "{{ route('lession.plan.details') }}",
         data: {
            "activiy_id": ActivityId,
            "_token": "{{ csrf_token() }}"
         },
         type: 'GET',
         success: function(response) {

            $.each(response.activityDetail, function(key, val) {

               var word = "wp-content";
               var img = imagepath = '';
               var mystring = val.image;


               if (mystring.indexOf(word) !== -1) {
                  imagepath = val.image;
               } else if (val.image == '') {
                  imagepath = 'public/change-activities/default_activity_img.svg';
               } else {
                  imagepath = 'public/uploads/' + val.image;
               }

               $("#modal-image").attr("src", imagepath);
               $("#model-title-id").html(val.title.toUpperCase());

               if (val.description && val.description.length > 0) {
                  $("#model-description-parent_id").show();
                  $("#model-description-id").html(val.description);
               } else {
                  $("#model-description-parent_id").hide();
               }

               if (val.learning_outcomes && val.learning_outcomes.length > 0) {
                  $("#learning_outcomes_parent_id").show();
                  $("#learning_outcomes_id").html(val.learning_outcomes);
               } else {
                  $("#learning_outcomes_parent_id").hide();
               }

               if (val.change_it && val.change_it.length > 0) {
                  $("#change_it_parent_id").show();
                  $("#change_it_id").html(val.change_it);
               } else {
                  $("#change_it_parent_id").hide();
               }


               if (val.coaching && val.coaching.length > 0) {
                  $("#coaching_parent_id").show();
                  $("#coaching_id").html(val.coaching);
               } else {
                  $("#coaching_parent_id").hide();
               }

               if (val.equipment && val.equipment.length > 0) {
                  $("#equipment_parent_id").show();
                  $("#equipment_id").html(val.equipment);
               } else {
                  $("#equipment_parent_id").hide();
               }





               $("#youtubeurl_id").attr("src", val.url);
               $('#activityDetailId').modal('show');
               $('#model-skill-area-id').html(skillarea);
               $('#model-sports-id').html(sports);
               $('#model-technique-id').html(technique);
               $('#model-cls-sec-id').html(ClassSectionName);
               if (TickMark == 'active-completed')
                  $('#is-activity-happend').addClass(TickMark).html('<span>Completed</span>');
               else
                  $('#is-activity-happend').addClass('').text('');





               var src = $("#youtubeurl_id").attr('src');
               if (src && src.length > 0) {
                  $(".img-act").hide();
                  $(".act__video").show();
               } else {
                  $(".act__video").hide();
                  $(".img-act").show();
               }

            });


         }
      });

   }
</script>
@endsection