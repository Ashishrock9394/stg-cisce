@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
@section('content')

<style type="text/css">
/*   css code here*/
.tbl-style {
   font-size: 14px;
   background-color: #fff;
   overflow: hidden;
}
.table tbody+tbody {
    border-top: 0px;
}
.table td, .table th {
    border-top: 0px;
    padding: 0.5rem;
}
.table thead th {
    border-bottom: 0px;
    background-color: #333;
    color:#fff;
}
.btn-secondary {
    background-color: #e6e6f9;
    border-color: #292775;
    color: #292775;
}
.btn-secondary:hover {
   background-color: #cdcde7;
   color: #292775;
}
.btn-secondary:not(:disabled):not(.disabled).active, .btn-secondary:not(:disabled):not(.disabled):active, .show>.btn-secondary.dropdown-toggle {
    background-color: #292775;
    border-color: #292775;
}
.responsive-tbl {
   overflow-x: auto;
   border: 1px solid #333;
   margin: 30px 0;
   border-radius: 6px;
}
.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgb(237 239 244 / 80%);
}

</style>


<div class="pg-yallow-color">
   <div class="container">
      <div class="navbar-expand-lg">
         <div id="fillter" class="" role="group" aria-label="Basic example">
         </div>
      </div>
   </div>
</div>
<div class="">
   <div class="all-chaptr-cards">
      <!-- Success message -->
      <form class="container" method="POST" name="view-trainer-report" id="reportform"  action="javascript(0);">
         <div class="row">
            <div class="col">
               <div class="heading-rw mb-4">
                  <h1>View Trainers</h1>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-12">
               <div id="activity_from_div" class="sports-filtr overlay">
                  <div class="form-row">

                     <div class="col-12 mb-4">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons" id="chksubmit_button">
                           <label class="btn btn-secondary ">
                           <input type="radio" name="options_bytype" value="bytrainer" checked> By Trainer
                           </label>
                           <label class="btn btn-secondary active">
                           <input type="radio" name="options_bytype" value="byclass" > By Class
                           </label>                        
                        </div>
                     </div>
                    
                     <div class="form-group col-12 col-sm-6 col-md-3 mb-3">
                        <label for="Class">By Trainer</label><br>
                        <select class="form-control mx-0 w-100" name="trainer_id" id="trainer_id" >
                           <option value="">All Trainer</option>
                           @foreach($schoolData as $key => $trainer)
                              @foreach($trainer['getTrainers'] as $trainer)
                              <option value="{{ $trainer['trainer']['id'] }}">{{ $trainer['trainer']['name'] }}</option>
                              @endforeach
                           @endforeach
                        </select>
                     </div>

                     <div class="form-group col-12 col-sm-6 col-md-3 mb-3">
                        <label for="Period">By Class</label><br>
                        <select class="form-control mx-0 w-100" name="custom_class_id" id="custom_class_id">
                           <option value="">All Class</option>

                           @foreach($schoolData as $key => $classes)
                              @foreach($classes['getClasses'] as $class)
                              <option value="{{ $class['class_id'] }}-{{ $class['id'] }} ">{{ $class['class']['name'] }}-{{ $class['section'] }} </option>
                              @endforeach
                           @endforeach
                        </select>
                     </div>
                     <div class="form-group col-6 col-sm-6 col-md-2 mb-3">
                        <label for="skillarea">From Date</label><br>
                        <input type="date" class="form-control mx-0 w-100" value="" required id="from_date_id" name="from_date">
                     </div>
                     <div class="form-group col-6 col-sm-6 col-md-2 mb-3">
                        <label for="sports">To Date</label><br>
                        <input type="date" class="form-control selctopt" value="" required id="to_date_id" name="to_date">
                     </div>
                     <div class="form-group col-12 col-sm-12 col-md-2 mt-4 pt-0">
                        <button type="submit" name="filldata" id="activity_fillter" value="filldatasubmit"
                           class="btn btn-primary d-block w-100 mt-2 submit_btn"><i class="fa fa-filter" aria-hidden="true"></i> View Report
                        </button>
                        <!-- <a  class="btn btn-primary mt-1" href="">Reset</a>  -->
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!---->
      </form>


       @foreach($schoolData as $key => $trainerlist)
               @foreach($trainerlist['getTrainers'] as $trainer)

                     @php 
                     echo"<pre>";print_r($trainer['trainer']['name']); 
                       

                        @endphp
                @endforeach
            @endforeach


      <div class="container-fluid">
         <div class="responsive-tbl mb-5">
         <table class="table table-sm table-striped tbl-style" calspan="0" rowspan="0">
            <thead>
               <tr>
                  <th scope="col">Trainer Name</th>
                  <th scope="col">Class </th>                  
                  <th scope="col">Section </th>
                  <th scope="col">Period</th>
                  <th scope="col">Date</th>
                  <th scope="col">Skill Area </th>
                  <th scope="col">Skill / Sports </th>
                  <th scope="col">Technique </th>
                  <th scope="col">Activity </th>                  
                  <th scope="col">Present</th>
                  <th scope="col">Absent</th>
               </tr>
            </thead>

           


            <tbody id="reportdetails"> </tbody>


          <!--      @foreach($schoolData as $key => $trainerlist)
                  @foreach($trainerlist['getTrainers'] as $trainer)

                  @endforeach
               @endforeach -->
               
               {{--
                @foreach($ReportsData as $trainerName => $reports)
                @php
                    $printedPeriods = []; // Array to store periods that have already been printed for this trainer
                @endphp

                @foreach($reports as $index => $report)
                    @php
                        $periodIdentifier = $trainerName . '-' . $report['period'] .$report['class_name']. $report['class_section'].$report['skillArea'].$report['sport'].$report['technique'].$report['activity'];
                    @endphp

                    @unless(in_array($periodIdentifier, $printedPeriods))
                        <tr>
                            <td>{{ $report['trainer_name'] }}</td>
                            <td>{{ $report['class_name'] }}</td>
                            <td>{{ $report['class_section'] }}</td>
                            <td>{{ $report['period'] }}</td>
                            <td>{{ $report['date'] }}</td>
                            <td>{{ $report['skillArea'] }}</td>
                            <td>{{ $report['sport'] }}</td>
                            <td>{{ $report['technique'] }}</td>
                            <td>{{ $report['activity'] }}</td>   
                            <td>{{ $report['present'] }}</td>                   
                            <td>{{ $report['absent'] }}</td> 
                        </tr>
                        
                        @php $printedPeriods[] = $periodIdentifier; @endphp 
                    @endunless
                @endforeach
            @endforeach
               --}}

                
                {{--
                @foreach($ReportsData as $data)
                <tr>
                  <th scope="row">{{ $data->date }}</th>
                  <td>{{ $data->trainer_name }}</td> 
                  <td>{{ $data->period }}</td>
                  <td>{{ $data->class_name }}</td>
                  <td>{{ $data->class_section }}</td>
                  <td>{{ $data->skillArea }}</td>
                  <td>{{ $data->sport }}</td>
                  <td>{{ $data->technique }}</td>                  
                  <td>{{ $data->activity }}</td>
                  <td> {{ $present_students }}</td>
                  <td>{{ $absent_students }}</td>
                </tr>   
                @endforeach
                    --}}
         </table>
         </div>
      </div>
   </div>
</div>
</div>
<!-- DataTables -->
<script>
    $(document).on('submit', '#reportform', function (e) {
       e.preventDefault();
       $('#reportdetails').html(``);
   
       var form = $('#reportform')[0];
       var formData = new FormData(form);
   
       $.ajaxSetup({
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           }
       });
   
        $.ajax({
           type: "POST",
           url: "{{ route('getReport') }}",
           data: formData, //$("form#reportform").serialize(),            
           processData: false,
           contentType: false,
           dataType: 'json',
   
            success: function(response) {
               var data = response.data; // Access the 'data' array from the JSON response
               $('#reportdetails').html(``);
               var tableRows = '';
               var count = 0;
               data.forEach(function(item) {
                    tableRows += '<tr>';
                  tableRows += '<td>' + item.date + '</td>';
                  tableRows += '<td>' + item.trainer.name + '</td>';                   
                  tableRows += '<td>' + item.period + '</td>';
                  tableRows += '<td>' + item.class_name + '</td>';
                  tableRows += '<td>' + item.custom_class.section + '</td>';
                  tableRows += '<td>' + (item.skill_area.name ? item.skill_area.name : 'N/A') + '</td>'; 
                  tableRows += '<td>' + (item.sport.name ? item.sport.name : 'N/A') + '</td>'; 
                  tableRows += '<td>' + (item.technique.name ? item.technique.name : 'N/A') + '</td>'; 
                  tableRows += '<td>' + (item.activity.title ? item.activity.title : 'N/A') + '</td>'; 
                  tableRows += '</tr>';
               });
   
               $('#reportdetails').html(tableRows); 
            },
             
            error: function(xhr) {
               // Handle the error response
               console.log(xhr.responseText);
            }
       });      
    });
    


    $(document).ready(function() {

        $("input[name='options_bytype']").change(function() {
            var selectedOption = $(this).val();

            console.log('selected_option   : ' + selectedOption)

        });
    });


   
</script>
@endsection