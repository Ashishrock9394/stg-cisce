@extends('layouts.filldart-app')
@section('title', 'Goforfit | ' . $title)
@section('content')
<!-- <div class="pg-yallow-color">
    <div class="container">
        <div class="navbar-expand-lg">
            <div id="fillter" class="" role="group" aria-label="Basic example">
            </div>
        </div>
    </div>
</div> -->



<!--<div id="loaderId">
<div class="common-loader">
Loading...</div>
</div>-->

    <div class="all-chaptr-cards">
        @if($errors->any())
        <div class="alert alert-info">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-info">
            {{ session('success') }}
        </div>
        @endif


        <!-- Success message -->
        <div class="container">
            <div class="t-mrg2">
                <div class="filter-bx">
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-9 col-lg-10">
                            
                            <a href="{{ route('filldart.dashboard') }}" class="back-button">
                                <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
                                    </svg></span>
                                <!-- <span class="back-txt">Back</span> -->
                            </a>
                            <div class="heading-rw mt-3 mt-lg-1 mb-0 pt-1">
                                <h1>{{$title}}</h1>
                            </div>
                        
                        </div>

						<div class="col-12 col-sm-6 col-md-3 col-lg-2 pt-2">
							<!-- <label for="Period"></label><br> -->

							<input type="hidden" name="school_id" id="school_id" value="{{ $schoolId }}">

							<select class="form-control mx-0 w-100" name="by_class_id" id="by_class_id"  onchange="getStudentDetail(0, this.value, this.options[this.selectedIndex].getAttribute('data-class-section'))">
								<option value="">Select Class</option>

								@foreach($classes as $key => $val)
								 <option value="{{ $val->id }}" data-class-section="{{ $val->classname.'-'.$val->section }}">{{ $val->classname.'-'.$val->section }}</option>
								@endforeach

							</select>
						</div>
                        <div class="col-12">
						
						
						
                        <p class="msg_info mt-5">Please select a class for Activities</p>
                        </div>
                        <div class="col-12">
                   
                   <form method="POST" class="row mt-4 pt-2" name="view-trainer-report" id="" action="{{ route('student.map.students') }}">
                   {{method_field('post')}}
                   @csrf
                       <div class="col-12">

                           <div id="activity_from_div" class="sports-filtr overlay activity_cards activity-info">
                               <div class="row">

                                   <div class="col-12 col-md-12">

                                       <div class="w-100 studs-list mb-4">
                                       <!-- <h3 class="list-heading mb-4"></h3> -->
                                           <ul id="student_id" class="grid-list grid-card">


                                           </ul>

                                       </div>

                                   </div>
                                   
                               </div>
                           </div>
                       </div>

                   </form>
                   </div>

                    </div>

                    
                </div>
                <!-- <div class="row">
                    
                </div> -->
            </div>
        </div>

    </div>

</div>



  <!-- The Modal -->
  <div class="modal" id="activityDetailId">
  
    <div class="modal-dialog modal-lg modal-xl modal-dialog-scrollable">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
            <!-- <h2>Activity Info</h2> -->
            <h3 class="modal-title" id="model-title-id"></h3>
        </div>
        
        <button type="button" class="close btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
		 
		<div class="break-line pt-3 pb-2 my-3" id="change_it_parent_id"><h4>Variation</h4>	<p id="change_it_id"></p></div>
		
		<div class="break-line pt-3 pb-2 my-3" id="coaching_parent_id"><h4>Coaching/Teaching Tips</h4>	<p id="coaching_id"></p></div>
		
		<div class="break-line pt-3 pb-2 my-3" id="equipment_parent_id"><h4>Equipment</h4>	<p id="equipment_id"></p></div>

        
        </div>
		
        
        <!-- Modal footer -->
        <!-- <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
        </div>
         -->
      </div>
    </div>
  </div>
  <!-- End The Model -->

 



<script>
    function getStudentDetail(i, val, clsSecName) {
        
		
		//jQuery('#loaderId').show();
		//$("#loaderId").removeClass("common-loader");
		
		 //$("#loaderId").removeClass("common-loader");
		 
        jQuery('#student_id').empty();
		jQuery('.msg_info').show();
        var school_id = jQuery('#school_id').val();
        jQuery.ajax({
            url: "{{ route('fetch.students.according.to.class') }}",
            data: {
                "custom_class_id": val,
                "school_id": school_id,
                "_token": "{{ csrf_token() }}"
            },
            type: 'GET',
            success: function(response) 
            {
				
				//jQuery('#loaderId').hide();
				// $("#loaderId").removeClass("common-loader");
				/*let lengths = response.activitieslist.map(function(item) 
				{
					return item.sportsname.toUpperCase();
				});*/
				
                //console.log(lengths);
				
				console.log('--------------new resource of ----');
			
                //console.log(response.ActivityAlreadyDone);
				 console.log(response.activitieslist);
                $.each(response.activitieslist, function(key, val) 
                {
                        
						//console.log(ActivityAlreadyDone);
						jQuery('.msg_info').hide();
						var word = "wp-content"; 
						var img = imagepath ='';
						var mystring = val.image;
						
						
						if(mystring.indexOf(word) !== -1) 
						{ 
						    imagepath = val.image;
						}
						else if(val.image == '')
						{
					    	imagepath = 'public/change-activities/default_activity_img.svg';
							
						}else
						{
						    imagepath = 'public/uploads/'+val.image;
						}
						
						
						
						
						
						// Check if activity is already done by looping through ActivityAlreadyDone
						var isDone = false; // variable to hold the state

						$.each(response.ActivityAlreadyDone, function(doneKey, doneActivity) {
						if (
							doneActivity.skill_area_id === val.skillId &&
							doneActivity.skill_sports_id === val.sportsID &&
							doneActivity.technique_id === val.techniqueId &&
							doneActivity.activity_id === val.activity_id
						) {
						isDone = true; // match found, mark as done
						return false; // exit the loop as we found a match
						}
						});

						// Now you can use the isDone variable for your logic
						var tickMark = isDone ?'active-completed':'';

                        
                        
                       /* var content =  '<div class="card" ><div class="activity-img" onclick="modelContent('+val.activity_id+',\"'+val.skillname+'\",\"'+val.sportsname+'\",\"'+val.techniquename+'\")"><div class="img_overlay"></div><img class="card-img-top" src="'+imagepath+'" alt="Card image cap"></div><div class="card-body"><h5 class="card-title">' + val.title + '</h5><p class="d-flex justify-content-between"><span><strong>SkillArea</strong></span><span>'+ val.skillname +'</span></p><p class="d-flex justify-content-between"><span><strong>Sports</strong></span> <span>'+ val.sportsname + '</span></p><p class="d-flex justify-content-between"><span><strong>Technique</strong></span><span>'+val.techniquename+'</span></p></div></div>';*/
					   
					   
						var content =  '<div class="card '+ tickMark+'" >' +
					   '<div class="activity-img" onclick="modelContent(' + val.activity_id + ', \'' + val.skillname + '\', \'' + val.sportsname + '\', \'' + val.techniquename + '\' , \'' + clsSecName + '\', \'' + tickMark + '\')">' +
					   '<div class="img_overlay"></div>' +
					   '<img class="card-img-top" src="' + imagepath + '" alt="' + val.title.toUpperCase()  +'">' +
					   '</div>' +
					   '<div class="card-body">' +
					   '<h5 class="card-title">' + val.title  +'</h5>' +
					   '<p class="d-flex justify-content-between"><span><strong>SkillArea</strong></span><span>' + val.skillname + '</span></p>' +
					   '<p class="d-flex justify-content-between"><span><strong>Sports</strong></span> <span>' + val.sportsname + '</span></p>' +
					   '<p class="d-flex justify-content-between"><span><strong>Technique</strong></span><span>' + val.techniquename + '</span></p>' +
					   '</div>' +
					   '</div>';

                        

                        jQuery('#student_id').append(content);
                        /*$("#model-title-id").html(val.title);   
                        $("#model-description-id").html(val.description);
                        $("#learning_outcomes_id").html(val.learning_outcomes);
                        $("#change_it_id").html(val.change_it);
                        $("#coaching_id").html(val.coaching);
                        $("#equipment_id").html(val.equipment);*/




                });


             

            }
        });
    }
	
  function modelContent(ActivityId, skillarea, sports, technique, ClassSectionName, TickMark) 
  {

  	  
	    //console.log('activity-id-'+ActivityId+','+'skillarea-'+skillarea+','+'sports-'+sports+','+'techniue-'+technique);
		//alert(item);
        jQuery.ajax({
            url: "{{ route('lession.plan.details') }}",
            data: {
                "activiy_id": ActivityId,
                "_token": "{{ csrf_token() }}"
            },
            type: 'GET',
            success: function(response) 
            {
				
			
                $.each(response.activityDetail, function(key, val) 
                {
						//console.log(val);
						//alert(val.url);
						var word = "wp-content"; 
						var img = imagepath ='';
						var mystring = val.image;
						
						
						if(mystring.indexOf(word) !== -1) 
						{ 
						    imagepath = val.image;
						}
						else if(val.image == '')
						{
					    	imagepath = 'public/change-activities/default_activity_img.svg';	
						}else
						{
						    imagepath = 'public/uploads/'+val.image;
						}

						$("#modal-image").attr("src", imagepath);
                        //jQuery('#student_id').append(content);
                        $("#model-title-id").html(val.title.toUpperCase());   
						
						if(val.description && val.description.length > 0)
						{
						  $("#model-description-parent_id").show();	
                          $("#model-description-id").html(val.description);
                        }else
						{
						 $("#model-description-parent_id").hide();
						}
						
						
					    if(val.learning_outcomes && val.learning_outcomes.length > 0)
						{	
						  $("#learning_outcomes_parent_id").show();	
                          $("#learning_outcomes_id").html(val.learning_outcomes);
						}else
						{
							$("#learning_outcomes_parent_id").hide();
						}
						
						if(val.change_it && val.change_it.length > 0)
						{
						 $("#change_it_parent_id").show();
                         $("#change_it_id").html(val.change_it);	
						}
						else
						{
						  $("#change_it_parent_id").hide();
						}
						
						
						if (val.coaching && val.coaching.length > 0) 
						{
						 $("#coaching_parent_id").show();
                         $("#coaching_id").html(val.coaching);
						}
						else
						{
						 $("#coaching_parent_id").hide();
						}
						
						if (val.equipment && val.equipment.length > 0) 
						{
						 $("#equipment_parent_id").show();
                         $("#equipment_id").html(val.equipment);
						}
						else
						{
						 $("#equipment_parent_id").hide();
						}
						

                        
						
						
                        $("#youtubeurl_id").attr("src",val.url);
						$('#activityDetailId').modal('show');
						$('#model-skill-area-id').html(skillarea);
						$('#model-sports-id').html(sports);
						$('#model-technique-id').html(technique);
						$('#model-cls-sec-id').html(ClassSectionName);
						if(TickMark == 'active-completed')
						 $('#is-activity-happend').addClass(TickMark).html('<span>Completed</span>');
					    else 
						 $('#is-activity-happend').addClass('').text('');
					
						
						
						
						
						var src = $("#youtubeurl_id").attr('src');
						if (src && src.length > 0) 
						{
						 $(".img-act").hide();
						 $(".act__video").show();
						}else
						{
						 $(".act__video").hide();
						 $(".img-act").show();
						}
						
                });


            }
        });
	  
  }
</script>


<style>
#loaderId {
	opacity: 1;
    height: 100vh;
    width: 100vw;
    position: absolute;
    inset: 0;
    z-index: 100;
    background: rgb(0 0 0 / 15%);
    display: flex;
    align-items: center;
    justify-content: center;
	
}
.common-loader {
  color: #000;
  /*position: absolute;
  inset:0;
  display: inline-block;
  margin:auto;
  font-family: Arial, Helvetica, sans-serif;*/
  font-size: 48px;
  /*letter-spacing: 4px;
  box-sizing: border-box;*/
}

/*
.common-loader::before {
  content: '';  
  position: absolute;
  right: 70px;
  bottom: 10px;
  height: 28px;
  width: 5.15px;
  background: currentColor;
  box-sizing: border-box;
  animation: animloader1 1s linear infinite alternate;
}
.common-loader::after {
  content: '';  
  width: 10px;
  height: 10px;
  position: absolute;
  left: 125px;
  top: 2px;
  border-radius: 50%;
  background: red;
  box-sizing: border-box;
  animation: animloader 1s linear infinite alternate;
}

@keyframes animloader {
  0% {
    transform: translate(0px, 0px) scaleX(1);
  }
  14% {
    transform: translate(-12px, -16px) scaleX(1.05);
  }
  28% {
    transform: translate(-27px, -28px) scaleX(1.07);
  }
  42% {
    transform: translate(-46px, -35px) scaleX(1.1);
  }
  57% {
    transform: translate(-70px, -37px) scaleX(1.1);
  }
  71% {
    transform: translate(-94px, -32px) scaleX(1.07);
  }
  85% {
    transform: translate(-111px, -22px) scaleX(1.05);
  }
  100% {
    transform: translate(-125px, -9px) scaleX(1);
  }
}

@keyframes animloader1 {
  0% {
    box-shadow: 0 -6px, -122.9px -8px;
  }
  25%, 75% {
    box-shadow: 0 0px, -122.9px -8px;
  }
  100% {
    box-shadow: 0 0px, -122.9px -16px;
  }
}*/

</style>




@endsection