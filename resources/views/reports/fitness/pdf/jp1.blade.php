<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Junior Reports</title>
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
            background-color: #fff;
        }

        page[size="A4"] {
            width: 21cm;
            height: 29.7cm;
            margin: 0;
            padding:0;
        }
        .act-tbl {
            width:100%;
        }
        .act-tbl td {
            border-bottom:1px solid #EFB9B8;
        }
        .page-3 td {
            margin: 0;
            padding: 2px 4px;
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
    <!-- Page 3 -->
    <table border="0" cellpadding="0" cellspacing="0" style="width:100%; page-break-before: always;">
        <tr>
            <td>
                 <!-- Inner page Header Area (Page 3) -->
                <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse; font-family: Roboto Condensed, sans-serif; font-size: 12px; border:0px solid transparent;">   
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


                                        <td style="width:300px; text-align: right;">
                                        	@if(!empty($studentsData->logo))
                                        		<div style="position: absolute; top: 30px; right:30px; display: flex; align-items: center; z-index: 1; width: auto; overflow: hidden;"><img src="{{ public_path('assets/uploads/logos/' . $studentsData->logo )}}" alt="" style="width: auto; height:60px;">
                                        		</div>
                                        	@else 
	                                        	<div style="margin-right: 30px; margin-top: 30px;">
	                                                School Logo
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
                <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                    <tr>
                        <td>
                            <table border="1" cellpadding="0" cellspacing="0" style="width: 94%; border: 0; border-collapse: collapse; margin: auto;">
                                <tr>
                                    <td style="text-indent: 0;">
                                        <div style="align-items: left; color: #fff; font-size: 18px; font-weight: 600; height: 32px; border-bottom: 3px solid #1c9b3e; ">
                                            <div style="float: left; padding: 1px 10px 3px 10px; display:inline-block; background: #1c9b3e;">Developmental Skills for {{ $studentsData->display_classname }}-{{ $studentsData->section }}</div><div style="text-align:right; float:left;"><img src="{{ public_path('assets/reports/heading-band-corder.jpg')}}" alt="" style="width: 33px; position:relative; top:1px;"></div>
                                        </div>
                                    </td>
                                </tr>

                               @foreach($FmsReportData  as $key => $reportData)
                                <tr>
                                    <td style="height: 15px;"></td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 10px; font-size: 20px; background:orange; color:#fff; font-size: 16px; font-weight: 600;">{{ $key }}</td>
                                </tr>

                                <tr>
                                    <td>
                                        <table cellpadding="0" cellspacing="0" style="width: 100%; border: 0; border-collapse: collapse;">
                                            <tr>
                                                <td style="vertical-align: top;">
                                                    <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; border: 1px solid orange; font-size: 13px; border-collapse: collapse; color:#333;">
                                                        <tr style="background-color: #fecd0a;">
                                                            <td style="font-weight: 500; width: 27%; background-color:#fecd0a; padding: 3px 4px 3px 6px; border: 1px solid orange; color:#000;" rowspan="2">Skills</td>
                                                            <td style="font-weight: 500; width: 12%; padding: 3px 4px 3px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Current Term</td>
                                                            <td style="font-weight: 500; width: 14%; padding: 3px 4px 3px 6px; border: 1px solid orange; color:#000; text-align: center; font-weight: bold;">Previous Term</td>
                                                            <td style="font-weight: 500; padding: 3px 4px 3px 6px; border: 1px solid orange; color:#000;" rowspan="2">Recommendation</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-weight: 500; background-color:#ffe167; padding: 3px 4px 3px 6px; border: 1px solid orange; color:#000; text-align: center;">Outcome</td>
                                                            <td style="font-weight: 500; background-color: #ffe167; padding: 3px 4px 3px 6px; border: 1px solid orange; color:#000; text-align: center;">Outcome</td>
                                                        </tr>


                                                        @foreach($reportData as $data)
                                                        <tr>
                                                            <td style="padding: 3px 4px 3px 6px; font-weight: 500; color:#000;">{{ $data['Test_Name'] ?? '' }}</td>

                                                            <td align="center">
                                                                @if($data['current_count'] !== null && $data['current_count'] > 0)
                                                                    <img src="{{ asset('public/assets/imgs/'.$data['current_count'].'smiles.png') }}" height="20" style="padding-left:5px; padding-right:5px;">
                                                                @else
                                                                    NA
                                                                @endif
                                                            </td>

                                                            <td align="center">
                                                                @if($data['previous_count'] !== null && $data['previous_count'] > 0)
                                                                    <img src="{{ asset('public/assets/imgs/'.$data['previous_count'].'smiles.png') }}" height="20" style="padding-left:5px; padding-right:5px;">
                                                                @else
                                                                    NA
                                                                @endif
                                                            </td>
                                                     
                                                            <td style="padding: 3px 4px 3px 6px; font-size: 12px;">{{ $data['recommendation'] ?? '' }}</td>

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
                                    <td style="height: 15px;"></td>
                                </tr>

                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
            <!-- Inner page Footer Area (Page 3) -->
        <tr>
            <td>
                <table cellpadding="0" cellspacing="0" style="width:100%;">
                    <tr>
                        <td style="width: 100%; text-align: right;">
                            <table cellpadding="0" cellspacing="0" style="border: 0px; width: 100%;">
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
</body>
</html>