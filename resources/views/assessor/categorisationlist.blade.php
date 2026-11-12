<div class="row text-center justify-content-md-center mt-5 mt-lg-4">
    <div class="col-12 col-md-12 col-lg-12">
        <div class="all-tests mb-5" id="accordionContainer">                          
            
            <!-- Item 1 (Expanded by default) -->
            <h4 class="test-cat d-flex justify-content-between" 
                data-toggle="collapse" 
                data-target="#juniorDataList" 
                role="button" 
                aria-expanded="true" 
                aria-controls="juniorDataList" 
                style="cursor: pointer;">
                <div>Development Skills for Age 5-8 (Class 1-3)</div>          
                <div>
                    <!-- Plus icon (transforms into dash/minus via CSS when expanded) -->
                    <span class="arrow-i"><i class="bi bi-plus-lg"></i></span>
                </div>
            </h4>

            <div class="collapse show" id="juniorDataList" data-parent="#accordionContainer">
                <ul class="list-group mt-0">
                    @foreach($juniorData as $key => $val)
                        <li>
                            <a href="{{ route('assessor-app-test', ['TestcategoryId' => $val->TestCategoryID]) }}">
                                <span>{{ $val->TestCategoryName }}</span>
                                <span class="arrow-i"><i class="bi bi-arrow-right"></i></span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <br>

            <!-- Item 2 -->
            <h4 class="test-cat d-flex justify-content-between" 
                data-toggle="collapse" 
                data-target="#juniorDataList1" 
                role="button" 
                aria-expanded="false" 
                aria-controls="juniorDataList1" 
                style="cursor: pointer;">
                <div>Physical Fitness Assessment for Age 5-8 (Class 1-3)</div>
                <div>
                    <span class="arrow-i"><i class="bi bi-plus-lg"></i></span>
                </div>
            </h4>

            <div class="collapse" id="juniorDataList1" data-parent="#accordionContainer">
                <ul class="list-group mt-0">
                    @foreach($juniorData1 as $key1 => $val1)
                        <li>
                            <a href="{{ route('assessor.app.physical.test', ['TestcategoryId' => $val1->TestCategoryID]) }}">
                                <span>{{ $val1->TestCategoryName }}</span>
                                <span class="arrow-i"><i class="bi bi-arrow-right"></i></span>
                            </a>
                        </li>
                    @endforeach    
                </ul>
            </div>

            <br>

            <!-- Item 3 -->
            <h4 class="test-cat d-flex justify-content-between" 
                data-toggle="collapse" 
                data-target="#seniorDataList" 
                role="button" 
                aria-expanded="false" 
                aria-controls="seniorDataList" 
                style="cursor: pointer;">
                <div>Physical Fitness Assessment for Age 9-18 (Class 4-12)</div>
                <div>
                    <span class="arrow-i"><i class="bi bi-plus-lg"></i></span>
                </div>
            </h4>

            <div class="collapse" id="seniorDataList" data-parent="#accordionContainer">
                <ul class="list-group mt-0">
                    @foreach($seniorData as $keys => $vals)
                        <li>
                            @if($vals->TestCategoryID == 3)
                                <a href="{{ route('senior.bmi.test', ['TestcategoryId' => $vals->TestCategoryID, 'SeniorBMI'=>True]) }}">
                                    <span>{{ $vals->TestCategoryName }} </span>
                                    <span class="arrow-i"><i class="bi bi-arrow-right"></i></span>
                                </a>
                            @else
                                <a href="{{ route('assessor.app.physical.test', ['TestcategoryId' => $vals->TestCategoryID]) }}">
                                    <span>{{ $vals->TestCategoryName }} </span>
                                    <span class="arrow-i"><i class="bi bi-arrow-right"></i></span>
                                </a>  
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

            <br>

            <!-- Item 4 -->
            <h4 class="test-cat d-flex justify-content-between" 
                data-toggle="collapse" 
                data-target="#cwsnDataList" 
                role="button" 
                aria-expanded="false" 
                aria-controls="cwsnDataList" 
                style="cursor: pointer;">
                <div>Physical Fitness Assessment for CWSN</div>
                <div>
                    <span class="arrow-i"><i class="bi bi-plus-lg"></i></span>
                </div>
            </h4>

            <div class="collapse" id="cwsnDataList" data-parent="#accordionContainer">
                <ul class="list-group mt-0">                        
                    @foreach($cwsnCategory as $category)
                        @php $encrypted = encrypt($category->TestCategoryID); @endphp
                        <li>
                            <a href="{{ route('assessor.cwsn.category', ['TestcategoryId' => $encrypted]) }}">
                                <span>{{ $category->TestCategoryName }}</span>
                                <span class="arrow-i"><i class="bi bi-arrow-right"></i></span>
                            </a>
                        </li>
                    @endforeach                    
                </ul>
            </div>

        </div>
    </div>
</div>