@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
@section('content')

<style>
.modal {
    padding: 0 !important;
    height: 100vh;
}

.modal-lg {
    max-width: 100%;
    margin: 0px;
    height: inherit;
}

.modal-content {
    border-radius: 0px;
    height: inherit;
}

.btn {
    min-width: auto;
}
.hide{
    pointer-events: none;   
    opacity: 0.6;    
    cursor: not-allowed;
}

#select_lane li a.selected {
    background-color: #292775;
    color: #fff;
}

#select_lane li a.hovered {
    background-color: #5c5bc0;
    color: #fff;
}

#select_lane.disabled a {
    pointer-events: none; 
    opacity: 0.5;          
    cursor: default;       
}

.remove-btn {
    color: #dc3545;
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 0 5px;
    margin-left: 10px;
}

.remove-btn:hover {
    color: #bd2130;
}

.list-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

.std-rank {
    flex-grow: 1;
}


</style>
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


            <form method="POST" name="{{ $TestTypeId }}" id="{{ $TestTypeId }}" action="javascript:void(0);">
                {{method_field('post')}}
                @csrf

                <input type="hidden" name="skillReportId" value="{{ $skillReportId }}"  id="skillReportId"> 
                <input type="hidden" name="TestTypeMasterID" value="{{ $TestTypeMasterID }}">
                <input type="hidden" name="SchoolId" id="SchoolId" value="{{ $SchoolId }}">
            

                <div class="form-row my-2">
                    <div class="col-12">
                        <div class="lanes">
                            <h3 class="mb-2 mt-1 text-left"><strong>Select no. of athletes to track</strong></h3>

                            <ul id="select_lane">
                                <li><a href="javascript:void(0)" data-lane-no="1">1</a></li>
                                <li><a href="javascript:void(0)" data-lane-no="2">2</a></li>
                                <li><a href="javascript:void(0)" data-lane-no="3">3</a></li>
                                <li><a href="javascript:void(0)" data-lane-no="4">4</a></li>
                                <li><a href="javascript:void(0)" data-lane-no="5">5</a></li>
                                <li><a href="javascript:void(0)" data-lane-no="6">6</a></li>
                                <li><a href="javascript:void(0)" data-lane-no="7">7</a></li>
                                <li><a href="javascript:void(0)" data-lane-no="8">8</a></li>
                            </ul>

                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form">
                            <h2 class="mb-2 mt-4 text-center">{{$title}} Score</h2>
                            <div class="input-group input-group__2 mb-3">
                                <span class="form-control">
                                    <label for="minuteId" class="form-label">min</label>
                                    <input type="number" name="total_min" class="form-control form-control-lg" id="minuteId"
                                        placeholder="00" readonly>
                                </span>
                                <span class="form-control">
                                    <label for="secondId" class="form-label">sec</label>
                                    <input type="number" name="total_sec" class="form-control form-control-lg" id="secondId"
                                        placeholder="00" readonly>
                                </span>
                                <span class="form-control">
                                    <label for="milisecondId" class="form-label">msec</label>
                                    <input type="number" name="total_mili" class="form-control form-control-lg"
                                        id="milisecondId" placeholder="00" readonly>
                                </span>
                            </div>
                            <div class="actions">
                                <a href="javascript:void(0)" id="startTimerBtn"
                                    class="btn btn-success py-2 w-100 d-flex justify-content-center" style="gap: 10px;">
                                    <i class="bi bi-stopwatch"></i><span id="timerLabel">Start Timer</span></a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">

                        <div class="rankings mt-3" style="display:none">
                            <ul class="list-group" id="laneList">
                            </ul>
                        </div>

                    </div>

                </div>

                <footer class="container-fluid position-fixed bg-white p-0"
                    style="bottom: 0; left: 0; right: 0; z-index: 100;">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="action-bar py-3 p-0 actions">
                                    <button type="reset" id="speed_reset_id"
                                        class="btn py-2 px-5 btn btn-outline-secondary" onclick="resetAll();">Reset </button>
                                    <button type="submit" id="speed_submit_id"
                                        class="btn py-2 px-5 btn-primary">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
            </form>
            
            @php  $type = "cwsnlist"; @endphp
            <x-speed-get-students :classes="$classes" :type="$type" :cwsn-type="$pwd_category_id" :title="$title" />
        </div>
    </div>
</div>

<script>

    const saveBtn = document.getElementById("speed_submit_id");
    window.onload = function() {
        saveBtn.classList.add("hide");
        localStorage.removeItem("selected_student");
    }

    const laneItems = document.querySelectorAll('#select_lane li a');
    let selectedLane = 0;

    laneItems.forEach(item => {
        const laneNo = parseInt(item.dataset.laneNo);

        item.addEventListener('click', () => {
            selectedLane = laneNo;
            updateSelection(selectedLane);
        });

        item.addEventListener('mouseover', () => {
            updateSelection(laneNo, true);
        });

        item.addEventListener('mouseout', () => {
            updateSelection(selectedLane);
        });
    });

    function updateSelection(upTo, flag = false) {
        laneItems.forEach(item => {
            const laneNo = parseInt(item.dataset.laneNo);
            item.classList.remove('selected', 'hovered');

            if (laneNo <= upTo) {
                item.classList.add(flag ? 'hovered' : 'selected');
            }
        });
    }



    $(document).ready(function() {

        const formName = String(@json($TestTypeId)); 

        $(document).on('submit', `#${formName}`, function(e) {
            e.preventDefault();

            const filledStudents = [];
            document.querySelectorAll('#laneList .student-id').forEach(input => {
                const value = input.value.trim();
                if (value) filledStudents.push(value);
            });

            if (filledStudents.length < liCount) {
                Swal.fire({
                    title: 'Missing Students',
                    text: `Please add remaining ${liCount-filledStudents.length} students before submitting.`,
                    icon: 'warning'
                });
                return;
            }
            
            localStorage.removeItem("selected_student");

            const route = '{{ route("cwsn.types.submit") }}';
            const formData = $(this).serialize();
            
            SubmitForm(formName, formData, route);
        });


        function SubmitForm(formid, formData, route) {
            submitLoader(); 

            $.ajax({
                url: route,
                method: 'POST',
                data: formData, 
                success: function(response) {
                  
                    if (typeof Swal !== 'undefined' && Swal.close) Swal.close(); 
                    $(`#${formid}`)[0].reset();

                    if (response.status === 'success' || response.success === true) {
                        handleResponseMessages('success', 'Success!', response.message, {
                            confirmText: 'OK',
                            onConfirm: function () {
                                location.reload();
                            }
                        });
                    } else {
                        handleResponseMessages('error', 'Failed', response.message || 'Action could not be completed.');
                    }
                },
                error: function (xhr) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: "Error!",
                            text: xhr.responseJSON?.message ?? 'An unexpected error occurred',
                            icon: "error"
                        });
                    } else {
                        alert(xhr.responseJSON?.message ?? 'An unexpected error occurred');
                    }
                }
            });
        }
    });





    let activeLane = null;
    let maxLi = 0;
    let liCount = 0;

    let minute = 0,
        second = 0,
        milisecond = 0;
    let interval = null;
    let isRunning = false;

    const minuteInput = document.getElementById('minuteId');
    const secondInput = document.getElementById('secondId');
    const milisecondInput = document.getElementById('milisecondId');
    const startTimerBtn = document.getElementById('startTimerBtn');
    const timerLabel = document.getElementById('timerLabel');
    const laneList = document.getElementById('laneList');

    function updateDisplay() {
        minuteInput.value = String(minute).padStart(2, '0');
        secondInput.value = String(second).padStart(2, '0');
        milisecondInput.value = String(milisecond).padStart(2, '0');
    }

    function resetClock() {
        minute = 0;
        second = 0;
        milisecond = 0;
        saveBtn.classList.add("hide");
        updateDisplay();
    }

    function startTimer() {
        interval = setInterval(() => {
            milisecond += 10;
            if (milisecond >= 1000) {
                milisecond = 0;
                second++;
            }
            if (second >= 60) {
                second = 0;
                minute++;
            }
            updateDisplay();
        }, 10);
        isRunning = true;
        timerLabel.textContent = "Split Timer";
    }

    function stopTimer() {
        clearInterval(interval);
        isRunning = false;
        saveBtn.classList.remove("hide");
        timerLabel.textContent = "Start Timer";
    }

    function appendSplit() {
        liCount++;
        const li = document.createElement('li');
        li.className = "list-group-item list-group-item-action p-2 px-3";
        li.setAttribute("data-rank", liCount);

        li.innerHTML = `
            <div class="list-item">
                <div class="std-rank mb-0">
                    <div class="rank mr-4">
                        <p><span class="h6 student-name">Student Name </span>&nbsp;|&nbsp;<span class="student-reg"> Registration Number: </span> </p>
                         <p><span class="student-class">Class </span>&nbsp;|&nbsp; Roll No:<span class="student_roll_no">  </span> </p>              
                        <input type="hidden" class="student-id" name="students[${liCount}][id]" />
                    </div>

                    <div class="mt-2 mt-md-0">
                        <div class="mr-1 d-flex align-items-center">
                            <i class="bi bi-stopwatch mr-1"></i>
                            <span>Rank ${liCount} Time:</span>
                        </div>
                        <div class="score">${String(minute).padStart(2,'0')}:${String(second).padStart(2,'0')}:${String(milisecond).padStart(2,'0')}</div>
                        <input type="hidden" name="students[${liCount}][time]" value="${minute*60*1000 + second*1000 + milisecond}" />
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <a href="#a" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg"
                        class="btn btn-outline-secondary px-3 ml-0 d-flex justify-content-center align-items-center border-btn add-student-btn"
                        data-rank="${liCount}" style="gap: 5px">
                        <span>Add</span>
                    </a>
                    <button type="button" class="remove-btn" data-rank="${liCount}">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>
            </div>`;
        laneList.appendChild(li);
        
        // Add event listener to the remove button
        const removeBtn = li.querySelector('.remove-btn');
        removeBtn.addEventListener('click', function() {
            removeLaneItem(this.getAttribute('data-rank'));
        });
    }

    // When clicking "Add" → open modal and store rank
    let selectedRank = null;
    document.addEventListener("click", function(e) {
        if (e.target.closest(".add-student-btn")) {
            $('#student_id').val('');
            selectedRank = e.target.closest(".add-student-btn").getAttribute("data-rank");
        }
    });

    function selectStudent(student) {
        if (!selectedRank) return;

        const li = document.querySelector(`li[data-rank="${selectedRank}"]`);
        if (li) {
            li.querySelector('.student-name').textContent = student.name;
            li.querySelector('.student-class').textContent = student.class;
            li.querySelector('.student-reg').textContent = student.reg;
            li.querySelector('.student-id').value = student.id;
        }

        // Close modal (Bootstrap 4/5)
        $('.bd-example-modal-lg').modal('hide');
        selectedRank = null;
    }

    // Lane selection
    document.querySelectorAll("ul li a").forEach(anchor => {
        anchor.addEventListener("click", e => {
            e.preventDefault();
            document.querySelectorAll("ul li a").forEach(a => a.classList.remove("active"));
            anchor.classList.add("active");

            activeLane = parseInt(anchor.getAttribute("data-lane-no"), 10);
            maxLi = activeLane;
            liCount = 0;
            laneList.innerHTML = "";
            document.querySelector(".rankings").style.display = "block";

            if (isRunning) stopTimer();
            resetClock();
        });
    });

    startTimerBtn.addEventListener("click", e => {
        e.preventDefault();

        if (!activeLane) {
            Swal.fire({
                title: '',
                text: 'Please select the Lane first',
                icon: 'warning'
              });
              return;
        }

        document.getElementById('select_lane').classList.add("disabled");

        if (!isRunning) {
            startTimer();
        } else {
            if (liCount < maxLi) {
                appendSplit();
                if (liCount === maxLi) {
                    stopTimer();
                    disableStartBtn();
                    
                }
            }
        }
    });

    function disableStartBtn() {
        startTimerBtn.classList.add("disabled");
        startTimerBtn.style.pointerEvents = "none";
        startTimerBtn.style.opacity = "0.6"; 
    }

    function enableStartBtn() {
        startTimerBtn.classList.remove("disabled");
        startTimerBtn.style.pointerEvents = "auto";
        startTimerBtn.style.opacity = "1";
    }

    function resetAll() {
        updateSelection(0, flag = false)
        clearInterval(interval);
        interval = null;
        isRunning = false;
        activeLane = null;
        maxLi = 0;
        liCount = 0;
        resetClock();
        laneList.innerHTML = "";
        timerLabel.textContent = "Start Timer";
        stdArray = [];
        localStorage.removeItem("selected_student");
        document.getElementById('select_lane')?.classList.remove('disabled');
        document.querySelectorAll("ul li a").forEach(a => a.classList.remove("active"));
        enableStartBtn(); // ✅ re-enable on reset
    }

    // Function to remove a lane item and update ranks
    function removeLaneItem(rankToRemove) {
        const itemToRemove = document.querySelector(`li[data-rank="${rankToRemove}"]`);
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to remove the Athlete!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, do it!',
            cancelButtonText: 'Cancel',
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {            
                if (!itemToRemove) return;
                const studentIdInput = itemToRemove.querySelector('.student-id');
                const studentId = studentIdInput ? studentIdInput.value : null;
                if (studentId) {
                    removeStudentFromLocalStorage(rankToRemove, studentId);
                }
                
                itemToRemove.remove();

                const remainingItems = document.querySelectorAll('#laneList li');
                remainingItems.forEach((item, index) => {
                    const newRank = index + 1;
                    const oldRank = item.getAttribute('data-rank');
                    
                    if (oldRank != newRank) {
                        updateRankInLocalStorage(oldRank, newRank);
                    }
                    
                    item.setAttribute('data-rank', newRank);
                    const rankSpan = item.querySelector('.mr-1 span');
                    if (rankSpan) {
                        rankSpan.textContent = `Rank ${newRank} Time:`;
                    }
                    
                    const addBtn = item.querySelector('.add-student-btn');
                    const removeBtn = item.querySelector('.remove-btn');
                    if (addBtn) addBtn.setAttribute('data-rank', newRank);
                    if (removeBtn) removeBtn.setAttribute('data-rank', newRank);
                });
                
                liCount--;
                $('#class_id').trigger('change');
                if(liCount == 0){    
                    resetAll();    
                }
            }
        });
    }
    function removeStudentFromLocalStorage(rank, studentId) {
        let storedData = localStorage.getItem(studentli);
        if (storedData) {
            try {
                let studentsArray = JSON.parse(storedData);
                studentsArray = studentsArray.filter(item => 
                    !(item.selectedRank == rank && item.studentId == studentId)
                );
                localStorage.setItem(studentli, JSON.stringify(studentsArray));
                stdArray = studentsArray;
            } catch (e) {
                console.error("Error updating localStorage:", e);
            }
        }
    }

    function updateRankInLocalStorage(oldRank, newRank) {
        let storedData = localStorage.getItem(studentli);
        if (storedData) {
            try {
                let studentsArray = JSON.parse(storedData);
                studentsArray = studentsArray.map(item => {
                    if (item.selectedRank == oldRank) {
                        return { ...item, selectedRank: String(newRank) };
                    }
                    return item;
                });
                localStorage.setItem(studentli, JSON.stringify(studentsArray));
                stdArray = studentsArray;
            } catch (e) {
                console.error("Error updating localStorage:", e);
            }
        }
    }

    resetClock();
</script>

@endsection