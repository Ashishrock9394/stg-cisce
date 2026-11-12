@extends('layouts.filldart-app')
@section('title', $title)
@section('content')

@push('custom-style')
    <link href="{{ asset('assets/css/student-dashboard.css') }}" rel="stylesheet" />
@endpush

<style>
    .score-bar {
        display: flex;
        width: 100px;
        height: 10px;
        border: 1px solid #333;
        border-radius: 5px;
        overflow: hidden;
    }

    .segment {
        flex: 1;
        height: 100%;
        opacity: 0.2;
        transition: opacity 0.3s ease-in-out;
    }

    .score-label {
        font-family: sans-serif;
        margin-top: 5px;
    }

    .level-bar {
        display: flex;
        margin: 10px 0;
        overflow: hidden;
        border: 1px solid #333;
        border-radius: 8px;
        height: 10px;
    }

    .segment2 {
        flex: 1;
        background-color: #e0e0e0;
        opacity: 0;
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.6s ease, opacity 0.4s ease;
    }

    .segment2.filled {
        opacity: 1;
        transform: scaleX(1);
    }

    .segment2:first-child {
        border-radius: 8px 0 0 8px;
    }
    .segment2:last-child {
        border-radius: 0 8px 8px 0;
    }
    .segment2.filled-max {
        border-top: 2px solid #e7da27;
        border-bottom: 2px solid #e7da27;
        box-sizing: border-box;
        animation: shimmer-green 2s infinite alternate;
    }

    .modal {
        display: none;
        position: fixed; 
        z-index: 1; 
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto; 
        background-color: rgba(0,0,0,0.5);
    }

    .modal-content {
        background-color: #fff;
        margin: 15% auto;
        padding: 20px;
        width: 60%;
        border-radius: 8px;
    }

    .close {
        float: right;
        font-size: 24px;
        cursor: pointer;
    }
    .color-container {
        display: flex;
        flex-direction: column;
        gap: 5px;
        width: auto;
    }

    .color-block {
        width: 100%;
        color: white;
        text-align: center;
        padding: 2px;
        font-weight: bold;
        display: block;
        border-radius: 5px
    }
    * {text-rendering: optimizeLegibility; font-size:100%;}
       
    #bmi_gauge svg g g {
        font-size: 0px !important;
    }

    .garph-01 {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
    }

    .donut_single {
        width: 150px;
        height: 150px;
    }

    .level-detail {
        text-align: center;
    }
    
    .back-btn {
        display: inline;
    }
    .back-btn img {
        display: inline;
    }
    .lazy-image {
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
    }

    .lazy-image.loaded {
        opacity: 1;
    }
    .select-terms{
        height: 35px;
        margin-left: 10px;
    }
    .term-select{
        border-color: var(--org-color);
        height: 100%;
        padding: 2px;
        border-radius:5px;
        color: var(--org-color);
    }
</style>

    @php
        $BmiClassess = ['1','2','3','4','5','6','7','8','9','10','11','12'];
        $juniorClassess = ['1','2','3',];
        $siniorClassess = ['4','5','6','7','8','9','10','11','12'];

    @endphp

<div class="all-chaptr-cards">
    <div class="container">
        <div class="row">
            <div class="col p-0 mt-3">
                <div class="heading-rw mt-3 mt-md-1 mb-0 p-0">
                    <a href="{{ route('student.dashboard')}}" class="back-button">
                        <span class="arrow">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 3.293l6 6V14a1 1 0 0 1-1 1h-4v-4H7v4H3a1 1 0 0 1-1-1V9.293l6-6z"/>
                            </svg>
                        </span>
                    </a>                
                    <h1 class="ml-md-4 mt-1 px-2">Back to home</h1>
                </div>            
            </div>
            <div class="col-auto mt-3">
                <div class="select-terms">
                    <select name="term" id="termSelect" class="term-select">
                        <option value="{{ $previousAcademicYear }}"
                            {{ $selectedYear == $previousAcademicYear ? 'selected' : '' }}>
                            Academic Year: {{ $previousAcademicYear }}
                        </option>

                        <option value="{{ $academicYear }}"
                            {{ $selectedYear == $academicYear ? 'selected' : '' }}>
                            Academic Year: {{ $academicYear }}
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12 col-md-12 col-lg-12 apst">
                <div class="user-information backgroud-01 w-100">
                <img src="{{ asset('resources/images/avtar.png') }}" class="d-inline-block align-top" height="35" alt="avtar">
                <div class="left-text pl-3 w-50 user-u-info">
                    <h2>{{ Auth::guard('sstudent')->user()->student_name }}</h2>
                    <h3>{{$studentAge}} Years, {{$studentData->gender}}</h3>
                </div>
                <div>
                    <h2>{{$studentData->school_name}}</h2>
                    <!-- <h2 class="lead">{{$studentData->school_name}}</h2> -->
                    <p>{{$studentData->className}}-{{$studentData->section}}, Roll No: {{$studentData->rollno}}</p>
                </div>
                </div>
            </div>

            <!-- <div class="col-4 col-md-2 col-lg-2 apst">
                <div class="student-rank left-text text-center backgroud-01 h-100 d-flex flex-column justify-content-center align-items-center">
                    <h4>School Rank</h4>
                    <span>12</span>
                </div>
            </div>

            <div class="col-4 col-md-2 col-lg-2 apst">
                <div class="student-rank left-text text-center backgroud-01 h-100 d-flex flex-column justify-content-center align-items-center">
                    <h4>State Rank</h4>
                    <span>13262</span>
                </div>
            </div>

            <div class="col-4 col-md-2 col-lg-2 apst">
                <div class="student-rank left-text text-center backgroud-01 h-100 d-flex flex-column justify-content-center align-items-center">
                    <h4>National Rank</h4>
                    <span>10222</span>
                </div>
            </div> -->
        </div>



        @if (in_array((string) $classId, $BmiClassess))        
            <div class="row mt-4">
                <div class="col-12 col-md-6 col-lg-4 col-xl-4 order-md-2 order-lg-1 apst">
                    <div class="fitness-box h-100">
                        <div class="garph-01">
                            <div id="donut_single" class="donut_single"></div>
                            <div id="overallFitness" class="level-detail left-text text-center">
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-md-12 col-lg-4 col-xl-4 order-md-1 order-lg-2 apst">
                    <div class="fitness-box h-100 w-100">
                        <div class="weight_01 left-text">
                            <div class="center-img-weight">
                                <img src="{{ asset('assets/imgs/height-icon.jpg') }}" alt="Height" class="img-fluid img lazy-image" loading="lazy" />
                                <div class="weight-input">
                                    <h4>Height</h4>
                                    <span>{{ !empty($bmiRecord->height) ? $bmiRecord->height . ' cm' : '---' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="weight_02 left-text">
                            <div class="center-img-weight">
                                <img src="{{ asset('assets/imgs/weight-icon.png') }}" alt="weight" class="img-fluid img lazy-image" loading="lazy" />
                                <div class="weight-input">
                                    <h4>Weight</h4>
                                    <span>{{ !empty($bmiRecord->weight) ? $bmiRecord->weight . ' kg' : '---' }}</span>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-4 col-xl-4 order-3 apst">
                    <div class="fitness-box h-100">
                        <div class="meater left-text w-100">                                
                            <div class="left-text benchmark">
                                <h4>BMI</h4>
                                <span>{{$bmiRecord->score ?? '---' }} ({{$bmiRecord->level ?? '---' }})</span>
                                <a href="#" id="openModal">BMI Benchmark <i class="fa fa-angle-right"></i></a>
                            </div>
                            <div id="bmi_gauge"></div>
                            <div class="color-container">
                                <div class="color-block" style="background-color: lightgrey;">UW</div>
                                <div class="color-block" style="background-color: #0F9D58;">N</div>
                                <div class="color-block" style="background-color: #ff9900;">OW</div>
                                <div class="color-block" style="background-color: #DB4437;">OB</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>           
        @endif


        
        <div class="clearfix"></div>

        <div class="my-3">
             {{-- for FMS tests --}}

            <div class="row no-gutters">

                @if (in_array((string) $classId, $juniorClassess))   
                <h2 class="test-heading text-center mt-2 mb-3">FMS Development Tests</h2>
                    @foreach($fmsTestData as $test)
                        <div class="col-12 col-md-6 col-lg-6 col-xl-4">
                            <div class="test-box">
                                <div class="tests left-text">
                                    <img src="{{ asset('uploads/icons/BatteryOfTests/' . $test->icons) }}" alt="logo" class="img-fluid img lazy-image" loading="lazy" />
                                    <div class="left-text w-100">
                                        <h4>{{ $test->skill_name }}</h4>
                                        <div class="score-bar w-100" id="scoreBar">
                                            <div class="segment"></div>
                                            <div class="segment"></div>
                                            <div class="segment"></div>
                                            <div class="segment"></div>
                                            <div class="segment"></div>
                                        </div>
                                        <div class="score-outcome d-flex justify-content-between align-items-center">
                                            <div class="score-label" id="scoreLabel">{{ $test->score }}</div>                           
                                            <div class="outcome">{{ $test->outcome }}</div>                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach 
                @else
                    @if(!in_array((string) $classId, $siniorClassess))
                        <h2 class="test-heading text-center">You have not given FMS tests yet</h2>
                    @endif
                @endif
    
            </div>
            <div class="clearfix"></div>
            <div class="row no-gutters">

                @if (in_array((string) $classId, $juniorClassess) || in_array((string) $classId, $siniorClassess))  
                <h2 class="test-heading text-center mt-2 mb-3">Fitness Tests</h2>
                    @foreach($fitnessTest as $index => $test) 
                        <div class="col-12 col-md-6 col-lg-6 col-xl-4">
                            <div class="test-box">
                                <div class="tests left-text">
                                    <img src="{{ asset('uploads/icons/BatteryOfTests/' . $test->icons) }}" alt="logo" class="img-fluid img lazy-image" loading="lazy" />
                                    <div class="left-text w-100">
                                        <h4>{{ $test->skill_name }}</h4>
                                        <div class="level-box">
                                            <div class="clearfix"></div>
                                            
                                            <div class="level-bar" data-score="{{ $test->level }}">
                                                @for ($i = 1; $i <= 7; $i++)
                                                    <div class="segment2"></div>
                                                @endfor
                                            </div>


                                            <div class="score-outcome d-flex justify-content-between align-items-center">
                                                <div class="score-label" id="scoreLabel">Level {{ $test->level }}</div>                           
                                                <div class="outcome">{{ $test->levelOutcome }}</div>                           
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach  
                @else
                    <h2 class="test-heading text-center mb-4 mt-5 pt-3 pt-md-5 mb-md-5">You have not given any Fitness tests yet</h2>
                @endif
            </div>
        </div>
        <!-- @php
            $id  = Crypt::encryptString($studentId);
            $view_link = route('reports.view', ['id' => $id, 'term_id' => $selectedTermId]);
            $download_link = route('reports.download', ['id' => $id, 'term_id' => $selectedTermId]);
        @endphp -->

        @php
            $id = Crypt::encryptString($studentId);

            if ($studentData->is_pwd == '1') {
                $viewRoute = 'cwsn.reports.view';
                $downloadRoute = 'cwsn.reports.download';
            } else {
                $viewRoute = 'reports.view';
                $downloadRoute = 'reports.download';
            }

            $view_link = route($viewRoute, ['id' => $id, 'term_id' => $selectedTermId]);
            $download_link = route($downloadRoute, [ 'id' => $id, 'term_id' => $selectedTermId]);
        @endphp

        
        <div class="form-row download-report mb-5">
            <div class="col-12 col-sm-4 col-lg-4 mb-3">
                {{-- <a href="#" class="btn btn-primary btn-lg w-100" data-toggle="tooltip" data-placement="top" title="End of the term"> --}}
                <a href="{{$view_link}}" class="btn btn-primary btn-lg w-100" target="_blank">
                    View Report 
                </a>
            </div>
            <div class="col-12 col-sm-4 col-lg-4 mb-3">
                {{-- <a href="#" class="btn btn-primary btn-lg w-100" data-toggle="tooltip" data-placement="top" title="End of the term"> --}}
                <a href="{{$download_link}}" class="btn btn-primary btn-lg w-100" target="_blank">
                    Download Report 
                </a>
            </div>
            <!-- <div class="col-12 col-sm-4 col-lg-4 mb-3">
                <a class="btn btn-primary btn-lg w-100 notAvailable" target="_blank">Fitness History <i class="fa fa-angle-right"></i> </a>
            </div> -->
            <div class="col-12 col-sm-4 col-lg-4 mb-3">
                <a href="" id="openFitnessModal" class="btn btn-primary btn-lg w-100" >National Benchmark </a>
            </div>
        </div>
        {{-- code end here --}}
    </div> 
</div>


<!-- BMI benchmark modal  -->
@include('modals.bmi-benchmarks-modal');

<!-- fitness benchmark modal  -->
@include('modals.fitness-benchmarks-modal');


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>

    // google gague for bmi 

    google.charts.load('current', {'packages':['gauge']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        const bmi = {{ $bmiRecord->score ?? 0 }};
        const benchmark = @json($getBmiBenchmark);

        const extractNumbers = (text) => {
            const matches = text.match(/[\d.]+/g);
            return matches ? matches.map(Number) : [];
        };

        const uwLimit = extractNumbers(benchmark.UW)[0] || 0;
        const nLimits = extractNumbers(benchmark.N);
        const owLimits = extractNumbers(benchmark.OW);
        const obLimit = extractNumbers(benchmark.OB)[0] || 0;

        // Determine BMI zone value
        let targetValue;
        let targetLabel;

        if (bmi < uwLimit) {
            targetValue = 0.5;
            targetLabel = 'UW';
        } else if (bmi < nLimits[1]) {
            targetValue = 1.5;
            targetLabel = 'N';
        } else if (bmi < owLimits[1]) {
            targetValue = 2.5;
            targetLabel = 'OW';
        } else {
            targetValue = 3.5;
            targetLabel = 'OB';
        }

        // Use the actual label in the data
        const data = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            [targetLabel, targetValue],
        ]);

        const options = {
            width: '70%',
            height: 150,
            min: 0,
            max: 4,
            greenFrom: 1, greenTo: 2,       // N - Green
            yellowFrom: 2, yellowTo: 3,     // OW - Orange
            redFrom: 3, redTo: 4,           // OB - Red
            majorTicks:[],
            minorTicks: 0,
            animation: { 
                duration: 50,
                easing: 'linear'
            }
        };

        const chart = new google.visualization.Gauge(document.getElementById('bmi_gauge'));
        chart.draw(data, options);

        let currentValue = 0;
        const step = 0.05;
        const interval = setInterval(() => {
            if (currentValue < targetValue) {
                currentValue += step;
                if (currentValue > targetValue) currentValue = targetValue;
            } else if (currentValue > targetValue) {
                currentValue -= step;
                if (currentValue < targetValue) currentValue = targetValue;
            }
            data.setValue(0, 1, currentValue);
            chart.draw(data, options);

            if (currentValue === targetValue) clearInterval(interval);
        }, 50);
    }

    
    // for fms tets 

    document.addEventListener('DOMContentLoaded', function () {
        const maxScore = 5;

        document.querySelectorAll('.test-box').forEach(box => {
            const scoreBar = box.querySelector('.score-bar');
            const scoreLabel = box.querySelector('.score-label');
            if (!scoreBar || !scoreLabel) return;

            const score = parseFloat(scoreLabel.textContent);
            const segments = scoreBar.querySelectorAll('.segment');

            const fillColor = getColorByScore(score);

            segments.forEach((segment, index) => {
                if (index < score) {
                    segment.style.opacity = '1';
                    segment.style.backgroundColor = fillColor;
                } else {
                    segment.style.opacity = '0.15';
                }
            });

            scoreLabel.textContent = `Score: ${score}/${maxScore}`;
        });

        function getColorByScore(score) {
            if (score <= 1) return '#f44336'; // red
            if (score <= 3) return '#ff9800'; // orange
            if (score === 4) return '#8bc34a'; // light green
            return '#4caf50'; // green
        }
    });

    // for fitness tests 

    document.addEventListener('DOMContentLoaded', function () {
        const levelColors = {
            1: '#f44336',
            2: '#ff5722',
            3: '#ff9800',
            4: '#ffc107',
            5: '#8bc34a',
            6: '#4caf50',
            7: '#388e3c'
        };

        function applyColoredLevelProgress() {
            document.querySelectorAll('.level-bar').forEach(levelBar => {

                const rawLevel = levelBar.getAttribute('data-score') || '0';
                const level = parseFloat(rawLevel.replace(/[^0-9.]/g, '')) || 0;
                const segments = levelBar.querySelectorAll('.segment2');

                if (level === 0) {
                    segments.forEach(segment => {
                        segment.classList.remove('filled', 'filled-max');
                        segment.style.backgroundColor = '#e0e0e0';
                        segment.style.opacity = '0.3';
                    });
                    return;
                }
                const color = levelColors[Math.min(Math.floor(level), 7)];

                segments.forEach((segment, index) => {
                    const segmentIndex = index + 1;

                    if (level >= segmentIndex) {
                        segment.classList.add('filled');
                        segment.style.backgroundColor = color;
                        segment.style.opacity = '1';
                        if (level === 8) {
                            segment.classList.add('filled-max');
                        } else {
                            segment.classList.remove('filled-max');
                        }

                    } else {
                        segment.classList.remove('filled', 'filled-max');
                        segment.style.backgroundColor = '#e0e0e0';
                        segment.style.opacity = '0.3';
                    }
                });
            });
        }
        applyColoredLevelProgress();
    });





    // for overall fitness 

    let fitnessTest = @json($fitnessTest);
    const levelMap = {
        'L0': 0,
        'L1': 1,
        'L2': 2,
        'L3': 3,
        'L4': 4,
        'L5': 5,
        'L6': 6,
        'L7': 7,
        'L8': 8
    };

    const levels = fitnessTest.map(test => test.level);

    const numericScores = levels.map(level => levelMap[level] || 0);

    const sum = numericScores.reduce((acc, score) => acc + score, 0);

    const avgScore = numericScores.length ? sum / numericScores.length : 0;

    const maxScore = 8;

    const overallPercentile = ((avgScore) / maxScore) * 100;

    // Display the initial structure
    document.getElementById('overallFitness').innerHTML = `
    <h4>Overall Fitness Level</h4>
    <span>0 %ile</span>
    `;


    let start = 0;
    const end = overallPercentile;
    const duration = 2000; // 2 seconds
    const stepTime = 20;   // update every 20ms
    const increment = end / (duration / stepTime);

    const spanElement = document.querySelector('#overallFitness span');

    const timer = setInterval(() => {
    start += increment;
    if (start >= end) {
        start = end;
        clearInterval(timer);
    }
    spanElement.textContent = `${start.toFixed(2)} %ile`;
    }, stepTime);


    // Load Google Charts
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawOverallFitness);

    function drawOverallFitness() {
        var targetValue = parseFloat(overallPercentile.toFixed(2));
        
        var data = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            ['Fitness', 0] 
        ]);
        var formatter = new google.visualization.NumberFormat({
            pattern: '#.##'
        });
        formatter.format(data, 1);
        
        var options = {
            greenFrom: 0,
            greenTo: 100,
            minorTicks: 0,
            majorTicks: [],
            hideTicks: true,
            max: 100,
            animation: { 
                duration: 500,
                easing: 'linear'
            }
        };

        var chart = new google.visualization.Gauge(document.getElementById('donut_single'));
        chart.draw(data, options);
        let current = 0;
        let step = targetValue / 50;

        let interval = setInterval(() => {
            current += step;
            if (current >= targetValue) {
                current = targetValue;
                clearInterval(interval);
            }

            data.setValue(0, 1, parseFloat(current.toFixed(2)));
            formatter.format(data, 1);
            chart.draw(data, options);
        }, 50);
    }

    document.addEventListener("DOMContentLoaded", function() {
        const images = document.querySelectorAll('.lazy-image');

        images.forEach(img => {
            if (img.complete) {
                img.classList.add('loaded');
            }

            img.addEventListener('load', () => {
                img.classList.add('loaded');
            });
        });
    });



    // open bmi benchmark 

    document.getElementById("openModal").addEventListener("click", function(e) {
            e.preventDefault();
            document.getElementById("bmiModal").style.display = "block";
    });
    document.querySelector(".close").addEventListener("click", function() {
            document.getElementById("bmiModal").style.display = "none";
    });

    document.getElementById("bmiModal").addEventListener("click", function(e) {
            if (e.target === this) {
                e.stopPropagation();
            }
        });
    window.addEventListener("click", function(event) {
            const modal = document.getElementById("bmiModal");
            if (event.target === modal) {
                modal.style.display = "none";
            }
    });

    // fitness benchmark modal 

    document.getElementById("openFitnessModal").addEventListener("click", function(e) {
        e.preventDefault();
        document.getElementById("fitnessBenchmark").style.display = "block";
    });
    const closeButtons = document.querySelectorAll("#fitnessBenchmark .close");

    closeButtons.forEach(button => {
        button.addEventListener("click", function() {
            document.getElementById("fitnessBenchmark").style.display = "none";
        });
    });

    document.getElementById("fitnessBenchmark").addEventListener("click", function(e) {
        if (e.target === this) {
            e.stopPropagation();
        }
    });
    window.addEventListener("click", function(event) {
        const modal = document.getElementById("fitnessBenchmark");
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const yearSelect = document.getElementById('termSelect');

        yearSelect.addEventListener('change', function () {
            const selectedValue = this.value;

            const url = new URL(window.location.href);
            url.searchParams.set('termSelect', selectedValue);

            window.location.href = url.toString();
        });
    });

</script>


@endsection