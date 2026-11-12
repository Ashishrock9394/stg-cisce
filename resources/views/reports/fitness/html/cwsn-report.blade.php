<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CWSN-Report</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        *,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Roboto Condensed", sans-serif;
            font-optical-sizing: auto;
            background-color: #eee;
        }

        page[size="A4"] {
            width: 21cm;
            height: 29.7cm;
            margin: 0;
        }

        .report-page {
            width: 21cm;
            height: 1122px;
            min-height: 1122px;
            max-height: 1122px;
            box-sizing: border-box;
            page-break-after: always;
            overflow: hidden;
        }
    </style>
</head>

<body>
    <table cellpadding="0" cellspacing="0" style="width: 21cm; border-collapse: collapse; margin-left: auto; margin-right: auto; font-family: Roboto Condensed, sans-serif; font-size: 12px; border: 0px; background-color: #fff;">
        <!-- Cover Page 1 -->
        <tr>
            <td>
                <table cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse; font-family: Roboto Condensed, sans-serif; font-size: 12px; border: 0;">
                    <tr style="background-color: #f28f18; height: 140px; ">
                        <td style="vertical-align: top;">
                            <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px; height: 100%;">
                                <tr>
                                    <td style="width:180px;"></td>
                                    <td style="position: relative; vertical-align: top; width: 200px; height: 100%;">
                                        <img src="{{ asset('assets/reports/green-dot.jpg')}}" alt="" style="width: 50px; height:50px; position: relative; left:-50px; top:0;">
                                        <div style="position: absolute; top: 0; display: flex; align-items: flex-start; z-index: 10; width: 200px; overflow: hidden;">
                                            <div class="logo" style="position: relative; width: 180px;">
                                                <span style="position: absolute; top:0; left:0; width: inherit; padding: 10px; box-sizing: border-box;">
                                                    <img src="{{ asset('assets/reports/cisce-logo.png')}}" alt="" style="width: inherit; margin-top: 20px;">
                                                </span>
                                                <img src="{{ asset('assets/reports/logo-bg.jpg')}}" alt="" style="width: 200px; margin-top: -50px;">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="margin-left: 30px; margin-right: 50px; margin-top: 40px; font-weight: 600; font-size: 24px;">Council for the Indian School Certificate Examinations</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-collapse: collapse;">
                            <div style="width: 86%; position: relative; border-collapse: collapse;">
                                <img src="{{ asset('assets/reports/report-graphic.png')}}" alt="" style="position: absolute; top:25%; right:-55px; width: 200px; border-collapse: collapse;">
                                <img src="{{ asset('assets/reports/cwsn.webp')}}" alt="" style="width: inherit;">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0;">
                                <tr>
                                    <td style="background-color:#F28F0C;">
                                        <div style="position:relative;">
                                            <span style="position:absolute; top:-44px; background:rgb(0 0 0/50%); padding:10px; width:100%; z-indix:2; box-sizing: border-box; text-align:center; color:#fff; font-size:16px; font-weight:600; letter-spacing: 2px; text-transform: uppercase;">Session: {{ $academicYear }}</span>
                                            <img src="{{ asset('/assets/reports/aa-bg.png')}}" alt="" style="width:198px; position: relative; top: -5px;">
                                        </div>
                                    </td>
                                    <td style="vertical-align: top; width: 100%;">
                                        <table cellpadding="0" cellspacing="0" style="width: 90%; border: 0;">
                                            <tr>
                                                <td style="height: 50px;"></td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 10px 25px 10px 40px; font-size: 20px; background:#1c9b3e; color:#fff; font-size: 24px; font-weight: 500; position:relative;">Physical Health and Fitness Assessment (CwSN)<span style=" position:absolute; top:46px; right:-20px;"><img src="{{ asset('assets/reports/yellow-bg.jpg')}}" alt="" style="width:20px;"></span></td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 30px 30px 0px 50px; font-size: 24px; font-weight: 500;">Personal Profile</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 10px 30px 10px 50px; font-size: 16px; color: #333;">
                                                    <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                        <tr>
                                                            <td colspan="2" style="padding: 6px 0;">
                                                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                    <tr>
                                                                        <td><span style="display: inline-block; margin-right: 5px;">Name</span></td>
                                                                        <td style="border-bottom: 1px solid #ccc; width: 100%; text-align: center; font-weight: 600; font-size: 18px;">{{ $studentsData->student_name }}</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 6px 0;">
                                                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px;">
                                                                    <tr>
                                                                        <td><span style="display: inline-block; margin-right: 5px;">Class&nbsp;&&nbsp;Section</span></td>
                                                                        <td style="border-bottom: 1px solid #ccc; width: 100%; text-align: center; font-weight: 600;"> {{ $studentsData->display_classname }} {{ $studentsData->section }}</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td style="padding: 6px 0; width:55%;">
                                                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                    <tr>
                                                                        <td><span style="display: inline-block; margin-right: 5px; margin-left: 5px;">Roll&nbsp;No.</span></td>
                                                                        <td style="border-bottom: 1px solid #ccc; width: 100%; text-align: center; font-weight: 600;">{{ $studentsData->rollno ?? ''}}</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" style="padding: 6px 0;">
                                                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                    <tr>
                                                                        <td><span style="display: inline-block; margin-right: 5px;">School</span></td>
                                                                        <td style="border-bottom: 1px solid #ccc; width: 100%; text-align: center; font-weight: 600;">{{ $studentsData->school_name }}</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 6px 0;">
                                                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                    <tr>
                                                                        <td><span style="display: inline-block; margin-right: 5px;">Code</span></td>
                                                                        <td style="border-bottom: 1px solid #ccc; width: 100%; text-align: center; font-weight: 600;">{{ $studentsData->school_code }}</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td style="padding: 6px 0;">
                                                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                    <tr>
                                                                        <td><span style="display: inline-block; margin-right: 5px; margin-left: 5px;">APAAR&nbsp;ID&nbsp;(Optional)</span></td>
                                                                        <td style="border-bottom: 1px solid #ccc; width: 100%; text-align: center; font-weight: 600;">{{ $studentsData->apaarId ?? ''}}</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" style="padding: 6px 0;">
                                                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                    <tr>
                                                                        <td><span style="display: inline-block; margin-right: 5px;">CISCE&nbsp;Registration&nbsp;No</span></td>
                                                                        <td style="border-bottom: 1px solid #ccc; width: 100%; text-align: center; font-weight: 600;">{{ $studentsData->admissionnumber }}</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 6px 0;">
                                                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                    <tr>
                                                                        <td><span style="display: inline-block; margin-right: 5px;">DOB</span></td>

                                                                        @php
                                                                            use Carbon\Carbon;

                                                                            $dob = Carbon::parse($studentsData->dob); 
                                                                            $formattedDob = $dob->format('d M Y'); // 05 Jul 2019
                                                                            $age = $dob->age; // will calculate age automatically
                                                                           

                                                                            if (strtolower($studentsData->gender) === 'male') {
                                                                                $gender = 'Boy';
                                                                            } else {
                                                                                $gender = 'Girl';
                                                                            }
                                                                        @endphp
                                                                        <td style="border-bottom: 1px solid #ccc; width: 100%; text-align: center; font-weight: 600;"> {{ $formattedDob }} ({{ $age }} Years)</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td style="padding: 6px 0;">
                                                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                    <tr>
                                                                        <td><span style="display: inline-block; margin-right: 5px; margin-left: 5px;">Gender</span></td>
                                                                        <td style="border-bottom: 1px solid #ccc; width: 100%; text-align: center; font-weight: 600;">{{ $gender }}</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" style="padding: 6px 0;">
                                                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px;">
                                                                    <tr>
                                                                        <td style="padding: 0px 0px 2px 0px;"><span style="display: inline-block; margin-right: 5px;">Disability&nbsp;&nbsp;Category</span></td>
                                                                        <td style="border-bottom: 1px solid #ccc; width: 100%; text-align: center; font-weight: 600; padding: 2px 0px;"> {{ $studentsData->disability_category }}</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            
                                                        </tr>
                                                        <tr>
                                                            <td style="height: 15px;"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" style="padding: 6px 0;">
                                                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                    <tr>
                                                                        <td><span style="display: inline-block; margin-right: 5px; font-weight:500;">Brief Summary</span></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="border-bottom: 1px solid #ccc; width: 100%; text-align: center; font-weight: 600; height: 24px;"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="border-bottom: 1px solid #ccc; width: 100%; text-align: center; font-weight: 600; height: 24px;"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="border-bottom: 1px solid #ccc; width: 100%; text-align: center; font-weight: 600; height: 24px;"></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="height:20px;"></td>
                                            </tr>

                                            <tr>
                                                <td style="text-align:right; padding: 0px 0px 0px 46px; ">
                                                    <div style="float:right; text-align:center;">
                                                    <p style="margin-bottom:0px; color:#666; font-size:10px;">Powered by</p>
                                                    <img src="{{ asset('assets/reports/fitness365-logo-web.png')}}" alt="fitness365 logo" style="height:28px;">
                                                    </div>
                                                </td>
                                            </tr>
                                            
                                        </table>
                                    </td>

                                </tr>
                                <tr>
                                    <td style="background-color: #1c9b3e; height: 30px;"></td>
                                    <td style="background-color: #F28F0C; height: 30px;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Start Page 2 -->
        <tr>
            <td style="vertical-align: top; height: 100%;">
                <table border="1" cellpadding="0" cellspacing="0" style="page-break-before: always; width: 100%; border-collapse: collapse; font-family: Roboto Condensed, sans-serif; font-size: 14px; border:0px solid transparent;">
                    <!-- Inner page Header Area (Page 2) -->
                    <tr style="height: 100px;">
                        <td style="vertical-align: top;">
                            <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px; height: 100%; border:0;">
                                <tr>
                                    
                                   <!--  <td style="width:300px;">
                                        <div style="margin-left: 30px; margin-top: 30px;">
                                            School Logo -1
                                        </div>
                                    </td> -->


                                    <td style="position: relative; vertical-align: top; width: 300px; height: 100%; " >
                                        @if(!empty($studentsData->logo))
                                        <div style="position: absolute; top: 17px; left:30px; display: flex; align-items: center; z-index: 1; width: auto; overflow: hidden;">
                                            <img src="{{ asset('assets/uploads/logos/' . $studentsData->logo )}}" alt="" style="width: auto; height:60px;">
                                        </div>
                                        @else 
                                        <div style="margin-left: 30px; margin-top: 30px;">
                                            School Logo -1
                                        </div>
                                        @endif 
                                    </td>


                                    <td rowspan="2" style="position: relative; vertical-align: top; width: auto; height: 100%; text-align: right;">
                                        <div style="position: absolute; top: 30px; right:30px; display: flex; align-items: center; z-index: 1; width: auto; overflow: hidden;">
                                            <img src="{{ asset('assets/reports/cisce-logo.png')}}" alt="" style="width: auto; height:60px;">
                                        </div>
                                        <img src="{{ asset('assets/reports/inner-header-bg.png')}}" alt="" style="width: 450px; height:auto; position: relative; right:0px; top:0;">
                                    </td>

                                </tr>
                            </table>
                        </td>

                    </tr>
                    <!-- Inner page Content (Page 2) -->
                    <tr>
                        <td>
                            <table cellpadding="0" cellspacing="0" style="width: 94%; border: 0; border-collapse: collapse; margin: auto; ">
                                <tr>
                                    <td style="border-bottom: 3px solid #1c9b3e;">
                                        <div style="background: #1c9b3e; float:left; display: inline-flex; align-items: center; color: #fff; font-size: 18px; font-weight: 600; height: 32px;">
                                            <div style="float: left; padding: 1px 0px 0px 10px; margin-bottom: -3px;">Physical Fitness Assessment for {{ $studentsData->display_classname }}-{{ $studentsData->section }}</div>
                                            <div style="float:left; transform: skew(26deg,0deg); display:inline-block; width: 20px; height: 32px; background: #1c9b3e; position: relative; right: -10px;"></div>
                                        </div>
                                    </td>
                                </tr>
                                
                                @foreach($orderedReportData as $key => $value)

                                @php
                                    $currentTerm = $value->get('Current_Term', collect())->first();
                                    $previousTerm = $value->get('Previous_Term', collect())->first();
                                    $displayKey = str_contains($key, 'Body Composition')  
                                        ? str_replace('Body Composition (BMI)', 'BMI (Body Mass Index)', $key) 
                                        : $key;
                                @endphp
                               
                                @if($key === 'Body Composition (BMI)')
                                <tr> <td style="height: 15px;"></td></tr>
                                <tr>
                                    <td style="padding: 6px 10px 6px 0px; font-size: 20px; color: #000; font-size: 18px; font-weight: 600;">{{ $displayKey }}</td> 
                                </tr>

                                <tr>
                                    <td>
                                        <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0; border-collapse: collapse;">
                                            <tr>
                                                <td style="vertical-align: top;">
                                                    <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                                                        <tr style="font-size: 13px; line-height: 1.25rem;">
                                                            <td style="border-top: 1px solid orange; border-left: 1px solid orange; border-right: 1px solid orange; border-bottom: 0px solid transparent; border-collapse: collapse; padding:5px 15px; vertical-align: middle;">
                                                                <ul style="margin-left: 15px;">
                                                                    <li>Height recorded in cm and mm</li>
                                                                    <li>Weight will be recorded in kilogram (kg) and grams(gms)</li>
                                                                </ul>
                                                            </td>
                                                            <td style="border-top: 1px solid orange; border-right: 1px solid orange; border-bottom: 0px solid transparent; border-collapse: collapse; padding:5px 15px; vertical-align: top; padding-bottom:10px;">
                                                                <table border="0" cellpadding="0" cellspacing="0">
                                                                    <tr>
                                                                        <td>Body Mass Index =</td>
                                                                        <td style="padding:0 10px;">
                                                                        <p style="border-bottom: 1px solid #c5c5c5; padding-bottom: 2px; margin:0;">Weight (in kg)</p>
                                                                        <p style="padding-top: 0px; margin:0;">Height (in m)2</p>
                                                                        </td>
                                                                    </tr>
                                                                </table>

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 100%;" colspan="2">
                                                                <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; border: 1px solid orange; font-size: 13px; border-collapse: collapse; color:#333;">
                                                                    <tr style="background-color: #fecd0a;">
                                                                        <td style="width: 15%; background-color: orange; padding: 0px 4px 2px 6px; border: 1px solid orange; color:#fff; text-align: center; font-weight: bold;" rowspan="2">Current Term</td>
                                                                        <td style="padding: 4px 0px 4px 0px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Date</td>
                                                                        <td style="padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Weight (kg)</td>
                                                                        <td style="padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Height (cm)</td>
                                                                        <td style="padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">BMI</td>
                                                                        <td style="padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Level</td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td style="padding: 4px 0px 4px 0px; font-weight: 500; color:#000; text-align: center;">{{ $value['Current_Term'][0]['created_at'] ?? '---'}}</td>
                                                                        <td style="padding: 0px 4px 2px 6px; text-align: center; border: 1px solid orange;"> {{ $value['Current_Term'][0]['weight'] ?? '---'}}</td>
                                                                        <td style="padding: 0px 4px 2px 6px; text-align: center; border: 1px solid orange;">  {{ $value['Current_Term'][0]['height'] ?? '---'}}</td>
                                                                        <td style="padding: 0px 4px 2px 6px; text-align: center; border: 1px solid orange;">{{ $value['Current_Term'][0]['score'] ?? '---'}}</td>
                                                                        <td style="padding: 0px 4px 2px 6px; text-align: center; border: 1px solid orange;">{{ $value['Current_Term'][0]['bmiLevel'] ?? '---'}}</td>
                                                                    </tr>

                                                                </table>
                                                            </td>
                                                        </tr>

                                                    </table>

                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; border-top: 1px solid transparent; border-left: 1px solid orange; border-right: 1px solid orange; border-bottom: 1px solid orange; font-size: 13px; border-collapse: collapse; color:#333;">
                                                        <tr>
                                                            <td style="width: 15%; border-top: 1px solid #fecd0a; background-color: #fecd0a; color: #000;text-align: center; font-weight: bold; padding: 5px;">Recommendation</td>
                                                            <td style="width: 85%; padding: 4px;">{{ $value['Current_Term'][0]['recommendation'] ?? '---' }}</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                @else

                                <tr> <td style="height: 15px;"></td></tr>
                                <tr>
                                    <td style="padding: 6px 10px 6px 0px; font-size: 20px; color:#000; font-size: 18px; font-weight: 600;">{{ $displayKey }}</td> 
                                </tr>

                                <tr>
                                    <td>
                                        <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0; border-collapse: collapse;">
                                            <tr>
                                                <td style="vertical-align: top;">
                                                    <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                                                        <tr>
                                                            <td style="width: 100%">
                                                                <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; border: 1px solid orange; border-bottom: 1px solid orange; font-size: 14px; border-collapse: collapse; color:#333;">
                                                                    <tr style="background-color: #fecd0a;">
                                                                        <td style="width:15%; background-color:orange; border: 1px solid orange; color:#fff; text-align: center; font-weight: bold;" rowspan="2">Current Term</td>
                                                                        <td style="width:15%; padding: 4px 4px 4px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Date</td>
                                                                        <td style="width:25%; padding: 4px 4px 4px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Score</td>
                                                                        <td style="width:15%; padding: 4px 4px 4px 6px; border: 1px solid orange; color:#000; font-weight: bold; text-align: center;">NI</td>
                                                                        <td style="width:15%; padding: 4px 4px 4px 6px; border: 1px solid orange; color:#000; font-weight: bold; text-align: center;">AFZ</td>
                                                                        <td style="width:15%; padding: 4px 4px 4px 6px; border: 1px solid orange; color:#000; font-weight: bold; text-align: center;">HFZ</td>
                                                                    </tr>
                                                                    <tr>                
                                                                        <td style="padding: 4px 4px 4px 6px; font-weight: 500; color:#000; text-align: center;">{{ $value['Current_Term'][0]['created_at'] ?? '---'}}</td>
                                                                        <td style="text-align: center;">{{ $value['Current_Term'][0]['score'] ?? '---'}}</td>
                                                                        <td style="text-align: center;">{{ $value['Current_Term'][0]['ni'] ?? '---'}}</td>
                                                                        <td style="text-align: center;">{{ $value['Current_Term'][0]['afz'] ?? '---'}}</td>
                                                                        <td style="text-align: center;">{{ $value['Current_Term'][0]['hfz'] ?? '---'}}</td>
                                                                        <!-- <td style="text-align: center;">---</td> -->
                                                                    </tr>

                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; border-top: 1px solid transparent; border-left: 1px solid orange; border-right: 1px solid orange; border-bottom: 1px solid orange; font-size: 13px; border-collapse: collapse; color:#333;">
                                                        <tr>
                                                            <td style="width: 15%; border-top: 1px solid #fecd0a; background-color: #fecd0a; color: #000; text-align: center; font-weight: bold; padding:5px;">Recommendation</td>
                                                            <td style="width: 85%; padding: 4px;">{{ $value['Current_Term'][0]['recommendation'] ?? '---' }}</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>                    
                                @endif

                                @endforeach

                                <tr>
                                    <td style="height: 10px;"></td>
                                </tr>
                               
                                <tr>
                                    <td>
                                        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; border: 1px solid #1c9b3e; font-size: 14px; border-collapse: collapse; color:#fff; text-align:left;">
                                            <tr style="background-color: #1c9b3e;">
                                                <td style="padding: 5px 10px; font-weight: bold; color:#fff; font-size: 16px;" colspan="8">BMI Benchmarks for {{ $age }} years {{ $gender }}</td>
                                            </tr>
                                            <tr style="font-weight: bold; background-color: #fecd0a; font-size: 13px; color: #000;  text-align:center;">
                                                <td style="padding: 5px 10px;">Very Lean</td>
                                                <td style="padding: 5px 10px;">Healthy Fitness Zone</td>
                                                <td style="padding: 5px 10px;">Needs improvement</td>
                                                <td style="padding: 5px 10px;">Health Risk</td>
                                            </tr>                                            
                                            
                                            @if(is_array($getBmiBenchmark) && count($getBmiBenchmark) > 0)
                                            <tr style="text-align:center;">
                                                <td style="padding: 5px 10px; color: #000;">{{ $getBmiBenchmark['very_lean'] ?? 'N/A' }}</td>
                                                <td style="padding: 5px 10px; color: #000;">{{ $getBmiBenchmark['hfz'] ?? 'N/A' }}</td>
                                                <td style="padding: 5px 10px; color: #000;">{{ $getBmiBenchmark['ni'] ?? 'N/A' }}</td>
                                                <td style="padding: 5px 10px; color: #000;">{{ $getBmiBenchmark['health_risk'] ?? 'N/A' }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height: 5px;"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="color: #525252; font-size:12px;">
                                            <strong>Fitness Zone Abbreviations</strong>:- &nbsp;
                                            <strong>NI:</strong> Needs Improvement &nbsp; | &nbsp;
                                            <strong>AFZ:</strong> Adapted Fitness Zone &nbsp; | &nbsp;
                                            <strong>HFZ:</strong> Healthy Fitness Zone
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height: 8px;"></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 13px; letter-spacing: 0.01rem;"><b>Interpretation: </b>You should meet Healthy Fitness Zones (HFZs) for aerobic capacity, flexibility, and body composition; meets Adapted Fitness Zones (AFZs) for selected components where applicable; and falls within the Needs Improvement (NI) zone for components below the applicable fitness-zone standard.
                                    <br><b>Needs: </b>Priorities—Development of upper-body strength/endurance and abdominal strength/endurance. 
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
        <!-- Footer Area (Page 2) -->
        <tr>
            <td>
                <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                    <tr>
                        <td style="height: 30px;"></td>
                    </tr>
                    <tr>
                        <td style="width:94px;">
                            <div style="float: left; position: relative; width: 80px;">
                                <span style="position: absolute; left: 50%; top:50%; transform: translate(-50%, 0); color: #fff; z-index: 1; display: inline-block; padding: 6px 0 0 30px; font-size: 13px; font-weight: 600;">2</span>
                                    <img src="{{ asset('assets/reports/footer-bg.png')}}" alt="" style="width: inherit;">
                            </div>
                        </td>
                        <td style="text-align: right;">
                            <table cellpadding="0" cellspacing="0" style="border: 0px; width: 100%;">
                                <tr>
                                     <td style="height: 14px;"></td>
                                        <td style="height: 14px;"></td>
                                    </tr>
                                <tr>
                                    <td style="font-weight: 400; font-size: 13px; color:#666; text-align:left;">
                                        Physical Health and Fitness Assessment
                                    </td>
                                    <td style="text-align:right; padding: 0px 30px 0px 0px;">
                                        <div style="float:right; text-align:center; position:relative;">
                                            <p style="color:#666; font-size:10px; position:absolute; top:-17px; width:100%; text-align:center;">Powered by</p>
                                                <img src="{{ asset('assets/reports/fitness365-logo-web.png')}}" alt="fitness365 logo" style="height:28px;">
                                        </div> 
                                        </td>
                                </tr>
                            </table>            
                        </td>                        
                    </tr>
                </table>
            </td>
        </tr>
        <!-- End of page 2  -->
        <!-- start page 3 -->
        <tr>
            <td style="vertical-align: top;">
                <table border="1" cellpadding="0" cellspacing="0" style="page-break-before: always; page-break-after: avoid; width: 100%; border-collapse: collapse; font-family: Roboto Condensed, sans-serif; font-size: 12px; border:0px solid transparent;">
                    <!-- Inner page Header Area (Page 2) -->
                    <tr style="height: 100px;">
                        <td style="vertical-align: top;">
                            <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px; height: 100%; border:0;">
                                <tr>
                                    <td style="position: relative; vertical-align: top; width: 300px; height: 100%; " >
                                        @if(!empty($studentsData->logo))
                                        <div style="position: absolute; top: 17px; left:30px; display: flex; align-items: center; z-index: 1; width: auto; overflow: hidden;">
                                            <img src="{{ asset('assets/uploads/logos/' . $studentsData->logo )}}" alt="" style="width: auto; height:60px;">
                                        </div>
                                        @else 
                                        <div style="margin-left: 30px; margin-top: 30px;">
                                            School Logo -1
                                        </div>
                                        @endif 
                                    </td>


                                    <td rowspan="2" style="position: relative; vertical-align: top; width: auto; height: 100%; text-align: right;">
                                        <div style="position: absolute; top: 30px; right:30px; display: flex; align-items: center; z-index: 1; width: auto; overflow: hidden;">
                                            <img src="{{ asset('assets/reports/seqfast-logo.png')}}" alt="" style="width: auto; height:60px;">
                                        </div>
                                        <img src="{{ asset('assets/reports/inner-header-bg.png')}}" alt="" style="width: 450px; height:auto; position: relative; right:0px; top:0;">
                                    </td>

                                </tr>
                            </table>
                        </td>

                    </tr>                    
                </table>
            </td>
        </tr>
        <!-- content page 3 -->
        <tr>
            <td>
                <!-- Inner page Content (Page 3) -->
                <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <table cellpadding="0" cellspacing="0" style="width:94%; border:0; border-collapse:collapse; margin:auto; color:#333; font-size:12px;">

                                <!-- CWSN INTRODUCTION -->
                                <tr>
                                    <td style="border: 1px solid #ED6D1E; padding: 10px 15px 8px 15px; background: #FEFBEF;">

                                        <h3 style="color: #ED6D1E; margin-bottom: 6px; font-size: 18px;">
                                            WHO Guidelines on Physical Activity and Sedentary Behaviour 2020
                                        </h3>

                                        <h4 style="color: #000; margin-bottom: 5px; font-size: 16px;">
                                            Physical Activity Guidelines for CWSN
                                        </h4>

                                        <p style="line-height: 1.25rem;">
                                            <strong>Some physical activity is better than none.</strong>
                                            Even when children are not meeting the recommended activity levels,
                                            doing some physical activity can provide health benefits.
                                        </p>

                                        <p style="line-height: 1.25rem;">
                                            Children should <strong>start with small amounts of physical activity</strong>
                                            and gradually increase the frequency, intensity and duration according
                                            to their abilities.
                                        </p>

                                        <p style="line-height: 1.25rem;">
                                            Physical activity should be appropriate to the child's
                                            <strong>current activity level, health status and physical function</strong>.
                                            When appropriately adapted, the health benefits generally outweigh the risks.
                                        </p>

                                        <p style="line-height: 1.25rem;">
                                            Some children may benefit from guidance from a
                                            <strong>health-care professional or physical activity and disability specialist</strong>
                                            to determine the type and amount of activity that is appropriate for them.
                                        </p>

                                    </td>
                                </tr>
                                <!-- RECOMMENDED PHYSICAL ACTIVITIES FOR CWSN -->
                                <tr id="recommended-physical-activities">
                                    <td style="height:12px;"></td>
                                </tr>

                                <tr id="recommended-activities-heading">
                                    <td>
                                        <h3 id="recommended-activities-title" style="color:#000; font-size:16px; margin:0 0 8px 0;">
                                            Recommended Activities for Improving Fitness in CWSN
                                        </h3>
                                    </td>
                                </tr>

                                <tr id="recommended-activities-content">
                                    <td>
                                        <table id="recommended-activities-table" cellpadding="0" cellspacing="0" style="width:100%; border:0; border-collapse:collapse; color:#333;">

                                            <!-- Row 1 -->
                                            <tr id="recommended-activities-row-1">

                                                <td id="aerobic-activities" style="width:50%; padding:0 25px 8px 0; vertical-align:top;">
                                                    <h4 id="aerobic-activities-title" style="color:#000; margin:0 0 3px 0; font-size:13px;">
                                                        1. Aerobic-Capacity
                                                    </h4>
                                                    <p id="aerobic-activities-description" style="margin:0; line-height:14px; font-size:13px;">
                                                        One-Mile Run/Walk, 20-m PACER and 15-m PACER according to the child's functional ability.
                                                    </p>
                                                </td>    
                                                <td id="flexibility-activities" style="width:50%; padding:0 25px 8px 0; vertical-align:top;">
                                                    <h4 id="flexibility-activities-title" style="color:#000; margin:0 0 3px 0; font-size:13px;">
                                                        2. Flexibility-related Activities
                                                    </h4>

                                                    <p id="flexibility-activities-description" style="margin:0; line-height:14px; font-size:13px;">
                                                            Back-Saver Sit-and-Reach, Shoulder Stretch and Modified Apley Test according to the child's individual ability.
                                                    </p>
                                                </td>                                
                                            </tr>

                                            <!-- Row 2 -->
                                            <tr id="recommended-activities-row-2">
                                                <td id="strength-endurance-activities" style="width:50%; padding:0 0 8px 0; vertical-align:top;">
                                                    <h4 id="strength-endurance-activities-title" style="color:#000; margin:0 0 3px 0; font-size:13px;">
                                                        3. Strength &amp; Endurance
                                                    </h4>
                                                    <p id="strength-endurance-activities-description" style="margin:0; line-height:14px; font-size:13px;">
                                                        Modified Curl-ups, Seated Push-ups, Push-ups, Pull-ups, Trunk Lift, Reverse Curl and 40-Meter Push/Walk activities according to the child's functional ability.
                                                    </p>
                                                </td>
                                                <td id="balance-coordination-activities" style="width:50%; padding:0 0 8px 0; vertical-align:top;">

                                                    <h4 id="balance-coordination-activities-title"
                                                        style="color:#000; margin:0 0 3px 0; font-size:13px;">
                                                        4. Body Composition
                                                    </h4>
                                                    <p id="balance-coordination-activities-description" style="margin:0; line-height:14px; font-size:13px;"> 
                                                        Encourage regular adapted physical activity, active play and healthy eating habits to support healthy body weight according to the child's functional ability and individual needs.
                                                    </p>
                                                </td>

                                            </tr>

                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height:10px;"></td>
                                </tr>  
                                <tr>
                                    <td>
                                        <h3 style="color:#000; font-size:16px; margin:0 0 8px 0;">
                                            Adaptation Notes for {{ $studentsData->disability_category }}<sup>*<sup>
                                        </h3>
                                        <p style="font-size:13px;">{{ $studentsData->adoptation_note }}</p>
                                        </td>
                                </tr>
                                <tr>
                                    <td style="height:10px;"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <span>* These notes are general guides. Always follow the student’s Individualized Education Plan (IEP), medical advice, and personal preferences for the safest and most effective activity.</span>
                                    </td>   
                                </tr> 

                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>  
        <tr>
            <td style="height:100px;"></td>
        </tr>    

        <!-- Footer Area (Page 3) -->
        <tr>
            <td>
                <table cellpadding="0" cellspacing="0" style="width: 94%; border: 0; border-collapse: collapse; margin: auto; color: #333; font-size: 12px;">
                    <tr>
                        <td>
                            <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; border: 1px solid orange; font-size: 14px; border-collapse: collapse; color:#333;">
                                <tr>
                                    <td style="font-weight: 600; padding: 10px 15px; font-size: 14px; color: #000; height:110px; vertical-align:top;">PE Teacher's Observations and Comments (if any)</td>
                                    <td style="font-weight: 600; padding: 10px 15px; font-size: 14px; color: #000; text-align:center; height:100px; vertical-align:bottom;">
                                        {{-- @if($studentsData->hod_sign)
                                            <div style="margin: 10px 0 0 0; text-align:center;">
                                                <img src="{{ asset('assets/uploads/hod-signatures/' . $studentsData->hod_sign) }}" alt="" style="height: 70px;">
                                            </div>
                                        @endif  --}}
                                        <p>Teacher's Signature</p> 
                                    </td>
                                </tr>
                                
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; font-size: 14px; border-collapse: collapse; color:#000;">
                                <tr>
                                    <td style="border: 1px solid transparent; width:50%;">
                                        <div style="padding: 15px; height: 30px; margin: 10px 0; background-color: #fff; border-right: 3px solid #fff;"></div>
                                        <p style="text-align: center; font-weight: 600;">Parent's Signature</p>
                                    </td>
                                    <td style="border: 1px solid transparent; width:50%;">
                                        @if($studentsData->signature)
                                            <div style="margin: 10px 0 0 0; text-align:center;">
                                                <img src="{{ asset('assets/uploads/signatures/' . $studentsData->signature) }}" alt="" style="height: 70px;">
                                            </div>
                                        @endif
                                        <p style="text-align: center; font-weight: 600;">Signature of Principal with Stamp</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 16px;"></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table cellpadding="0" cellspacing="0" style="width: 100%; border:0;">
                    
                    <tr>
                        <td style="background-color: #E60A00; height: 32px; width: 30%; padding: 0 30px; color:#fff;">Physical Health and Fitness Assessment</td>
                        <td style="background-color: #fecd0a; height: 32px; width: 30%; padding: 0 30px; text-align:right;">Powered by <span style="font-weight:500;">fitness365.me</span></td>
                    </tr>
                </table>
            </td>
        </tr>
        <!-- End of page 3  -->
    </table>

</body>

</html>