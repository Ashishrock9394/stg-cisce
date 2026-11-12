@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
@section('content')



<style>
    #timer {
      font-size: 30px;
      font-weight: bold;
    }

    #startBtn {
      padding: 10px 20px;
      font-size: 16px;
      cursor: pointer;
      margin-top: 10px;
    }

    #startBtn.paused {
      background-color: red;
      color: white;
      border: none;
      border-radius: 10px;
    }

    #aftertimeUPsId,
    #disqualifiedMsg {
      margin-top: 15px;
      font-size: 20px;
      display: none;
    }

    #aftertimeUPsId {
      color: green;
    }

    #disqualifiedMsg {
      color: red;
      font-weight: bold;
    }
    .hide{
        pointer-events: none;   
        opacity: 0.6;    
        cursor: not-allowed;
    }
  </style>

    <!-- Audio element for the whistle sound -->
    <audio id="whistleSound" src="{{ asset('assets/audio/whistle.mp3') }}"></audio>

<div class="all-chaptr-cards">
  <div class="container">
      <div class="t-mrg2 mb-5 pb-5">
          <div class="row">
              <div class="col-12">
                  <div class="heading-rw mt-0 mt-md-1 mb-0 p-0">
                  @if(auth()->guard('web')->check())

                  <a href="javascript:history.back()" class="back-button">
                    <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
                      </svg></span> 
                  </a>

                        @elseif(auth()->guard('sstudent')->check())

                  <a href="javascript:history.back()" class="back-button">
                    <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
                      </svg></span> 
                  </a>
          
                  @endif

                  
                      <h1 class="ml-md-4 mb-0">{{$title}}</h1>
                  </div>
              </div>
          </div>
      
        {{-- get student list componet --}}

        @php
        $type = "fitnessTest";
        @endphp

        <x-get-student-list :classes="$classes" :type="$type"  />

        <form class="row" method="POST" name="saveFlamingoRecord" id="save_flamingo_record_id" action="">
          {{method_field('post')}}
          @csrf
        
            <input type="hidden" name="skillReportId" value="{{ $skillReportId }}" id="skillReportId">
            <input type="hidden" name="TestTypeMasterID" value="{{ $TestTypeMasterID }}">
            <input type="hidden" id="SchoolId" name="SchoolId" value="{{ $SchoolId }}">
            <input type="hidden" id="selected_student_id" name="student_id">
            

              <input type="hidden" id="timetaken" name="timetaken">
                      <div class="col-12">
                          <div class="form mb-4">                   
                              <h2 class="mb-3 mt-4 text-center">Flamingo Balance Test Score</h2>
                              <div class="input-group mb-3 text-center">
                                  <span class="form-control single-input">
                                      <label for="pauseCount" class="form-label">Counts</label>
                                      <input type="number" id="pauseCount" name="total_number" class="form-control form-control-lg text-center" value="00" readonly>
                    <input type="hidden" id="disquaId" name="is_disqualify" class="form-control form-control-lg text-center" value="">
                                  </span>
                                  
                              </div>
                
                <div id="disqualifiedMsg" class="text-center mb-3">Disqualified!</div>
                
                              <div id="timer" class="mt-0 mb-3 text-center">00:00:00</div>
                <div class="actions">
                  <a href="javascript:void(0)" id="startBtn" class="btn btn-success py-2 w-100 d-flex justify-content-center" style="gap: 10px;">
                    <i class="bi bi-stopwatch"></i><span>Start Timer</span>
                  </a>
                  
                  
                </div>
                
                          </div>
                      </div>
          
          {{-- footer for submit and reset button --}}
          @php
              $id = "flamingo";
          @endphp
          <x-reset-submit-btn :id="$id"/>				
      </form>	
      
      </div>
  </div>
</div>

<script>
  const saveBtn = document.getElementById("submit_flamingo");
    window.onload = function() {
        saveBtn.classList.add("hide");

    }
</script>

<script>
  let timer = null;
  let elapsed = 0;
  let pauseCount = 0;
  const totalTime = 60000; // 60 seconds
  const disqualifyLimit = 30000; // 30 seconds
  const display = document.getElementById("timer");
  const startBtn = document.getElementById("startBtn");
  const pauseCountInput = document.getElementById("pauseCount");
  const disqualifiedMsg = document.getElementById("disqualifiedMsg");
  let isDisqualify = document.getElementById("disquaId");
  let lastStartTime = null;

  function updateTimerDisplay(time) {
    let minutes = Math.floor(time / 60000);
    let seconds = Math.floor((time % 60000) / 1000);
    let milliseconds = Math.floor((time % 1000) / 10);

    minutes = minutes < 10 ? '0' + minutes : minutes;
    seconds = seconds < 10 ? '0' + seconds : seconds;
    milliseconds = milliseconds < 10 ? '0' + milliseconds : milliseconds;

    display.innerHTML = `${minutes}:${seconds}:${milliseconds}`;

  }

  startBtn.addEventListener("click", () => {
    // If disqualified, don't allow to start again
    if (isDisqualify.value === 'Yes') return;

    if (timer === null) {
      // Start or resume
      startBtn.innerHTML = '<i class="bi bi-stopwatch"></i><span>Pause Timer</span>';
      startBtn.classList.add("paused");
      lastStartTime = Date.now();

      timer = setInterval(() => {
        const now = Date.now();
        elapsed += now - lastStartTime;
        lastStartTime = now;

        if (elapsed >= totalTime) {
          saveBtn.classList.remove("hide");
          updateTimerDisplay(totalTime);
          clearInterval(timer);
          timer = null;
          startBtn.disabled = true;
          startBtn.innerHTML = '<i class="bi bi-stopwatch"></i><span>Time\'s up</span>';
          startBtn.classList.add("paused");
          isDisqualify.value = 'No';
          return;
        }

        updateTimerDisplay(elapsed);
      }, 10);
    } else {
      // Pause timer
      clearInterval(timer);
      timer = null;
      pauseCount++;
      pauseCountInput.value = pauseCount;
      startBtn.innerHTML = '<i class="bi bi-stopwatch"></i><span>Continue</span>';  
      startBtn.classList.remove("paused");

      // Disqualify check
      if ((elapsed <= disqualifyLimit && pauseCount > 14) || (elapsed <= totalTime && pauseCount > 26) ) {
        saveBtn.classList.remove("hide");
        isDisqualify.value = 'Yes';
        disqualifiedMsg.style.display = "block";
        // timeUpDiv.style.display = "none";
        startBtn.disabled = true;
      }
    }
  });

</script>


<script>
        // Get the button and audio elements
        const button = document.getElementById('startBtn');
        const whistleSound = document.getElementById('whistleSound');

        // Add click event listener to the button
        button.addEventListener('click', () => {
            whistleSound.play().catch(error => {
                console.error('Error playing sound:', error);
            });
        });
</script>



<script>
$(document).ready(function() {
    $('#save_flamingo_record_id').submit(function(e) {
        e.preventDefault(); 
        $('#timetaken').val($('#timer').text());
        
        const studentId = document.getElementById('selected_student_id').value;
        if (!studentId) {
            handleResponseMessages( 'warning',  'Add Student', 'Please select the student');
            return;
        }

        if (elapsed === 0) {
           handleResponseMessages( 'warning',  'Start Timer', 'Please start timer');
           return;
        }
        submitLoader();
        $.ajax({
            url: '{{ route("flamingo.record.submit") }}', // or your route URL
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
              Swal.close();
              $('#save_flamingo_record_id')[0].reset();	
                      handleResponseMessages( 'success',  '', response.message, {
                          confirmText: 'OK',
                          onConfirm: function () {
                              location.reload();
                          }
                      }); 		
                  },
            error: function(xhr) {
              Swal.close();
              let errors = xhr.responseJSON.errors;
              let errorHtml = '<ul>';
              $.each(errors, function(key, value) {
                  errorHtml += '<li>' + value[0] + '</li>';
              });
              errorHtml += '</ul>';
              $('#response').html('<div style="color:red;">' + errorHtml + '</div>');
				
				Swal.fire({
					title: "error!",
					text: response.message,
					icon: "error"
					});
				
            }
        });
    });
});
</script>



</body>
</html>

@endsection