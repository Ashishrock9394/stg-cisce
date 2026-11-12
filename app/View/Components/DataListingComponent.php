<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Auth;
use DB;
use Session;
use App\Models\School;
use App\Models\Sclass;
use Illuminate\Support\Facades\Cache;
use App\Models\TermMaster;
use App\Traits\ReportHelperTrait;

class DataListingComponent extends Component
{
    use ReportHelperTrait;
    public $id;
    public $headers;
    public $columns;
    public $ajaxUrl;
    public $title;
    public $exportButtons;
    public $enableExportButtons;
    public $enableClassFilter;
    public $enableOnlyClassFilter;
    public $enableStatusFilter;
    public $enableSkillNameFilter;
    public $enableClassSectionFilter;
    public $enableTestStatusFilter;
    public $enableSchoolTermsFilter;       //added 07-05-2026
    public $enableStudentCategoryFilter;       //added 07-10-2026
    public $classes;
    public $skillIds;    
    public $schoolTerms;
    public $testStatus;
    public $studentCategory;
    public $status;
    public $pageLength;
    public $enableLengthMenu;
    public $searchPlaceholder;
    public $statusModeFlag;
    public $selectedStatus;
    public $selectedTestStatus;

    public $selectedClass; // ADD THIS PROPERTY FOR PRE-SELECTED CLASS
    public $selectedOnlyClass; // ADD THIS PROPERTY FOR PRE-SELECTED CLASS
    public $selectedSection; // ADD THIS FOR PRE-SELECTED SECTION
    public $exportButtonText;   // ADD THIS TO CHANGE EXPORT BUTTON -> BULK ACTION

    public function __construct(
        $id, 
        $headers = [], 
        $columns = [], 
        $ajaxUrl = '', 
        $title = '', 
        $exportButtons = [], 
        $classes = [],
        $skillIds = [],
        $schoolTerms = [],
        $enableClassFilter = false, 
        $enableOnlyClassFilter = false,
        $enableSkillNameFilter = false,
        $enableClassSectionFilter = false, 
        $enableTestStatusFilter = false,
        $enableStudentCategoryFilter = false,
        $enableStatusFilter = false,
        $enableSchoolTermsFilter = null,
        $statusModeFlag = 0,
        $enableExportButtons = false,
        $enableLengthMenu = true,
        $pageLength = 100,
        $searchPlaceholder = 'Search...',
        $selectedClass = null, // ADD THIS PARAMETER
        $selectedOnlyClass = null, // ADD THIS PARAMETER
        $selectedSection = null, // ADD THIS PARAMETER
        $selectedStatus = null, // ADD THIS PARAMETER
        $selectedStudentCategory = null, // ADD THIS PARAMETER
        $selectedTestStatus = null, // ADD THIS PARAMETER
        $exportButtonText = null        //Add this for Export button text

        ){

        $this->id = $id;
        $this->headers = $headers;
        $this->columns = $columns;
        $this->ajaxUrl = $ajaxUrl;             

        $this->enableStatusFilter = filter_var($enableStatusFilter, FILTER_VALIDATE_BOOLEAN);
        $this->enableTestStatusFilter = filter_var($enableTestStatusFilter, FILTER_VALIDATE_BOOLEAN);
        $this->enableStudentCategoryFilter = filter_var($enableStudentCategoryFilter, FILTER_VALIDATE_BOOLEAN);
        $this->statusModeFlag = (int)$statusModeFlag;
        $this->enableClassFilter = filter_var($enableClassFilter, FILTER_VALIDATE_BOOLEAN);
        $this->enableOnlyClassFilter = filter_var($enableOnlyClassFilter, FILTER_VALIDATE_BOOLEAN);


        $this->enableSchoolTermsFilter = filter_var($enableSchoolTermsFilter, FILTER_VALIDATE_BOOLEAN);


        $this->enableSkillNameFilter = filter_var($enableSkillNameFilter, FILTER_VALIDATE_BOOLEAN);
        $this->enableClassSectionFilter = filter_var($enableClassSectionFilter, FILTER_VALIDATE_BOOLEAN);
        $this->enableExportButtons = filter_var($enableExportButtons, FILTER_VALIDATE_BOOLEAN);
        $this->enableLengthMenu = filter_var($enableLengthMenu, FILTER_VALIDATE_BOOLEAN); 
        $this->pageLength = (int)$pageLength;
        $this->selectedClass = $selectedClass; // SET SELECTED CLASS
        $this->selectedOnlyClass = $selectedOnlyClass; // SET SELECTED CLASS
        $this->selectedStatus = $selectedStatus; // SET SELECTED STATUS
        $this->selectedTestStatus = $selectedTestStatus; // SET SELECTED STATUS
        $this->selectedSection = $selectedSection; // SET SELECTED SECTION
        $this->selectedStudentCategory = $selectedStudentCategory; // SET SELECTED SECTION
        $this->searchPlaceholder = $searchPlaceholder;
        $this->exportButtonText = $exportButtonText;
        
 
        $this->classes = $this->SchoolClassList();        
        $this->schoolTerms = $this->getSchoolTerms();
        $this->skillIds = $this->SkillIds();
        
        $defaultButtons = $this->enableExportButtons ? [ ['type' => 'excelHtml5', 'text' => 'Excel']] : [];
        $this->exportButtons = array_merge($defaultButtons, $exportButtons ?? []);

        if ($this->enableTestStatusFilter) {
            $this->testStatus = $this->getTestStatus();
        }
        if ($this->enableStudentCategoryFilter) {
            $this->studentCategory = $this->getStudentCategory();
        }
        if ($this->enableStatusFilter) {
            $this->status = $this->getStatus();
        }
    }

    protected function SkillIds() {
        return DB::table('skill_reports')
        ->select('id', 'skill_name', 'TestTypeMasterID', 'icons', 'status', 'created_at', 'updated_at')
        ->get();
    }

    protected function SchoolClassList() {

        $creatorId = Auth::id();
        
        if(Auth::user()->role_id == 3){

        	if(Session::get('SelectSchoolId')) {
				$schoolId = Session::get('SelectSchoolId');
			}else{
				$schoolId = DB::table('school_trainers')
				->join('schools','schools.id','=','school_trainers.school_id')
				->where('school_trainers.trainer_id',$creatorId)->where('school_trainers.status', 1)->value('school_trainers.school_id');
			}
        }else{
			$schoolId = DB::table('school_reference')->where('school_user_id',$creatorId)->where('status', 1)->value('school_id');
		}
        

        $school = School::find($schoolId);
        $classList = $school->getClasses;

        $classOrder = [
            'pre-nursery' => 1,
            'prenursery'  => 1,
            'nursery'     => 2,
            'lkg'         => 3,
            'ukg'         => 4,
        ];
        $baseClasses = Sclass::pluck('name', 'id');

        $classList = $classList->map(function ($class) use ($baseClasses) {

            $baseName = strtolower($baseClasses[$class->class_id] ?? '');
            return [
                'base_name' => $baseName,
                'name' => !empty($class->nomenclature)  ? $class->nomenclature : ($baseClasses[$class->class_id] ?? null),
                'class_id' => $class->class_id,
                'section' => $class->section,
            ];
        })
        ->sortBy(function ($item) use ($classOrder) {

            $normalized = str_replace([' ', '-'], '', $item['base_name']);

            if (isset($classOrder[$normalized])) {
                $classSort = $classOrder[$normalized];
            } elseif (preg_match('/class\s*(\d+)/i', $item['base_name'], $matches)) {
                $classSort = 100 + (int) $matches[1];
            } else {
                $classSort = 999;
            }
            
            $section = $item['section'];
            $sectionSort = is_numeric($section) ? (int) $section : ord(strtoupper($section)) - ord('A');
            return [$classSort, $sectionSort];

        })->values();

        return $classList;
    }


    protected function getTestStatus(): array{
        if ($this->statusModeFlag === 1) {
            return [
                ['value' => 'all', 'label' => 'All'],
                ['value' => 'incomplete', 'label' => 'Incomplete'],
            ];
        }
        return [
            ['value' => 'all', 'label' => 'All'],
            ['value' => 'complete', 'label' => 'Complete'],
            ['value' => 'incomplete', 'label' => 'Incomplete'],
        ];
    }
    protected function getStudentCategory(): array{
       
        return [
            ['value' => 'all', 'label' => 'All Category'],
            ['value' => 'normal', 'label' => 'Normal'],
            ['value' => 'cwsn', 'label' => 'CWSN'],
        ];
    }

    protected function getStatus(): array{        
        return [
            ['value' => 'active', 'label' => 'Active'],
            ['value' => 'transfer', 'label' => 'Transfered'],
            ['value' => 'promoted', 'label' => 'Promoted'],
            ['value' => 'failed', 'label' => 'Failed'],
            ['value' => '', 'label' => 'All Students'],
        ];
    }

    protected function getSchoolTerms() {

        $creatorId = Auth::id();
        if(Auth::user()->role_id == 3){

        	if(Session::get('SelectSchoolId')) {
				$schoolId = Session::get('SelectSchoolId');
			}else{
				$schoolId = DB::table('school_trainers')
				->join('schools','schools.id','=','school_trainers.school_id')
				->where('school_trainers.trainer_id',$creatorId)->where('school_trainers.status', 1)->value('school_trainers.school_id');
			}
        }else{
			$schoolId = DB::table('school_reference')->where('school_user_id',$creatorId)->where('status', 1)->value('school_id');
		}
        
        $cacheKey = "school_terms_{$schoolId}";
        
        Cache::forget($cacheKey);
        $TermMasterId = $this->getTermId($schoolId);

		$TermIds = $this->getCurrentAndPreviousTermIds($schoolId, (int) $TermMasterId);
		
		$terms = TermMaster::whereIn('id', $TermIds)->get();
    
        return Cache::remember($cacheKey, now()->addHours(6), function () use ($terms) {
            return $terms
                ->map(fn ($term) => [
                    'term_id'   => $term->id,
                    'school_id' => $term->school_id,
                    'academic_year' => $term->academic_year,
                    'term_name' => $term->term_name,
                ])
                ->values()
                ->toArray();
        });
    }


    public function render() {

        return view('components.listing-data');
    }
}