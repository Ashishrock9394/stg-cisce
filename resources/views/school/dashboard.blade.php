@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
@section('content')


<style type="text/css">
p.card-text {
        font-size: 29px;
    text-align: center;
    margin-top: 20px;
}

h5.card-title {
    text-align: center;
    margin-top: 23px;
}

.table thead th {
    border-bottom: 0px;
    background-color: #434386;
    color: #fff;
}

.students_count {
    display: flex;
    justify-content: center;
    column-gap: 15px;
    margin-top: 16px;
}

.students_count p{
   font-weight: 500;
}

</style>

<div class="container all-chaptr-cards mt-5">
   <div class="row">
      <div class="col-12">
         <a href="{{ route('filldart.dashboard') }}"  class="back-button">
            <span class="arrow">
               <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
               </svg>
            </span>
         </a>
         <div class="heading-rw mt-0 mb-2 px-5">
            <h1>{{$title}}</h1>
         </div>
      </div>
   </div>


   <div class="row senior_card mt-4 mb-5">

    
    <div class="col col-md-12 col-lg-12 mb-1" class="getactive"> 

        <table class="table table-bordered">
          <thead>
            <tr>
                <th scope="col">Class</th>
                <th scope="col">Section</th>
                <th scope="col">Students</th>
                <th scope="col">Boys</th>
                <th scope="col">Girls</th>
            </tr>
          </thead>
          <tbody>

            @foreach ($classData as $classKey => $data)
            <tr>
              <th scope="row">{{ $data['class'] }} </th>
              <td>{{ $data['section'] }} </td>
              <td>{{ $data['total_students'] }}</td>        
              <td>{{ $data['total_boys'] }}</td>
              <td>{{ $data['total_girls'] }}</td>
            </tr>
            @endforeach
            
          </tbody>
        </table>
    </div>


      <div class="col col-md-4 col-lg-6 mb-1"> 
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Active Trainers  </h5>

                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th scope="col">Name</th>
                      
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($trainerActivities as $trainerId => $data)
                    <tr>
                      <td>{{ $data['TrainerName'] }}</td>
                    
                    </tr>
                     @endforeach
                  </tbody>
                </table>
              </div>
         </div>   
      </div>
	   

      
        <div class="col col-md-4 col-lg-6 mb-1" class="getactive" style="overflow:auto"> 
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">All Students</h5>
                 <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th scope="col">Total Students</th>
                      <th scope="col" >Boys</th>
                      <th scope="col">Girls</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
						<td>{{ $SchoolData['students']->count() }}</td>
                        <td>{{ $SchoolData['students']->where('gender', 'Male')->count() }}</td>
                        <td>{{ $SchoolData['students']->where('gender', 'Female')->count() }}</td>						
                    </tr>
                  </tbody>
                </table>
              </div>
         </div>   
      </div> 

   </div>
</div>




@endsection