@extends('layouts.filldart-app') 
@section('title', $title)
@section('content')

<style>

    .stat-card {
        border-radius: 6px;
        padding: 5px 20px;
        color: #fff;
        height: 120px;
        display: flex;
        align-items: center;
    }
    .stat-content {
        width: 60%;
    }
    .stat-card.green { background: #039a48; }
    .stat-card.yellow { background: #ffcb08; }
    .stat-card.blue { background: #396afc; }

    .stat-content h2 {
        font-size: 32px;
        font-weight: 700;
        margin: 0;
        color: #fff;
    }


    .stat-content p {        
        color: #fff;
        font-size: 1rem;
        text-transform: uppercase;
        margin: 5px 0 0;
    }
    .stat-icon i {
        font-size: 70px;
        opacity: 0.4;
        transition: transform 0.3s ease, opacity 0.3s ease;
    }
    .stat-icon {
        width: 40%;
        display: flex;
        justify-content: center;
        align-items: center; 
    }
    .stat-card:hover .stat-icon i {
        transform: scale(1.2);
        opacity: 0.7;
    }

    .counter {
        font-size: 2.4rem;
        font-weight: 700;
        margin-top: 10px;
    }
    .stat-card:hover {
        transform: scale(1.05);
    }
    .gradient-green { background: #039a48; }
    .gradient-orange { background: #ffcb08; }
    .gradient-blue { background: #007ec6; }
    .card-title { font-size: 1rem; text-transform: uppercase; }
    .counter { font-size: 2.5rem; font-weight: 700; }
    html, body {
        height: 100%;
        margin: 0;
        overflow: hidden;
    }

    .container-fluid {
        height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .container-fluid > .row {
        flex: 1;
        overflow: hidden;
    }
    .sidebar {
        width: 150px;
        min-width: 150px;
        height: 85%;
        overflow-y: auto;
        background: #f8f9fa;
        border-right: 1px solid #e5e5e5;
    }
    .main-content {
        height: 88%;
        overflow-y: auto;
    }
    .sidebar::-webkit-scrollbar {
        width: 5px;
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 10px;
    }
    #mapChart {
        width: 100%;
        height: 600px;
        position: relative;
        overflow: hidden; 
    }
    #mapTooltip{
        position: absolute;
        display: none;
        background: #fff;
        border: 1px solid #ccc;
        padding: 10px 12px;
        border-radius: 6px;
        font-size: 13px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        pointer-events: none;
        z-index: 1000;
    }
    #indiaMap {
        width: 100%;
        height: 100%;
    }
    #indiaMap svg {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: contain;
    }

    #indiaMap svg path {
        transition: fill 0.3s ease;
        cursor: pointer;
    }

    .map-legend {
        position: absolute;
        bottom: 10px;
        right: 15px;
        background: #fff;
        padding: 0 5px;
        border-radius: 6px;
        font-size: 9px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        min-width: 120px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 4px;
    }

    .legend-item:last-child {
        margin-bottom: 0;
    }

    .legend-item span {
        width: 10px;
        height: 10px;
        display: inline-block;
        margin-right: 4px;
        border-radius: 2px;
    }

    thead th {
        position: sticky !important;
        top: 0;
        /* z-index: 2; */
    }
    .filter-select{
        height:44px;
        border:1px solid #ff8800;
        border-radius:8px;
        color:#444;
        font-size:14px;
    }

    .filter-select:focus{
        border-color:#ff8800;
        box-shadow:0 0 0 .15rem rgba(255,136,0,.15);
    }

    .filter-btn{
        height:44px;
        min-width:110px;
        border-radius:8px;
        font-weight:600;
    }

    .row.gx-2 > *{
        padding-left:6px;
        padding-right:6px;
    }
</style>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-auto sidebar pt-4 pb-4">
            <div class="d-flex flex-column justify-content-md-center text-center">
            
                <a href="{{ route('cicse.schoollist') }}" class="box"><div>
                    <img class="img-fluid" src="{{ asset('assets/icons/schools.svg') }}" alt=""></div><span>School Summary</span>
                </a>
                <a href="{{ route('schools.assessement.status') }}" class="box"><div>
                    <img class="img-fluid" src="{{ asset('assets/icons/fa.svg') }}" alt=""></div><span>Payment & Assessements Status</span>
                </a>                
                @if(Auth::user()->role_id == 9)         
                <a href="{{ route('cicse.addschool') }}" class="box"><div>
                    <img class="img-fluid" src="{{ asset('assets/icons/add-school.svg') }}" alt=""></div><span>School Registration</span>
                </a>
                <a href="{{ route('view.superadmin') }}" class="box"><div>
                    <img class="img-fluid" src="{{ asset('assets/icons/viewer.svg') }}" alt=""></div><span>Admin Roles</span>
                </a>
                
                <a href="{{ route('training.resources') }}" class="box"><div>
                    <img class="img-fluid" alt="" src="{{asset('assets/icons/trainer-manual2.svg') }}" target="_blank"></div><span>
                        Training Resources</span>
                </a>

                @endif
            </div>
        </div>

        <!-- MAIN DASHBOARD -->
        <div class="col pt-4 pb-4 mb-5 main-content">

            <div class="row align-items-center gx-2 gy-2 mb-4">
                <form id="filterForm" method="GET" action="{{ route('cicse.dashboard') }}" class="d-flex flex-wrap align-items-center gx-2 gy-2 w-100">
                    <div class="col-lg">        
                        <select class="form-control filter-select" id="region" name="region">
                            <option value="">All Regions</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->name }}|{{ $region->id }}"
                                    {{ $selectedRegion == $region->name.'|'.$region->id ? 'selected' : '' }}>
                                    {{ $region->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg">            
                        <select class="form-control filter-select" id="zone" name="zone">
                            <option value="">All Zones</option>
                        </select>
                    </div>

                    <div class="col-lg">            
                        <select class="form-control filter-select" id="state" name="state">
                            <option value="">All States</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}|{{ $state->name }}"
                                    {{ $selectedState == $state->id.'|'.$state->name ? 'selected' : '' }}>
                                    {{ $state->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg">            
                        <select class="form-control filter-select" id="district" name="district">
                            <option value="">All Districts</option>
                        </select>
                    </div>
                    <div class="col-lg">
                        <select class="form-control filter-select" name="academic_year" id="academic_year">
                            <option value="{{ $previousAcademicYear }}"
                                {{ $selectedYear == $previousAcademicYear ? 'selected' : '' }}>
                                 Year: {{ $previousAcademicYear }}
                            </option>
                            <option value="{{ $academicYear }}"
                                {{ $selectedYear == $academicYear ? 'selected' : '' }}>
                                 Year: {{ $academicYear }}
                            </option>
                        </select>
                    </div>
                    <div class="col-lg">
                        <a href="{{ route('cicse.dashboard') }}" class="form-control btn btn-secondary border">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                    </div>                    
                </form>
            </div>

            <!-- TOP CARDS -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stat-card green">
                        <div class="stat-content">
                            <p>Registered Schools</p>
                            <h2 class="counter" data-target="{{ $regSchools ?? 0 }}">0</h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-school"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card yellow">
                        <div class="stat-content">
                            <p>Registered Trainers</p>
                            <h2 class="counter" data-target="{{ $regTrainers ?? 0 }}">0</h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card blue">
                        <div class="stat-content">
                            <p>Total Students</p>
                            <h2 class="counter" data-target="{{ $totalStudents ?? 0 }}">0</h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CHARTS -->
            <div class="row g-3 mb-4">

                <div class="col-12 col-md-4">
                    <div class="card shadow p-2" style="height:350px;">
                        <div class="card-header fw-bold">School Completion Status</div>
                        <div class="card-body p-2">
                            <canvas id="schoolStatusChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card shadow p-2" style="height:350px;">
                        <div class="card-header fw-bold">Student Completion Status</div>
                        <div class="card-body p-2">
                            <canvas id="studentSummaryChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card shadow p-2" style="height:350px;">
                        <div class="card-header fw-bold">Top 5 Schools by Status</div>
                        <div class="card-body p-2">
                            <canvas id="topSchoolsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="card shadow p-2" style="height:400px;">
                        <div id="skillChart"></div>
                    </div>
                </div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-6">
                    <div class="card shadow p-2" style="height:400px;">
                        <div id="healthPie"></div>
                    </div>
                </div>
                <div class="col-6 col-md-6">
                    <div class="card shadow p-2" style="height:400px;">
                        <h5 class="card-title text-center mt-2 mb-3 text-dark">State-wise Data</h5>
                        <div id="mapChart">
                            <div id="indiaMap">
                                {!! file_get_contents(public_path('assets/uploads/map.svg')) !!}
                            </div>
                            <div class="map-legend mt-3"></div>
                            <div id="mapTooltip"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="stateModal" tabindex="-1" role="dialog" aria-labelledby="stateModalTitle" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-xl modal-dialog-centered">
        <div class="modal-content">        
            <div class="modal-header">
                <h5 class="modal-title">Schools Detail</h5>
                <button type="button" class="close" data-dismiss="modal">
                <span>&times;</span>
                </button>
            </div>

            <div class="modal-body" id="stateModalBody">
                <table border="1" id="schoolTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>School Code</th>
                            <th>School Name</th>
                            <th>Region</th>
                            <th>Registered</th>
                            <th>Completed</th>
                            <th>Ongoing</th>
                            <th>Yet to Start</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <!-- <div class="text-center p-4">Loading...</div> -->
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/packed-bubble.js"></script>
<script src="https://code.highcharts.com/modules/no-data-to-display.js"></script>


<script>

    let selectedZone = @json($selectedZone);
    let selectedDistrict = @json($selectedDistrict);

    /**
     * Load Zones
     */
    function loadZones(submitForm = false) {
        let regionValue = $("#region").val();
        const selectZone = $("#zone");
        selectZone.html('<option value="">All Zones</option>');
        if (!regionValue) {
            if (submitForm) {
                $("#filterForm").submit();
            }
            return;
        }

        let regionId = regionValue.split('|')[1];

        $.ajax({
            url: "{{ route('school.getZones') }}",
            type: "POST",
            data: {
                regionId: regionId,
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                $.each(response, function (index, zone) {
                    selectZone.append(`
                        <option value="${zone.name}"
                            ${selectedZone === zone.name ? 'selected' : ''}>
                            ${zone.name}
                        </option>
                    `);
                });
                if (submitForm) {
                    $("#filterForm").submit();
                }
            }
        });
    }

    /**
     * Load Districts
     */
    function loadDistricts(submitForm = false) {
        let stateValue = $("#state").val();
        const selectDistrict = $("#district");
        selectDistrict.html('<option value="">All Districts</option>');
        if (!stateValue) {
            if (submitForm) {
                $("#filterForm").submit();
            }
            return;
        }

        let stateId = stateValue.split('|')[0];

        $.ajax({
            url: "{{ route('get-districts',':id') }}".replace(':id', stateId),
            type: "GET",
            success: function (data) {
                $.each(data, function (id, name) {
                    let value = id + "|" + name;
                    selectDistrict.append(`
                        <option value="${value}"
                            ${selectedDistrict === value ? 'selected' : ''}>
                            ${name}
                        </option>
                    `);
                });
                if (submitForm) {
                    $("#filterForm").submit();
                }
            }
        });
    }

    $("#region").on("change", function () {
        selectedZone = "";
        loadZones(true);
    });

    $("#state").on("change", function () {
        selectedDistrict = "";
        loadDistricts(true);
    });

    $("#zone, #district, #academic_year").on("change", function () {
        $("#filterForm").submit();
    });

    /**
     * Page Load
     */
    $(document).ready(function () {
        if ($("#region").val() !== "") {
            loadZones(false);
        }
        if ($("#state").val() !== "") {
            loadDistricts(false);
        }
    });


    document.addEventListener("DOMContentLoaded", function () {

        //  Animate Counters 
        const counters = document.querySelectorAll('.counter');
        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText.replace(/,/g, '');
                const increment = Math.ceil(target / 100);

                if (count < target) {
                    const newCount = count + increment;
                    counter.innerText = newCount.toLocaleString();
                    setTimeout(updateCount, 20);
                } else {
                    counter.innerText = target.toLocaleString();
                }
            };
            updateCount();
        });

        const doughnutData = [
            {{ $completedSchools ?? 0 }},
            {{ $ongoingSchools ?? 0 }},
            {{ $yetToStartSchools ?? 0 }}
        ];

        new Chart(document.getElementById('schoolStatusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Ongoing', 'Yet To Start'],
                datasets: [{
                    data: doughnutData,
                    backgroundColor: ['#396afc', '#ffcb08', '#ec0000'],
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 10,
                        bottom: 20,
                    }
                },
                plugins: { 
                    legend: { 
                        display: true,
                        position: 'bottom', 
                        labels: { 
                            boxWidth: 15, 
                            boxHeight: 12, 
                            padding: 12,
                        }
                    }
                }
            }
        });

        // STUDENT SUMMARY BAR
        new Chart(document.getElementById('studentSummaryChart'), {
            type: 'bar',
            data: {
                labels: ['Total', 'Completed', 'Ongoing', 'Yet To Start'],
                datasets: [{
                    label: 'Students',
                    data: [
                        {{ $totalStudents }},
                        {{ $totalCompleted }},
                        {{ $totalOngoing }},
                        {{ $totalYetToStart }}
                    ],
                    backgroundColor: ['#396afc', '#28a745', '#ffcb08', '#ec0000']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });

        const allSchools = @json($topSchools);

        function getTop5(type) {
            return [...allSchools]
                .sort((a, b) => b[type] - a[type])
                .slice(0, 5);
        }

        function prepareData(type) {
            const top5 = getTop5(type);

            return {
                labels: top5.map(item => item.school_code),
                data: top5.map(item => item[type])
            };
        }

        const colors = {
            completed: '#28a745',
            ongoing: '#ffcb08',
            yet_to_start: '#ec0000'
        };

        // Default = Completed
        let currentType = 'completed';
        let initialData = prepareData(currentType);

        const topChart = new Chart(document.getElementById('topSchoolsChart'), {
            type: 'bar',
            data: {
                labels: initialData.labels,
                datasets: [{
                    label: 'Top 5 Completed',
                    data: initialData.data,
                    backgroundColor: colors[currentType]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: { beginAtZero: true }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 15,
                            boxHeight: 12,
                            padding: 12,
                            generateLabels: function () {
                                return [
                                    { text: 'Completed', fillStyle: '#28a745' },
                                    { text: 'Ongoing', fillStyle: '#ffcb08' },
                                    { text: 'Yet To Start', fillStyle: '#ec0000' }
                                ];
                            }
                        },
                        onClick: function (e, legendItem) {

                            let type = 'completed';
                            if (legendItem.text === 'Ongoing') type = 'ongoing';
                            if (legendItem.text === 'Yet To Start') type = 'yet_to_start';

                            const newData = prepareData(type);

                            topChart.data.labels = newData.labels;
                            topChart.data.datasets[0].data = newData.data;
                            topChart.data.datasets[0].backgroundColor = colors[type];
                            topChart.data.datasets[0].label = 'Top 5 ' + legendItem.text;

                            topChart.update();
                        }
                    }
                }
            }
        });


        // indian map with hight chart
        
        const tooltip = document.getElementById("mapTooltip");
        const StateMapUrl = "https://active.cisce.org/api/states-data";

        let FitnessMap = [];
        fetch(StateMapUrl, {
            
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error(`API response not OK: ${response.status}`);
            return response.json();
        })
        .then(result => {
            FitnessMap = result.stateData || (result.data && result.data.stateData) || [];
            buildIndiaMap(FitnessMap);
            renderIndiaMap();
        })
        .catch(error => {
            console.error("Could not load mapdata API:", error);
        });
        
        const stateCodeMap = {
            "Andhra Pradesh": "INAP",
            "Arunachal Pradesh": "INAR",
            "Assam": "INAS",
            "Bihar": "INBR",
            "Chhattisgarh": "INCT",
            "Goa": "INGA",
            "Gujarat": "INGJ",
            "Haryana": "INHR",
            "Himachal Pradesh": "INHP",
            "Jharkhand": "INJH",
            "Karnataka": "INKA",
            "Kerala": "INKL",
            "Madhya Pradesh": "INMP",
            "Maharashtra": "INMH",
            "Manipur": "INMN",
            "Meghalaya": "INML",
            "Mizoram": "INMZ",
            "Nagaland": "INNL",
            "Odisha": "INOR",
            "Punjab": "INPB",
            "Rajasthan": "INRJ",
            "Sikkim": "INSK",
            "Tamil Nadu": "INTN",
            "Telangana": "INTG",
            "Tripura": "INTR",
            "Uttar Pradesh": "INUP",
            "Uttarakhand": "INUT",
            "West Bengal": "INWB",
            "Delhi": "INDL",
            "Andaman and Nicobar Islands": "INAN",
            "Chandigarh": "INCH",
            "Dadra and Nagar Haveli": "INDH",
            "Daman and Diu": "INDD",
            "Jammu and Kashmir": "INJK",
            "Ladakh": "INLA",
            "Lakshadweep": "INLD",
            "Puducherry": "INPY"
        };

        const stateData = {};
        const codeToStateMap = Object.fromEntries(
            Object.entries(stateCodeMap).map(([name, code]) => [code, name])
        );

        // India Map with multiple fallback options

        function buildIndiaMap(FitnessMap){
            FitnessMap.forEach(item => {
                let stateName = item.name;
                const code = stateCodeMap[stateName];

                if (code) {
                    stateData[code] = item;
                }
            });
        }      

        function renderIndiaMap(){

            const mapColors = ['#fe4a5d','#ffaa62','#ffd26e','#74c4d6','#a3d55f','#6bc04b','#00953b'];

            function generateLegend() {
                const legendContainer = document.querySelector('.map-legend');
                if (!legendContainer) return;

                legendContainer.innerHTML = '';

                mapColors.forEach((color, index) => {
                    let min = index * 25;
                    let max = (index + 1) * 25;

                    let label = index === mapColors.length - 1 
                        ? `${min}+` 
                        : `${min}–${max}`;

                    const item = document.createElement('div');
                    item.className = 'legend-item';
                    item.innerHTML = `<span style="background:${color}"></span> ${label} Schools`;

                    legendContainer.appendChild(item);
                });
            }

            generateLegend();


            function getBaseColor(schools) {
                if (!schools) return '#bbb';

                let index = Math.floor((schools - 1) / 25);
                if (index >= mapColors.length) index = mapColors.length - 1;

                return mapColors[index] + 'CC';
            }


            document.querySelectorAll("#indiaMap svg path").forEach(path => {
                const code = path.id;
                const data = stateData[code];
                const schools = data ? data.schools : 0;

                path.dataset.baseColor = getBaseColor(schools);
                path.style.fill = path.dataset.baseColor;

                path.addEventListener("mouseenter", function () {
                    const baseColor = this.dataset.baseColor;

                    this.style.fill = baseColor.replace(/CC$/, 'FF'); 

                    if (!data) {
                        const stateName = codeToStateMap[code] || "Unknown State";
                        tooltip.innerHTML = `<strong>${stateName}</strong><br>No Data`;
                    } else {
                        tooltip.innerHTML = `
                            <strong>${data.name}</strong><br>
                            Schools: ${data.schools}<br>
                            Trainers: ${data.trainers}<br>
                            Students: ${data.students}
                        `;
                    }

                    tooltip.style.display = "block";
                });

                path.addEventListener("mousemove", function (e) {
                    const container = document.getElementById("mapChart");
                    const rect = container.getBoundingClientRect();

                    let left = e.clientX - rect.left + 10;
                    let top = e.clientY - rect.top - 40;

                    if (left + 150 > rect.width) left -= 160;
                    if (top < 0) top = 10;

                    tooltip.style.left = left + "px";
                    tooltip.style.top = top + "px";
                });

                path.addEventListener("mouseleave", function () {
                    this.style.fill = this.dataset.baseColor;
                    tooltip.style.display = "none";
                });

                path.addEventListener("click", function () {
                    const stateCode = code; 
                    // Open modal
                    const modal = new bootstrap.Modal(document.getElementById('stateModal'));
                    modal.show();

                    // Show loading
                    const modalBody = document.getElementById('stateModalBody');
                    modalBody.innerHTML = `<div class="text-center p-4">Loading...</div>`;


                    fetch(`https://active.cisce.org/api/states-details/${stateCode}`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('Network response was not ok');
                        return res.json();
                    })
                    .then(res => {
                        const modalBody = document.getElementById('stateModalBody');

                        if (!res.status || !res.data.stateDetails || res.data.stateDetails.length === 0) {
                            modalBody.innerHTML = `
                                <div class="text-center text-danger p-4">
                                    No data found for this state
                                </div>
                            `;
                            return;
                        }

                        let tableHTML = `
                            <div style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>School Code</th>
                                            <th>School Name</th>
                                            <th>Region</th>
                                            <th>Registered</th>
                                            <th>Completed</th>
                                            <th>Ongoing</th>
                                            <th>Yet to Start</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            `;

                            res.data.stateDetails.forEach((item, index) => {
                                tableHTML += `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${item.school_code}</td>
                                        <td>${item.school_name}</td>
                                        <td>${item.region}</td>
                                        <td>${item.registered_students}</td>
                                        <td>${item.completed}</td>
                                        <td>${item.ongoing}</td>
                                        <td>${item.yet_to_start}</td>
                                    </tr>
                                `;
                            });

                        tableHTML += `</tbody></table></div>`;
                        modalBody.innerHTML = tableHTML;
                    })
                    .catch(err => {
                        console.error(err);
                        modalBody.innerHTML = `<div class="text-danger">Failed to load data: ${err.message}</div>`;
                    });
                });
            });
        }

        const skillCategories = @json($skillCategories);
        const skillSeries = @json($skillSeries);
        const bmiCategories = @json($bmiCategories);
        const bmiSeries = @json($bmiSeries);

        if (document.getElementById('skillChart')) {
            try {
                Highcharts.chart('skillChart', {
                    chart: { 
                        type: 'bar',                        
                        backgroundColor: '#ffffff',
                        plotBackgroundColor: '#ffffff',
                    },
                    title: { 
                        text: 'National Fitness Indicator',
                        style: {
                            color: '#000000'
                        }
                    },
                    xAxis: {
                        categories: skillCategories,
                        labels: {
                            style: {
                                fontSize: '13px',
                                fontWeight: '500',                                
                                color: '#000000'
                            }
                        },
                        lineWidth: 0,
                        gridLineWidth: 0
                    },
                    yAxis: {
                        min: 0,
                        labels: { 
                            formatter() { return Math.round(this.value); },
                            style: {                               
                                color: '#000000'
                            }
                        },
                        gridLineWidth: 0,
                        title: { text: null }
                    },
                    legend: { enabled: false },
                    plotOptions: {
                        series: {
                            stacking: 'percent',                            
                            pointPadding: 0,
                            groupPadding: 0,
                            borderWidth: 1,
                            dataLabels: {
                                enabled: true,
                                formatter: function () {
                                    const level = this.series.name.split(' - ')[1] || this.series.name;
                                    return `${level} (${Math.round(this.percentage)}%)`;
                                },
                                style: { textOutline: 'none', fontSize: '12px' }
                            },
                            states: { inactive: { opacity: 1 } },
                            point: {
                                events: {
                                    mouseOver: function () {
                                        const chart = this.series.chart;
                                        chart.series.forEach((s) => {
                                            s.group.attr({ opacity: s.index === this.series.index ? 1 : 0.2 });
                                        });
                                    }
                                }
                            },
                            events: {
                                mouseOut: function () {
                                    this.chart.series.forEach(s => s.group.attr({ opacity: 1 }));
                                }
                            }
                        }
                    },
                    tooltip: {
                        formatter: function() {
                            return `Students: ~${this.y} (${Math.round(this.percentage)}%)`;
                        }
                    },
                    series: skillSeries
                });
            } catch (error) {
                console.error('Skill Chart Error:', error);
                document.getElementById('skillChart').innerHTML = '<div class="map-error">Error loading skill chart</div>';
            }
        }

        const bmiChartData = bmiSeries.map(s => {
            const total = s.data.reduce((a, b) => a + b, 0);

            return {
                name: s.name.replace('Level - ', ''),
                y: total,
                color: s.color
            };
        });

        if (document.getElementById('healthPie')) {
            try {
                Highcharts.chart('healthPie', {
                    chart: {
                        type: 'pie',
                        height: 380,
                        backgroundColor: '#ffffff',
                        plotBackgroundColor: '#ffffff',
                    },
                    title: {
                        text: 'National Health Indicator',
                        style: {
                            color: '#000000'
                        }
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.y}</b> ({point.percentage:.1f}%)'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: [{
                                enabled: true,
                                distance: 20
                            }, {
                                enabled: true,
                                distance: -50,
                                format: '{point.percentage:.1f} %',
                                style: {
                                    fontSize: '1.05em',
                                    textOutline: 'none',
                                    opacity: 0.7
                                },
                                filter: {
                                    operator: '>',
                                    property: 'percentage',
                                    value: 10
                                }
                            }]
                        }
                    },
                    series: [{
                        name: 'Students',
                        colorByPoint: true,
                        data: bmiChartData
                    }]
                });
            } catch (error) {
                console.error('BMI Chart Error:', error);
                document.getElementById('healthPie').innerHTML =
                    '<div class="map-error">Error loading BMI chart</div>';
            }
        }

    });
</script>

@endsection