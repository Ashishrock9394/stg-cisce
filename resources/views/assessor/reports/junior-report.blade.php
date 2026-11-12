<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
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
    </style>
</head>

<body>
    <table cellpadding="0" cellspacing="0" style="width: 21cm; border-collapse: collapse; margin-left: auto; margin-right: auto; font-family: Roboto Condensed, sans-serif; font-size: 12px; border: 0px; background-color: #fff;">
        <!-- Cover Page -->
        <tr>
            <td>
                <table cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse; font-family: Roboto Condensed, sans-serif; font-size: 12px; border: 0;">
                    <tr style="background-color: #fecd0a; height: 140px; ">
                        <td style="vertical-align: top;">
                            <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px; height: 100%;">
                                <tr>
                                    <td style="width:180px;"></td>
                                    <td style="position: relative; vertical-align: top; width: 200px; height: 100%;">
                                        <img src="{{ asset('/public/assets/reports/green-dot.jpg')}}" alt="" style="width: 50px; height:50px; position: relative; left:-50px; top:0;">
                                        <div style="position: absolute; top: 0; display: flex; align-items: flex-start; z-index: 10; width: 200px; overflow: hidden;">
                                            <div class="logo" style="position: relative; width: 180px;">
                                                <span style="position: absolute; top:0; left:0; width: inherit; padding: 10px; box-sizing: border-box;">
                                                    <img src="{{ asset('/public/assets/reports/cisce-logo.png')}}" alt="" style="width: inherit; margin-top: 20px;">
                                                </span>
                                                <img src="{{ asset('/public/assets/reports/logo-bg.jpg')}}" alt="" style="width: 200px; margin-top: -50px;">
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
                                <img src="{{ asset('/public/assets/reports/report-graphic.png')}}" alt="" style="position: absolute; top:25%; right:-55px; width: 200px; border-collapse: collapse;">
                                <img src="{{ asset('/public/assets/reports/report-cover-j-img.png')}}" alt="" style="width: inherit;">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0;">
                                <tr>
                                    <td style="background-color:#f28f18;">
                                        <img src="{{ asset('/public/assets/reports/aa-bg.png')}}" alt="" style="width:198px;">
                                    </td>
                                    <td style="vertical-align: top; width: 100%;">
                                        <table cellpadding="0" cellspacing="0" style="width: 90%; border: 0;">
                                            <tr>
                                                <td style="height: 50px;"></td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 10px 30px 10px 50px; font-size: 20px; background:#1c9b3e; color:#fff; font-size: 24px; font-weight: 500; position:relative;">Physical Health and Fitness Assessment<span style=" position:absolute; top:46px; right:-20px;"><img src="{{ asset('/public/assets/reports/yellow-bg.jpg')}}" alt="" style="width:20px;"></span></td>
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
                                                                        <td style="border-bottom: 1px solid #ccc; width: 100%; text-align: center; font-weight: 600;">{{ $studentsData->rollno ?? ''}} </td>
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
                                                                        <td><span style="display: inline-block; margin-right: 5px;">DOB</span></td>
                                                                        <td style="border-bottom: 1px solid #ccc; width: 100%; text-align: center; font-weight: 600;">{{ $formattedDob }} ({{ $age }} Years)</td>
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
                                                <td style="height:40px;"></td>
                                            </tr>

                                            <tr>
                                                <td style="text-align:right; padding: 0px 0px 0px 46px;">
                                                    <div style="float:right; text-align:center;">
                                                    <p style="margin-bottom:0px; color:#666; font-size:10px;">Powered by</p>
                                                    <img src="{{ asset('public/assets/reports/fitness365-logo-web.png')}}" alt="fitness365 logo" style="height:28px;">
                                                    </div>
                                                </td>
                                            </tr>


                                        </table>
                                    </td>

                                </tr>
                                <!-- <tr>
                                    <td style="background-color: #1c9b3e; height: 30px;"></td>
                                    <td style="background-color: #fecd0a; height: 30px;"></td>
                                </tr> -->
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <!-- Page 2 -->
        <tr>
             <td style="vertical-align: top; height: 1032px;">
                <table border="1" cellpadding="0" cellspacing="0" style="page-break-before: always; width: 100%; border-collapse: collapse; font-family: Roboto Condensed, sans-serif; font-size: 12px; border:0px solid transparent;">
                    <!-- Inner page Header Area (Page 2) -->
                    <tr style="height: 100px;">
                        <td style="vertical-align: top;">
                            <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px; height: 100%; border:0;">
                                <tr>

                     
                                    <td style="position: relative; vertical-align: top; width: 300px; height: 100%; " >
                                        @if(!empty($studentsData->logo))
                                        <div style="position: absolute; top: 17px; left:30px; display: flex; align-items: center; z-index: 1; width: auto; overflow: hidden;">
                                            <img src="{{ asset('public/assets/uploads/logos/' . $studentsData->logo )}}" alt="" style="width: auto; height:60px;">
                                        </div>
                                        @else 
                                        <div style="margin-left: 30px; margin-top: 30px;">
                                            School Logo -1
                                        </div>
                                        @endif 
                                    </td>

                                    <td rowspan="2" style="position: relative; vertical-align: top; width: auto; height: 100%;">
                                        <div style="position: absolute; top: 30px; right:30px; display: flex; align-items: center; z-index: 1; width: 90px; overflow: hidden;">
                                            <img src="{{ asset('/public/assets/reports/cisce-logo.png')}}" alt="" style="width: inherit;">
                                        </div>
                                        <img src="{{ asset('/public/assets/reports/inner-header-bg.png')}}" alt="" style="width: 550px; height:auto; position: relative; right:0px; top:0;">
                                    </td>

                                </tr>
                            </table>
                        </td>

                    </tr>
                    <!-- Inner page Content (Page 2) -->
                    <tr>
                        <td>
                            <table cellpadding="0" cellspacing="0" style="width: 94%; border: 0; border-collapse: collapse; margin: auto;">
                                <tr>
                                    <td style="border-bottom: 3px solid #1c9b3e;">
                                        <div style="background:#1c9b3e; float:left; display: inline-flex; align-items: center; color: #fff; font-size: 18px; font-weight: 600; height: 32px;">
                                            <div style="float: left; padding: 1px 0px 0px 10px; margin-bottom: -3px;">Fundamental Movement Skills - Class 1 to 3</div>
                                            <div style="float:left; transform: skew(26deg,0deg); display:inline-block; width: 20px; height: 32px; background: #1c9b3e; position: relative; right: -10px;"></div>
                                        </div>
                                    </td>
                                </tr>


                                @foreach($FmsReportData  as $key => $reportData)
                                <tr>
                                    <td style="height: 15px;"></td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 10px; font-size: 20px; background:#f28f18; color:#fff; font-size: 16px; font-weight: 600;">{{ $key }}</td>
                                </tr>

                                <tr>
                                    <td>
                                        <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0; border-collapse: collapse;">
                                            <tr>
                                                <td style="vertical-align: top;">
                                                    <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; border: 1px solid orange; font-size: 13px; border-collapse: collapse; color:#333;">
                                                        <tr style="background-color: #fecd0a;">
                                                            <td style="font-weight: 500; width: 30%; background-color:#fecd0a; padding: 5px 4px 5px 6px; border: 1px solid orange; color:#000;" rowspan="2">Skills</td>
                                                            <td style="font-weight: 500; width: 12%; padding: 5px 4px 5px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Current Term</td>
                                                            <td style="font-weight: 500; width: 14%; padding: 5px 4px 5px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Previous Term</td>
                                                            <td style="font-weight: 500; padding: 5px 4px 5px 6px; border: 1px solid orange; color:#000;" rowspan="2">Recommendation</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-weight: 500; background-color:#ffe167; padding: 5px 4px 5px 6px; border: 1px solid orange; color:#000; text-align: center;">Outcome</td>
                                                            <td style="font-weight: 500; background-color: #ffe167; padding: 5px 4px 5px 6px; border: 1px solid orange; color:#000; text-align: center;">Outcome</td>
                                                        </tr>


                                                        @foreach($reportData as $data)
                                                        <tr>
                                                            <td style="padding: 5px 4px 5px 6px; font-weight: 500; color:#000;">{{ $data['Test_Name'] ?? '' }}</td>
                                                            <td style="padding: 5px 4px 5px 6px; text-align: center;">{{ $data['outcome'] ?? '' }}</td>
                                                            <td style="padding: 5px 4px 5px 6px;"></td>
                                                            <td style="padding: 5px 4px 5px 6px; font-size: 12px;">{{ $data['recommendation'] ?? '' }}</td>
                                                        </tr>
                                                       
                                                        @endforeach

                                                    </table>
                                                </td>
                                            </tr>
                                           
                                        </table>
                                    </td>

                                </tr>


                                @endforeach

                                <tr>
                                    <td style="height: 10px;"></td>
                                </tr>
                                
                                <tr>
                                    <td style="padding: 5px 10px; font-size: 20px; background:#f28f18; color:#fff; font-size: 16px; font-weight: 600;">Developmental Stages for 8 year Boy</td>
                                </tr>

                                <tr>
                                    <td>
                                        <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0; border-collapse: collapse;">
                                            <tr>
                                                <td style="vertical-align: top;">
                                                    <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; border: 1px solid orange; font-size: 13px; border-collapse: collapse; color:#333;">
                                                        <tr>
                                                            <td style="padding: 4px; font-weight: 500; text-align:center;">1</td>
                                                            <td style="padding: 4px; font-weight: 500; color:#000;">Emerging</td>
                                                            <td style="padding: 4px; text-align: left;">Taking first steps with willingness to try and improve!</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 4px; font-weight: 500; text-align:center;">2</td>
                                                            <td style="padding: 4px; font-weight: 500; color:#000;">Developing</td>
                                                            <td style="padding: 4px; text-align: left;">Making great progress with noticeable improvement and effort!</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 4px; font-weight: 500; text-align:center;">3</td>
                                                            <td style="padding: 4px; font-weight: 500; color:#000;">Acquired</td>
                                                            <td style="padding: 4px; text-align: left;">Confidently performing skills with consistency and joy!</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 4px; font-weight: 500; text-align:center;">4</td>
                                                            <td style="padding: 4px; font-weight: 500; color:#000;">Advanced</td>
                                                            <td style="padding: 4px; text-align: left;">Executes skills accurately and efficiently, with growing flexibility!</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 4px; font-weight: 500; text-align:center;">5</td>
                                                            <td style="padding: 4px; font-weight: 500; color:#000;">Accomplished</td>
                                                            <td style="padding: 4px; text-align: left;">Masterful execution with precision, and adaptability!</td>
                                                        </tr>

                                                    </table>
                                                </td>

                                            </tr>
                                            <tr>
                                                <td style="height: 20px;"></td>
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

        <!-- Inner page Footer Area (Page 2) -->
        <tr>
            <td>
                <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                    <!-- <tr>
                        <td style="height: 10px;"></td>
                    </tr> -->
                    <tr>
                        <td style="width:94px;">
                            <div style="float: left; position: relative; width: 80px;">
                                <span style="position: absolute; left: 50%; top:50%; transform: translate(-50%, 0); color: #fff; z-index: 1; display: inline-block; padding: 6px 0 0 30px; font-size: 13px; font-weight: 600;">2</span>
                                    <img src="{{ asset('public/assets/reports/footer-bg.png')}}" alt="" style="width: inherit;">
                            </div>
                        </td>
                        <td style="text-align: right;">
                            <table cellpadding="0" cellspacing="0" style="border: 0px; width: 100%;">
                                <tr>
                                     <td style="height: 20px;"></td>
                                        <td style="height: 20px;"></td>
                                    </tr>
                                <tr>
                                    <td style="font-weight: 400; font-size: 13px; color:#666; text-align:left;">
                                        Physical Health and Fitness Assessment
                                    </td>
                                    <td style="text-align:right; padding: 0px 30px 0px 0px;">
                                        <div style="float:right; text-align:center; position:relative;">
                                            <p style="color:#666; font-size:10px; position:absolute; top:-17px; width:100%; text-align:center;">Powered by</p>
                                                <img src="{{ asset('public/assets/reports/fitness365-logo-web.png')}}" alt="fitness365 logo" style="height:28px;">
                                        </div> 
                                        </td>
                                </tr>
                            </table>            
                        </td>                        
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Page 3 -->
        <tr>
            <td style="vertical-align: top; height: 1032px;">
                <table border="1" cellpadding="0" cellspacing="0" style="page-break-before: always; width: 100%; border-collapse: collapse; font-family: Roboto Condensed, sans-serif; font-size: 12px; border:0px solid transparent;">
                    <!-- Inner page Header Area (Page 3) -->
                    <tr style="height: 110px;">
                        <td style="vertical-align: top;">
                            <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0px; height: 100%; border:0;">
                                <tr>

                                    <td rowspan="2" style="position: relative; vertical-align: top; width: auto; height: 100%;">
                                        <div style="position: absolute; top: 30px; left:30px; display: flex; align-items: center; z-index: 1; width: auto; overflow: hidden;">
                                            <img src="{{ asset('/public/assets/reports/cisce-logo.png')}}" alt="" style="width: auto; height:60px;">
                                        </div>
                                        <img src="{{ asset('/public/assets/reports/inner-header2-bg.png')}}" alt="" style="width: 550px; height:auto; position: relative; left:0px; top:0;">
                                    </td>

                                    
                                    <td style="position: relative; vertical-align: top; width: 300px; height: 100%; text-align: right;" >
                                        @if(!empty($studentsData->logo))
                                        <div style="position: absolute; top: 30px; right:30px; display: flex; align-items: center; z-index: 1; width: auto; overflow: hidden;">
                                            <img src="{{ asset('public/assets/uploads/logos/' . $studentsData->logo )}}" alt="" style="width: auto; height:60px;">
                                        </div>
                                        @else 
                                        <div style="margin-right: 30px; margin-top: 30px;">
                                            School Logo -2
                                        </div>
                                        @endif 
                                    </td>
                                </tr>

                            </table>
                        </td>

                    </tr>
                    <!-- Inner page Content (Page 3) -->
                    <tr>
                        <td>
                            <table cellpadding="0" cellspacing="0" style="width: 94%; border: 0; border-collapse: collapse; margin: auto;">
                                <tr>
                                    <td style="border-bottom: 3px solid #1c9b3e;">
                                        <div style="background:#1c9b3e; float:left; display: inline-flex; align-items: center; color: #fff; font-size: 18px; font-weight: 600; height: 32px;">
                                            <div style="float: left; padding: 1px 0px 0px 10px; margin-bottom: -3px;">Physical Fitness Assessment for Age 5-8 (Class 1-3)</div>
                                            <div style="float:left; transform: skew(26deg,0deg); display:inline-block; width: 20px; height: 32px; background: #1c9b3e; position: relative; right: -10px;"></div>
                                        </div>
                                    </td>
                                </tr>



                                @foreach($orderedReportData as $key => $value)

                                @php
                                    $displayKey = str_contains($key, 'Body Composition')  
                                        ? str_replace('Body Composition (BMI)', 'BMI (Body Mass Index)', $key) 
                                        : $key;

                                    $score = $value[0]['score'] ?? null;
                                    $level = $value[0]['Level'] ?? '---';

                                    if ($key === 'Static Balance (Flamingo Balance Test)' && $score == '1000 times') {
                                        $score = '> 15 times (30s)';
                                        $level = 'Disqualified';
                                    }

                                    if ($key === 'Static Balance (Flamingo Balance Test)' && $score == '500 times') {
                                        $score = '> 26 times (60s)';
                                        $level = 'Disqualified';
                                    }

                                @endphp

                                <tr><td style="height: 15px;"></td> </tr>
                                <tr>
                                    <td style="padding: 6px 10px 6px 0px; font-size: 18px; color:#000; font-size: 16px; font-weight: 600;">{{ $displayKey }}</td>
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
                                                                    <li>Height will be recorded in cm and mm</li>
                                                                    <li>Weight will be recorded in kilogram (kg) and grams(g)</li>
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
                                                                        <td style="width: 20%; background-color:orange; padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;" rowspan="2">Current Term</td>
                                                                        <td style="width: 25%; padding: 4px 0px 4px 0px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Date</td>
                                                                        <td style="width: 28%; padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Weight</td>
                                                                        <td style="width: 28%; padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Height</td>
                                                                        <td style="width: 28%; padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">BMI</td>
                                                                        <td style="padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; font-weight: bold; text-align: center;">Level</td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td style="padding: 4px 0px 4px 0px; font-weight: 500; color:#000; text-align: center;">{{ $value[0]['created_at'] ?? '---'}}</td>
                                                                        <td style="padding: 0px 4px 2px 6px; text-align: center; border: 1px solid orange;">{{ $value[0]['weight'] ?? '---'}} Kg </td>
                                                                        <td style="padding: 0px 4px 2px 6px; text-align: center; border: 1px solid orange;">{{ $value[0]['height'] ?? '---'}} Cm </td>
                                                                        <td style="padding: 0px 4px 2px 6px; text-align: center; border: 1px solid orange;">{{ $value[0]['score'] ?? '---'}}</td>
                                                                        <td style="padding: 0px 4px 2px 6px; text-align: center; border: 1px solid orange;">{{ $value[0]['Level'] ?? '---'}}</td>

                                                                    </tr>

                                                                </table>
                                                            </td>
                                                            <td style="width: 50%;">
                                                                <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; border: 1px solid orange; font-size: 13px; border-collapse: collapse; color:#333;">
                                                                    <tr style="background-color: #fecd0a;">
                                                                        <td style="width: 20%; background-color:orange; padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;" rowspan="2">Previous Term</td>
                                                                        <td style="width: 25%; padding: 4px 0px 4px 0px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Date</td>
                                                                        <td style="width: 28%; padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Weight</td>
                                                                        <td style="width: 28%; padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Height</td>
                                                                        <td style="width: 28%; padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">BMI</td>
                                                                        <td style="padding: 0px 4px 2px 6px; border: 1px solid orange; color:#000; font-weight: bold; text-align: center;">Level</td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td style="padding: 0px 4px 2px 6px; font-weight: 500; color:#000; text-align: center;">---</td>
                                                                        <td style="padding: 4px 0px 4px 0px;  text-align: center; border: 1px solid orange;">--- </td>
                                                                        <td style="padding: 0px 4px 2px 6px; text-align: center; border: 1px solid orange;"> --- </td>
                                                                        <td style="padding: 0px 4px 2px 6px; text-align: center; border: 1px solid orange;"> --- </td>
                                                                        <td style="padding: 0px 4px 2px 6px; text-align: center; border: 1px solid orange;">---</td>

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
                                                                <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; border: 1px solid orange; font-size: 14px; border-collapse: collapse; color:#333;">
                                                                    <tr style="background-color: #fecd0a;">
                                                                        <td style="width: 20%; background-color:orange; padding: 4px 4px 4px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;" rowspan="2">Current Term </td>
                                                                        <td style="width: 25%; padding: 4px 4px 4px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Date</td>
                                                                        <td style="width: 28%; padding: 4px 4px 4px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Score</td>
                                                                        <td style="padding: 4px 4px 4px 6px; border: 1px solid orange; color:#000; font-weight: bold; text-align: center;">Level</td>
                                                                    </tr>

                                                                    <tr>                
                                                                        <td style="padding: 4px 4px 4px 6px; font-weight: 500; color:#000; text-align: center;">{{ $value[0]['created_at'] ?? '---'}}</td>
                                                                        <td style="padding: 4px 4px 4px 6px; text-align: center;">{{ $score ?? '---'}}</td>
                                                                        <td style="padding: 4px 4px 4px 6px; text-align: center;">{{ $level ?? '---'}}</td>

                                                                    </tr>

                                                                </table>
                                                            </td>
                                                            <td style="width: 50%;">
                                                                <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; border: 1px solid orange; font-size: 14px; border-collapse: collapse; color:#333;">
                                                                    <tr style="background-color: #fecd0a;">
                                                                        <td style="font-weight: 500; width: 20%; background-color:orange; padding: 4px 4px 4px 6px; border: 1px solid orange; color:#000; text-align: center;" rowspan="2">Previous Term</td>
                                                                        <td style="font-weight: 500; width: 25%; padding: 4px 4px 4px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Date</td>
                                                                        <td style="font-weight: 500; width: 28%; padding: 4px 4px 4px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Score</td>
                                                                        <td style="font-weight: 500; padding: 4px 4px 4px 6px; border: 1px solid orange; color:#000; font-weight: bold; text-align: center;">Level</td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td style="padding: 4px 4px 4px 6px; font-weight: 500; color:#000; text-align: center;">---</td>
                                                                        <td style="padding: 4px 4px 4px 6px; text-align: center;">---</td>
                                                                        <td style="padding: 4px 4px 4px 6px; text-align: center;">---</td>

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
                                                            <td style="padding: 4px;">{{ $value[0]['recommendation'] ?? '---'}}</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>                    
                                @endif

                                @endforeach

                                <tr><td style="height: 15px;"></td> </tr>
                                <tr><td style="height: 15px;"></td> </tr>
                                <tr>
                                    <td>
                                        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; border: 1px solid #ccc; font-size: 12px; border-collapse: collapse; color:#333;">
                                            <tr style="background-color: #ccc;">
                                                <td style="padding: 5px 10px; font-weight: bold; color:#000; font-size: 16px;" colspan="8">Fitness Benchmarks for a {{ $age }}-year-old-{{ $gender }}</td>
                                            </tr>
                                            <tr style="font-weight: bold; background-color: #eee; font-size: 12px; color: #000;">
                                                <td style="padding: 4px;"></td>
                                                <td style="padding: 4px;">L1 (Very Low)</td>
                                                <td style="padding: 4px;">L2 (Low)</td>
                                                <td style="padding: 4px;">L3 (Developing)</td>
                                                <td style="padding: 4px;">L4 (Moderate)</td>
                                                <td style="padding: 4px;">L5 (Good)</td>
                                                <td style="padding: 4px;">L6 (High)</td>
                                                <td style="padding: 4px;">L7 (Excellent)</td>
                                            </tr>
                                            <tr style="background-color: #eee; font-weight: 500;">
                                                <td style="padding: 4px;"></td>
                                                <td style="padding: 4px;">
                                                    < 20 %ile</td>
                                                <td style="padding: 4px;">≥ 20 %ile</td>
                                                <td style="padding: 4px;">≥ 40 %ile</td>
                                                <td style="padding: 4px;">≥ 60 %ile</td>
                                                <td style="padding: 4px;">≥ 70 %ile</td>
                                                <td style="padding: 4px;">≥ 80 %ile</td>
                                                <td style="padding: 4px;">≥ 90 %ile</td>
                                            </tr>


                                          
                                            @foreach($getFitnessBenchmark as $key => $skillname)
                                            <tr>
                                                <td style="padding: 4px; font-weight: bold; color: #000;">{{ $skillname->skill_name }}</td>
                                                @foreach($skillname->ranges as $level => $range)
                                                    <td style="padding: 4px;">{{ $range }}</td>
                                                @endforeach
                                            </tr>
                                            @endforeach

                                            

                                        </table>
                                    </td>
                                </tr>

                                                                <tr>
                                    <td style="height: 15px;"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; border: 0px; font-size: 14px; border-collapse: collapse; color:#333; text-align:left;">
                                            <tr style="background-color: #ccc;">
                                                <td style="padding: 5px 10px; font-weight: bold; color:#000; font-size: 16px;" colspan="8">BMI Benchmarks for  a {{ $age }}-year-old-{{ $gender }}</td>
                                            </tr>
                                            <tr style="font-weight: bold; background-color: #eee; font-size: 12px; color: #000; text-align: center;">
                                                <td style="padding: 5px 10px;">UW</td>
                                                <td style="padding: 5px 10px;">N</td>
                                                <td style="padding: 5px 10px;">OW</td>
                                                <td style="padding: 5px 10px;">OB</td>
                                            </tr>
                                            
                                            
                                            @if(is_array($getBmiBenchmark) && count($getBmiBenchmark) > 0)
                                            <tr style="text-align: center;">
                                                <td style="padding: 5px 10px; color: #000;">{{ $getBmiBenchmark['UW'] ?? 'N/A' }}</td>
                                                <td style="padding: 5px 10px; color: #000;">{{ $getBmiBenchmark['N'] ?? 'N/A' }}</td>
                                                <td style="padding: 5px 10px; color: #000;">{{ $getBmiBenchmark['OW'] ?? 'N/A' }}</td>
                                                <td style="padding: 5px 10px; color: #000;">{{ $getBmiBenchmark['OB'] ?? 'N/A' }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="height: 15px;"></td>
                                </tr>

                                
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 130px;"></td>
                    </tr>                              

                </table>
            </td>
        </tr>

       <!-- Inner page Footer Area (Page 3) -->
        <tr>
            <td>
                <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                                <tr>
                                    <td style="text-align: right; padding-right:30px;">
                                        <table cellpadding="0" cellspacing="0" style="border: 0px; width: 100%;">
                                            <tr>
                                                <td style="height: 24px;"></td>
                                                <td style="height: 24px;"></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align:left; padding: 0px 0px 0px 30px;">
                                                    <div style="float:left; text-align:center; position:relative;">
                                                        <p style="color:#666; font-size:10px; position:absolute; top:-17px; width:100%; text-align:center;">Powered by</p>
                                                        <img src="{{ asset('/public/assets/reports/fitness365-logo-web.png')}}" alt="fitness365 logo" style="height:28px;">
                                                    </div> 
                                                </td>

                                                <td style="font-weight: 400; font-size: 13px; color:#666; text-align:right;">
                                                    Physical Health and Fitness Assessment
                                                </td>
                                                
                                            </tr>

                                        </table>
                                    </td>

                                    <td style="width:94px;">
                                        <div style="float: left; position: relative; width: 80px;">
                                            <span style="position: absolute; left: 50%; top:50%; transform: translate(-50%, 0); color: #fff; z-index: 1; display: inline-block; padding: 6px 30px 0 0px; font-size: 13px; font-weight: 600;">3</span>
                                            <img src="{{ asset('/public/assets/reports/footer-bg2.png')}}" alt="" style="width: inherit;">
                                        </div>
                                    </td>

                                </tr>
                            </table>
            </td>
        </tr>

        <!-- Page 4 -->
        <tr>
            <td style="vertical-align: top; height: 1032px;">
                <table border="1" cellpadding="0" cellspacing="0" style="page-break-before: always; width: 100%; border-collapse: collapse; font-family: Roboto Condensed, sans-serif; font-size: 12px; border:0px solid transparent;">
                    <!-- Inner page Header Area (Page 4) -->
                    <tr style="height: 100px;">
                        <td style="vertical-align: top;">
                            <table cellpadding="0" cellspacing="0" style="width: 100%; height: 100%; border:0;">
                                <tr>


                       


                                    <td style="position: relative; vertical-align: top; width: 300px; height: 100%; text-align: right;" >
                                        @if(!empty($studentsData->logo))
                                        <div style="position: absolute; top: 17px; left:30px; display: flex; align-items: center; z-index: 1; width: auto; overflow: hidden;">
                                            <img src="{{ asset('public/assets/uploads/logos/' . $studentsData->logo )}}" alt="" style="width: auto; height:60px;">
                                        </div>
                                        @else 
                                        <div style="margin-left: 30px; margin-top: 30px;">
                                            School Logo -3
                                        </div>
                                        @endif 
                                    </td>



                                    <td rowspan="2" style="position: relative; vertical-align: top; width: auto; height: 100%;">
                                        <div style="position: absolute; top: 30px; right:30px; display: flex; align-items: center; z-index: 1; width: auto; overflow: hidden;">
                                            <img src="{{ asset('/public/assets/reports/cisce-logo.png')}}" alt="" style="width: auto; height:60px;">
                                        </div>
                                        <img src="{{ asset('/public/assets/reports/inner-header-bg.png')}}" alt="" style="width: 550px; height:auto; position: relative; right:0px; top:0;">
                                    </td>

                                </tr>
                            </table>
                        </td>

                    </tr>
                    <!-- Inner page Content (Page 4) -->
                    <tr>
                        <td>
                            <table cellpadding="0" cellspacing="0" style="width: 94%; border: 0; border-collapse: collapse; margin: auto; color: #333; font-size: 12px;">
                                <tr>
                                    <td style="border: 1px solid orange; padding: 10px 15px 8px 15px; background:#FEFBEF;">
                                        <h3 style="color: #ED6D1E; margin-bottom: 6px; font-size: 18px;">WHO Guidelines on Physical Activity and Sedentary Behaviour 2020</h3>
                                        <h4 style="color: #000; margin-bottom: 5px; font-size: 16px">Age-Appropriate Fitness Protocols and Guidelines for Ages 5–18 Years</h4>
                                        <p style="line-height: 1.25rem;">At least an average of 60 minutes per day of moderate-to-vigorous intensity physical activity, across the week; most of this physical activity should be aerobic.</p>
                                        <p style="line-height: 1.25rem;">Vigorous-intensity aerobic activities, as well as those that strengthen muscle and bone should be incorporated at least 3 days a week.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height: 15px;"></td>
                                </tr>

                                <tr>
                                    <td>
                                        <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0; border-collapse: collapse;">
                                            <tr>
                                                <td colspan="2">
                                                    <h3 style="color: #000; font-size: 16px; margin-bottom: 5px;">Recommended Activities for Improving Fundamental Movement Skills for Ages 5–8 Years (Class 1–3)</h3>
                                                    <p>The focus is on the development of key Fundamental Movement Skills that are required for life. Fit India recommends the following activities for improvement of fitness for ages 5–8:</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="height: 6px;"></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <h4 style="color: #000; margin-bottom: 5px; font-size: 14px;">1. Locomotor Skills</h4>
                                                    <p>Walking, Running, Leaping/Jumping, Hopping/Skipping/Galloping, Sliding/Crawling/Rolling/rotating</p>
                                                </td>
                                                <td>
                                                    <h4 style="color: #000; margin-bottom: 5px; font-size: 14px;">2. Manipulative Skills</h4>
                                                    <p>Throwing, Catching, Bouncing/Dribbling, Trapping, Kicking (with hand/with leg), Volleying, Striking, etc.</p>
                                                </td>

                                            </tr>
                                            <tr>
                                                <td style="height: 10px;"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <h4 style="color: #000; margin-bottom: 5px; font-size: 14px;">3. Body Management/Non-locomotor Skills</h4>
                                                    <p>Curling, Stretching, Twisting / Turning / Spinning, Pushing / Pulling, Rocking, Swinging / Pivoting, Balancing / Counter Balancing, Counter-tension etc.</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>

                                </tr>
                                <tr>
                                    <td style="height: 30px;"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0; border-collapse: collapse;">
                                            <tr>
                                                <td colspan="2">
                                                    <h3 style="color: #000; font-size: 16px; margin-bottom: 10px;">Recommended Activities for Improving Fitness for Ages 5–8 Years</h3>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <h4 style="color: #000; margin-bottom: 5px; font-size: 14px;">1. Hand-Eye Coordination Activities</h4>
                                                    <p>Ball Toss Against Wall, Juggling with Scarves or Balls, Target Throws, Table Tennis / Bat & Ball, Clap-Catch Game, Reaction Ball Drill</p>
                                                </td>
                                                <td>
                                                    <h4 style="color: #000; margin-bottom: 5px; font-size: 14px;">2. One-Leg Balance Activities</h4>
                                                    <p>Single-Leg Stance, Balance with Arm Movements, Reach-and-Touch, Leg Swings, Tree Pose (Yoga), Balance Pad / Cushion Stance</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="height: 32px;"></td>
                                </tr>

                                <tr>
                                    <td>
                                        <h2 style="font-size: 24px; color:#ED6D1E; font-size: 18px; font-weight: 600; text-align: center;">EXERCISE AND PHYSICAL ACTIVITY</h2>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size: 20px; color:#000; font-size: 16px; font-weight: 500; text-align: center;">
                                        <p>* Energy expenditure on various physical activities (Kcal/Hr)</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height: 80px;"></td>
                                </tr>

                                <tr>
                                    <td>
                                        <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0; border-collapse: collapse;">
                                            <tr>
                                                <td style="vertical-align: top;" colspan="3">
                                                    <div style="display: flex; gap: 5px;">
                                                        <table border="1" cellpadding="0" cellspacing="0" style="width: 220px; border: 1px solid #EFB9B8; font-size: 12px; border-collapse: collapse; color:#333;">
                                                            <tr style="background-color: #FBE1E0;">
                                                                <td style="width: 155px; background-color:#FBE1E0; padding: 3px 4px; border: 1px solid #EFB9B8; color:#7B2321; font-weight: bold;" colspan="2">Activity</td>
                                                                <td style="padding: 3px 4px; border: 1px solid #EFB9B8; color:#7B2321; text-align: right; font-weight: bold;">Kcal/hr</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;">Sleeping</td>
                                                                <td style="text-align: right; padding: 3px 4px; border-left: 1px solid transparent;"></td>
                                                                <td style="text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">44</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;">Sitting</td>
                                                                <td style="text-align: right; padding: 3px 4px; border-left: 1px solid transparent;"></td>
                                                                <td style="text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">57</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;">Standing</td>
                                                                <td style="text-align: right; padding: 3px 4px; border-left: 1px solid transparent;"></td>
                                                                <td style="text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">63</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;">Walking</td>
                                                                <td style="text-align: right; padding: 3px 4px; border-left: 1px solid transparent;">6.43 km/hr</td>
                                                                <td style="text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">227</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;">Climbing Stairs</td>
                                                                <td style="text-align: right; padding: 3px 4px; border-left: 1px solid transparent;"></td>
                                                                <td style="text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">485</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;">Housecleaning</td>
                                                                <td style="text-align: right; padding: 3px 4px; border-left: 1px solid transparent;"></td>
                                                                <td style="text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">176</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;">Gardening</td>
                                                                <td style="text-align: right; padding: 3px 4px; border-left: 1px solid transparent;"></td>
                                                                <td style="text-align: right; padding: 3px 4px;">227</td>
                                                            </tr>
                                                        </table>

                                                        <table border="1" cellpadding="0" cellspacing="0" style="width: 200px; border: 1px solid #EFB9B8; font-size: 12px; border-collapse: collapse; color:#333;">
                                                            <tr style="background-color: #FBE1E0;">
                                                                <td style="width:165px; background-color:#FBE1E0; padding: 3px 4px; border: 1px solid #EFB9B8; color:#7B2321; font-weight: bold;" colspan="2">Activity</td>
                                                                <td style="padding: 3px 4px; border: 1px solid #EFB9B8; color:#7B2321; text-align: right; font-weight: bold;">Kcal/hr</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;">Cycling</td>
                                                                <td style="text-align: right; padding: 3px 4px; border-left: 1px solid transparent;">City cycling</td>
                                                                <td style="text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">302</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;"></td>
                                                                <td style="text-align: right; padding: 3px 4px; border-left: 1px solid transparent;">16.1 km/hr </td>
                                                                <td style="text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">391</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;"></td>
                                                                <td style="text-align: right; padding: 3px 4px; border-left: 1px solid transparent;">22.53 km/hr</td>
                                                                <td style="text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">567</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;"></td>
                                                                <td style="text-align: right; padding: 3px 4px; border-left: 1px solid transparent;">32.18 km/hr</td>
                                                                <td style="text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">932</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;">Running</td>
                                                                <td style="text-align: right; padding: 3px 4px; border-left: 1px solid transparent;">8 km/hr</td>
                                                                <td style="text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">523</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;"></td>
                                                                <td style="text-align: right; padding: 3px 4px; border-left: 1px solid transparent;">10 km/hr</td>
                                                                <td style="text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">617</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;"></td>
                                                                <td style="text-align: right; padding: 3px 4px; border-left: 1px solid transparent;">13 km/hr</td>
                                                                <td style="text-align: right; padding: 3px 4px;">743</td>
                                                            </tr>
                                                        </table>

                                                        <table border="1" cellpadding="0" cellspacing="0" style="width: 170px; border: 1px solid #EFB9B8; font-size: 12px; border-collapse: collapse; color:#333;">
                                                            <tr style="background-color: #FBE1E0;">
                                                                <td style="width:135px; background-color:#FBE1E0; padding: 3px 4px; border: 1px solid #EFB9B8; color:#7B2321; font-weight: bold;">Activity</td>
                                                                <td style="font-weight: 500; padding: 3px 4px; border: 1px solid #EFB9B8; color:#7B2321; text-align: right; font-weight: bold;">Kcal/hr</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;">Billiards/ Snooker</td>
                                                                <td style="text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">176</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;">Roller Skating</td>
                                                                <td style="font-weight: 500; text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">365</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;">Swimming</td>
                                                                <td style="font-weight: 500; text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">302</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;">Horseback Riding</td>
                                                                <td style="font-weight: 500; text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">202</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;">Squash</td>
                                                                <td style="font-weight: 500; text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">491</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;">Badminton</td>
                                                                <td style="font-weight: 500; text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">441</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;">Table Tennis</td>
                                                                <td style="font-weight: 500; text-align: right; padding: 3px 4px;">277</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <p style="font-size: 12px; margin-top: 5px; line-height: 1.15rem;">*Approx. energy expenditure for a 60 kg reference man. Individuals with higher body weight will expend more calories than those with lower body weight. A reference woman (50 kg) will expend 5% fewer calories.</p>
                                                </td>

                                                <td style="vertical-align: top;">
                                                    <div style="display: flex; gap: 5px;">
                                                        <table border="1" cellpadding="0" cellspacing="0" style="width: 140px; border: 1px solid #EFB9B8; font-size: 12px; border-collapse: collapse; color:#333;">
                                                            <tr style="background-color: #FBE1E0;">
                                                                <td style="width: 125px; background-color:#FBE1E0; padding: 3px 4px; border: 1px solid #EFB9B8; color:#7B2321; font-weight: bold;">Activity</td>
                                                                <td style="font-weight: 500; padding: 3px 4px; border: 1px solid #EFB9B8; color:#7B2321; text-align: right; font-weight: bold;">Kcal/hr</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px; ">Tennis</td>
                                                                <td style="font-weight: 500; text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">353</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;">Volleyball</td>
                                                                <td style="font-weight: 500; text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">252</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;">Football</td>
                                                                <td style="font-weight: 500; text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">441</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;">Basketball</td>
                                                                <td style="font-weight: 500; text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">403</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;">Dancing</td>
                                                                <td style="font-weight: 500; text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">302</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;">Gymnastic</td>
                                                                <td style="font-weight: 500; text-align: right; padding: 3px 4px; border-bottom: 1px solid transparent;">202</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;">Yoga</td>
                                                                <td style="font-weight: 500; text-align: right; padding: 3px 4px;">195</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 500; color:#000; text-align: left; padding: 3px 4px;">HIIT Workout</td>
                                                                <td style="font-weight: 500; text-align: right; padding: 3px 4px;">504</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </td>

                                            </tr>
                                        </table>
                                    </td>

                                </tr>
                                <tr>
                                    <td style="height: 40px;"></td>
                                <tr>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>
                                        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; border: 1px solid orange; font-size: 14px; border-collapse: collapse; color:#333;">
                                            <tr>
                                                <td style="font-weight: 600; padding: 10px 15px; font-size: 14px; color: #000; height:100px; vertical-align:top;">PE Teacher's Observations and Comments (if any)</td>
                                                <td style="font-weight: 600; padding: 10px 15px; font-size: 14px; color: #000; text-align:center; height:100px; vertical-align:bottom;">
                                                    @if($studentsData->hod_sign)
                                                        <div style="margin: 10px 0 0 0; text-align:center;">
                                                            <img src="{{ asset('public/assets/uploads/hod-signatures/' . $studentsData->hod_sign) }}" alt="" style="height: 70px;">
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
                                                    <div style="padding: 15px; height: 40px; margin: 10px 0; background-color: #fff; border-right: 3px solid #fff;"></div>
                                                    <p style="text-align: center; font-weight: 600;">Parent's Signature</p>
                                                </td>
                                                <td style="border: 1px solid transparent; width:50%;">
                                                    @if($studentsData->signature)
                                                        <div style="margin: 10px 0 0 0; text-align:center;">
                                                            <img src="{{ asset('public/assets/uploads/signatures/' . $studentsData->signature) }}" alt="" style="height: 70px;">
                                                        </div>
                                                    @endif
                                                    <p style="text-align: center; font-weight: 600;">Signature of Principal with Stamp</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height: 20px;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Inner page Footer Area (Page 4) -->
                    <tr>
                        <td>
                            <table cellpadding="0" cellspacing="0" style="width: 100%; border:0;">
                                <tr>
                                    <td style="background-color: #1c9b3e; height: 32px; width: 30%; padding: 0 30px; color:#fff;">CISCE Physical Health and Fitness Assessment</td>
                                    <td style="background-color: #fecd0a; height: 32px; width: 30%; padding: 0 30px; text-align:right;">Powered by <span style="font-weight:500;">fitness365.me</span></td>
                                </tr>
                            </table>
                        </td>

                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>

</html>