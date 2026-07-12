<?php

namespace App\Livewire\Admin;

use App\Models\Module;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ModuleManagement extends Component
{
    use WithPagination;

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

        return Module::with([
            'creator',
            'editors',
            'classSessions',
            'enrolledStudents',
            'assignments',
            'resources',
        ])->find($this->selectedModuleId);
    }

    #[Computed]
    public function lecturers()
    {
        return User::role('lecturer')->orderBy('name')->get();
    }

    public function sortColumn(string $column): void
    {
        $this->sortBy  = $this->sortBy === $column && $this->sortDir === 'asc'
            ? $this->sortBy
            : $column;
        $this->sortDir = $this->sortBy === $column && $this->sortDir === 'asc'
            ? 'desc'
            : 'asc';
        $this->sortBy  = $column;
    }

    public function selectModule(int $id): void
    {
        $this->selectedModuleId = ($this->selectedModuleId === $id) ? null : $id;
        $this->mode             = 'view';
        $this->activeTab        = 'classes';
        unset($this->selectedModule);
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function showCreateForm(): void
    {
        $this->selectedModuleId = null;
        $this->mode             = 'create';
        $this->resetFormFields();
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
            'formLecturerId'   => 'required|exists:users,id',
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

        $module->editors()->sync([$this->formLecturerId => [
            'role'       => 'editor',
            'created_at' => now(),
        ]]);

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
        $this->formLecturerId   = null;
        $this->resetValidation();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.module-management');
    }
}
