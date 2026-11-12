<style type="text/css">    
table#students-sports-mapping-table th.dt-orderable-none .dt-column-order { display: none !important; }
.dt-container .top .filter-right { order: 2; margin-right: 8%; margin-top: 5px; }

.dt-button-collection.dropdown-menu {
    left: auto !important;
    right: 4px !important;
    top: 36px !important;
    border-radius: 8px 0px 8px 8px;
    border: 1px solid var(--navi-color);
    box-shadow: 0.05em 0.05em 0.15rem var(--navi-color);
}

.dt-search input[type=search] {
    width: 180px !important;
    border: none;
    font-size: 13px;
}

</style>

<div class="responsive mt-4 pt-2">
    <div class="datatable-wrapper">

        @isset($actionsOutside)
            <div class="datatable-actions-outside d-flex justify-content-between align-items-center mb-3">
                {{ $actionsOutside }}
            </div>
        @endisset

        <div class="datatable-toolbar d-flex justify-content-between align-items-center mb-3">
            <div id="{{ $id }}-filters" class="d-flex align-items-center gap-2"></div>
        </div>

        <table id="{{ $id }}" class="table table-bordered tbl-style w-100">
            <thead>
                <tr>
                    <th style="width:40px;"><input type="checkbox" id="{{ $id }}-select-all" /></th>
                    @foreach($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
        </table>
    </div>

  
    @isset($actionsInside)
        <div data-filter-slot-for="#{{ $id }}" style="display:none;">
            {{ $actionsInside }}
        </div>
    @endisset

</div>


@push('scripts')
<script>
$(function () {
    const tableId = '#{{ $id }}';
    const selectAllId = '#{{ $id }}-select-all';
    const selectedIds = new Set();

    const selectedClass = @json($selectedClass ?? null); 
    const selectedOnlyClass = @json($selectedOnlyClass ?? null); 
    const selectedSkillName = @json($selectedSkillName ?? null); 
    const selectedSection = @json($selectedSection ?? null);
    const selectedStatus = @json($selectedStatus ?? null);
    const selectedTestStatus = @json($selectedTestStatus ?? null);
    const selectedStudentCategory = @json($selectedStudentCategory ?? null);
    const enableClassFilter = @json($enableClassFilter ?? false);
    const enableOnlyClassFilter = @json($enableOnlyClassFilter ?? false);
    const enableSkillNameFilter = @json($enableSkillNameFilter ?? false);
    const enableTestStatusFilter = @json($enableTestStatusFilter ?? false);
    const enableStudentCategoryFilter = @json($enableStudentCategoryFilter ?? false);
    const enableStatusFilter = @json($enableStatusFilter ?? false);
    const enableClassSectionFilter = @json($enableClassSectionFilter ?? false);

    const enableSchoolTermsFilter = @json($enableSchoolTermsFilter ?? false);


    const enableLengthMenu = @json($enableLengthMenu ?? true);
    const pageLength = @json($pageLength ?? 100);
    const exportButtonText = @json($exportButtonText ?? null);

    const buttonsConfig = [];


    @foreach($exportButtons as $btn)

        @if(isset($btn['type']) && $btn['type'] !== 'custom')
            buttonsConfig.push({
                extend: '{{ $btn['type'] }}',
                text: '{{ $btn['text'] }}',
                exportOptions: {
                    columns: function (idx) {
                        return idx !== 0;
                    }
                }
            });
        @elseif(isset($btn['type']) && $btn['type'] === 'custom')
            buttonsConfig.push({
                text: '{{ $btn['text'] }}',
                action: function (e, dt, node, config) {
                    if (typeof window['{{ $btn['action'] ?? '' }}'] === 'function') {
                        window['{{ $btn['action'] }}'](e, dt, node, config, Array.from(selectedIds));
                    } else {
                        console.warn('Custom action not found: {{ $btn['action'] ?? '' }}');
                    }
                }
            });
        @endif

    @endforeach

    const columnsConfig = [
        {
            data: 'id', orderable: false, searchable: false,
            render: function (data, type, row) {
                const checked = selectedIds.has(data) ? 'checked' : '';
                const isDisabled =['processing', 'requested'].includes(row.email_status) || ['requested', 'processing'].includes(row.report_status) || ['Incomplete'].includes(row.test_status) || row.status == 'transfer';
                let disabled = isDisabled ? 'disabled' : '';
                
                return `<input type="checkbox" class="row-checkbox" id="${row.test_status}" value="${data}" ${checked} ${disabled}/>`;
            }
        },


        ...{!! json_encode($columns) !!}
    ];

    const buttonsConfigFinal = buttonsConfig.length > 0 ? [{
        extend: 'collection',
        text: exportButtonText ? exportButtonText : 'Export', 
        className: 'exportButton',
        buttons: buttonsConfig
    }] : [];


    const domConfig = enableLengthMenu 
        ? `<"top"lf><"filter-right"B>rt<"bottom"ip><"clear">`  // With length menu ('l')
        : `<"top"f><"filter-right"B>rt<"bottom"ip><"clear">`;


    const table = $(tableId).DataTable({
        dom: domConfig,
        lengthChange: enableLengthMenu,
        lengthMenu: enableLengthMenu ? [ // CONDITIONAL LENGTH MENU OPTIONS
            [50, 100, 200, 500, -1],
            [50, 100, 200, 500, 'All']
        ] : false,
        pageLength: pageLength,
        info: true,
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ $ajaxUrl }}",
            data: function (d) {
                d.selectedOnlyClass = $('#filter-only-class').val();
                d.status = $('#filter-status').length ? $('#filter-status').val() : selectedStatus;
                $('[id^="filter-"]').each(function () {
                    const name = $(this).attr('id').replace('filter-', '');
                    d[name] = $(this).val();

                    selectedIds.clear();
                    $(selectAllId).prop('checked', false);
                    const visibleRows = table.rows({ page: 'current', search: 'applied' }).nodes();
                    $('.row-checkbox', visibleRows).prop('checked', false);
                    
                });

            },

            beforeSend: function () {
                Swal.fire({
                    icon: 'info',
                    title: 'Loading...',
                    text: 'Fetching data, please wait',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading()
                });
            },

            complete: function () {
                Swal.close();
            },
            
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load data.',
                    confirmButtonColor: '#3085d6'
                });
            }
        },
        columns: columnsConfig,
        buttons: buttonsConfigFinal,

        language: {
            searchPlaceholder: "{{ $searchPlaceholder }}",
            search: ""
        },

        initComplete: function () {

            $('#{{ $id }}_filter input').attr('placeholder', "{{ $searchPlaceholder }}");

            /* Added 07-05-2026 */
            if(enableSchoolTermsFilter){


                const TermList = @json($schoolTerms ?? []);  
                console.log(TermList);
                const $dropdown = $('<select class="form-select form-select-sm ms-2" id="filter-school-terms" style="font-size: 13px;color: #2c2d78;"></select>');               

                TermList.forEach(option => {
                    const displayText = `${option.academic_year} | ${option.term_name}`;
                    const value = option.academic_year + '|' + option.term_id;
        
                    // const isSelected = (value == currentTermId); 
                    const isSelected = (option.term_id == currentTermId);
                    $dropdown.append(new Option(displayText, value, false, isSelected));
                });

                const $schoolTermFilter = $('<div class="pull-right"></div>').append($dropdown);
                $schoolTermFilter.appendTo(`${tableId}_wrapper .top`).next('.dt-length').addClass("pull-right");

                if (selectedClass) {
                    setTimeout(() => {
                        $dropdown.trigger('change');
                        // console.log('Triggered filter for pre-selected class:', selectedClass);
                    }, 100);
                } 

            }


            if (enableClassFilter) {
                const classList = @json($classes ?? []);
                const $dropdown = $('<select class="form-select form-select-sm ms-2" id="filter-class-section"></select>');               
                $dropdown.append(new Option('All Classes', ''));

                classList.forEach(option => {
                    const section = option.section ? ` - ${option.section}` : '';
                    const displayText = option.name + section ;
                    const value = option.class_id + '-' + option.section;
                    const isSelected = (selectedClass == option.class_id && selectedSection == option.section);

                    $dropdown.append(new Option(displayText, value, false, isSelected));
                });

                const $classFilterContainer = $('<div class="pull-right"></div>').append($dropdown);
                $classFilterContainer.appendTo(`${tableId}_wrapper .top`).next('.dt-length').addClass("pull-right");

                if (selectedClass) {
                    setTimeout(() => {
                        $dropdown.trigger('change');
                        // console.log('Triggered filter for pre-selected class:', selectedClass);
                    }, 100);
                }        
            }

            if (enableSkillNameFilter) {
                const skillIds = @json($skillIds ?? []);
                const seenSkillIds = new Set();
                const uniqueSkillIds = [];

                skillIds.forEach(item => {
                    if (!seenSkillIds.has(item.id)) {
                        seenSkillIds.add(item.id);
                        uniqueSkillIds.push(item);
                    }
                });

                const $dropdown = $('<select class="form-select form-select-sm ms-2" id="filter-skill"></select>');
                $dropdown.append(new Option('All Skills', ''));

                uniqueSkillIds.forEach(option => {
                    const displayText = option.skill_name;
                    const value = option.id;
                    $dropdown.append(new Option(displayText, value));
                });

                const $skillsFilterContainer = $('<div class="pull-right"></div>').append($dropdown);
                $skillsFilterContainer.appendTo(`${tableId}_wrapper .top`).next('.dt-length').addClass("pull-right");

                $dropdown.on('change', function () {
                    table.draw();
                });

                $.fn.dataTable.ext.errMode = 'throw';
                table.settings()[0].ajax.data = function(d) {
                    const skillId = $dropdown.val();
                    if (skillId) {
                        d.skills = skillId;
                    } else {
                        d.skills = null;
                    }
                };
            }



            if (enableOnlyClassFilter) {
                const classList = @json($classes ?? []);
                const seenClassIds = new Set();
                const uniqueClassData = [];

                classList.forEach(item => {
                    if (!seenClassIds.has(item.class_id)) {
                        seenClassIds.add(item.class_id);
                        uniqueClassData.push(item);
                    }
                });

                const $dropdown = $('<select class="form-select form-select-sm ms-2" id="filter-only-class"></select>');               
                $dropdown.append(new Option('All Classes', ''));

                uniqueClassData.forEach(option => {
                    const displayText = option.name;
                    const value = option.class_id;
                    const isSelected = (selectedOnlyClass == value);
                    $dropdown.append(new Option(displayText, value, false, isSelected));
                });
                
                const $classFilterContainer = $('<div class="pull-right"></div>').append($dropdown);
                $classFilterContainer.appendTo(`${tableId}_wrapper .top`).next('.dt-length').addClass("pull-right");

                if (selectedClass) {
                    setTimeout(() => {
                        $dropdown.trigger('change');
                        // console.log('Triggered filter for pre-selected class:', selectedClass);
                    }, 100);
                }  
            }


            if (enableClassSectionFilter) {

                const classList = @json($classes ?? []);
                const $classDropdown = $('<select class="form-select form-select-sm ms-2" id="filter-class"></select>'); 
                const $sectionDropdown = $('<select class="form-select form-select-sm ms-2" id="filter-section"></select>'); 
                
                $sectionDropdown.append(new Option('All Section', ''));
                $classDropdown.append(new Option('All Classes', ''));

                const uniqueClasses = [...new Map(classList.map(item => [item.class_id, item])).values()];
                uniqueClasses.forEach(cls => {
                    const isSelected = (selectedClass == cls.class_id);
                    $classDropdown.append(new Option(cls.name, cls.class_id, false, isSelected));
                });

                function updateSections(selectedClassId) {

                    $sectionDropdown.empty().append(new Option('All Sections', ''));
                    if (!selectedClassId) return;

                    const sections = [
                        ...new Set(
                            classList
                                .filter(item => item.class_id == selectedClassId)
                                .map(item => item.section)
                                .filter(Boolean)
                        )
                    ];

                    sections.forEach(section => {
                        const isSelected = (selectedSection == section);
                        $sectionDropdown.append(new Option(section, section, false, isSelected));
                    });
                }

                $classDropdown.on('change', function () {
                    const selectedClassId = $(this).val();
                    updateSections(selectedClassId);
                });

                if (selectedClass) {
                    updateSections(selectedClass);
                    setTimeout(() => {
                        $classDropdown.trigger('change');
                    }, 100);
                }

                const $classFilterContainer = $('<div class="pull-right"></div>').append($classDropdown);
                const $sectionFilterContainer = $('<div class="pull-right"></div>').append($sectionDropdown);
                $classFilterContainer.appendTo(`${tableId}_wrapper .top`);
                $sectionFilterContainer.appendTo(`${tableId}_wrapper .top`);

            }


            if(enableStatusFilter){
                const studentStatus = @json($status ?? []);
                const $statusDropDown = $('<select class="form-control form-select-sm ms-2"" id="filter-status"></select>');
                studentStatus.forEach(option => {  
                    const isSelected = option.value === selectedStatus;                
                    $statusDropDown.append(new Option(option.label, option.value, false, isSelected));
                });

                const $statusFilterContainer = $('<div class="pull-right"></div>').append($statusDropDown);
                $statusFilterContainer.appendTo(`${tableId}_wrapper .top`).next('.dt-length').addClass("pull-right");
                
                $('[id^="filter-"]').on('change', function () {
                    table.ajax.reload();
                });
            }

            if(enableTestStatusFilter){
                const testStatus = @json($testStatus ?? []);
                const $testStatusDropDown = $('<select class="form-control form-select-sm ms-2"" id="filter-test-status"></select>');
                testStatus.forEach(option => {  
                    const isSelected = option.value === selectedTestStatus;                
                    $testStatusDropDown.append(new Option(option.label, option.value, false, isSelected));
                });

                const $testFilterContainer = $('<div class="pull-right"></div>').append($testStatusDropDown);
                $testFilterContainer.appendTo(`${tableId}_wrapper .top`).next('.dt-length').addClass("pull-right");
                if ($('#upload_test_data_wrapper').length) {
                    const $wrapper = $('#upload_test_data_wrapper .top');
                    const $statusDiv = $wrapper.find('#filter-test-status').parent();
                    const $skillDiv = $wrapper.find('#filter-skill').parent();

                    $statusDiv.insertBefore($skillDiv);
                }
                $('[id^="filter-"]').on('change', function () {
                    table.ajax.reload();
                });
            }

            if(enableStudentCategoryFilter){
                const studentCategory = @json($studentCategory ?? []);
                console.log(studentCategory);
                const $studentCategoryDropDown = $('<select class="form-control form-select-sm ms-2"" id="filter-student-category"></select>');
                studentCategory.forEach(option => {  
                    const isSelected = option.value === selectedStudentCategory;                
                    $studentCategoryDropDown.append(new Option(option.label, option.value, false, isSelected));
                });

                const $studentCategoryContainer = $('<div class="pull-right"></div>').append($studentCategoryDropDown);
                $studentCategoryContainer.appendTo(`${tableId}_wrapper .top`).next('.dt-length').addClass("pull-right");
                
                $('[id^="filter-"]').on('change', function () {
                    table.ajax.reload();
                });
            }


        }
    });

    

    // FIXED: Select All checkbox handler
    $(selectAllId).on('change', function () {
        const checked = this.checked;
        const visibleRows = table.rows({ search: 'applied' }).nodes();
        
        $('.row-checkbox', visibleRows).each(function () {

            if ($(this).is(':disabled')) return;

            this.checked = checked;
            const id = $(this).val();
            if (checked) {
                selectedIds.add(id);
            } else {
                selectedIds.delete(id);
            }
        });
    });


    // FIXED: Individual row checkbox handler
    $(tableId).on('change', '.row-checkbox', function () {
        const id = $(this).val();
        if (this.checked) {
            selectedIds.add(id);
        } else {
            selectedIds.delete(id);
            // Uncheck select-all when any checkbox is unchecked
            $(selectAllId).prop('checked', false);
        }

        // Update select-all checkbox state
        const visibleRows = table.rows({ search: 'applied' }).nodes();
        const visibleCheckboxes = $('.row-checkbox', visibleRows);
        const allChecked = visibleCheckboxes.length > 0 && 
                          visibleCheckboxes.length === visibleCheckboxes.filter(':checked').length;
        
        $(selectAllId).prop('checked', allChecked);
    });

    // FIXED: Draw event handler - reset select-all on page change
    table.on('draw', function () {
        // Update checkboxes for current page
        const visibleRows = table.rows({ page: 'current', search: 'applied' }).nodes();
        
        $('.row-checkbox', visibleRows).each(function () {
            const id = $(this).val();
            $(this).prop('checked', selectedIds.has(id));
        });

        // Update select-all checkbox for CURRENT PAGE only
        const currentPageRows = table.rows({ page: 'current', search: 'applied' }).nodes();
        const currentPageCheckboxes = $('.row-checkbox', currentPageRows);
        const allCurrentPageChecked = currentPageCheckboxes.length > 0 && currentPageCheckboxes.length === currentPageCheckboxes.filter(':checked').length;        
        $(selectAllId).prop('checked', allCurrentPageChecked);
    });

     // NEW: Handle page length change - clear select-all checkbox
    table.on('length.dt', function (e, settings, len) {
        selectedIds.clear();
        $(selectAllId).prop('checked', false);
        const visibleRows = table.rows({ page: 'current', search: 'applied' }).nodes();
        $('.row-checkbox', visibleRows).prop('checked', false);

    });


    table.on('processing.dt', function (e, settings, processing) {
        if (processing) {
            Swal.fire({
                title: 'Loading...',
                text: 'Processing your request...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });
        } else {
            Swal.close();
        }
    });

});
</script>
@endpush

