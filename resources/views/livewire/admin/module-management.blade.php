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
                        <th wire:click="sortColumn('code')" class="text-left px-4 py-3 cursor-pointer select-none">Code</th>
                        <th wire:click="sortColumn('name')" class="text-left px-4 py-3 cursor-pointer select-none">Name</th>
                        <th class="text-left px-4 py-3">Lecturer</th>
                        <th class="text-left px-4 py-3">Credits</th>
                        <th class="text-left px-4 py-3">Semester</th>
                        <th class="text-left px-4 py-3">Status</th>
                        <th wire:click="sortColumn('created_at')" class="text-left px-4 py-3 cursor-pointer select-none">Created</th>
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
    <div class="bg-white dark:bg-gray-800 min-h-48 p-4">

        {{-- Empty state --}}
        @if (! $this->selectedModule && $mode === 'view')
            <div class="flex flex-col items-center justify-center h-40 text-gray-400 dark:text-gray-500 text-sm gap-2">
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

            {{-- Tabs --}}
            <div class="flex gap-1 border-b border-gray-200 dark:border-gray-700 mb-4">
                @foreach ([
                    ['key' => 'classes',  'label' => 'Classes ('  . $module->classSessions->count() . ')'],
                    ['key' => 'students', 'label' => 'Students (' . $module->enrolledStudents->count() . ')'],
                    ['key' => 'lecturer', 'label' => 'Lecturers (' . $module->editors->count() . ')'],
                ] as $tab)
                    <button
                        wire:click="setTab('{{ $tab['key'] }}')"
                        class="px-4 py-2 text-xs font-medium border-b-2 transition-colors {{ $activeTab === $tab['key'] ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}"
                    >
                        {{ $tab['label'] }}
                    </button>
                @endforeach
            </div>

            {{-- Tab content --}}
            @if ($activeTab === 'classes')
                <div class="flex flex-col gap-2">
                    @forelse ($this->moduleClasses as $session)
                        @php
                            $location = $this->resolveLocation($session->location);
                            $isOpen   = $expandedClassId === $session->id;
                        @endphp

                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">

                            {{-- Accordion header --}}
                            <button
                                type="button"
                                wire:click="toggleClass({{ $session->id }})"
                                class="w-full flex items-center justify-between px-4 py-3 text-left bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="text-xs px-1.5 py-0.5 text-center font-medium shrink-0 w-15 rounded-sm
                                        {{ $session->type === 'PHYSICAL' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' }}">
                                        {{ ucfirst(strtolower($session->type)) }}
                                    </span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $session->title }}</span>
                                </div>

                                <div class="flex items-center gap-4 shrink-0 ml-4">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $session->starts_at->format('d M Y, H:i') }} — {{ $session->ends_at->format('H:i') }}
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        @if ($location['type'] === 'link')
                                            @ <a href="{{ $location['value'] }}" target="_blank" class="text-blue-500 hover:underline" wire:click.stop>{{ $location['label'] }}</a>
                                        @else
                                            @ {{ $location['value'] }}
                                        @endif
                                    </span>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform {{ $isOpen ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </button>

                            {{-- Accordion body --}}
                            @if ($isOpen)
                                <div class="grid grid-cols-2 gap-0 border-t border-gray-200 dark:border-gray-700">

                                    {{-- Left: class details --}}
                                    <div class="p-4 border-r border-gray-200 dark:border-gray-700">
                                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-3">Session details</p>
                                        <div class="flex flex-col gap-2 text-xs">
                                            <div class="flex gap-2">
                                                <span class="text-gray-400 w-16 shrink-0">Type</span>
                                                <span class="text-gray-900 dark:text-gray-100">{{ ucfirst(strtolower($session->type)) }}</span>
                                            </div>
                                            <div class="flex gap-2">
                                                <span class="text-gray-400 w-16 shrink-0">Location</span>
                                                @if ($location['type'] === 'link')
                                                    <a href="{{ $location['value'] }}" target="_blank" class="text-blue-500 hover:underline">{{ $location['label'] }}</a>
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
                                    </div>

                                    {{-- Right: resource folder contents --}}
                                    <div class="p-4">
                                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-3">Resources</p>
                                        @if ($session->resourceFolder)
                                            @php
                                                $items = $session->resourceFolder->children
                                                    ->merge($session->resourceFolder->resources);
                                            @endphp
                                            @if ($items->isEmpty())
                                                <p class="text-xs text-gray-400">No resources in this folder.</p>
                                            @else
                                                <div class="grid grid-cols-3 gap-2">
                                                    @foreach ($session->resourceFolder->children as $folder)
                                                        <div class="flex flex-col items-center gap-1 p-2 rounded-lg bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer text-center">
                                                            <svg class="w-8 h-8 text-yellow-500" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M10 4H2v16h20V6H12l-2-2z"/></svg>
                                                            <span class="text-xs text-gray-700 dark:text-gray-300 truncate w-full text-center leading-tight">{{ $folder->name }}</span>
                                                        </div>
                                                    @endforeach
                                                    @foreach ($session->resourceFolder->resources as $resource)
                                                        <div class="flex flex-col items-center gap-1 p-2 rounded-lg bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer text-center">
                                                            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                            <span class="text-xs text-gray-700 dark:text-gray-300 truncate w-full text-center leading-tight">{{ $resource->title }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @else
                                            <p class="text-xs text-gray-400">No resource folder linked to this session.</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 py-4 text-center">No classes scheduled.</p>
                    @endforelse

                    <div class="mt-2">{{ $this->moduleClasses->links() }}</div>
                </div>
            @endif

            @if ($activeTab === 'students')
                @if ($this->moduleStudents->isEmpty())
                    <p class="text-xs text-gray-400 py-4 text-center">No students enrolled.</p>
                @else
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs text-gray-400 dark:text-gray-500">
                                <th class="text-left py-2 pr-4 font-medium">
                                    <button wire:click="sortStudents('name')" class="flex items-center gap-1 hover:text-gray-600 dark:hover:text-gray-300">
                                        Name
                                        @if ($studentSort === 'name')
                                            <span>{{ $studentSortDir === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </button>
                                </th>
                                <th class="text-left py-2 pr-4 font-medium">
                                    <button wire:click="sortStudents('institutional_id')" class="flex items-center gap-1 hover:text-gray-600 dark:hover:text-gray-300">
                                        ID
                                        @if ($studentSort === 'institutional_id')
                                            <span>{{ $studentSortDir === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </button>
                                </th>
                                <th class="text-left py-2 font-medium">
                                    <button wire:click="sortStudents('status')" class="flex items-center gap-1 hover:text-gray-600 dark:hover:text-gray-300">
                                        Status
                                        @if ($studentSort === 'status')
                                            <span>{{ $studentSortDir === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            @foreach ($this->moduleStudents as $student)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-2">{{ $this->moduleStudents->links() }}</div>
                @endif
            @endif

            @if ($activeTab === 'lecturer')
                <div class="grid grid-cols-2 gap-3">
                    @forelse ($this->moduleLecturers as $lecturer)
                        <div class="flex items-center gap-3 px-3 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 flex items-center justify-center font-medium text-sm shrink-0">
                                {{ strtoupper(substr($lecturer->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $lecturer->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $lecturer->email }}</div>
                                <div class="text-xs text-gray-400 font-mono">{{ $lecturer->institutional_id }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 py-4 col-span-2 text-center">No lecturers assigned.</p>
                    @endforelse
                </div>
                @if ($this->moduleLecturers->hasPages())
                    <div class="mt-2">{{ $this->moduleLecturers->links() }}</div>
                @endif
            @endif
        @endif
    </div>
</div>
