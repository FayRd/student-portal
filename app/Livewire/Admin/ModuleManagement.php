<?php

namespace App\Livewire\Admin;

use App\Models\Module;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\ClassSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class ModuleManagement extends Component
{
    use WithPagination, WithFileUploads;

    // Resource browser state
    public ?int $browsedFolderId = null;

    // Resource creation modal
    public bool   $showResourceModal = false;
    public string $resourceStep      = 'choose'; // 'choose', 'folder', 'file'
    public string $folderName        = '';
    public string $fileName          = '';
    public mixed  $uploadedFile      = null;

    // Filters
    public string $search     = '';
    public string $statusFilter = '';
    public string $sortBy     = 'created_at';
    public string $sortDir    = 'desc';

    // State
    public ?int  $selectedModuleId = null;
    public string $mode            = 'view';
    public string $activeTab       = 'classes';

    // Form fields
    public string $formCode         = '';
    public string $formName         = '';
    public string $formDescription  = '';
    public int    $formCredits      = 3;
    public string $formAcademicYear = '2025/2026';
    public int    $formSemester     = 1;
    public string $formStatus       = 'UPCOMING';
    public array $formLecturerIds = [];

    // Tab sorting
    public ?int  $expandedClassId = null;
    public string $studentSort    = 'name';
    public string $studentSortDir = 'asc';

    // Modal visibility
    public bool $showClassModal    = false;
    public bool $showStudentModal  = false;
    public bool $showLecturerModal = false;

    // Class modal fields
    public string  $classTitle     = '';
    public string  $classLocation  = '';
    public string  $classType      = 'PHYSICAL';
    public string  $classStartsAt  = '';
    public string  $classEndsAt    = '';

    // Student/Lecturer modal fields
    public string $modalSearch          = '';
    public array  $selectedStudentIds   = [];
    public array  $selectedLecturerIds  = [];

    // Assignments and Submissions tabs
    public ?int $selectedAssignmentId = null;
    public string $submissionSort    = 'name';
    public string $submissionSortDir = 'asc';

    public function updatedSearch(): void       { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }

    #[Computed]
    public function stats(): array
    {
        return [
            'total'       => Module::count(),
            'active'      => Module::where('status', 'ACTIVE')->count(),
            'upcoming'    => Module::where('status', 'UPCOMING')->count(),
            'archived'    => Module::where('status', 'ARCHIVED')->count(),
            'enrollments' => Enrollment::where('status', 'ACTIVE')->count(),
        ];
    }

    #[Computed]
    public function modules()
    {
        return Module::with(['creator', 'editors'])
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%");
            }))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(10);
    }

    #[Computed]
    public function selectedModule(): ?Module
    {
        if (! $this->selectedModuleId) {
            return null;
        }

        return Module::with(['creator', 'editors'])
            ->find($this->selectedModuleId);
    }

    #[Computed]
    public function lecturers()
    {
        return User::role('lecturer')->orderBy('name')->get();
    }

    public function sortColumn(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $column;
            $this->sortDir = 'asc';
        }

        unset($this->modules);
    }

    public function selectModule(int $id): void
    {
        $this->selectedModuleId = ($this->selectedModuleId === $id) ? null : $id;
        $this->mode             = 'view';
        $this->activeTab        = 'classes';
        $this->selectedAssignmentId = null;
        $this->expandedClassId  = null;
        unset($this->selectedModule);
    }

    public function setTab(string $tab): void
    {
        $this->activeTab       = $tab;
        $this->expandedClassId = null;
        $this->selectedAssignmentId = null;
        $this->resetPage('assignmentsPage');
        $this->resetPage('submissionsPage');
        $this->resetPage('classesPage');
        $this->resetPage('studentsPage');
        $this->resetPage('lecturersPage');
    }

    public function showCreateForm(): void
    {
        $this->selectedModuleId = null;
        $this->mode             = 'create';
        $this->resetFormFields();
    }

    public function removeLecturer(int $id): void
    {
        $this->formLecturerIds = array_values(
            array_diff($this->formLecturerIds, [$id])
        );
    }

    public function toggleLecturer(int $id): void
    {
        if (in_array($id, $this->formLecturerIds)) {
            $this->formLecturerIds = array_values(array_diff($this->formLecturerIds, [$id]));
        } else {
            $this->formLecturerIds[] = $id;
        }
    }

    public function showEditForm(): void
    {
        if (! $this->selectedModule) {
            return;
        }

        $this->mode              = 'edit';
        $this->formCode          = $this->selectedModule->code;
        $this->formName          = $this->selectedModule->name;
        $this->formDescription   = $this->selectedModule->description;
        $this->formCredits       = $this->selectedModule->credits;
        $this->formAcademicYear  = $this->selectedModule->academic_year;
        $this->formSemester      = $this->selectedModule->semester;
        $this->formStatus        = $this->selectedModule->status;
        $this->formLecturerIds   = $this->selectedModule->editors->pluck('id')->toArray();
    }

    public function createModule(): void
    {
        $this->validate([
            'formCode'          => 'required|string|max:20|unique:modules,code',
            'formName'          => 'required|string|max:255',
            'formDescription'   => 'required|string',
            'formCredits'       => 'required|integer|min:1|max:12',
            'formAcademicYear'  => 'required|string',
            'formSemester'      => 'required|integer|in:1,2',
            'formStatus'        => 'required|in:UPCOMING,ACTIVE,ARCHIVED',
            'formLecturerIds'   => 'required|array|min:1',
            'formLecturerIds.*' => 'exists:users,id',
        ]);

        $module = Module::create([
            'code'          => strtoupper($this->formCode),
            'name'          => $this->formName,
            'description'   => $this->formDescription,
            'credits'       => $this->formCredits,
            'academic_year' => $this->formAcademicYear,
            'semester'      => $this->formSemester,
            'status'        => $this->formStatus,
            'created_by'    => auth()->id(),
        ]);

        $module->editors()->sync(
            collect($this->formLecturerIds)->mapWithKeys(fn ($id) => [
                $id => ['role' => 'editor', 'created_at' => now()]
            ])->toArray()
        );

        $this->selectedModuleId = $module->id;
        $this->mode             = 'view';
        $this->resetFormFields();
        unset($this->stats, $this->modules, $this->selectedModule);
    }

    public function updateModule(): void
    {
        $this->validate([
            'formCode'         => ['required', 'string', 'max:20', Rule::unique('modules', 'code')->ignore($this->selectedModuleId)],
            'formName'         => 'required|string|max:255',
            'formDescription'  => 'required|string',
            'formCredits'      => 'required|integer|min:1|max:12',
            'formAcademicYear' => 'required|string',
            'formSemester'     => 'required|integer|in:1,2',
            'formStatus'       => 'required|in:UPCOMING,ACTIVE,ARCHIVED',
            'formLecturerIds'   => 'required|array|min:1',
            'formLecturerIds.*' => 'exists:users,id',
        ]);

        $module = Module::find($this->selectedModuleId);
        $module->update([
            'code'          => strtoupper($this->formCode),
            'name'          => $this->formName,
            'description'   => $this->formDescription,
            'credits'       => $this->formCredits,
            'academic_year' => $this->formAcademicYear,
            'semester'      => $this->formSemester,
            'status'        => $this->formStatus,
        ]);

        $module->editors()->sync(
            collect($this->formLecturerIds)->mapWithKeys(fn ($id) => [
                $id => ['role' => 'editor', 'created_at' => now()]
            ])->toArray()
        );

        $this->mode = 'view';
        $this->resetFormFields();
        unset($this->modules, $this->selectedModule);
    }

    public function deleteModule(int $id): void
    {
        Module::find($id)->delete();
        $this->selectedModuleId = null;
        $this->mode             = 'view';
        unset($this->stats, $this->modules, $this->selectedModule);
    }

    public function cancelForm(): void
    {
        $this->mode = 'view';
        $this->resetFormFields();
    }

    private function resetFormFields(): void
    {
        $this->formCode         = '';
        $this->formName         = '';
        $this->formDescription  = '';
        $this->formCredits      = 3;
        $this->formAcademicYear = '2025/2026';
        $this->formSemester     = 1;
        $this->formStatus       = 'UPCOMING';
        $this->formLecturerIds   = [];
        $this->resetValidation();
    }

    public function resolveLocation(string $location): array
    {
        if (! str_starts_with($location, 'http')) {
            return ['type' => 'text', 'value' => $location];
        }

        $label = match (true) {
            str_contains($location, 'zoom.us')             => 'Zoom',
            str_contains($location, 'teams.microsoft')     => 'Teams',
            str_contains($location, 'meet.google')         => 'Google Meet',
            str_contains($location, 'webex.com')           => 'Webex',
            default                                        => 'Link',
        };

        return ['type' => 'link', 'value' => $location, 'label' => $label];
    }

    public function toggleClass(int $id): void
    {
        $this->expandedClassId  = ($this->expandedClassId === $id) ? null : $id;
        $this->browsedFolderId  = null;
    }

    public function sortStudents(string $column): void
    {
        if ($this->studentSort === $column) {
            $this->studentSortDir = $this->studentSortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->studentSort    = $column;
            $this->studentSortDir = 'asc';
        }

        unset($this->moduleStudents);
    }

    #[Computed]
    public function availableStudents()
    {
        $enrolled = Enrollment::where('module_id', $this->selectedModuleId)
            ->pluck('user_id');

        return User::role('student')
            ->whereNotIn('id', $enrolled)
            ->when($this->modalSearch, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->modalSearch}%")
                ->orWhere('institutional_id', 'like', "%{$this->modalSearch}%");
            }))
            ->orderBy('name')
            ->paginate(8, pageName: 'availableStudentsPage');
    }

    #[Computed]
    public function availableLecturers()
    {
        $assigned = \DB::table('module_user')
            ->where('module_id', $this->selectedModuleId)
            ->pluck('user_id');

        return User::role('lecturer')
            ->whereNotIn('id', $assigned)
            ->when($this->modalSearch, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->modalSearch}%")
                ->orWhere('institutional_id', 'like', "%{$this->modalSearch}%");
            }))
            ->orderBy('name')
            ->paginate(8, pageName: 'availableLecturersPage');
    }

    #[Computed]
    public function selectedStudents()
    {
        if (empty($this->selectedStudentIds)) {
            return collect();
        }
        return User::whereIn('id', $this->selectedStudentIds)->get(['id', 'name']);
    }

    #[Computed]
    public function selectedLecturersForModal()
    {
        if (empty($this->selectedLecturerIds)) {
            return collect();
        }
        return User::whereIn('id', $this->selectedLecturerIds)->get(['id', 'name']);
    }

    public function updatedModalSearch(): void
    {
        $this->resetPage('availableStudentsPage');
        $this->resetPage('availableLecturersPage');
        unset($this->availableStudents, $this->availableLecturers);
    }

    public function openClassModal(): void
    {
        $this->showClassModal = true;
        $this->classTitle     = '';
        $this->classLocation  = '';
        $this->classType      = 'PHYSICAL';
        $this->classStartsAt  = '';
        $this->classEndsAt    = '';
        $this->resetValidation();
    }

    public function closeClassModal(): void
    {
        $this->showClassModal = false;
    }

    public function createClass(): void
    {
        $this->validate([
            'classTitle'    => 'required|string|max:255',
            'classLocation' => 'required|string|max:255',
            'classType'     => 'required|in:PHYSICAL,VIRTUAL',
            'classStartsAt' => 'required|date',
            'classEndsAt'   => 'required|date|after:classStartsAt',
        ]);

        $folder = \App\Models\ResourceFolder::create([
            'module_id' => $this->selectedModuleId,
            'parent_id' => null,
            'name'      => $this->classTitle,
            'order'     => 0,
        ]);

        ClassSession::create([
            'module_id'          => $this->selectedModuleId,
            'resource_folder_id' => $folder->id,
            'title'              => $this->classTitle,
            'location'           => $this->classLocation,
            'type'               => $this->classType,
            'starts_at'          => $this->classStartsAt,
            'ends_at'            => $this->classEndsAt,
        ]);

        $this->showClassModal = false;
        unset($this->moduleClasses, $this->stats);
    }

    public function openStudentModal(): void
    {
        $this->showStudentModal    = true;
        $this->modalSearch         = '';
        $this->selectedStudentIds  = [];
        unset($this->availableStudents, $this->selectedStudents);
    }

    public function closeStudentModal(): void
    {
        $this->showStudentModal = false;
    }

    public function toggleStudent(int $id): void
    {
        if (in_array($id, $this->selectedStudentIds)) {
            $this->selectedStudentIds = array_values(array_diff($this->selectedStudentIds, [$id]));
        } else {
            $this->selectedStudentIds[] = $id;
        }
        unset($this->selectedStudents);
    }

    public function enrollStudents(): void
    {
        if (empty($this->selectedStudentIds)) {
            return;
        }

        foreach ($this->selectedStudentIds as $studentId) {
            Enrollment::firstOrCreate(
                ['user_id' => $studentId, 'module_id' => $this->selectedModuleId],
                ['status' => 'ACTIVE', 'enrolled_at' => now()]
            );
        }

        $this->showStudentModal   = false;
        $this->selectedStudentIds = [];
        unset($this->moduleStudents, $this->stats, $this->availableStudents);
    }

    public function openLecturerModal(): void
    {
        $this->showLecturerModal    = true;
        $this->modalSearch          = '';
        $this->selectedLecturerIds  = [];
        unset($this->availableLecturers, $this->selectedLecturersForModal);
    }

    public function closeLecturerModal(): void
    {
        $this->showLecturerModal = false;
    }

    public function toggleModalLecturer(int $id): void
    {
        if (in_array($id, $this->selectedLecturerIds)) {
            $this->selectedLecturerIds = array_values(array_diff($this->selectedLecturerIds, [$id]));
        } else {
            $this->selectedLecturerIds[] = $id;
        }
        unset($this->selectedLecturersForModal);
    }

    public function assignLecturers(): void
    {
        if (empty($this->selectedLecturerIds)) {
            return;
        }

        $newPivots = collect($this->selectedLecturerIds)
            ->mapWithKeys(fn ($id) => [
                $id => ['role' => 'editor', 'created_at' => now()]
            ])->toArray();

        $module = Module::find($this->selectedModuleId);
        $existing = $module->editors()->pluck('users.id')->toArray();
        $all = array_unique(array_merge($existing, $this->selectedLecturerIds));

        $module->editors()->sync(
            collect($all)->mapWithKeys(fn ($id) => [
                $id => ['role' => 'editor', 'created_at' => now()]
            ])->toArray()
        );

        $this->showLecturerModal   = false;
        $this->selectedLecturerIds = [];
        unset($this->moduleLecturers, $this->selectedModule, $this->availableLecturers);
    }

    public function selectAssignment(int $id): void
    {
        $this->selectedAssignmentId = ($this->selectedAssignmentId === $id) ? null : $id;
        $this->resetPage('submissionsPage');
        unset($this->moduleSubmissions, $this->selectedAssignment);
    }

    public function sortSubmissions(string $column): void
    {
        if ($this->submissionSort === $column) {
            $this->submissionSortDir = $this->submissionSortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->submissionSort    = $column;
            $this->submissionSortDir = 'asc';
        }
        unset($this->moduleSubmissions);
    }

    public function formatFileSize(int $bytes): string
    {
        return match (true) {
            $bytes >= 1_073_741_824 => number_format($bytes / 1_073_741_824, 2) . ' GB',
            $bytes >= 1_048_576     => number_format($bytes / 1_048_576, 2) . ' MB',
            default                 => number_format($bytes / 1_024, 2) . ' KB',
        };
    }

    public function formatMimeType(string $mime): string
    {
        return match ($mime) {
            'application/pdf'                                                                          => 'PDF',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword' => 'Word',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation'               => 'PowerPoint',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'                       => 'Excel',
            'application/zip', 'application/x-zip-compressed'                                         => 'ZIP',
            'text/plain'                                                                               => 'Text',
            'video/mp4'                                                                               => 'MP4',
            'audio/mpeg'                                                                              => 'MP3',
            default                                                                                   => $mime,
        };
    }

    public function browseFolder(?int $folderId): void
    {
        $this->browsedFolderId = $folderId;
    }

    public function openResourceModal(): void
    {
        $this->showResourceModal = true;
        $this->resourceStep      = 'choose';
        $this->folderName        = '';
        $this->fileName          = '';
        $this->uploadedFile      = null;
        $this->resetValidation();
    }

    public function closeResourceModal(): void
    {
        $this->showResourceModal = false;
        $this->resourceStep      = 'choose';
    }

    public function chooseResourceType(string $type): void
    {
        $this->resourceStep = $type; // 'folder' or 'file'
    }

    public function createFolder(): void
    {
        $this->validate([
            'folderName' => 'required|string|max:255',
        ]);

        \App\Models\ResourceFolder::create([
            'module_id' => $this->selectedModuleId,
            'parent_id' => $this->browsedFolderId ?? $this->getLinkedFolderId(),
            'name'      => $this->folderName,
            'order'     => 0,
        ]);

        $this->showResourceModal = false;
        $this->folderName        = '';
        unset($this->resourceContents, $this->moduleClasses);
    }

    public function uploadFile(): void
    {
        $this->validate([
            'fileName'     => 'required|string|max:255',
            'uploadedFile' => [
                'required',
                'file',
                'max:102400',
                'mimes:pdf,doc,docx,ppt,pptx,txt,mp4,mp3,zip',
            ],
        ]);

        $originalName = $this->uploadedFile->getClientOriginalName();
        $mimeType     = $this->uploadedFile->getMimeType();
        $fileSize     = $this->uploadedFile->getSize();
        $path         = $this->uploadedFile->store('module-resources', 'local');

        \App\Models\ModuleResource::create([
            'module_id'   => $this->selectedModuleId,
            'folder_id'   => $this->browsedFolderId ?? $this->getLinkedFolderId(),
            'uploaded_by' => auth()->id(),
            'title'       => $this->fileName,
            'file_path'   => $path,
            'file_name'   => $originalName,
            'file_size'   => $fileSize,
            'mime_type'   => $mimeType,
        ]);

        $this->showResourceModal = false;
        $this->fileName          = '';
        $this->uploadedFile      = null;
        unset($this->resourceContents, $this->moduleClasses);
    }

    private function getLinkedFolderId(): ?int
    {
        if (! $this->expandedClassId) {
            return null;
        }

        return ClassSession::find($this->expandedClassId)?->resource_folder_id;
    }

    public function getFolderBreadcrumb(): array
    {
        if (! $this->browsedFolderId) {
            return [];
        }

        $crumbs = [];
        $folder = \App\Models\ResourceFolder::find($this->browsedFolderId);

        while ($folder) {
            array_unshift($crumbs, ['id' => $folder->id, 'name' => $folder->name]);
            $folder = $folder->parent_id ? \App\Models\ResourceFolder::find($folder->parent_id) : null;
        }

        return $crumbs;
    }

    public function updatedUploadedFile(): void
    {
        if ($this->uploadedFile) {
            $this->fileName = pathinfo(
                $this->uploadedFile->getClientOriginalName(),
                PATHINFO_FILENAME
            );
        }
    }

    #[Computed]
    public function resourceContents(): array
    {
        $folderId = $this->browsedFolderId;

        if ($folderId) {
            $folder = \App\Models\ResourceFolder::with(['children', 'resources'])->find($folderId);
            return [
                'folders'   => $folder?->children ?? collect(),
                'resources' => $folder?->resources ?? collect(),
            ];
        }

        // Root of linked folder
        $linkedFolderId = $this->getLinkedFolderId();
        if (! $linkedFolderId) {
            return ['folders' => collect(), 'resources' => collect()];
        }

        $folder = \App\Models\ResourceFolder::with(['children', 'resources'])->find($linkedFolderId);
        return [
            'folders'   => $folder?->children ?? collect(),
            'resources' => $folder?->resources ?? collect(),
        ];
    }

    #[Computed]
    public function moduleAssignments()
    {
        if (! $this->selectedModuleId) {
            return collect();
        }

        return \App\Models\Assignment::where('module_id', $this->selectedModuleId)
            ->orderBy('due_at')
            ->paginate(12, pageName: 'assignmentsPage');
    }

    #[Computed]
    public function selectedAssignment(): ?\App\Models\Assignment
    {
        if (! $this->selectedAssignmentId) {
            return null;
        }
        return \App\Models\Assignment::find($this->selectedAssignmentId);
    }

    #[Computed]
    public function moduleSubmissions()
    {
        if (! $this->selectedAssignmentId || ! $this->selectedModuleId) {
            return collect();
        }

        $sortColumn = match ($this->submissionSort) {
            'name'           => 'users.name',
            'file_name'      => 'submissions.file_name',
            'file_size'      => 'submissions.file_size',
            'mime_type'      => 'submissions.mime_type',
            'status'         => 'submissions.status',
            'submitted_at'   => 'submissions.submitted_at',
            'processed_at'   => 'submissions.processed_at',
            default          => 'users.name',
        };

        return User::join('enrollments', 'users.id', '=', 'enrollments.user_id')
            ->where('enrollments.module_id', $this->selectedModuleId)
            ->where('enrollments.status', '!=', 'DROPPED')
            ->leftJoin('submissions', function ($join) {
                $join->on('users.id', '=', 'submissions.user_id')
                    ->where('submissions.assignment_id', '=', $this->selectedAssignmentId);
            })
            ->select(
                'users.id',
                'users.name',
                'users.institutional_id',
                'submissions.file_name',
                'submissions.file_size',
                'submissions.mime_type',
                'submissions.status as submission_status',
                'submissions.submitted_at',
                'submissions.processed_at',
            )
            ->orderBy($sortColumn, $this->submissionSortDir)
            ->paginate(10, pageName: 'submissionsPage');
    }

    // Tab methods
    #[Computed]
    public function moduleClasses()
    {
        if (! $this->selectedModuleId) {
            return collect();
        }

        return ClassSession::with('resourceFolder.resources', 'resourceFolder.children')
            ->where('module_id', $this->selectedModuleId)
            ->orderBy('starts_at')
            ->paginate(10, pageName: 'classesPage');
    }

    #[Computed]
    public function moduleStudents()
    {
        if (! $this->selectedModuleId) {
            return collect();
        }

        return User::join('enrollments', 'users.id', '=', 'enrollments.user_id')
            ->where('enrollments.module_id', $this->selectedModuleId)
            ->where('enrollments.status', '!=', 'DROPPED')
            ->select('users.*', 'enrollments.status as enrollment_status', 'enrollments.enrolled_at')
            ->orderBy(
                $this->studentSort === 'status' ? 'enrollments.status' : "users.{$this->studentSort}",
                $this->studentSortDir
            )
            ->paginate(10, pageName: 'studentsPage');
    }

    #[Computed]
    public function moduleLecturers()
    {
        if (! $this->selectedModuleId) {
            return collect();
        }

        return User::whereHas('moduleAssignments', fn ($q) =>
            $q->where('module_id', $this->selectedModuleId)
        )
        ->paginate(10, pageName: 'lecturersPage');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.module-management');
    }
}
