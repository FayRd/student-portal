<div class="flex flex-col gap-0">

    {{-- ── SECTION 1: Stats strip ── --}}
    <div class="grid grid-cols-5 gap-3 p-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        @foreach ([
            ['label' => 'Total modules',  'value' => $this->stats['total'],       'color' => ''],
            ['label' => 'Active',         'value' => $this->stats['active'],      'color' => 'text-green-600'],
            ['label' => 'Upcoming',       'value' => $this->stats['upcoming'],    'color' => 'text-blue-600'],
            ['label' => 'Archived',       'value' => $this->stats['archived'],    'color' => 'text-gray-400'],
            ['label' => 'Enrollments',    'value' => $this->stats['enrollments'], 'color' => ''],
        ] as $stat)
            <div class="text-left p-3 rounded-lg bg-gray-50 dark:bg-gray-700">
                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $stat['label'] }}</div>
                <div class="text-xl font-medium {{ $stat['color'] }}">{{ $stat['value'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- ── SECTION 2: Module table ── --}}
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">

        {{-- Toolbar --}}
        <div class="flex items-center gap-3 p-4 flex-wrap">
            <div class="relative">
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search name or code…"
                    class="pl-8 pr-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 w-64 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                <svg class="absolute left-2.5 top-2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
            </div>

            <select
                wire:model.live="statusFilter"
                class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                aria-label="Filter by status"
            >
                <option value="">All statuses</option>
                <option value="ACTIVE">Active</option>
                <option value="UPCOMING">Upcoming</option>
                <option value="ARCHIVED">Archived</option>
            </select>

            <button
                wire:click="showCreateForm"
                class="ml-auto flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create module
            </button>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-xs text-gray-500 dark:text-gray-400 font-medium">
                    <tr>
                        <th wire:click="sortColumn('code')" class="text-left px-4 py-3 cursor-pointer select-none hover:text-gray-700 dark:hover:text-gray-200">
                            <div class="flex items-center gap-1">
                                Code
                                @if ($sortBy === 'code')
                                    <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortColumn('name')" class="text-left px-4 py-3 cursor-pointer select-none hover:text-gray-700 dark:hover:text-gray-200">
                            <div class="flex items-center gap-1">
                                Name
                                @if ($sortBy === 'name')
                                    <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </div>
                        </th>
                        <th class="text-left px-4 py-3">Lecturer</th>
                        <th class="text-left px-4 py-3">Credits</th>
                        <th class="text-left px-4 py-3">Semester</th>
                        <th class="text-left px-4 py-3">Status</th>
                        <th wire:click="sortColumn('created_at')" class="text-left px-4 py-3 cursor-pointer select-none hover:text-gray-700 dark:hover:text-gray-200">
                            <div class="flex items-center gap-1">
                                Created
                                @if ($sortBy === 'created_at')
                                    <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($this->modules as $module)
                        <tr
                            wire:click="selectModule({{ $module->id }})"
                            class="cursor-pointer transition-colors {{ $selectedModuleId === $module->id ? 'bg-blue-50 dark:bg-blue-900/20' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                        >
                            <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $module->code }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $module->name }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                @php $editors = $module->editors; @endphp
                                {{ $editors->first()?->name ?? '—' }}
                                @if ($editors->count() > 1)
                                    <span class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 px-1.5 py-0.5 rounded-full">
                                        +{{ $editors->count() - 1 }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $module->credits }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">Sem {{ $module->semester }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium w-20 justify-center rounded-sm
                                    {{ $module->status === 'ACTIVE'   ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                    {{ $module->status === 'UPCOMING' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                    {{ $module->status === 'ARCHIVED' ? 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' : '' }}
                                ">
                                    {{ ucfirst(strtolower($module->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $module->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-400">No modules found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
            {{ $this->modules->links() }}
        </div>
    </div>

    {{-- ── SECTION 3: Detail panel ── --}}
    <div class="bg-white dark:bg-gray-800 p-4 min-h-80">

        {{-- Empty state --}}
        @if (! $this->selectedModule && $mode === 'view')
            <div class="flex flex-col items-center justify-center h-40 text-gray-400 dark:text-gray-500 text-sm gap-2 min-h-75">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Select a module from the table to view details
            </div>
        @endif

        {{-- Create / Edit form --}}
        @if ($mode === 'create' || $mode === 'edit')
            <div class="max-w-3xl">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    {{ $mode === 'create' ? 'Create new module' : 'Edit module' }}
                </h3>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Module code</label>
                        <input wire:model="formCode" type="text" placeholder="e.g. CS101" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono uppercase">
                        @error('formCode') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Module name</label>
                        <input wire:model="formName" type="text" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('formName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="col-span-3">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Description</label>
                        <textarea wire:model="formDescription" rows="3" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        @error('formDescription') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Credits</label>
                        <input wire:model="formCredits" type="number" min="1" max="12" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('formCredits') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Academic year</label>
                        <input wire:model="formAcademicYear" type="text" placeholder="2025/2026" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('formAcademicYear') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Semester</label>
                        <select wire:model="formSemester" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="1">Semester 1</option>
                            <option value="2">Semester 2</option>
                        </select>
                        @error('formSemester') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Status</label>
                        <select wire:model="formStatus" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="UPCOMING">Upcoming</option>
                            <option value="ACTIVE">Active</option>
                            <option value="ARCHIVED">Archived</option>
                        </select>
                        @error('formStatus') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="col-span-3">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                            Assign lecturers
                        </label>
                        <div x-data="{ open: false, search: '' }" class="relative">

                            {{-- Selected chips --}}
                            @if (count($formLecturerIds) > 0)
                                <div class="flex flex-wrap gap-1 mb-1">
                                    @foreach ($this->lecturers->whereIn('id', $formLecturerIds) as $selected)
                                        <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 text-xs px-2 py-0.5 rounded-full">
                                            {{ $selected->name }}
                                            <button
                                                type="button"
                                                wire:click.stop="removeLecturer({{ $selected->id }})"
                                                class="hover:text-blue-900 leading-none"
                                            >×</button>
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Dropdown trigger --}}
                            <button
                                type="button"
                                @click="open = !open"
                                class="w-full text-left text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-500 dark:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-between"
                            >
                                <span>{{ count($formLecturerIds) === 0 ? 'Select lecturers…' : count($formLecturerIds) . ' selected' }}</span>
                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            {{-- Dropdown list --}}
                            <div
                                x-show="open"
                                @click.outside="open = false"
                                class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg"
                            >
                                <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                                    <input
                                        x-model="search"
                                        type="text"
                                        placeholder="Search lecturers…"
                                        class="w-full text-xs border border-gray-200 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none"
                                    >
                                </div>
                                <ul class="max-h-48 overflow-y-auto py-1">
                                    @foreach ($this->lecturers as $lecturer)
                                        <li
                                            x-show="search === '' || '{{ strtolower($lecturer->name) }}'.includes(search.toLowerCase())"
                                            class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
                                            wire:click="toggleLecturer({{ $lecturer->id }})"
                                        >
                                            <div class="w-4 h-4 rounded border flex items-center justify-center shrink-0
                                                {{ in_array($lecturer->id, $formLecturerIds) ? 'bg-blue-600 border-blue-600' : 'border-gray-300 dark:border-gray-500' }}">
                                                @if (in_array($lecturer->id, $formLecturerIds))
                                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                @endif
                                            </div>
                                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $lecturer->name }}</span>
                                            <span class="ml-auto text-xs text-gray-400 font-mono">{{ $lecturer->institutional_id }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @error('formLecturerIds') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex gap-2 mt-4">
                    <button
                        wire:click="{{ $mode === 'create' ? 'createModule' : 'updateModule' }}"
                        class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors"
                    >
                        {{ $mode === 'create' ? 'Create module' : 'Save changes' }}
                    </button>
                    <button wire:click="cancelForm" class="px-4 py-1.5 border border-gray-300 dark:border-gray-600 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        @endif

        {{-- Module detail --}}
        @if ($this->selectedModule && $mode === 'view')
            @php $module = $this->selectedModule; @endphp

            {{-- Header --}}
            <div class="flex items-start justify-between mb-4 flex-wrap gap-3">
                <div>
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-xs text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $module->code }}</span>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $module->name }}</h3>
                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium w-20 justify-center rounded-sm
                            {{ $module->status === 'ACTIVE'   ? 'bg-green-100 text-green-700' : '' }}
                            {{ $module->status === 'UPCOMING' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $module->status === 'ARCHIVED' ? 'bg-gray-100 text-gray-500' : '' }}
                        ">
                            {{ ucfirst(strtolower($module->status)) }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $module->credits }} credits · {{ $module->academic_year }} · Semester {{ $module->semester }} ·
                        @if ($module->editors->isEmpty())
                            No lecturer assigned
                        @else
                            {{ $module->editors->first()->name }}
                            @if ($module->editors->count() > 1)
                                +{{ $module->editors->count() - 1 }}
                            @endif
                        @endif
                    </p>
                </div>

                <div class="flex gap-2">
                    <button wire:click="showEditForm" class="flex items-center gap-1 px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Edit
                    </button>
                    <button wire:click="deleteModule({{ $module->id }})" wire:confirm="Delete this module? This cannot be undone." class="flex items-center gap-1 px-3 py-1.5 text-xs border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                        Delete
                    </button>
                </div>
            </div>

            {{-- Description --}}
            <div class="text-sm text-gray-900 dark:text-gray-100 mb-3 py-3">
                {{ $module->description }}
            </div>

            {{-- Tabs --}}
            <div class="flex items-center border-b border-gray-200 dark:border-gray-700 mb-4">
                <div class="flex gap-1 flex-1">
                    @foreach ([
                        ['key' => 'classes',  'label' => 'Classes ('  . $this->moduleClasses->total() . ')'],
                        ['key' => 'students', 'label' => 'Students (' . $this->moduleStudents->total() . ')'],
                        ['key' => 'lecturer', 'label' => 'Lecturers (' . $this->moduleLecturers->total() . ')'],
                        ['key' => 'assignments', 'label' => 'Assignments (' . $this->moduleAssignments->total() . ')'],
                    ] as $tab)
                        <button
                            wire:click="setTab('{{ $tab['key'] }}')"
                            class="px-4 py-2 text-xs font-medium border-b-2 transition-colors {{ $activeTab === $tab['key'] ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}"
                        >
                            {{ $tab['label'] }}
                        </button>
                    @endforeach
                </div>

                {{-- Tab action buttons --}}
                @if ($activeTab === 'classes')
                    <button wire:click="openClassModal" class="mb-1 flex items-center gap-1 px-2.5 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add class
                    </button>
                @elseif ($activeTab === 'students')
                    <button wire:click="openStudentModal" class="mb-1 flex items-center gap-1 px-2.5 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Enroll students
                    </button>
                @elseif ($activeTab === 'lecturer')
                    <button wire:click="openLecturerModal" class="mb-1 flex items-center gap-1 px-2.5 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Assign lecturer
                    </button>
                @elseif ($activeTab === 'assignments')
                    {{-- No add button for now — assignments are lecturer-created --}}
                @endif
            </div>

            {{-- Tab content --}}
            @if ($activeTab === 'classes')
                <div class="grid grid-cols-5 gap-4">

                    {{-- Left: accordion list --}}
                    <div class="flex flex-col gap-2 col-span-2">
                        @forelse ($this->moduleClasses as $session)
                            @php
                                $location = $this->resolveLocation($session->location);
                                $isOpen   = $expandedClassId === $session->id;
                            @endphp

                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                <button
                                    type="button"
                                    wire:click="toggleClass({{ $session->id }})"
                                    class="w-full flex items-center justify-between px-4 py-3 text-left {{ $isOpen ? 'bg-blue-50 dark:bg-blue-900/20' : 'bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600' }} transition-colors"
                                >
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span class="text-xs px-1.5 py-0.5 text-center font-medium shrink-0 rounded-sm
                                            {{ $session->type === 'PHYSICAL' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' }}">
                                            {{ ucfirst(strtolower($session->type)) }}
                                        </span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $session->title }}</span>
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0 ml-3">
                                        <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                            {{ $session->starts_at->format('d M, H:i') }} — {{ $session->ends_at->format('H:i') }}
                                        </span>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform shrink-0 {{ $isOpen ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </button>

                                @if ($isOpen)
                                    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 flex flex-col gap-2 text-xs">
                                        <div class="flex gap-2">
                                            <span class="text-gray-400 w-16 shrink-0">Location</span>
                                            @if ($location['type'] === 'link')
                                                <a href="{{ $location['value'] }}" target="_blank" class="text-blue-500 hover:underline" wire:click.stop>{{ $location['label'] }}</a>
                                            @else
                                                <span class="text-gray-900 dark:text-gray-100">{{ $location['value'] }}</span>
                                            @endif
                                        </div>
                                        <div class="flex gap-2">
                                            <span class="text-gray-400 w-16 shrink-0">Starts</span>
                                            <span class="text-gray-900 dark:text-gray-100">{{ $session->starts_at->format('d M Y, H:i') }}</span>
                                        </div>
                                        <div class="flex gap-2">
                                            <span class="text-gray-400 w-16 shrink-0">Ends</span>
                                            <span class="text-gray-900 dark:text-gray-100">{{ $session->ends_at->format('H:i') }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-8 gap-3 text-gray-400 dark:text-gray-500">
                                <p class="text-xs">No classes scheduled.</p>
                                <button wire:click="openClassModal" class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded text-gray-400 dark:text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                        @endforelse

                        <div class="mt-2">{{ $this->moduleClasses->links() }}</div>
                    </div>

                    {{-- Right: resource panel --}}
                    <div class="col-span-3 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden min-h-32">
                        @if (! $expandedClassId)
                            <div class="flex flex-col items-center justify-center h-full gap-3 py-8 text-gray-400 dark:text-gray-500 min-h-50">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                <span class="text-xs">Open a class to view resources</span>
                            </div>
                        @else
                            @php
                                $linkedFolderId = $this->getLinkedFolderId();
                                $contents       = $this->resourceContents;
                                $hasContent     = $contents['folders']->isNotEmpty() || $contents['resources']->isNotEmpty();
                                $crumbs         = $this->getFolderBreadcrumb();
                            @endphp

                            {{-- Header: breadcrumb + no linked folder warning --}}
                            <div class="px-3 py-2 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 min-h-9">
                                @if (! $linkedFolderId)
                                    <p class="text-xs text-gray-400">No resource folder linked to this session.</p>
                                @else
                                    <div class="flex items-center gap-1 text-xs text-gray-400 flex-wrap">
                                        <button
                                            wire:click="browseFolder(null)"
                                            class="hover:text-gray-600 dark:hover:text-gray-300 transition-colors truncate max-w-32"
                                        >
                                            {{ \App\Models\ResourceFolder::find($linkedFolderId)?->name ?? 'Root' }}
                                        </button>
                                        @foreach ($crumbs as $crumb)
                                            @if ($crumb['id'] == $linkedFolderId)
                                                @continue
                                            @endif
                                            <span>/</span>
                                            <button
                                                wire:click="browseFolder({{ $crumb['id'] }})"
                                                class="hover:text-gray-600 dark:hover:text-gray-300 transition-colors truncate max-w-32"
                                            >
                                                {{ $crumb['name'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- Resource grid --}}
                            @if ($linkedFolderId)
                                <div class="p-3">
                                    {{-- @if (! $hasContent)
                                        <div class="flex flex-col items-center justify-center py-6 gap-3 text-gray-400 dark:text-gray-500">
                                            <p class="text-xs">No resources here.</p>
                                        </div>
                                    @endif --}}

                                    <div class="grid grid-cols-5 gap-2">
                                        {{-- Folders --}}
                                        @foreach ($contents['folders'] as $child)
                                            <button
                                                type="button"
                                                wire:click="browseFolder({{ $child->id }})"
                                                class="flex flex-col items-center gap-1 p-2 rounded-lg bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer text-center transition-colors"
                                            >
                                                <svg class="w-10 h-10 text-yellow-500" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M10 4H2v16h20V6H12l-2-2z"/></svg>
                                                <span class="text-xs text-gray-700 dark:text-gray-300 truncate w-full leading-tight">{{ $child->name }}</span>
                                            </button>
                                        @endforeach

                                        {{-- Files --}}
                                        @foreach ($contents['resources'] as $resource)
                                            <a
                                                href="{{ route('resources.download', $resource->id) }}"
                                                class="flex flex-col items-center gap-1 p-2 rounded-lg bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-center transition-colors"
                                                title="{{ $resource->file_name }}"
                                            >
                                                <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <span class="text-xs text-gray-700 dark:text-gray-300 truncate w-full leading-tight">{{ $resource->title }}</span>
                                            </a>
                                        @endforeach

                                        {{-- Add button -- always last in grid --}}
                                        <button
                                            type="button"
                                            wire:click="openResourceModal"
                                            class="flex flex-col items-center justify-center gap-1 p-2 rounded-lg border border-dashed border-gray-300 dark:border-gray-600 text-gray-400 dark:text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-400 transition-colors text-center min-h-16"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            <span class="text-xs">Add</span>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @endif

            @if ($activeTab === 'students')
                @if ($this->moduleStudents->isEmpty())
                    <div class="flex flex-col items-center justify-center py-8 gap-3 text-gray-400 dark:text-gray-500">
                        <p class="text-xs">No students enrolled.</p>
                        <button wire:click="openStudentModal" class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded text-gray-400 dark:text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                @else
                    <table class="w-full text-sm table-fixed">
                        <thead>
                            <tr class="text-xs text-gray-400 dark:text-gray-500">
                                <th class="text-left py-2 pr-4 font-medium w-5/12">
                                    <button wire:click="sortStudents('name')" class="flex items-center gap-1 hover:text-gray-600 dark:hover:text-gray-300">
                                        Name
                                        @if ($studentSort === 'name')
                                            <span>{{ $studentSortDir === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </button>
                                </th>
                                <th class="text-left py-2 pr-4 font-medium w-3/12">
                                    <button wire:click="sortStudents('institutional_id')" class="flex items-center gap-1 hover:text-gray-600 dark:hover:text-gray-300">
                                        ID
                                        @if ($studentSort === 'institutional_id')
                                            <span>{{ $studentSortDir === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </button>
                                </th>
                                <th class="text-left py-2 font-medium w-10">
                                    <button wire:click="sortStudents('status')" class="flex items-center gap-1 hover:text-gray-600 dark:hover:text-gray-300">
                                        Status
                                        @if ($studentSort === 'status')
                                            <span>{{ $studentSortDir === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </button>
                                </th>
                                <th class="py-2 font-medium w-5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            @foreach ($this->moduleStudents as $student)
                                <tr
                                    x-data="{ hovered: false }"
                                    @mouseenter="hovered = true"
                                    @mouseleave="hovered = false"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors"
                                >
                                    <td class="py-2.5 pr-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 flex items-center justify-center text-xs font-medium shrink-0">
                                                {{ strtoupper(substr($student->name, 0, 1) . (strstr($student->name, ' ') ? substr(strstr($student->name, ' '), 1, 1) : '')) }}
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $student->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-2.5 pr-4 text-xs font-mono text-gray-500 dark:text-gray-400">
                                        {{ $student->institutional_id }}
                                    </td>
                                    <td class="py-2.5">
                                        @php $status = $student->enrollment_status ?? 'ACTIVE'; @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium w-20 justify-center rounded-sm
                                            {{ $status === 'ACTIVE'    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                            {{ $status === 'COMPLETED' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                            {{ $status === 'DROPPED'   ? 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                        ">
                                            {{ ucfirst(strtolower($status)) }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 text-left pr-3">
                                        <button
                                            x-show="hovered"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="opacity-0 scale-90"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            wire:click="removeStudent({{ $student->id }})"
                                            class="w-5 h-5 rounded-full bg-red-100 dark:bg-red-900/30 text-red-500 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/50 flex items-center justify-center transition-colors ml-auto"
                                            aria-label="Remove {{ $student->name }} from module"
                                        >
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-2">{{ $this->moduleStudents->links() }}</div>
                @endif
            @endif

            @if ($activeTab === 'lecturer')
                <div class="grid grid-cols-3 gap-3">
                    @forelse ($this->moduleLecturers as $lecturer)
                        <div
                            x-data="{ hovered: false }"
                            @mouseenter="hovered = true"
                            @mouseleave="hovered = false"
                            class="relative flex items-center gap-3 px-3 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
                        >
                            <div class="w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 flex items-center justify-center font-medium text-sm shrink-0">
                                {{ strtoupper(substr($lecturer->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $lecturer->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $lecturer->email }}</div>
                                <div class="text-xs text-gray-400 font-mono">{{ $lecturer->institutional_id }}</div>
                            </div>
                            <button
                                x-show="hovered"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-90"
                                x-transition:enter-end="opacity-100 scale-100"
                                wire:click="detachLecturer({{ $lecturer->id }})"
                                class="absolute top-2 right-2 w-5 h-5 rounded-full bg-red-100 dark:bg-red-900/30 text-red-500 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/50 flex items-center justify-center transition-colors"
                                aria-label="Remove {{ $lecturer->name }} from module"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                            </button>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-8 gap-3 col-span-3 text-gray-400 dark:text-gray-500">
                            <p class="text-xs">No lecturers assigned.</p>
                            <button wire:click="openLecturerModal" class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded text-gray-400 dark:text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                    @endforelse
                </div>
                @if ($this->moduleLecturers->hasPages())
                    <div class="mt-2">{{ $this->moduleLecturers->links() }}</div>
                @endif
            @endif

            @if ($activeTab === 'assignments')
                <div class="grid grid-cols-4 gap-4">

                    {{-- Left: assignment icon grid --}}
                    <div>
                        @if ($this->moduleAssignments->isEmpty())
                            <div class="flex flex-col items-center justify-center py-8 gap-3 text-gray-400 dark:text-gray-500">
                                <p class="text-xs">No assignments created.</p>
                            </div>
                        @else
                            <div class="col-span-1 grid grid-cols-3 gap-2 mb-2">
                                @foreach ($this->moduleAssignments as $assignment)
                                    @php
                                        $now       = now();
                                        $isUpcoming = $assignment->available_at > $now;
                                        $isPast    = $assignment->due_at < $now;
                                        $isSelected = $selectedAssignmentId === $assignment->id;
                                    @endphp

                                    <button
                                        @if ($isUpcoming) disabled @else wire:click="selectAssignment({{ $assignment->id }})" @endif
                                        type="button"
                                        class="flex flex-col items-center gap-1.5 p-3 rounded-lg text-center transition-colors border
                                            {{ $isUpcoming    ? 'opacity-40 cursor-not-allowed border-transparent bg-gray-50 dark:bg-gray-700/50' : '' }}
                                            {{ $isSelected    ? 'border-blue-400 bg-blue-50 dark:bg-blue-900/20' : '' }}
                                            {{ ! $isUpcoming && ! $isSelected ? 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer' : '' }}
                                        "
                                    >
                                        {{-- Assignment icon --}}
                                        <svg class="w-10 h-10 {{ $isUpcoming ? 'text-gray-300 dark:text-gray-600' : ($isPast ? 'text-gray-400' : 'text-blue-400') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>

                                        <span class="text-xs font-medium text-gray-800 dark:text-gray-200 truncate w-full leading-tight">
                                            {{ $assignment->title }}
                                        </span>

                                        <span class="text-xs {{ $isPast ? 'text-red-400' : 'text-gray-400' }}">
                                            Due {{ $assignment->due_at->format('d M') }}
                                        </span>

                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-sm text-xs font-medium
                                            {{ $isUpcoming ? 'bg-gray-100 text-gray-400 dark:bg-gray-600 dark:text-gray-400' : '' }}
                                            {{ ! $isUpcoming && ! $isPast ? 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                            {{ $isPast && ! $isUpcoming ? 'bg-gray-100 text-gray-500 dark:bg-gray-600 dark:text-gray-400' : '' }}
                                        ">
                                            {{ $isUpcoming ? 'Upcoming' : ($isPast ? 'Past' : 'Active') }}
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                            <div>{{ $this->moduleAssignments->links() }}</div>
                        @endif
                    </div>

                    {{-- Right: submissions table --}}
                    <div class="col-span-3 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                        @if (! $this->selectedAssignment)
                            <div class="flex flex-col items-center justify-center h-full py-12 gap-2 text-gray-300 dark:text-gray-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <span class="text-xs">Select an assignment to view submissions</span>
                            </div>
                        @else
                            @php $assignment = $this->selectedAssignment; @endphp

                            {{-- Assignment detail strip --}}
                            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $assignment->title }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            Due {{ $assignment->due_at->format('d M Y, H:i') }}
                                            @if ($assignment->max_score)
                                                · Max score: {{ $assignment->max_score }}
                                            @endif
                                            · {{ ucfirst(strtolower($assignment->type)) }}
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-sm text-xs font-medium shrink-0
                                        {{ now()->between($assignment->available_at, $assignment->due_at) ? 'bg-green-100 text-green-600' : '' }}
                                        {{ $assignment->due_at < now() ? 'bg-gray-100 text-gray-500' : '' }}
                                        {{ $assignment->available_at > now() ? 'bg-blue-100 text-blue-600' : '' }}
                                    ">
                                        {{ $assignment->available_at > now() ? 'Upcoming' : ($assignment->due_at < now() ? 'Past' : 'Active') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Submissions table --}}
                            <div class="overflow-x-auto">
                                <table class="w-full text-xs">
                                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-400 dark:text-gray-500">
                                        <tr>
                                            <th class="text-left px-3 py-2 font-medium">
                                                <button wire:click="sortSubmissions('name')" class="flex items-center gap-1 hover:text-gray-600 dark:hover:text-gray-300">
                                                    Student @if ($submissionSort === 'name') <span>{{ $submissionSortDir === 'asc' ? '↑' : '↓' }}</span> @endif
                                                </button>
                                            </th>
                                            <th class="text-left px-3 py-2 font-medium">
                                                <button wire:click="sortSubmissions('file_name')" class="flex items-center gap-1 hover:text-gray-600 dark:hover:text-gray-300">
                                                    File @if ($submissionSort === 'file_name') <span>{{ $submissionSortDir === 'asc' ? '↑' : '↓' }}</span> @endif
                                                </button>
                                            </th>
                                            <th class="text-left px-3 py-2 font-medium">
                                                <button wire:click="sortSubmissions('file_size')" class="flex items-center gap-1 hover:text-gray-600 dark:hover:text-gray-300">
                                                    Size @if ($submissionSort === 'file_size') <span>{{ $submissionSortDir === 'asc' ? '↑' : '↓' }}</span> @endif
                                                </button>
                                            </th>
                                            <th class="text-left px-3 py-2 font-medium">
                                                <button wire:click="sortSubmissions('mime_type')" class="flex items-center gap-1 hover:text-gray-600 dark:hover:text-gray-300">
                                                    Type @if ($submissionSort === 'mime_type') <span>{{ $submissionSortDir === 'asc' ? '↑' : '↓' }}</span> @endif
                                                </button>
                                            </th>
                                            <th class="text-left px-3 py-2 font-medium">
                                                <button wire:click="sortSubmissions('status')" class="flex items-center gap-1 hover:text-gray-600 dark:hover:text-gray-300">
                                                    Status @if ($submissionSort === 'status') <span>{{ $submissionSortDir === 'asc' ? '↑' : '↓' }}</span> @endif
                                                </button>
                                            </th>
                                            <th class="text-left px-3 py-2 font-medium">
                                                <button wire:click="sortSubmissions('submitted_at')" class="flex items-center gap-1 hover:text-gray-600 dark:hover:text-gray-300">
                                                    Submitted @if ($submissionSort === 'submitted_at') <span>{{ $submissionSortDir === 'asc' ? '↑' : '↓' }}</span> @endif
                                                </button>
                                            </th>
                                            <th class="text-left px-3 py-2 font-medium">
                                                <button wire:click="sortSubmissions('processed_at')" class="flex items-center gap-1 hover:text-gray-600 dark:hover:text-gray-300">
                                                    Processed @if ($submissionSort === 'processed_at') <span>{{ $submissionSortDir === 'asc' ? '↑' : '↓' }}</span> @endif
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                        @forelse ($this->moduleSubmissions as $row)
                                            @php $hasSubmission = ! is_null($row->submission_status); @endphp
                                            <tr class="{{ $hasSubmission ? '' : 'opacity-50' }} hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors">
                                                <td class="px-3 py-2">
                                                    <div class="flex items-center gap-1.5">
                                                        <div class="w-5 h-5 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 flex items-center justify-center text-xs font-medium shrink-0">
                                                            {{ strtoupper(substr($row->name, 0, 1)) }}
                                                        </div>
                                                        <span class="{{ $hasSubmission ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400 dark:text-gray-500' }} font-medium truncate max-w-24">{{ $row->name }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-3 py-2 {{ $hasSubmission ? 'text-gray-700 dark:text-gray-300' : 'text-gray-300 dark:text-gray-600' }} truncate max-w-28">
                                                    {{ $row->file_name ?? '—' }}
                                                </td>
                                                <td class="px-3 py-2 {{ $hasSubmission ? 'text-gray-700 dark:text-gray-300' : 'text-gray-300 dark:text-gray-600' }}">
                                                    {{ $row->file_size ? $this->formatFileSize($row->file_size) : '—' }}
                                                </td>
                                                <td class="px-3 py-2 {{ $hasSubmission ? 'text-gray-700 dark:text-gray-300' : 'text-gray-300 dark:text-gray-600' }}">
                                                    {{ $row->mime_type ? $this->formatMimeType($row->mime_type) : '—' }}
                                                </td>
                                                <td class="px-3 py-2">
                                                    @if (! $hasSubmission)
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-sm text-xs font-medium bg-gray-100 text-gray-400 dark:bg-gray-700 dark:text-gray-500">
                                                            NOT SUBMITTED
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-sm text-xs font-medium
                                                            {{ $row->submission_status === 'ONTIME'     ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                                            {{ $row->submission_status === 'LATE'       ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                                            {{ $row->submission_status === 'PENDING'    ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                                            {{ $row->submission_status === 'PROCESSING' ? 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400' : '' }}
                                                            {{ $row->submission_status === 'ERROR'      ? 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                                        ">
                                                            {{ $row->submission_status }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2 {{ $hasSubmission ? 'text-gray-600 dark:text-gray-400' : 'text-gray-300 dark:text-gray-600' }} whitespace-nowrap">
                                                    {{ $row->submitted_at ? \Carbon\Carbon::parse($row->submitted_at)->format('d/m/Y - H:i:s') : '—' }}
                                                </td>
                                                <td class="px-3 py-2 {{ $hasSubmission ? 'text-gray-600 dark:text-gray-400' : 'text-gray-300 dark:text-gray-600' }} whitespace-nowrap">
                                                    {{ $row->processed_at ? \Carbon\Carbon::parse($row->processed_at)->format('d/m/Y - H:i:s') : '—' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="px-3 py-8 text-center text-xs text-gray-400">No enrolled students found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="px-3 py-2 border-t border-gray-100 dark:border-gray-700">
                                {{ $this->moduleSubmissions->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>
    {{-- ── CLASS CREATION MODAL ── --}}
    @if ($showClassModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg mx-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Add class session</h3>
                </div>

                <div class="p-5 flex flex-col gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Session title</label>
                        <input wire:model="classTitle" type="text" placeholder="e.g. Week 1 — Introduction" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('classTitle') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Starts at</label>
                            <input wire:model="classStartsAt" type="datetime-local" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('classStartsAt') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Ends at</label>
                            <input wire:model="classEndsAt" type="datetime-local" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('classEndsAt') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Type</label>
                        <select wire:model="classType" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="PHYSICAL">Physical</option>
                            <option value="VIRTUAL">Virtual</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Location</label>
                        <input wire:model="classLocation" type="text" placeholder="Room 101 or https://zoom.us/j/..." class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('classLocation') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-2 px-5 py-4 border-t border-gray-200 dark:border-gray-700">
                    <button
                        wire:click="closeClassModal"
                        wire:confirm="Discard this class? All changes will be lost."
                        class="px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 transition-colors"
                    >
                        Discard
                    </button>
                    <button
                        wire:click="createClass"
                        class="px-3 py-1.5 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                    >
                        Create class
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── STUDENT ENROLMENT MODAL ── --}}
    @if ($showStudentModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl mx-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Enrol students</h3>
                </div>

                <div class="p-5">
                    <div class="relative mb-3">
                        <input
                            wire:model.live.debounce.300ms="modalSearch"
                            type="text"
                            placeholder="Search by name or ID…"
                            class="w-full pl-8 pr-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        <svg class="absolute left-2.5 top-2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
                    </div>

                    <table class="w-full text-sm table-fixed">
                        <thead>
                            <tr class="text-xs text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700">
                                <th class="w-8 py-2 text-left"></th>
                                <th class="py-2 text-left font-medium w-1/2">Name</th>
                                <th class="py-2 text-left font-medium">ID</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            @forelse ($this->availableStudents as $student)
                                <tr
                                    wire:click="toggleStudent({{ $student->id }})"
                                    class="cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors {{ in_array($student->id, $selectedStudentIds) ? 'bg-blue-50 dark:bg-blue-900/10' : '' }}"
                                >
                                    <td class="py-2.5">
                                        <div class="w-4 h-4 rounded border flex items-center justify-center
                                            {{ in_array($student->id, $selectedStudentIds) ? 'bg-blue-600 border-blue-600' : 'border-gray-300 dark:border-gray-500' }}">
                                            @if (in_array($student->id, $selectedStudentIds))
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-2.5">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 flex items-center justify-center text-xs font-medium shrink-0">
                                                {{ strtoupper(substr($student->name, 0, 1)) }}
                                            </div>
                                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $student->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-2.5 text-xs font-mono text-gray-500 dark:text-gray-400">
                                        {{ $student->institutional_id }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-6 text-center text-xs text-gray-400">No available students found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-2">{{ $this->availableStudents->links() }}</div>

                    {{-- Selection summary --}}
                    @if (count($selectedStudentIds) > 0)
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <p class="text-xs text-gray-500 mb-2">
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ count($selectedStudentIds) }}</span>
                                {{ count($selectedStudentIds) === 1 ? 'student' : 'students' }} selected
                            </p>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($this->selectedStudents as $s)
                                    <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 text-xs px-2 py-0.5 rounded-full">
                                        {{ $s->name }}
                                        <button type="button" wire:click="toggleStudent({{ $s->id }})" class="hover:text-blue-900 leading-none">×</button>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-2 px-5 py-4 border-t border-gray-200 dark:border-gray-700">
                    <button
                        wire:click="closeStudentModal"
                        wire:confirm="Discard selection? No students will be enrolled."
                        class="px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 transition-colors"
                    >
                        Discard
                    </button>
                    <button
                        wire:click="enrollStudents"
                        @disabled(empty($selectedStudentIds))
                        class="px-3 py-1.5 text-xs bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg transition-colors"
                    >
                        Enrol {{ count($selectedStudentIds) > 0 ? count($selectedStudentIds) : '' }} {{ count($selectedStudentIds) === 1 ? 'student' : 'students' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── LECTURER ASSIGNMENT MODAL ── --}}
    @if ($showLecturerModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl mx-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Assign lecturers</h3>
                </div>

                <div class="p-5">
                    <div class="relative mb-3">
                        <input
                            wire:model.live.debounce.300ms="modalSearch"
                            type="text"
                            placeholder="Search by name or ID…"
                            class="w-full pl-8 pr-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        <svg class="absolute left-2.5 top-2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
                    </div>

                    <table class="w-full text-sm table-fixed">
                        <thead>
                            <tr class="text-xs text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700">
                                <th class="w-8 py-2 text-left"></th>
                                <th class="py-2 text-left font-medium w-1/2">Name</th>
                                <th class="py-2 text-left font-medium">ID</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            @forelse ($this->availableLecturers as $lecturer)
                                <tr
                                    wire:click="toggleModalLecturer({{ $lecturer->id }})"
                                    class="cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors {{ in_array($lecturer->id, $selectedLecturerIds) ? 'bg-blue-50 dark:bg-blue-900/10' : '' }}"
                                >
                                    <td class="py-2.5">
                                        <div class="w-4 h-4 rounded border flex items-center justify-center
                                            {{ in_array($lecturer->id, $selectedLecturerIds) ? 'bg-blue-600 border-blue-600' : 'border-gray-300 dark:border-gray-500' }}">
                                            @if (in_array($lecturer->id, $selectedLecturerIds))
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-2.5">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 flex items-center justify-center text-xs font-medium shrink-0">
                                                {{ strtoupper(substr($lecturer->name, 0, 1)) }}
                                            </div>
                                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $lecturer->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-2.5 text-xs font-mono text-gray-500 dark:text-gray-400">
                                        {{ $lecturer->institutional_id }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-6 text-center text-xs text-gray-400">No available lecturers found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-2">{{ $this->availableLecturers->links() }}</div>

                    @if (count($selectedLecturerIds) > 0)
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <p class="text-xs text-gray-500 mb-2">
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ count($selectedLecturerIds) }}</span>
                                {{ count($selectedLecturerIds) === 1 ? 'lecturer' : 'lecturers' }} selected
                            </p>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($this->selectedLecturersForModal as $l)
                                    <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 text-xs px-2 py-0.5 rounded-full">
                                        {{ $l->name }}
                                        <button type="button" wire:click="toggleModalLecturer({{ $l->id }})" class="hover:text-blue-900 leading-none">×</button>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-2 px-5 py-4 border-t border-gray-200 dark:border-gray-700">
                    <button
                        wire:click="closeLecturerModal"
                        wire:confirm="Discard selection? No lecturers will be assigned."
                        class="px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 transition-colors"
                    >
                        Discard
                    </button>
                    <button
                        wire:click="assignLecturers"
                        @disabled(empty($selectedLecturerIds))
                        class="px-3 py-1.5 text-xs bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg transition-colors"
                    >
                        Assign {{ count($selectedLecturerIds) > 0 ? count($selectedLecturerIds) : '' }} {{ count($selectedLecturerIds) === 1 ? 'lecturer' : 'lecturers' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── RESOURCE CREATION MODAL ── --}}
    @if ($showResourceModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm mx-4 border border-gray-200 dark:border-gray-700">

                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        @if ($resourceStep === 'choose') Add resource
                        @elseif ($resourceStep === 'folder') New folder
                        @else Upload file
                        @endif
                    </h3>
                </div>

                <div class="p-5">

                    {{-- Step 1: Choose type --}}
                    @if ($resourceStep === 'choose')
                        <div class="grid grid-cols-2 gap-3">
                            <button
                                type="button"
                                wire:click="chooseResourceType('folder')"
                                class="flex flex-col items-center gap-2 p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                            >
                                <svg class="w-10 h-10 text-yellow-500" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M10 4H2v16h20V6H12l-2-2z"/></svg>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">New folder</span>
                            </button>
                            <button
                                type="button"
                                wire:click="chooseResourceType('file')"
                                class="flex flex-col items-center gap-2 p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                            >
                                <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Upload file</span>
                            </button>
                        </div>
                    @endif

                    {{-- Step 2a: Folder name --}}
                    @if ($resourceStep === 'folder')
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Folder name</label>
                            <input
                                wire:model="folderName"
                                type="text"
                                placeholder="e.g. Lecture Slides"
                                autofocus
                                class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                            @error('folderName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    {{-- Step 2b: File upload --}}
                    @if ($resourceStep === 'file')
                        <div class="flex flex-col gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Upload file</label>
                                <input
                                    wire:model="uploadedFile"
                                    type="file"
                                    accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.mp4,.mp3,.zip"
                                    class="w-full text-xs text-gray-600 dark:text-gray-400 file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:bg-gray-100 dark:file:bg-gray-700 file:text-gray-700 dark:file:text-gray-300 hover:file:bg-gray-200"
                                >
                                @error('uploadedFile') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                <p class="text-xs text-gray-400 mt-1">PDF, Word, PowerPoint, TXT, MP4, MP3, ZIP · Max 100MB</p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Display name</label>
                                <input
                                    wire:model="fileName"
                                    type="text"
                                    placeholder="File display name"
                                    class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                @error('fileName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Upload progress --}}
                            <div wire:loading wire:target="uploadedFile" class="text-xs text-blue-500">
                                Uploading…
                            </div>
                        </div>
                    @endif

                </div>

                <div class="flex justify-end gap-2 px-5 py-4 border-t border-gray-200 dark:border-gray-700">
                    <button
                        wire:click="closeResourceModal"
                        wire:confirm="Discard? Changes will be lost."
                        class="px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 transition-colors"
                    >
                        @if ($resourceStep === 'choose') Cancel @else Discard @endif
                    </button>

                    @if ($resourceStep === 'folder')
                        <button
                            wire:click="createFolder"
                            class="px-3 py-1.5 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                        >
                            Create folder
                        </button>
                    @elseif ($resourceStep === 'file')
                        <button
                            wire:click="uploadFile"
                            class="px-3 py-1.5 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                            wire:loading.attr="disabled"
                            wire:target="uploadedFile,uploadFile"
                        >
                            <span wire:loading.remove wire:target="uploadFile">Upload file</span>
                            <span wire:loading wire:target="uploadFile">Uploading…</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
