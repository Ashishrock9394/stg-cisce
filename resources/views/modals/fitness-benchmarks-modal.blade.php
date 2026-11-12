<div id="fitnessBenchmark" class="modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-content">
        <span class="close">&times;</span>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; margin-top:30px; border: 0px; font-size: 14px; border-collapse: collapse; color:#333; text-align:left;">
            <tr>
                <td>
                    <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; border: 1px solid #0A87CD; font-size: 12px; border-collapse: collapse; color:#000;">
                        <tr style="background-color: #0A87CD;">
                            <td style="padding: 5px 10px; font-weight: bold; color:#fff; font-size: 14px;" colspan="8">Fitness Benchmarks for {{ $studentAge }} years {{ $studentData->gender }}</td>
                        </tr>
                        <tr style="font-weight: bold; background-color: #fecd0a; font-size: 12px; color: #000;">
                            <td style="padding: 4px;"></td>
                            <td style="padding: 4px;">L1 (Very Low)</td>
                            <td style="padding: 4px;">L2 (Low)</td>
                            <td style="padding: 4px;">L3 (Developing)</td>
                            <td style="padding: 4px;">L4 (Moderate)</td>
                            <td style="padding: 4px;">L5 (Good)</td>
                            <td style="padding: 4px;">L6 (High)</td>
                            <td style="padding: 4px;">L7 (Excellent)</td>
                        </tr>
                        <tr style="background-color: #fff6d1; font-weight: 500; color:#333;">
                            <td style="padding: 4px;"></td>
                            <td style="padding: 4px;">< 20 %ile</td>
                            <td style="padding: 4px;">≥ 20 %ile</td>
                            <td style="padding: 4px;">≥ 40 %ile</td>
                            <td style="padding: 4px;">≥ 60 %ile</td>
                            <td style="padding: 4px;">≥ 70 %ile</td>
                            <td style="padding: 4px;">≥ 80 %ile</td>
                            <td style="padding: 4px;">≥ 90 %ile</td>
                        </tr>
                        
                        @forelse($getFitnessBenchmark as $key => $skillname)
                        <tr>            
                            <td style="padding: 4px; font-weight: bold; color: #000;">{{ $skillname->skill_name }}</td>
                            @foreach($skillname->ranges as $level => $range)
                                <td style="padding: 4px;">{{ $range }}</td>
                            @endforeach
                        </tr>
                        @empty
                                <tr>                                                  
                                <td style="padding: 4px;" colspan="8"> 
                                    <p style="text-align:center;"> <span style="padding: 4px; font-weight: bold; color: #000;">Note : </span> No Fitness Benchmarks available for a {{ $studentAge }}-year-old {{ $studentData->gender }}.</p>
                                </td>
                                
                                </tr>                                                 
                        @endforelse       

                    </table>
                </td>
            </tr>
        </table>
    </div>
</div>
