<div class="flex flex-col gap-0">

    {{-- Flash message --}}
    @if (session('status'))
        <div class="bg-green-50 border-b border-green-200 text-green-800 text-sm px-4 py-2">
            {{ session('status') }}
        </div>
    @endif

    {{-- ── SECTION 1: Stats strip ── --}}
    <div class="grid grid-cols-7 gap-3 p-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        @foreach ([
            ['label' => 'Total users',   'key' => 'total',      'value' => $this->stats['total'],      'color' => ''],
            ['label' => 'Students',      'key' => 'students',   'value' => $this->stats['students'],   'color' => ''],
            ['label' => 'Lecturers',     'key' => 'lecturers',  'value' => $this->stats['lecturers'],  'color' => ''],
            ['label' => 'Admins',        'key' => 'admins',     'value' => $this->stats['admins'],     'color' => ''],
            ['label' => 'Unverified',    'key' => 'unverified', 'value' => $this->stats['unverified'], 'color' => 'text-yellow-600'],
            ['label' => 'Must reset',    'key' => 'mustChange', 'value' => $this->stats['mustChange'], 'color' => 'text-yellow-600'],
            ['label' => 'Soft deleted',  'key' => 'deleted',    'value' => $this->stats['deleted'],    'color' => 'text-red-600'],
        ] as $stat)
            <button
                wire:click="filterByStat('{{ $stat['key'] }}')"
                class="text-left p-3 rounded-lg border border-transparent bg-gray-50 dark:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-500 transition-colors"
            >
                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 truncate">{{ $stat['label'] }}</div>
                <div class="text-xl font-medium {{ $stat['color'] }}">{{ $stat['value'] }}</div>
            </button>
        @endforeach
    </div>

    {{-- ── SECTION 2: User table ── --}}
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">

        {{-- Toolbar --}}
        <div class="flex items-center gap-3 p-4 flex-wrap">
            <div class="relative">
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search name, email, ID…"
                    class="pl-8 pr-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 w-64 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                <svg class="absolute left-2.5 top-2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
            </div>

            <select
                wire:model.live="roleFilter"
                class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                aria-label="Filter by role"
            >
                <option value="">All roles</option>
                <option value="student">Student</option>
                <option value="lecturer">Lecturer</option>
                <option value="admin">Admin</option>
            </select>

            <select
                wire:model.live="statusFilter"
                class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                aria-label="Filter by status"
            >
                <option value="active">Active</option>
                <option value="deleted">Soft deleted</option>
            </select>

            <button
                wire:click="showCreateForm"
                class="ml-auto flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create user
            </button>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-xs text-gray-500 dark:text-gray-400 font-medium">
                    <tr>
                        <th wire:click="sortColumn('institutional_id')" class="text-left px-4 py-3 cursor-pointer select-none hover:text-white">
                            Institutional ID
                            @if ($sortBy === 'institutional_id')
                                <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th wire:click="sortColumn('name')" class="text-left px-4 py-3 cursor-pointer select-none hover:text-white">
                            Name
                            @if ($sortBy === 'name')
                                <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="text-left px-4 py-3">Email</th>
                        <th class="text-left px-4 py-3">Role</th>
                        <th class="text-left px-4 py-3">Verified</th>
                        <th wire:click="sortColumn('created_at')" class="text-left px-4 py-3 cursor-pointer select-none hover:text-white">
                            Created
                            @if ($sortBy === 'created_at')
                                <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($this->users as $user)
                        <tr
                            wire:click="selectUser({{ $user->id }})"
                            class="cursor-pointer transition-colors {{ $selectedUserId === $user->id ? 'bg-blue-50 dark:bg-blue-900/20' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                        >
                            <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $user->institutional_id ?? '—' }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                                {{ $user->name }}
                                @if ($user->must_change_password)
                                    <span class="ml-1 text-xs text-yellow-600">(must reset)</span>
                                @endif
                                @if ($user->trashed())
                                    <span class="ml-1 text-xs text-red-500">(deleted)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                @php $role = $user->roles->first()?->name; @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $role === 'admin' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' : '' }}
                                    {{ $role === 'lecturer' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                    {{ $role === 'student' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                ">
                                    {{ ucfirst($role ?? '—') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($user->email_verified_at)
                                    <span class="text-green-600 dark:text-green-400 text-xs">✓ Verified</span>
                                @else
                                    <span class="text-red-500 dark:text-red-400 text-xs">✗ Unverified</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-400">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
            {{ $this->users->links() }}
        </div>
    </div>

    {{-- ── SECTION 3: Detail panel ── --}}
    <div class="bg-white dark:bg-gray-800 min-h-48 p-4">

        {{-- Empty state --}}
        @if (! $this->selectedUser && $mode === 'view')
            <div class="flex flex-col items-center justify-center h-40 text-gray-400 dark:text-gray-500 text-sm gap-2">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Select a user from the table to view details
            </div>
        @endif

        {{-- Create / Edit form --}}
        @if ($mode === 'create' || $mode === 'edit')
            <div class="max-w-2xl">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    {{ $mode === 'create' ? 'Create new user' : 'Edit user' }}
                </h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Full name</label>
                        <input wire:model="formName" type="text" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('formName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Email</label>
                        <input wire:model="formEmail" type="email" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('formEmail') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Institutional ID</label>
                        <input wire:model="formInstitutionalId" type="text" placeholder="1xxxxxxx or 2xxxxxxx" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                        @error('formInstitutionalId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Role</label>
                        <select wire:model="formRole" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="student">Student</option>
                            <option value="lecturer">Lecturer</option>
                            <option value="admin">Admin</option>
                        </select>
                        @error('formRole') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                @if ($mode === 'create')
                    <p class="mt-3 text-xs text-gray-500">A temporary password will be generated and displayed once on creation. The user will be prompted to change it on first login.</p>
                @endif

                <div class="flex gap-2 mt-4">
                    <button
                        wire:click="{{ $mode === 'create' ? 'createUser' : 'updateUser' }}"
                        class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors"
                    >
                        {{ $mode === 'create' ? 'Create user' : 'Save changes' }}
                    </button>
                    <button wire:click="cancelForm" class="px-4 py-1.5 border border-gray-300 dark:border-gray-600 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        @endif

        {{-- User detail --}}
        @if ($this->selectedUser && $mode === 'view')
            @php $user = $this->selectedUser; @endphp
            <div>
                {{-- Header row --}}
                <div class="flex items-start justify-between mb-4 flex-wrap gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 flex items-center justify-center font-medium text-sm">
                            {{ strtoupper(substr($user->name, 0, 1) . substr(strrchr($user->name, ' '), 1, 1)) }}
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $user->institutional_id }} · {{ $user->email }}</div>
                        </div>
                    </div>

                    {{-- Action buttons --}}
                    <div class="flex flex-wrap gap-2">
                        <button wire:click="showEditForm" class="flex items-center gap-1 px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Edit
                        </button>
                        <button wire:click="resetPassword({{ $user->id }})" class="flex items-center gap-1 px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Reset password
                        </button>
                        @if (! $user->email_verified_at)
                            <button wire:click="resendVerification({{ $user->id }})" class="flex items-center gap-1 px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Resend verification
                            </button>
                        @endif
                        @if ($user->two_factor_confirmed_at)
                            <button wire:click="resetTwoFactor({{ $user->id }})" class="flex items-center gap-1 px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Reset 2FA
                            </button>
                        @endif
                        @if ($user->trashed())
                            <button wire:click="restore({{ $user->id }})" wire:confirm="Restore this user?" class="flex items-center gap-1 px-3 py-1.5 text-xs border border-green-300 text-green-700 rounded-lg hover:bg-green-50 transition-colors">
                                Restore
                            </button>
                        @else
                            <button wire:click="softDelete({{ $user->id }})" wire:confirm="Soft delete this user? They can be restored later." class="flex items-center gap-1 px-3 py-1.5 text-xs border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                                Delete
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Detail body --}}
                <div class="grid grid-cols-2 gap-6">
                    {{-- Profile fields --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Profile</p>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach ([
                                'Full name'        => $user->name,
                                'Role'             => ucfirst($user->roles->first()?->name ?? '—'),
                                'Institutional ID' => $user->institutional_id ?? '—',
                                'Email'            => $user->email,
                                'Created'          => $user->created_at->format('d M Y'),
                                'Deleted at'       => $user->deleted_at?->format('d M Y') ?? '—',
                            ] as $label => $value)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg px-3 py-2">
                                    <div class="text-xs text-gray-400 mb-0.5">{{ $label }}</div>
                                    <div class="text-xs font-medium text-gray-900 dark:text-gray-100 truncate">{{ $value }}</div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Status pills --}}
                        <div class="flex flex-wrap gap-2 mt-3">
                            <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full {{ $user->email_verified_at ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                {{ $user->email_verified_at ? '✓ Email verified' : '✗ Email unverified' }}
                            </span>
                            <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full {{ $user->two_factor_confirmed_at ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $user->two_factor_confirmed_at ? '✓ 2FA enabled' : '2FA disabled' }}
                            </span>
                            @if ($user->must_change_password)
                                <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-700">
                                    ⚠ Must reset password
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Modules --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">
                            {{ $user->isStudent() ? 'Enrolled modules' : ($user->isLecturer() ? 'Assigned modules' : 'Access') }}
                        </p>
                        @if ($user->isAdmin())
                            <p class="text-xs text-gray-400">Full system access — no module restrictions.</p>
                        @elseif ($user->isLecturer())
                            <div class="flex flex-col gap-1">
                                @forelse ($user->moduleAssignments as $pivot)
                                    <div class="flex items-center justify-between px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <span class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ $pivot->module->name }}</span>
                                        <span class="text-xs text-gray-400 font-mono">{{ $pivot->module->code }}</span>
                                    </div>
                                @empty
                                    <p class="text-xs text-gray-400">No modules assigned.</p>
                                @endforelse
                            </div>
                        @else
                            <div class="flex flex-col gap-1">
                                @forelse ($user->enrolledModules as $module)
                                    <div class="flex items-center justify-between px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <span class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ $module->name }}</span>
                                        <span class="text-xs text-gray-400 font-mono">{{ $module->code }}</span>
                                    </div>
                                @empty
                                    <p class="text-xs text-gray-400">No enrollments.</p>
                                @endforelse
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Temp password banner --}}
        @if ($tempPassword)
            <div class="bg-yellow-50 border-b border-yellow-200 px-4 py-3 my-3 flex items-center justify-between">
                <div class="text-sm text-yellow-800">
                    <span class="font-medium">Temporary password:</span>
                    <code class="ml-2 bg-yellow-100 px-2 py-0.5 rounded font-mono">{{ $tempPassword }}</code>
                    <span class="ml-2 text-yellow-600">— copy this now, it won't be shown again.</span>
                </div>
                <button wire:click="$set('tempPassword', '')" class="text-yellow-600 hover:text-yellow-800 text-xs">Dismiss</button>
            </div>
        @endif
    </div>
</div>
