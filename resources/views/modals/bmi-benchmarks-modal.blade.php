<div id="bmiModal" class="modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-content">
        <span class="close">&times;</span>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%; margin-top:30px; border: 0px; font-size: 14px; border-collapse: collapse; color:#333; text-align:left;">
            <tr style="background-color: #ccc;">
                <td style="padding: 5px 10px; font-weight: bold; color:#000; font-size: 16px;" colspan="8">BMI Benchmarks for {{ $studentAge }} years {{ $studentData->gender }}</td>
            </tr>
            <tr style="font-weight: bold; text-align: center; background-color: #eee; font-size: 12px; color: #000;">
                <td>UW</td>
                <td>N</td>
                <td>OW</td>
                <td>OB</td>
            </tr>
                    
            @if(is_array($getBmiBenchmark) && count($getBmiBenchmark) > 0)
            <tr>
                <td class="text-center" style="padding: 5px 10px; color: #000;">{{ $getBmiBenchmark['UW'] ?? 'N/A' }}</td>
                <td class="text-center" style="padding: 5px 10px; color: #000;">{{ $getBmiBenchmark['N'] ?? 'N/A' }}</td>
                <td class="text-center" style="padding: 5px 10px; color: #000;">{{ $getBmiBenchmark['OW'] ?? 'N/A' }}</td>
                <td class="text-center" style="padding: 5px 10px; color: #000;">{{ $getBmiBenchmark['OB'] ?? 'N/A' }}</td>
            </tr>
            @endif
        </table>
    </div>
</div>