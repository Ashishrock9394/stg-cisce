<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senior Reports</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        *,
        body { margin: 0; padding: 0; }

        body { font-family: "Roboto Condensed", sans-serif; font-optical-sizing: auto; background-color: #fff; }

        page[size="A4"] { width: 21cm;  height: 29.7cm; margin: 0; padding:0; }
        .act-tbl { width:100%; }
        .act-tbl td { border-bottom:1px solid #EFB9B8; }
        .page-3 td {  margin: 0; padding: 2px 4px;
           white-space:normal; 
            white-space: normal; /* collapses leading spaces/newlines */
        }
        .cell {
            line-height:12px;
        }
        .cell tr {
            border-bottom:1px solid #e5e5e5;
        }

        
    </style>
</head>

<body>
 <!-- Cover Page -->

    <table cellpadding="0" cellspacing="0" style="width: 21cm; border-collapse: collapse; margin-left: auto; margin-right: auto; font-family: Roboto Condensed, sans-serif; font-size: 12px; border: 0px; background-color: #fff;">
        <tr>
            <td>
                <table cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse; font-family: Roboto Condensed, sans-serif; font-size: 12px; border: 0;">
                   
                    <tr>
                        <td style="border-collapse: collapse;">
                            <div style="position: relative; background-color: #fff;">
                                <div style="position: relative; background-color: #F28F0C; margin-top:0px; z-index:0; height:136px;">
                                    <img src="{{ public_path('assets/reports/green-dot.jpg')}}" alt="" style="width: 40px; height:40px; position: relative; left:150px; top:0;">
                                </div>
                             
                                <div style="width: auto; position:absolute; top:-30px; left: 176px; z-index:10; padding:30px 15px 0 15px; display:flex;">
                                <div style="background:#fff; padding:25px 15px 10px 15px; height:140px; float:left;">
                                    <img src="{{ public_path('assets/reports/cisce-logo.png')}}" alt="" style="width: auto; height:100px;">
                                </div>
                                <p style="margin-left:30px; float:left; font-size:24px; padding:34px 20px 20px 0px; width:300px; font-weight:600; line-height:24px; color:#fff;">Council for the Indian School Certificate Examinations</p>
                                </div>
                               
                                <img src="{{ public_path('assets/reports/report-graphic.png')}}" alt="" style="position: absolute; top:26%; right:40px; width: 200px; border-collapse: collapse; z-index:9;">
                               
                                <img src="{{ public_path('assets/reports/report-cover-img.jpg')}}" alt="" style="width: 76%; z-index:2; position: relative; top:0;">
                            </div>
                        
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0;">
                                <tr>
                                    <td style="background-color: #F28F0C;">
                                        <div style="position:relative;">
                                            <span style="position:absolute; top:-48px; background:rgb(0 0 0/50%); padding:10px; width:90%; z-index:2; box-sizing: border-box; text-align:center; color:#fff; font-size:16px; font-weight:600; letter-spacing: 2px;  text-transform: uppercase;">Session: {{ $academicYear }}</span>
                                            <img src="{{ public_path('/assets/reports/aa-bg.png')}}" alt="" style="width:198px; position: relative; top: -3px; height: 570px;">
                                        </div>
                                    </td>
                                    <td style="vertical-align: top; width: 100%;">
                                        <table cellpadding="0" cellspacing="0" style="width: 90%; border: 0;">
                                            <tr>
                                                <td style="height: 20px;"></td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 1px 30px 5px 46px; font-size: 20px; background:#1c9b3e; color:#fff; font-size: 24px; font-weight: 500; position:relative;">Physical Health and Fitness Assessment<span style=" position:absolute; top:44px; right:-21px;"><img src="{{ public_path('assets/reports/yellow-bg.jpg')}}" alt="" style="width:20px;"></span></td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 30px 30px 10px 50px; font-size: 24px; font-weight: 500;">Personal Profile</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 10px 30px 10px 50px; font-size: 16px; color: #333;">
                                                    <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                        <tr>
                                                            <td colspan="2" style="padding: 6px 0;">
                                                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                    <tr>
                                                                        <td><span style="display: inline-block; margin-right: 5px;">Name</span></td>
                                                                        <td style="border-bottom: 1px solid #e5e5e5; width: 100%; text-align: center; font-weight: 600; font-size: 18px;">{{ $studentsData->student_name }}</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 6px 0;">
                                                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px;">
                                                                    <tr>
                                                                        <td><span style="display: inline-block; margin-right: 5px;">Class&nbsp;&&nbsp;Section</span></td>
                                                                        <td style="border-bottom: 1px solid #e5e5e5; width: 100%; text-align: center; font-weight: 600;"> {{ $studentsData->display_classname }} {{ $studentsData->section }}</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td style="padding: 6px 0;">
                                                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                    <tr>
                                                                        <td><span style="display: inline-block; margin-right: 5px; margin-left: 5px;">Roll&nbsp;No.</span></td>
                                                                        <td style="border-bottom: 1px solid #e5e5e5; width: 100%; text-align: center; font-weight: 600;">{{ $studentsData->rollno ?? ''}}</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" style="padding: 6px 0;">
                                                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                    <tr>
                                                                        <td><span style="display: inline-block; margin-right: 5px;">School</span></td>
                                                                        <td style="border-bottom: 1px solid #e5e5e5; width: 100%; text-align: center; font-weight: 600;">{{ $studentsData->school_name }}</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 6px 0;">
                                                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                    <tr>
                                                                        <td><span style="display: inline-block; margin-right: 5px;">Code</span></td>
                                                                        <td style="border-bottom: 1px solid #e5e5e5; width: 100%; text-align: center; font-weight: 600;">{{ $studentsData->school_code }}</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td style="padding: 6px 0;">
                                                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                    <tr>
                                                                        <td><span style="display: inline-block; margin-right: 5px; margin-left: 5px;">APAAR&nbsp;ID</span></td>
                                                                        <td style="border-bottom: 1px solid #e5e5e5; width: 100%; text-align: center; font-weight: 600;">{{ $studentsData->apaarId ?? ''}}</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" style="padding: 6px 0;">
                                                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                    <tr>
                                                                        <td><span style="display: inline-block; margin-right: 5px;">CISCE&nbsp;Registration&nbsp;No</span></td>
                                                                        <td style="border-bottom: 1px solid #e5e5e5; width: 100%; text-align: center; font-weight: 600;">{{ $studentsData->admissionnumber }}</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>


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

                                                            <td style="padding: 6px 0;">
                                                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                    <tr>
                                                                        <td><span style="display: inline-block; margin-right: 5px;">DOB</span></td>
                                                                        <td style="border-bottom: 1px solid #e5e5e5; width: 100%; text-align: center; font-weight: 600;"> {{ $formattedDob }} ({{ $age }} Years)</td>
                                                                    </tr>
                                                                </table>
                                                            </td>

                                                           

                                                            <td style="padding: 6px 0;">
                                                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px">
                                                                    <tr>
                                                                        <td><span style="display: inline-block; margin-right: 5px; margin-left: 5px;">Gender</span></td>
                                                                        <td style="border-bottom: 1px solid #e5e5e5; width: 100%; text-align: center; font-weight: 600;">{{ $gender }}</td>
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
                                                                        <td><span style="display: inline-block; margin-right: 5px;">Brief Summary</span></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="border-bottom: 1px solid #e5e5e5; width: 100%; text-align: center; font-weight: 600; height: 24px;"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="border-bottom: 1px solid #e5e5e5; width: 100%; text-align: center; font-weight: 600; height: 24px;"></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
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
    </table>

<!-- Page 2 -->
    <table border="0" cellpadding="0" cellspacing="0" style="width:100%; page-break-before: always;" >
        <tr>
            <td>
                <!-- Inner page Header Area (Page 2) -->
                <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse; font-family: Roboto Condensed, sans-serif; font-size: 12px; border:0px solid transparent;">
                    <tr>
                        <td style="vertical-align: top; height: 80px;">
                            <table cellpadding="0" cellspacing="0" style="width: 100%; border:0;">
                                <tr>

                                    <td style="width:300px;">
                                        @if(!empty($studentsData->logo))
                                        <div style="position: absolute; top: 17px; left:30px; display: flex; align-items: center; z-index: 1; width: auto; overflow: hidden;">
                                            <img src="{{ public_path('assets/uploads/logos/' . $studentsData->logo )}}" alt="" style="width: auto; height:60px;">
                                        </div>
                                        @else 
                                        <div style="margin-left: 30px; margin-top: 30px;">
                                            School Logo -1
                                        </div>
                                        @endif 
                                    </td>


                                    <td rowspan="2" style="position: relative; vertical-align: top; width: auto; text-align: right;">
                                        <div style="position: absolute; top: 30px; right:30px; display: flex; align-items: center; z-index: 1; width: 90px; overflow: hidden;">
                                            <img src="{{ public_path('assets/reports/cisce-logo.png')}}" alt="" style="width: inherit;">
                                        </div>
                                        <img src="{{ public_path('assets/reports/inner-header-bg.png')}}" alt="" style="width: 450px; height:auto; position: relative; right:0px; top:0;">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
        <tr>
            <td>
                 <!-- Inner page Content (Page 2) -->
                <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; border: 0; border-collapse: collapse; margin: auto;">
                    <tr>
                        <td>

                            <table cellpadding="0" cellspacing="0" style="width: 94%; border: 0; border-collapse: collapse; margin: auto; ">
                                <tr>
                                    <td>
                                        <div style="align-items: center; color: #fff; font-size: 18px; font-weight: 600; height: 32px; border-bottom: 3px solid #1c9b3e;">
                                            <div style="float: left; padding: 1px 10px 3px 10px; display:inline-block; background: #1c9b3e;">Physical Fitness Assessment - Class 4 to 12</div><div style="text-align:right; bacground:red; float:left;"><img src="{{ public_path('assets/reports/heading-band-corder.jpg')}}" alt="" style="width: 33px; position:relative; top:1px;"></div>
                                        </div>
                                    </td>
                                </tr>
                                
                                @foreach($orderedReportData as $key => $value)

                                @php

                                    $displayKey = str_contains($key, 'Body Composition')  
                                        ? str_replace('Body Composition (BMI)', 'BMI (Body Mass Index)', $key) 
                                        : $key;
                                @endphp
                               
                                <tr> <td style="height: 10px;"></td></tr>
                                <tr>
                                    <td style="padding: 6px 10px 6px 0px; font-size: 20px; color:#000; font-size: 18px; font-weight: 600;">{{ $displayKey }}</td> 
                                </tr>
                                


                                @if($key === 'Body Composition (BMI)')


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
                                                            <td style="width: 50%;">
                                                                <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; border: 1px solid orange; font-size: 13px; border-collapse: collapse; color:#333;">
                                                                    <tr style="background-color: #fecd0a;">
                                                                        <td style="width: 20%; background-color: orange; padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;" rowspan="2">Current Term</td>
                                                                        <td style="width: 28%; padding: 4px 0px 4px 0px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Date</td>
                                                                        <td style="width: 25%; padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Weight (kg)</td>
                                                                        <td style="width: 28%; padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Height (cm)</td>
                                                                        <td style="width: 28%; padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">BMI</td>
                                                                        <td style="padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; font-weight: bold; text-align: center;">Level</td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td style="padding: 4px 0px 4px 0px; font-weight: 500; color:#000; text-align: center;">{{ $value['Current_Term'][0]['created_at'] ?? '---'}}</td>
                                                                        <td style="padding: 0px 4px 2px 6px; text-align: center; border: 1px solid orange;"> {{ $value['Current_Term'][0]['weight'] ?? '---'}}</td>
                                                                        <td style="padding: 0px 4px 2px 6px; text-align: center; border: 1px solid orange;">  {{ $value['Current_Term'][0]['height'] ?? '---'}}</td>
                                                                        <td style="padding: 0px 4px 2px 6px; text-align: center; border: 1px solid orange;">{{ $value['Current_Term'][0]['score'] ?? '---'}}</td>
                                                                        <td style="padding: 0px 4px 2px 6px; text-align: center; border: 1px solid orange;">{{ $value['Current_Term'][0]['Level'] ?? '---'}}</td>
                                                                    </tr>

                                                                </table>
                                                            </td>
                                                            <td style="width: 50%;">
                                                                <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; border: 1px solid orange; font-size: 13px; border-collapse: collapse; color:#333;">
                                                                    <tr style="background-color: #fecd0a;">
                                                                        <td style="width: 20%; background-color: orange; padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;" rowspan="2">Previous Term</td>
                                                                        <td style="width: 28%; padding: 4px 0px 4px 0px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Date</td>
                                                                        <td style="width: 25%; padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Weight (kg)</td>
                                                                        <td style="width: 28%; padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Height (cm)</td>
                                                                        <td style="width: 28%; padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">BMI</td>
                                                                        <td style="padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; font-weight: bold; text-align: center;">Level</td>
                                                                    </tr>   

                                                                    <tr>
                                                                        <td style="padding: 0px 4px 2px 6px; font-weight: 500; color:#000; text-align: center;">{{ $value['Previous_Term'][0]['created_at']  ?? '---'}}</td>
                                                                        <td style="padding: 4px 0px 4px 0px;  text-align: center; border: 1px solid orange;">{{ $value['Previous_Term'][0]['weight'] ?? '---'}} </td>
                                                                        <td style="padding: 0px 4px 2px 6px; text-align: center; border: 1px solid orange;">{{ $value['Previous_Term'][0]['height'] ?? '---'}} </td>
                                                                        <td style="padding: 0px 4px 2px 6px; text-align: center; border: 1px solid orange;"> {{ $value['Previous_Term'][0]['score'] ?? '---'}} </td>
                                                                        <td style="padding: 0px 4px 2px 6px; text-align: center; border: 1px solid orange;">{{ $value['Previous_Term'][0]['Level'] ?? '---'}}</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>

                                                    </table>

                                                </td>
                                            </tr>

                                        </table>
                                    </td>
                                </tr>

                                @else

                                <tr>
                                    <td>
                                        <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0; border-collapse: collapse;">
                                            <tr>
                                                <td style="vertical-align: top;">
                                                    <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                                                        <tr>
                                                            <td style="width: 50%;">
                                                                <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; border: 1px solid orange; border-bottom: 1px solid orange; font-size: 14px; border-collapse: collapse; color:#333;">
                                                                    <tr style="background-color: #fecd0a;">
                                                                        <td style="width: 20%; background-color:orange; padding: 4px 4px 4px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;" rowspan="2">Current Term</td>
                                                                        <td style="width: 22%; padding: 4px 4px 4px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Date</td>
                                                                        <td style="width: 31%; padding: 4px 4px 4px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Score</td>
                                                                        <td style="padding: 4px 4px 4px 6px; border: 1px solid orange; color:#000; font-weight: bold; text-align: center;">Level</td>
                                                                    </tr>

                                                                    <tr>                
                                                                        <td style="padding: 4px 4px 4px 6px; font-weight: 500; border: 1px solid orange; color:#000; text-align: center;">{{ $value['Current_Term'][0]['created_at'] ?? '---'}}</td>
                                                                        <td style="text-align: center; border: 1px solid orange;">{{ $value['Current_Term'][0]['score'] ?? '---'}}</td>
                                                                        <td style="text-align: center; border: 1px solid orange;">{{ $value['Current_Term'][0]['Level'] ?? '---'}}</td>

                                                                    </tr>

                                                                </table>
                                                            </td>
                                                            <td style="width: 50%;">
                                                                <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; border: 1px solid orange; border-bottom: 1px solid orange; font-size: 14px; border-collapse: collapse; color:#333;">
                                                                    <tr style="background-color: #fecd0a;">
                                                                        <td style="font-weight: 500; width: 20%; background-color:orange; padding: 4px 4px 4px 6px; border: 1px solid orange; color:#000; text-align: center;" rowspan="2">Previous Term</td>
                                                                        <td style="font-weight: 500; width: 22%; padding: 4px 4px 4px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Date</td>
                                                                        <td style="font-weight: 500; width: 31%; padding: 4px 4px 4px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Score</td>
                                                                        <td style="font-weight: 500; padding: 4px 4px 4px 6px; border: 1px solid orange; color:#000; font-weight: bold; text-align: center;">Level</td>
                                                                    </tr>

                                                                    <tr>                
                                                                        <td style="padding: 4px 4px 4px 6px; font-weight: 500; border: 1px solid orange; color:#000; text-align: center;">{{ $value['Previous_Term'][0]['created_at'] ?? '---'}}</td>
                                                                        <td style="text-align: center; border: 1px solid orange;">{{ $value['Previous_Term'][0]['score'] ?? ''}}</td>
                                                                        <td style="text-align: center; border: 1px solid orange;">{{ $value['Previous_Term'][0]['Level'] ?? '---'}}</td>
                                                                    </tr>

                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; border-top: 1px solid transparent; border-left: 1px solid orange; border-right: 1px solid orange; border-bottom: 1px solid orange; font-size: 14px; border-collapse: collapse; color:#333;">
                                                        <tr>
                                                            <td style="border-top: 1px solid #fecd0a; background-color: #fecd0a; padding: 4px; padding: 5px 10px; color: #000; text-align: center; width: 120px; font-weight: bold;">Recommendation</td>
                                                            <td style="padding: 4px;">{{ $value['Current_Term'][0]['recommendation'] ?? '---' }}</td>
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

                            </table>


                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Page 3 -->
    <table border="0" cellpadding="0" cellspacing="0" style="width:100%; page-break-before: always;">
            <tr>
                <td>
                    <!-- Inner page Header Area (Page 3) -->
                    <table border="0" cellpadding="0" cellspacing="0" style=" width: 100%; border-collapse: collapse; font-family: Roboto Condensed, sans-serif; font-size: 12px; border:0px solid transparent;">
                        <tr>

                            <td style="vertical-align: top; height: 100px;">
                                <table cellpadding="0" cellspacing="0" style="width: 100%; border:0;">
                                    <tr>
                                        <td rowspan="2" style="position: relative; vertical-align: top; width: auto; text-align: left;">
                                            <div style="position: absolute; top: 30px; left:30px; display: flex; align-items: center; z-index: 1; width: 90px; overflow: hidden;">
                                                <img src="{{ public_path('assets/reports/cisce-logo.png')}}" alt="" style="width: inherit;">
                                            </div>
                                            <img src="{{ public_path('assets/reports/inner-header2-bg.png')}}" alt="" style="width: 450px; height:auto; position: relative; left:0px; top:0;">
                                        </td>

                                        <td rowspan="2" style="position: relative; vertical-align: top; width: auto; text-align: left;">
                                            @if(!empty($studentsData->logo))
                                            <div style="position: absolute; top: 17px; right:30px; display: flex; align-items: center; z-index: 1; width: auto; overflow: hidden;">
                                                <img src="{{ public_path('assets/uploads/logos/' . $studentsData->logo )}}" alt="" style="width: auto; height:60px;">
                                            </div>
                                            @else 
                                            <div style="margin-left: 30px; margin-top: 30px;">
                                                School Logo -1
                                            </div>
                                            @endif 
                                        </td>
                                    </tr>
                                </table>
                            </td>



                        </tr>
                    </table>
                </td>
            </tr>
           <!-- Inner page Content (Page 3) -->
            <tr>
                <td>
                    <table cellpadding="0" cellspacing="0" style="border: 0; border-collapse: collapse; margin: auto; width:94%;">
                        <tr>
                            <td style="text-indent: 0;">
                                <div style="align-items: left; color: #fff; font-size: 18px; font-weight: 600; height: 32px; border-bottom: 3px solid #1c9b3e; ">
                                    <div style="float: left; padding: 1px 10px 3px 10px; display:inline-block; background: #1c9b3e; ">Recommended Sports</div><div style="text-align:right; bacground:red; float:left;"><img src="{{ public_path('assets/reports/heading-band-corder.jpg')}}" alt="" style="width: 33px; position:relative; top:1px;"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="height: 10px;"></td>
                        </tr>
                        <tr>
                            <td>
                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0; border-collapse: collapse; border:1px solid #ED6D1E; font-size: 12px; text-align:left; line-height:13px;">
                                    <tr style="background-color: #ED6D1E; color:#fff; font-size: 13px;">


                                       <!--  <th style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; width:24px; text-align:center; "></th> -->

                                        <th style="vertical-align: top; padding: 2px 4px; width:44%; border:1px solid #ED6D1E; text-align:left;">Fitness Profile</th>
                                        <th style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; text-align:left;">Recommended Team</th>
                                        <th style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; text-align:left;">Recommended Individual Sports</th>
                                    </tr>
                                    <tr>
                                        <!-- <td style="vertical-align: top; padding: 4px 4px; border:1px solid #ED6D1E; text-align:center; ">
                                            <img src="{{ public_path('assets/reports/checked.png')}}" alt="checked" style="width:12px;"></td> -->


                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E;">High Cardiovascular Endurance, High Muscular Endurance, Moderate Strength, High Speed, Moderate Flexibility</td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">Hockey, Football, Rugby 7s </td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">Athletics (1500m - 10,000m), Marathon, Cycling (Track/Road), Triathlon </td>
                                    </tr>
                                    <tr>
                                        <!-- <td style="vertical-align: top; padding: 4px 4px; border:1px solid #ED6D1E; text-align:center;  "><img src="{{ public_path('assets/reports/checked.png')}}" alt="checked" style="width:12px;"> </td> -->

                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">
                                            Moderate Cardiovascular Endurance, High Strength, High Speed, Moderate Muscular Endurance, Moderate Flexibility
                                        </td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">
                                            Kabaddi, Cricket, Kho-Kho
                                        </td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">
                                            Athletics (100m, 200m), Weightlifting, Wrestling, Boxing
                                        </td>
                                    </tr>
                                    <tr>
                                       <!--  <td style="vertical-align: top; padding: 4px 4px; border:1px solid #ED6D1E; text-align:center; ">

                                        </td> -->
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E;">
                                            High Strength, Moderate Speed, Moderate Cardiovascular Endurance, Moderate Flexibility, Moderate Muscular Endurance
                                        </td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">
                                            Tug of War, Rowing (Team)
                                        </td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">
                                            Shot Put, Javelin, Hammer Throw, Wrestling, Powerlifting
                                        </td>
                                    </tr>
                                    <tr>
                                        <!-- <td style="vertical-align: top; padding: 4px 4px; border:1px solid #ED6D1E; text-align:center; ">
                                            <img src="{{ public_path('assets/reports/checked.png')}}" alt="checked" style="width:12px;">
                                        </td> -->
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">
                                            High Flexibility, High Muscular Endurance, Moderate Speed, Moderate Strength, Moderate Cardiovascular Endurance
                                        </td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">
                                            Artistic Roller Skating (Team Display), Gymnastics Team
                                        </td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">
                                            Gymnastics, Yoga, Mallakhamb, Artistic Skating
                                        </td>
                                    </tr>
                                    <tr>
                                       <!--  <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E;">

                                        </td> -->
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E;">
                                            High Speed, High Cardiovascular Endurance, Moderate Strength, Moderate Flexibility Moderate Muscular Endurance
                                        </td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">
                                            Cricket, Hockey, Basketball, Ultimate Frisbee
                                        </td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">
                                            Athletics (400m, 800m), Swimming (Freestyle, Backstroke), Rowing
                                        </td>
                                    </tr>
                                    <tr>
                                        <!-- <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">

                                        </td> -->
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">
                                            Moderate in all components
                                        </td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">
                                            Volleyball, Handball, Baseball, Basketball
                                        </td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">
                                            Badminton, Table Tennis, Shooting, Archery, Tennis, Squash
                                        </td>
                                    </tr>
                                    <tr>
                                        <!-- <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E;">

                                        </td> -->
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E;">
                                            High Muscular Endurance, High Strength, Moderate Cardiovascular Endurance, Moderate Speed, Moderate Flexibility
                                        </td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">
                                            Rowing (Team), Kabaddi
                                        </td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">
                                            Rowing (Single Sculls), Canoeing, Mountaineering, Rock Climbing
                                        </td>
                                    </tr>
                                    <tr>
                                        <!-- <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">

                                        </td> -->
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">
                                            High Flexibility, High Cardiovascular Endurance, High Speed, Moderate Strength, High Muscular Endurance
                                        </td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">
                                            Basketball, Ultimate Frisbee
                                        </td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">
                                            Judo, Taekwondo, Karate, Fencing, Wushu, Archery, Dance Sports
                                        </td>
                                    </tr>
                                    <tr>
                                       <!--  <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">

                                        </td> -->
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">
                                            High Flexibility, Moderate Speed, Moderate Strength, High Muscular Endurance, Moderate Cardiovascular Endurance
                                        </td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">
                                            Gymnastics (Team), Mallakhamb
                                        </td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #ED6D1E; ">
                                            Diving, Pole Vault, Artistic Gymnastics, Yoga
                                        </td>
                                    </tr>

                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td style="height: 10px;"></td>
                        </tr>
                        <tr>
                            <td>
                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0; border-collapse: collapse; border:1px solid #1c9b3e; font-size: 12px;">
                                    <tr style="background-color: #1c9b3e; color:#fff; font-size: 13px;">
                                        <th style="vertical-align: top; padding: 2px 4px; width:14%; border:1px solid #1c9b3e; text-align: left;">
                                            Descriptor
                                        </th>
                                        <th style="vertical-align: top; padding: 2px 4px; width:18%; border:1px solid #1c9b3e; text-align: left;">
                                            Corresponding Levels
                                        </th>
                                        <th style="vertical-align: top; padding: 2px 4px; border:1px solid #1c9b3e; text-align: left;">
                                            Remarks
                                        </th>
                                    </tr>
                                    <tr>

                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #1c9b3e;">
                                            High
                                        </td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #1c9b3e;">
                                            L6 and L7
                                        </td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #1c9b3e;">
                                            Top 20% of performers — well above average
                                        </td>
                                    </tr>
                                    <tr>

                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #1c9b3e;">
                                            Moderate
                                        </td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #1c9b3e;">
                                            L4 and L5
                                        </td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #1c9b3e;">
                                            Between 60–80 %ile — competent range
                                        </td>
                                    </tr>
                                    <tr>

                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #1c9b3e;">
                                            Low
                                        </td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #1c9b3e;">
                                            L1 to L3
                                        </td>
                                        <td style="vertical-align: top; padding: 2px 4px; border:1px solid #1c9b3e;">
                                            Used only for identifying gaps or support needs
                                        </td>
                                    </tr>

                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td style="height: 10px;"></td>
                        </tr>
                        <tr >
                            <td>
                                <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; border: 1px solid #ccc; font-size: 12px; border-collapse: collapse; color:#333;" class="cell">                                    

                                    <tr style="background-color: #ccc; width: 100%;" >
                                        <td style="padding: 5px 10px; font-weight: bold; color:#000; font-size: 13px;" colspan="8">Fitness Benchmarks for a {{ $age }}-year-old-{{ $gender }}</td>
                                    </tr>

                                    <tr style="font-weight: bold; background-color: #eee; font-size: 12px; color: #000;">
                                        <td style="padding:2px 4px; width:40px; border: 1px solid #ccc;"></td>

                                        <td style="padding:2px 4px; border: 1px solid #ccc;">L1&nbsp;(Very Low)</td>
                                        <td style="padding:2px 4px; border: 1px solid #ccc;">L2&nbsp;(Low)</td>
                                        <td style="padding:2px 4px; border: 1px solid #ccc;">L3&nbsp;(Developing)</td>
                                        <td style="padding:2px 4px; border: 1px solid #ccc;">L4&nbsp;(Moderate)</td>
                                        <td style="padding:2px 4px; border: 1px solid #ccc;">L5&nbsp;(Good)</td>
                                        <td style="padding:2px 4px; border: 1px solid #ccc;">L6&nbsp;(High)</td>
                                        <td style="padding:2px 4px; border: 1px solid #ccc;">L7&nbsp;(Excellent)</td>
                                    </tr>
                                    <tr style="background-color: #eee; font-weight: 500;">
                                        <td style="padding:2px 4px; border: 1px solid #ccc;"></td>

                                        <td style="padding:2px 4px; border: 1px solid #ccc;">&lt; 20 %ile</td>
                                        <td style="padding:2px 4px; border: 1px solid #ccc;">&gt; 20 %ile</td>
                                        <td style="padding:2px 4px; border: 1px solid #ccc;">&gt; 40 %ile</td>
                                        <td style="padding:2px 4px; border: 1px solid #ccc;">&gt; 60 %ile</td>
                                        <td style="padding:2px 4px; border: 1px solid #ccc;">&gt; 70 %ile</td>
                                        <td style="padding:2px 4px; border: 1px solid #ccc;">&gt; 80 %ile</td>
                                        <td style="padding:2px 4px; border: 1px solid #ccc;">&gt; 90 %ile</td>
                                    </tr>

                                    @foreach($getFitnessBenchmark as $key => $skillname)
                                    <tr style="width: 13%;"> 
                                        <td style="padding:2px 4px; font-weight: bold; color: #000; width:40px; ">{{ $skillname->skill_name }}</td>
                                        @foreach($skillname->ranges as $level => $range)
                                            <td style="padding: 1px 10px 3px 10px; border: 1px solid #ccc;">{{ $range }}</td>
                                        @endforeach
                                    </tr>
                                    @endforeach                                    
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td style="height: 10px;"></td>
                        </tr>
                       
                        <tr>
                            <td>
                                <table border="1" cellpadding="0" cellspacing="0" 
                                    style="width: 100%; border: 1px solid #ccc; font-size: 12px; border-collapse: collapse; color:#333;">                           
                                    <tbody>

                                        <!-- Header Title -->
                                        <tr style="background-color:#ccc;">
                                            <td colspan="4" style="padding: 5px 10px; font-weight: bold; color:#000; font-size: 14px;">
                                                BMI Benchmarks for a {{ $age }}-year-old-{{ $gender }}
                                            </td>
                                        </tr>

                                        <!-- Columns -->
                                        <tr style="font-weight: bold; background-color: #eee; font-size: 12px; color: #000; text-align: center;">
                                            <th style="padding: 4px;">UW</th>
                                            <th style="padding: 4px;">N</th>
                                            <th style="padding: 4px;" >OW</th>
                                            <th style="padding: 4px; ">OB</th>
                                        </tr>

                                        <!-- Values -->
                                        <tr style="text-align: center;">
                                            @if(is_array($getBmiBenchmark) && count($getBmiBenchmark) > 0)
                                                <td style="padding: 5px 10px; border: 1px solid #ccc;">{{ $getBmiBenchmark['UW'] ?? 'N/A' }}</td>
                                                <td style="padding: 5px 10px; border: 1px solid #ccc;">{{ $getBmiBenchmark['N'] ?? 'N/A' }}</td>
                                                <td style="padding: 5px 10px; border: 1px solid #ccc;">{{ $getBmiBenchmark['OW'] ?? 'N/A' }}</td>
                                                <td style="padding: 5px 10px; border: 1px solid #ccc;">{{ $getBmiBenchmark['OB'] ?? 'N/A' }}</td>

                                            @endif
                                        </tr>
                                    </tbody>

                                </table>
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
            
            <!-- Inner page Footer Area (Page 3) -->
            <tr>
                <td style="height: 10px;"></td>
            </tr>
            <tr>
                <td>
                    <table cellpadding="0" cellspacing="0" style="width:100%;">
                        <tr>
                            <td style="width: 100%; text-align: right;">
                                <table cellpadding="0" cellspacing="0" style="border: 0px; width: 100%;">
                                    <tr>
                                        <td style="height: 35px;"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div style="background-color:#FECD0A; padding: 4px 0; text-align: center; font-weight: 500; font-size: 13px;"><span style="margin-left: 60px;">Physical Health and Fitness Assessment</span></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="height: 10px;"></td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 94px;">
                                <div style="float: right; position: relative; width: 80px;">
                                    <span style="position: absolute; right: 50%; top:50%; transform: translate(-50%, 0); color: #fff; z-index: 1; display: inline-block; padding: 6px 0 0 20px; font-size: 13px; font-weight: 600;">3</span>
                                    <img src="{{ public_path('assets/reports/footer-bg2.png')}}" alt="" style="width: inherit;">
                                </div>

                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
    </table>

    <!-- Page 4 -->
    <table border="0" cellpadding="0" cellspacing="0" style="width:100%; page-break-before: always;">
        <tr>
            <td>
                <!-- Inner page Header Area (Page 4) -->
                <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse; font-family: Roboto Condensed, sans-serif; font-size: 12px; border:0px solid transparent;">
                    <tr>
                        <td style="vertical-align: top; height: 100px;">
                            <table cellpadding="0" cellspacing="0" style="width: 100%; border:0;">
                                <tr>

                                    <td style="width:300px;">
                                        @if(!empty($studentsData->logo))
                                        <div style="position: absolute; top: 17px; left:30px; display: flex; align-items: center; z-index: 1; width: auto; overflow: hidden;">
                                            <img src="{{ public_path('assets/uploads/logos/' . $studentsData->logo )}}" alt="" style="width: auto; height:60px;">
                                        </div>
                                        @else 
                                        <div style="margin-left: 30px; margin-top: 30px;">
                                            School Logo -1
                                        </div>
                                        @endif 
                                    </td>


                                    <td rowspan="2" style="position: relative; vertical-align: top; width: auto; text-align: right;">
                                        <div style="position: absolute; top: 30px; right:30px; display: flex; align-items: center; z-index: 1; width: 90px; overflow: hidden;">
                                            <img src="{{ public_path('assets/reports/cisce-logo.png')}}" alt="" style="width: inherit;">
                                        </div>
                                        <img src="{{ public_path('assets/reports/inner-header-bg.png')}}" alt="" style="width: 450px; height:auto; position: relative; right:0px; top:0;">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <!-- Inner page Content (Page 4) -->
                 <table border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <table cellpadding="0" cellspacing="0" style="width: 94%; border: 0; border-collapse: collapse; margin: auto; color: #333; font-size: 12px;">
                                        <tr>
                                            <td style="border: 1px solid orange; padding: 5px 10px 8px 10px; background:#FEFBEF;">
                                                <h3 style="color: #ED6D1E; margin-bottom: 0px; font-size: 18px;">WHO Guidelines on Physical Activity and Sedentary Behaviour 2020</h3>
                                                <h4 style="color: #000; margin-bottom: 3px; font-size: 16px">Age Appropriate Fitness Protocols and Guidelines for age 5-18 years</h4>
                                                <p style="line-height: 14px;">At least an average of 60 minutes per day of moderate-to-vigorous intensity physical activity, across the week; most of this physical activity should be aerobic.</p>
                                                <p style="line-height: 14px;">Vigorous-intensity aerobic activities, as well as those that strengthen muscle and bone should be incorporated at least 3 days a week.</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="height: 10px;"></td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0; border-collapse: collapse;">
                                                    <tr>
                                                        <td colspan="2">
                                                            <h3 style="color: #000; font-size: 16px; margin-bottom: 5px;">Recommends the following activities for improving fitness for ages 9-14 (Class 4-8)</h3>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 50%; padding-right: 30px;">
                                                            <h4 style="color: #000; margin-bottom: 3px; font-size: 13px;">1. Endurance-related Activities</h4>
                                                            <p>Spot Running, Climbing Stairs, Walking on Toes, Swimming, Jumping Jacks, March and Swing Your Arms</p>
                                                        </td>
                                                        <td style="width: 50%;">
                                                            <h4 style="color: #000; margin-bottom: 3px; font-size: 13px;">2. Strength Related Activities</h4>
                                                            <p>Straight Leg Raises, Push-ups on the Wall, Long Jump, Goal Keeping</p>
                                                        </td>

                                                    </tr>
                                                    <tr>
                                                        <td style="height: 5px;"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 50%; padding-right: 30px;">
                                                            <h4 style="color: #000; margin-bottom: 3px; font-size: 13px;">3. Flexibility-related Activities</h4>
                                                            <p>Calf Stretch, Child's Pose, Knee to Chest, Bend Down</p>
                                                        </td>
                                                        <td style="width: 50%;">
                                                            <h4 style="color: #000; margin-bottom: 3px; font-size: 13px;">4. Balance related Activities</h4>
                                                            <p>Single Leg Stance, Leg Swings, Walking on Lines of Different Shapes</p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td style="height: 10px;"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0; border-collapse: collapse;">
                                                    <tr>
                                                        <td colspan="2">
                                                            <h3 style="color: #000; font-size: 16px; margin-bottom: 10px;">Recommends the following additional activities for improving fitness for for ages 15-18 (Class 9-12)</h3>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding-right: 30px;">
                                                            <h4 style="color: #000; margin-bottom: 3px; font-size: 13px;">1. Endurance-related Activities</h4>
                                                            <p>400/800 m Race, Brisk Walking, Quick Air Punches, 4x 100 / 200 / 400 m Relay Race, Swimming, Walking Lunges</p>
                                                        </td>
                                                        <td>
                                                            <h4 style="color: #000; margin-bottom: 3px; font-size: 13px;">2. Strength related Activities</h4>
                                                            <p>Curl Up (Core Strength), Plank (Core Strength), Push Ups (Upper Body Strength), Squat (Lower Body Strength)s</p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="height: 10px;"></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2">
                                                            <h4 style="color: #000; margin-bottom: 3px; font-size: 13px;">3. Flexibility-related Activities</h4>
                                                            <p>Forward Bend</p>
                                                        </td>

                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="height: 10px;"></td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <h2 style="font-size: 24px; color:#ed6d1e; font-size: 18px; font-weight: 600; text-align: center;">EXERCISE AND PHYSICAL ACTIVITY</h2>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 18px; color:#000; font-size: 16px; font-weight: 500; text-align: center;">
                                                <p>* Energy expenditure on various physical activities (Kcal/Hr)</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="height: 15px;"></td>
                                        </tr>

                                        <tr>
                                            <td>
                                            <table  border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                                                <tr>
                                                    <td style="vertical-align: top; width:80%;">

                                                        <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                                            <tr>
                                                                <td>
                                                                    <table cellpadding="0" cellspacing="0" style="width:100%">
                                                                        <tr>
                                                                            <td style="vertical-align: top; padding-right:10px;">
                                                                                <table class="act-tbl col-3" border="1" cellpadding="0" cellspacing="0"
                                                                                                style="border: 1px solid #EFB9B8; font-size: 12px; border-collapse: collapse; color:#333;">
                                                                                                <tr style="background-color: #FBE1E0;">
                                                                                                    <td style="background-color:#FBE1E0; padding: 2px 4px; border: 1px solid #EFB9B8; color:#7B2321; font-weight: bold;"
                                                                                                        colspan="2">Activity</td>
                                                                                                    <td
                                                                                                        style="padding: 2px 4px; border: 1px solid #EFB9B8; color:#7B2321; text-align: right; font-weight: bold;">
                                                                                                        Kcal/hr</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td
                                                                                                        style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px;">
                                                                                                        Sleeping</td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-right:1px solid #EFB9B8;">
                                                                                                    </td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-bottom:1px solid transparent;">
                                                                                                        44</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td
                                                                                                        style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px;">
                                                                                                        Sitting</td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-right:1px solid #EFB9B8;">
                                                                                                    </td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-bottom:1px solid transparent;">
                                                                                                        57</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td
                                                                                                        style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px;">
                                                                                                        Standing</td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-right:1px solid #EFB9B8;">
                                                                                                    </td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-bottom:1px solid transparent;">
                                                                                                        63</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td
                                                                                                        style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px;">
                                                                                                        Walking</td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-right:1px solid #EFB9B8;">
                                                                                                        6.43 km/hr</td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-bottom:1px solid transparent;">
                                                                                                        227</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td
                                                                                                        style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px;">
                                                                                                        Climbing Stairs</td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-right:1px solid #EFB9B8;">
                                                                                                    </td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-bottom:1px solid transparent;">
                                                                                                        485</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td
                                                                                                        style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px;">
                                                                                                        Housecleaning</td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-right:1px solid #EFB9B8;">
                                                                                                    </td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-bottom:1px solid transparent;">
                                                                                                        176</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td
                                                                                                        style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px;">
                                                                                                        Gardening</td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-right:1px solid #EFB9B8;">
                                                                                                    </td>
                                                                                                    <td style="text-align: right; padding: 2px 4px;">227</td>
                                                                                                </tr>
                                                                                </table>
                                                                                </td>
                                                                            <td style="vertical-align: top; padding-right:10px;">
                                                                                <table class="act-tbl col-3" border="1" cellpadding="0" cellspacing="0"
                                                                                                style="border: 1px solid #EFB9B8; font-size: 12px; border-collapse: collapse; color:#333;">
                                                                                                <tr style="background-color: #FBE1E0;">
                                                                                                    <td style="background-color:#FBE1E0; padding: 2px 4px; border: 1px solid #EFB9B8; color:#7B2321; font-weight: bold;"
                                                                                                        colspan="2">Activity</td>
                                                                                                    <td
                                                                                                        style="padding: 2px 4px; border: 1px solid #EFB9B8; color:#7B2321; text-align: right; font-weight: bold;">
                                                                                                        Kcal/hr</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td
                                                                                                        style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px;">
                                                                                                        Cycling</td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-right:1px solid #EFB9B8;">City cycling
                                                                                                    </td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-bottom:1px solid transparent;">
                                                                                                        302</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td
                                                                                                        style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px;">
                                                                                                        </td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-right:1px solid #EFB9B8;">16.1 km/hr
                                                                                                    </td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-bottom:1px solid transparent;">
                                                                                                    391</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td
                                                                                                        style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px;">
                                                                                                        </td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-right:1px solid #EFB9B8;">22.53 km/hr
                                                                                                    </td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-bottom:1px solid transparent;">
                                                                                                        567</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td
                                                                                                        style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px;">
                                                                                                        </td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-right:1px solid #EFB9B8;">
                                                                                                        32.18 km/hr</td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-bottom:1px solid transparent;">
                                                                                                        932</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td
                                                                                                        style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px;">
                                                                                                        Running</td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-right:1px solid #EFB9B8;">8 km/hr
                                                                                                    </td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-bottom:1px solid transparent;">
                                                                                                        523</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td
                                                                                                        style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px;">
                                                                                                        </td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-right:1px solid #EFB9B8;">10 km/hr
                                                                                                    </td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-bottom:1px solid transparent;">
                                                                                                        617</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td
                                                                                                        style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px;">
                                                                                                        Garening</td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-right:1px solid #EFB9B8;">13 km/hr
                                                                                                    </td>
                                                                                                    <td style="text-align: right; padding: 2px 4px;">743</td>
                                                                                                </tr>
                                                                                </table>
                                                                                </td>
                                                                            <td style="vertical-align: top; padding-right:10px;">
                                                                                <table class="act-tbl col-2" border="1" cellpadding="0" cellspacing="0"
                                                                                                style="border: 1px solid #EFB9B8; font-size: 12px; border-collapse: collapse; color:#333; width:100%;">
                                                                                                <tr style="background-color: #FBE1E0;">
                                                                                                    <td style="background-color:#FBE1E0; padding: 2px 4px; border: 1px solid #EFB9B8; color:#7B2321; font-weight: bold;"
                                                                                                        >Activity</td>
                                                                                                    <td
                                                                                                        style="padding: 2px 4px; border: 1px solid #EFB9B8; color:#7B2321; text-align: right; font-weight: bold;">
                                                                                                        Kcal/hr</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td
                                                                                                        style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px; border-right:1px solid #EFB9B8;">
                                                                                                        Billiards/ snooker</td>
                                                                                                    
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-bottom:1px solid transparent;">
                                                                                                        44</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td
                                                                                                        style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px; border-right:1px solid #EFB9B8;">
                                                                                                        Roller skating</td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-bottom:1px solid transparent;">
                                                                                                        57</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td
                                                                                                        style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px; border-right:1px solid #EFB9B8;">
                                                                                                        Swimming</td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-bottom:1px solid transparent;">
                                                                                                        63</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td
                                                                                                        style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px; border-right:1px solid #EFB9B8;">
                                                                                                        Horseback riding</td>
                                                                                                    
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-bottom:1px solid transparent;">
                                                                                                        227</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td
                                                                                                        style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px; border-right:1px solid #EFB9B8;">
                                                                                                        Squash</td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-bottom:1px solid transparent;">
                                                                                                        485</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td
                                                                                                        style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px; border-right:1px solid #EFB9B8;">
                                                                                                        Badminton</td>
                                                                                                    <td
                                                                                                        style="text-align: right; padding: 2px 4px; border-bottom:1px solid transparent;">
                                                                                                        176</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td
                                                                                                        style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px; border-right:1px solid #EFB9B8;">
                                                                                                        Table tennis</td>
                                                                                                            <td style="text-align: right; padding: 2px 4px;">227</td>
                                                                                                </tr>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-size: 12px; padding-top: 4px; padding-right: 6px; line-height: 12px;">
                                                                    *Approx.energy expenditure for 60 Kg reference man. Individuals with higher body weight will
                                                                    be spending more calories than those with lower body weight. Reference woman (50 kg) will be spending 5%
                                                                    less calories.
                                                                </td>
                                                            </tr>
                                                        </table>

                                                    </td>

                                                    <td style="vertical-align: top; width:20%;">
                                                        
                                                            <table border="1" cellpadding="0" cellspacing="0" class="act-tbl col-2"
                                                                style="border: 1px solid #EFB9B8; font-size: 12px; border-collapse: collapse; color:#333;">
                                                                <tr style="background-color: #FBE1E0;">
                                                                    <td
                                                                        style="width: 100%; background-color:#FBE1E0; padding: 2px 4px; border: 1px solid #EFB9B8; color:#7B2321; font-weight: bold;">
                                                                        Activity</td>
                                                                    <td
                                                                        style="font-weight: 500; padding: 2px 4px; border: 1px solid #EFB9B8; color:#7B2321; text-align: right; font-weight: bold;">
                                                                        Kcal/hr</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px; ">Tennis</td>
                                                                    <td
                                                                        style="font-weight: 500; text-align: right; padding: 2px 4px;">
                                                                        353</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px;">Volleyball
                                                                    </td>
                                                                    <td
                                                                        style="font-weight: 500; text-align: right; padding: 2px 4px;">
                                                                        252</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px;">Football</td>
                                                                    <td
                                                                        style="font-weight: 500; text-align: right; padding: 2px 4px;">
                                                                        441</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px;">Basketball
                                                                    </td>
                                                                    <td
                                                                        style="font-weight: 500; text-align: right; padding: 2px 4px;">
                                                                        403</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px;">Dancing</td>
                                                                    <td
                                                                        style="font-weight: 500; text-align: right; padding: 2px 4px;">
                                                                        302</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px;">Gymnastic</td>
                                                                    <td
                                                                        style="font-weight: 500; text-align: right; padding: 2px 4px;">
                                                                        202</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px;">Yoga</td>
                                                                    <td style="font-weight: 500; text-align: right; padding: 2px 4px;">195</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-weight: 500; color:#000; text-align: left; padding: 2px 4px;">HIIT Workout
                                                                    </td>
                                                                    <td style="font-weight: 500; text-align: right; padding: 2px 4px;">504</td>
                                                                </tr>
                                                            </table>
                                                        
                                                    </td>


                                                </tr>
                                            </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                </table>
            </td>
        </tr>
        <!-- Inner page Footer Area (Page 4) -->
        <tr>
             <td style="height: 20px;"></td>
        </tr>
        <tr>
            <td>
                <table border="0" cellpadding="0" cellspacing="0" style="width: 94%; border: 1px solid orange; font-size: 14px; border-collapse: collapse; color:#333; margin:auto;">
                    <tr>
                        <td style="font-weight: 600; padding: 10px 15px; font-size: 14px; color: #000; height:70px; vertical-align:top;">PE Teacher's Observations and Comments (if any)</td>
                        <td style="font-weight: 600; padding: 10px 15px; font-size: 14px; color: #000; text-align:center; height:100px; vertical-align:bottom;">
                            @if($studentsData->hod_sign)
                                <div style="margin: 10px 0 0 0; text-align:center;">
                                    <img src="{{ public_path('assets/uploads/hod-signatures/' . $studentsData->hod_sign) }}" alt="" style="height: 70px;">
                                </div>
                            @endif
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
                            <div style="padding: 10px 0px; height: 60px; background-color: #fff; border-right: 3px solid #fff;"></div>
                                 <p style="text-align: center; font-weight: 600;">Parent's Signature</p>
                        </td>
                        <td style="border: 1px solid transparent; width:50%;">
                            @if($studentsData->signature)
                                <div style="margin: 10px 0 0 0; text-align:center;">
                                    <img src="{{ public_path('assets/uploads/signatures/' . $studentsData->signature) }}" alt="" style="height: 70px;">
                                </div>
                            @endif
                            <p style="text-align: center; font-weight: 600;">Signature of Principal with Stamp</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table cellpadding="0" cellspacing="0" style="width: 100%; border:0; font-size:13px;">
                    <tr>
                        <td style="background-color: #fecd0a; height: 40px; padding: 0 30px;">CISCE Physical Health and Fitness Assessment</td>
                        <td style="background-color: #1c9b3e; height: 40px; width: 30%; padding: 0 30px; text-align:right; color:#fff;">Powered by fitness365.me</td>
                    </tr>
                </table>
             </td>
        </tr>
    </table>

</body>

</html>