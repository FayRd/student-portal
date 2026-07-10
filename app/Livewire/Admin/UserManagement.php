<?php

namespace App\Livewire\Admin;

use App\Actions\Admin\CreateUserAction;
use App\Actions\Admin\ResetUserPasswordAction;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    // Filters
    public string $search       = '';
    public string $roleFilter   = '';
    public string $statusFilter = 'active';
    public string $sortBy       = 'created_at';
    public string $sortDir      = 'desc';

    // State
    public ?int  $selectedUserId  = null;
    public string $mode           = 'view'; // 'view', 'create', 'edit'
    public string $tempPassword   = '';

    // Form fields
    public string $formName            = '';
    public string $formEmail           = '';
    public string $formInstitutionalId = '';
    public string $formRole            = 'student';

    // Reset pagination on filter change
    public function updatedSearch(): void       { $this->resetPage(); }
    public function updatedRoleFilter(): void   { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }

    #[Computed]
    public function stats(): array
    {
        return [
            'total'      => User::count(),
            'students'   => User::role('student')->count(),
            'lecturers'  => User::role('lecturer')->count(),
            'admins'     => User::role('admin')->count(),
            'unverified' => User::whereNull('email_verified_at')->count(),
            'mustChange' => User::where('must_change_password', true)->count(),
            'deleted'    => User::onlyTrashed()->count(),
        ];
    }

    #[Computed]
    public function users()
    {
        $query = User::with('roles')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('institutional_id', 'like', "%{$this->search}%");
            }))
            ->when($this->roleFilter, fn ($q) => $q->role($this->roleFilter));

        $this->statusFilter === 'deleted'
            ? $query->onlyTrashed()
            : $query->withoutTrashed();

        return $query->orderBy($this->sortBy, $this->sortDir)->paginate(10);
    }

    #[Computed]
    public function selectedUser(): ?User
    {
        if (! $this->selectedUserId) {
            return null;
        }

        return User::with(['roles', 'enrolledModules', 'moduleAssignments.module'])
            ->withTrashed()
            ->find($this->selectedUserId);
    }

    public function sortColumn(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $column;
            $this->sortDir = 'asc';
        }
    }

    public function filterByStat(string $type): void
    {
        $this->search       = '';
        $this->roleFilter   = '';
        $this->statusFilter = 'active';

        match ($type) {
            'students'   => $this->roleFilter   = 'student',
            'lecturers'  => $this->roleFilter   = 'lecturer',
            'admins'     => $this->roleFilter   = 'admin',
            'deleted'    => $this->statusFilter = 'deleted',
            'unverified' => $this->search       = '',
            default      => null,
        };

        $this->resetPage();
    }

    public function selectUser(int $id): void
    {
        $this->selectedUserId = ($this->selectedUserId === $id) ? null : $id;
        $this->mode           = 'view';
        $this->tempPassword   = '';
        unset($this->selectedUser);
    }

    public function showCreateForm(): void
    {
        $this->selectedUserId = null;
        $this->mode           = 'create';
        $this->tempPassword   = '';
        $this->resetFormFields();
    }

    public function showEditForm(): void
    {
        if (! $this->selectedUser) {
            return;
        }

        $this->mode                = 'edit';
        $this->formName            = $this->selectedUser->name;
        $this->formEmail           = $this->selectedUser->email;
        $this->formInstitutionalId = $this->selectedUser->institutional_id ?? '';
        $this->formRole            = $this->selectedUser->roles->first()?->name ?? 'student';
    }

    public function createUser(): void
    {
        $this->validate([
            'formName'            => 'required|string|max:255',
            'formEmail'           => 'required|email|unique:users,email',
            'formInstitutionalId' => 'required|string|unique:users,institutional_id',
            'formRole'            => 'required|in:admin,lecturer,student',
        ]);

        $result = app(CreateUserAction::class)->execute([
            'name'             => $this->formName,
            'email'            => $this->formEmail,
            'institutional_id' => $this->formInstitutionalId,
            'role'             => $this->formRole,
        ]);

        $this->tempPassword   = $result['tempPassword'];
        $this->selectedUserId = $result['user']->id;
        $this->mode           = 'view';
        $this->resetFormFields();
        unset($this->stats, $this->users, $this->selectedUser);
    }

    public function updateUser(): void
    {
        $this->validate([
            'formName'            => 'required|string|max:255',
            'formEmail'           => ['required', 'email', Rule::unique('users', 'email')->ignore($this->selectedUserId)],
            'formInstitutionalId' => ['required', 'string', Rule::unique('users', 'institutional_id')->ignore($this->selectedUserId)],
            'formRole'            => 'required|in:admin,lecturer,student',
        ]);

        $user = User::find($this->selectedUserId);
        $user->update([
            'name'             => $this->formName,
            'email'            => $this->formEmail,
            'institutional_id' => $this->formInstitutionalId,
        ]);
        $user->syncRoles($this->formRole);

        $this->mode = 'view';
        $this->resetFormFields();
        unset($this->users, $this->selectedUser);
    }

    public function resetPassword(int $userId): void
    {
        $result             = app(ResetUserPasswordAction::class)->execute($userId);
        $this->tempPassword = $result['tempPassword'];
        unset($this->selectedUser);
    }

    public function resendVerification(int $userId): void
    {
        User::find($userId)->sendEmailVerificationNotification();
        session()->flash('status', 'Verification email sent.');
    }

    public function resetTwoFactor(int $userId): void
    {
        User::find($userId)->forceFill([
            'two_factor_secret'         => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at'   => null,
        ])->save();

        session()->flash('status', '2FA has been reset.');
        unset($this->selectedUser);
    }

    public function softDelete(int $userId): void
    {
        User::find($userId)->delete();
        $this->selectedUserId = null;
        $this->mode           = 'view';
        unset($this->stats, $this->users, $this->selectedUser);
    }

    public function restore(int $userId): void
    {
        User::withTrashed()->find($userId)->restore();
        unset($this->stats, $this->users, $this->selectedUser);
    }

    public function cancelForm(): void
    {
        $this->mode = 'view';
        $this->resetFormFields();
    }

    private function resetFormFields(): void
    {
        $this->formName            = '';
        $this->formEmail           = '';
        $this->formInstitutionalId = '';
        $this->formRole            = 'student';
        $this->resetValidation();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.user-management');
    }
}
