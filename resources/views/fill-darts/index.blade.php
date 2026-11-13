@extends('layouts.filldart-app')
@section('title', 'Goforfit | ' . $title)
@section('content')

<?php $sty1 = 'display:block'; ?>

<!-- Add these in your Blade view inside the <head> tag -->



<div class="container">
    <div class="t-mrg2">
        <div class="container-fluid all-chaptr-cards filter-bx1 frm-info p-0">
            <div class="row">
                <div class="col">
                    <a href="{{ route('filldart.dashboard') }}"  class="back-button">
                        <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
                            </svg>
					    </span>
                        <!-- <span class="back-txt">Back</span> -->
                    </a>
                    <div class="heading-rw mt-0">
                        <h1>{{$title}}</h1>
                    </div>
                </div>
            </div>

            <!-- Success message -->
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif


            @if ($errors->any())
            <div class="alert alert-danger" id="form-error-message">
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif


<div class="row">
			<div class="col-12 mb-4" style="overflow: hidden;">
                    
				<div class="btn-group-toggle" data-toggle="buttons" id="chksubmit_button" style="gap: 10px;">
					<label class="btn btn-secondary flex-fill active">
						<input type="radio" name="options_bytype" id="at_school" value="at_school" checked> Regular Classes
					</label>
					<label class="btn btn-secondary flex-fill">
						<input type="radio" name="options_bytype" id="other_duty" value="other_duty"> Other Duties
					</label>
					<label class="btn btn-secondary flex-fill">
						<input type="radio" name="options_bytype" id="on_leave" value="on_leave"> On Leave
					</label>
					<label class="btn btn-secondary flex-fill">
						<input type="radio" name="options_bytype" id="holiday" value="holiday"> Holiday
					</label>
					<label class="btn btn-secondary flex-fill">
						<input type="radio" name="options_bytype" id="at_office" value="at_office"> At Office
					</label>
				</div>
                
                </div>
			</div>


            <form class="row" method="POST" name="fill_dart_data_submit" id="fill_dart_submit" action="{{ route('fill.dart.data.submit') }}" >
                {{method_field('post')}}
                @csrf


                <div class="col-12">

                    <div id="activity_from_div" class="sports-filtr overlay" style="<?= $sty1 ?>">


                        <?php

                        $sclasses = '<option value="">Class</option>';

                        if (!empty($classes)) {
                            foreach ($classes as $cls) {

                                $sclasses .= '<option value="' . $cls->id . '-' . $cls->class_id . '" ';
                                if (!empty($_GET['sclass'])) {
                                    if ($cls->id == $_GET['sclass']) {
                                        $sclasses .= ' selected';
                                        $sclsname = $cls->name;
                                    }
                                }

                                $sclasses .= ' >' . $cls->classname . '-' . $cls->section . '</option>';
                            }
                        }


                        $askillarea = '<option value="">Skill Area</option>';

                        if (!empty($skillareas)) {
                            foreach ($skillareas as $skillarea) {

                                $kselect = (!empty($_REQUEST['skillarea']) && ($_REQUEST['skillarea'] == $skillarea->id) ? 'selected="selected"' : '');

                                $askillarea .= '<option value="' . $skillarea->id . '" ' . $kselect . '>' . $skillarea->name . '</option>';
                            }
							$askillarea .= '<option value="1000" ' . $kselect . '>Sports for all</option>';
                        }

                        $asportskills = '<option value="">Skill/Sports</option>';

                        if (!empty($sportskills)) {
                            foreach ($sportskills as $sportskill) {
                                $spselect = (!empty($_REQUEST['skillsports']) && ($_REQUEST['skillsports'] == $sportskill->id) ? 'selected="selected"' : '');
                                $asportskills .= '<option value="' . $sportskill->id . '" ' . $spselect . '>' . $sportskill->name . '</option>';
                            }
                        }

                        $atechniques = '<option value="">Technique</option>';

                        if (!empty($techniques)) {
                            foreach ($techniques as $technique) {
                                $tselect = (!empty($_REQUEST['technique']) && ($_REQUEST['technique'] == $technique->id) ? 'selected="selected"' : '');
                                $atechniques .= '<option value="' . $technique->id . '" ' . $tselect . '>' . $technique->name . '</option>';
                            }
                        }


                        $getact = '<option value="">Activity</option>';

                        if (!empty($act)) {
                            foreach ($act as $ac) {
                                $sclasses .= '<option value="' . $ac->id . '" ';
                                $getact .= ' >' . $ac->title . '</option>';
                            }
                        }

                        ?>

                        <!-- <div class="form-group"></div> -->
                        <div class="form-row">
                            <div class="col-12 col-md-3 mb-3">
                                <label for="Date">Date</label><br>
                                <input class="form-control mx-0 w-100" type="date" max="<?php echo date("Y-m-d"); ?>" id="date" name="date">
                            </div>

                            <?php /*   <div class="col-12 col-md-12 mb-3">
                                 <label for="School Name">School Name</label><br>
                                    <select class="form-control mx-0 w-100" name="school_id" id="school_id" onchange="getclasses(0,this.value)">
                                    <option value="">School Name</option>
                                
                                    @foreach ($schools as $skey => $sval)
                                        <option value="{{ $sval->id  }}"> {{ $sval->school_name  }}</option>
                                    @endforeach
                                    </select>

                                </div> */ ?>

                            <input type="hidden" name="school_id" id="school_id" value="{{ $schoolId }}">


                            <div class="col-6 col-md-3 mb-3">
                                <label for="Class">Class</label><br>
                                <select class="form-control mx-0 w-100" name="sclass" id="sclass0" >
                                    <?= $sclasses ?>
                                </select>
                            </div>
                            <input type="hidden" name="custm_cls_id" id="custm_cls_id">

                            <div class="col-6 col-md-3 mb-3">
                                <label for="Period">Period</label><br>
                                <select class="form-control mx-0 w-100" name="period" id="period_id" onchange="getFillDARTskillarea(0,this.value)">
                                    <option value="">Period</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                </select>

                            </div>

                        </div>
                        <div class="form-row">
                            <div class="col-12 col-md-3 mb-3">
                                <label for="skillarea">Skill Area</label><br>
                                <select class="form-control mx-0 w-100" id="skillarea0" name="skillarea" onchange="getskillsports(0,this.value)">
                                    <option value="">Skill Area</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-3 mb-3">
                                <label for="sports">Skill/Sports</label><br>
                                <select class="form-control selctopt" id="skillsports0" name="skillsports" onchange="gettechnique(0,this.value)">
                                    <option value="">Skill/Sports</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-3 mb-3">
                                <label for="technique">Technique</label><br>
                                <select class="form-control selctopt" id="technique0" name="technique" onchange="getactivity(0,this.value)">
                                    <option value="">Technique</option>
                                </select>
                            </div>


                            <div class="col-12 col-md-3 mb-3">
                                <label for="activity">Activity</label><br>
                                <div class="activity-info">
								
                                <a href="#a" id="anchor-id" data-bs-toggle="modal" data-bs-target="#activityDetailId"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                                    </svg>
								</a>
                                    <select class="form-control mx-0 w-100" name="activity" id="activity_id" onchange="getstudents(0,this.value)">
                                        <option value="">Activity</option>
                                    </select>
                                </div>

                            </div>


                        </div>
                    </div>

                </div>


                <div class="col-12">
                    <div class="w-100 studs-list grid-row">
                        <ul id="std_tbl_id" class="grid-list">
                        </ul>
                    </div>
                </div>

            
                <div class="col-2 col-md-6 mt-4">
                <a class="btn btn-link btn-txt-info collapsed pl-4" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree"><span class="d-none d-md-block">Rubric Descriptions</span></a>
                </div>

				<div class="col-10 col-md-6 col-lg-3 ml-auto mt-4">
					<div class="form-group d-flex justify-content-end">
						<button type="reset" name="filldata" id="activity_fillter_cancel" value="filldatacancel" class="btn btn-outline-secondary">Cancel </button>
						<button type="submit" name="filldata" id="activity_fillter_submit" value="filldatasubmit" class="btn btn-primary ml-3">Save</button>
					</div>
				</div>



                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                   <div class="px-3">
                      <table class="table table-bordered mt-4 card-body p-0">
                         <thead>
                            <tr>
                               <th scope="col" width="60px">Level</th>
                               <th scope="col" width="120px">Level Name</th>
                               <th scope="col">Description</th>
                            </tr> 
                         </thead>
                         <tbody>
                            @foreach($levels as $level)
                            <tr>
                               <td>L-{{$level->level_value}} </td>
                               <td> {{$level->level_name}} </td>
                               <td> {{$level->description}} </td>
                            </tr>
                            @endforeach              
                         </tbody>
                      </table>
                   </div>
                </div>


			 </form>
			
			
			<form method="POST" name="other_duty_form" id="other_duty_id" action="{{ route('submit.other.duty') }}" style="display:none">
			 {{method_field('post')}}
                @csrf
				
				    <div class="col-12">

							<div id="activity_from_div" class="row sports-filtr overlay" style="<?= $sty1 ?>">

								<!-- <div class="form-group"></div> -->
								<div class="form-row">
									<div class="col-12 col-sm-4 col-md-2 mb-3">
										<label for="Date">Date</label><br>
										<input class="form-control mx-0 w-100" type="date" id="other_date" name="other_date">
									</div>

									<input type="hidden" name="school_id" id="school_id" value="{{ $schoolId }}">
									
									
									<div class="col-6 col-md-3 mb-3">
										<label for="Class">Class</label><br>
										<select class="form-control mx-0 w-100"  name="other_class" id="other_sclass_id" >
										<?= $sclasses ?>
										</select>
									</div>

									<div class="col-6 col-md-3 mb-3">
										<label for="Period">Period</label><br>
										<select class="form-control mx-0 w-100" name="period" id="period_id">
											<option value="">Period</option>
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
											<option value="4">4</option>
											<option value="5">5</option>
											<option value="6">6</option>
											<option value="7">7</option>
											<option value="8">8</option>
											<option value="9">9</option>
											<option value="10">10</option>
											<option value="11">11</option>
										</select>
									</div>
									
									
									<div class="col-12 col-sm-4 col-md-3 mb-3">
										<label for="skillarea">Activity</label><br>
										<select class="form-control mx-0 w-100" id="other_duty_activity_id" name="other_duty_activity" >
											<option value="">Select Activity</option>
											@foreach($TrainerOtherDuty as $othkey => $othval)
											<option value="{{ $othval->id }}"> {{ $othval->name }}</option>
											@endforeach
										</select>
									</div>
                                    <div class="col-12 col-md-3 mb-3">
										<div class="form-group d-flex justify-content-start mt-4 pt-2">
										<button type="reset" name="otherdutydata" id="other_duty_fillter" value="otherdutydatasubmit" class="btn btn-outline-secondary">Cancel </button>
										<button type="submit" name="otherfilldata" id="other_activity_fillter" value="otherdutyfilldatasubmit" class="btn btn-primary ml-3">Save</button>
										</div>
                                    </div>
								</div>
								
								</div>
								<div class="row">
									<!-- i shifted this code to above row div
										"I moved this code to the div in the row above."
									--> 
							    </div>

                </div>

				<!-- <div class="col-12 col-md-auto col-lg-3 ml-auto mt-4">
					<div class="form-group d-flex justify-content-end">
						<button type="cancel" name="otherdutydata" id="other_duty_fillter" value="otherdutydatasubmit" class="btn btn-outline-secondary">Cancel </button>
						<button type="submit" name="otherfilldata" id="other_activity_fillter" value="otherdutyfilldatasubmit" class="btn btn-primary ml-3">Save</button>


					</div>
				</div> -->


                


			</form>
                        

            


            <form method="POST" name="trainer_activity" id="trainer_activity_id" action="{{ route('fill.trainer.activity') }}" style="display:none">
                <div class="row">
					{{method_field('post')}}
					@csrf
				
				
				 <div class="col-12">
					<div class="row">
						<div class="col-12 mb-3">
							<label for="Date">Remarks</label><br>
							<textarea class="form-control mx-0 w-100" id="remarks_id" rows="5"  name="remarks" required style="height:auto !important;"></textarea>
							<input type="hidden" name="activity_type" id="activity_type_id">
						</div>
					</div>
                </div>
				
			
				<div class="col-12 col-md-auto col-lg-3 ml-auto mt-4">
					<div class="form-group d-flex justify-content-end">
						<button type="reset" name="activitycancel" id="activity_cancel_id" value="activitycancelval" class="btn btn-outline-secondary">Cancel </button>
						<button type="submit" name="activitysubmit" id="activity_submit_id" value="activitysubmitval" class="btn btn-primary ml-3">Save</button>
					</div>
				</div>
                </div>
			</form>
			
			


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
		
      
        <!-- Modal body -->
        <div class="modal-body pt-0 pb-4 px-4 mt-3">
        
        <div class="activity-details mb-4">
                                                                                                                   <!-- <div class="act__video">
         <iframe id="youtubeurl_id" src="https://www.youtube.com/embed/QUTYxwTsbiM?si=KHp-2Z1yYZFHCzJS" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
         </div>-->
          <div class="img-act">
           <!--<img id="modal-image" src="">-->
		   <div id="modal-image-id"></div>
         </div>
        <div class="f-row">
		<p id="is-activity-happend"></p>
        <div class="std-info mb-4">
			
           <!-- <p>Class: <span id="model-cls-sec-id"></span></p> -->
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
    $(document).ready(function() {
        var today = new Date();
        document.getElementById("date").value = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);

        var formlegth = $("#form-error-message").length;

        if (formlegth > 0) {
            HideValidationMsg();
        }
		
		
		$('input[type="radio"]').change(function() 
		{
			var selectedValue = $(this).val();
			console.log("Selected value: " + selectedValue);
			//alert(selectedValue);
			$("#activity_type_id").val(selectedValue);
			if(selectedValue == 'at_school')
			{			
				$("#fill_dart_submit").show();
				$("#trainer_activity_id").hide();
				$("#other_duty_id").hide();
			
			}else if(selectedValue == 'other_duty')		  
			{
				$("#fill_dart_submit").hide();
				$("#trainer_activity_id").hide();
			    $("#other_duty_id").show();
			}				
			else
			{				
			  $("#fill_dart_submit").hide();
			  $("#trainer_activity_id").show();
			  $("#other_duty_id").hide();
		      //You can use the selectedValue for other operations
			}
		});
		
		
		
	
    });


    function HideValidationMsg() {
        setTimeout(function() {
            $('#form-error-message').fadeOut('slow');
        }, 5000);
    }
	
	//alert($("#learning_outcomes_id").text().length +'==='+$("#model-title-id").text().length);
	if($("#learning_outcomes_id").text().length == 0 && $("#model-title-id").text().length == 0)
	{ 
		//alert('--first class');
		
		$("#anchor-id").hide();
	}else
	{
		//alert('--second class');
		$("#anchor-id").show();		
	}
</script>



<script>

document.addEventListener('DOMContentLoaded', function () 
{
    const form         = document.getElementById('fill_dart_submit');
    const submitButton = document.getElementById('activity_fillter_submit');
    
    form.addEventListener('submit', function (e) 
	{
        const isValid = form.checkValidity(); // Check if all required fields are valid

        if (isValid) 
		{
            submitButton.disabled = true; // Disable the button if the form is valid
        } 
		else 
		{
            e.preventDefault(); // Prevent form submission if the form is invalid
            //alert('Please fill out all required fields correctly.');
        }
    });
});

</script>




@endsection