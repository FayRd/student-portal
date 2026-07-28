<div class="flex flex-col gap-6 w-full">

    {{-- SECTION 1: USERS --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 shadow-xs flex flex-col gap-4">
        
        {{-- Section Header & Overall Bar --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Users</h2>
                    <span class="text-xs text-zinc-500 font-medium">({{ $this->userStats['total'] }} total)</span>
                </div>
            </div>

            {{-- Multi-colored Stacked Bar --}}
            <div class="h-3.5 w-full rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden flex shadow-inner">
                <div class="bg-indigo-500 h-full transition-all duration-500" style="width: {{ $this->userStats['lecturers_pct'] }}%" title="Lecturers: {{ $this->userStats['lecturers'] }} ({{ $this->userStats['lecturers_pct'] }}%)"></div>
                <div class="bg-emerald-500 h-full transition-all duration-500" style="width: {{ $this->userStats['students_pct'] }}%" title="Students: {{ $this->userStats['students'] }} ({{ $this->userStats['students_pct'] }}%)"></div>
                <div class="bg-purple-500 h-full transition-all duration-500" style="width: {{ $this->userStats['admins_pct'] }}%" title="Admins: {{ $this->userStats['admins'] }} ({{ $this->userStats['admins_pct'] }}%)"></div>
            </div>
        </div>

        {{-- Sub-section Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 pt-1">
            
            {{-- Lecturers --}}
            <div class="p-3.5 border border-zinc-100 dark:border-zinc-800 rounded-lg bg-zinc-50/50 dark:bg-zinc-800/40 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 shrink-0"></span>
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Lecturers</span>
                    </div>
                    <span class="text-xs text-zinc-400 font-mono">{{ $this->userStats['lecturers_pct'] }}%</span>
                </div>
                <div class="mt-2 text-xl font-bold text-zinc-900 dark:text-zinc-100">
                    {{ number_format($this->userStats['lecturers']) }}
                </div>
                <div class="h-1.5 w-full bg-zinc-200 dark:bg-zinc-700 rounded-full mt-2 overflow-hidden">
                    <div class="bg-indigo-500 h-full rounded-full" style="width: {{ $this->userStats['lecturers_pct'] }}%"></div>
                </div>
            </div>

            {{-- Students --}}
            <div class="p-3.5 border border-zinc-100 dark:border-zinc-800 rounded-lg bg-zinc-50/50 dark:bg-zinc-800/40 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Students</span>
                    </div>
                    <span class="text-xs text-zinc-400 font-mono">{{ $this->userStats['students_pct'] }}%</span>
                </div>
                <div class="mt-2 text-xl font-bold text-zinc-900 dark:text-zinc-100">
                    {{ number_format($this->userStats['students']) }}
                </div>
                <div class="h-1.5 w-full bg-zinc-200 dark:bg-zinc-700 rounded-full mt-2 overflow-hidden">
                    <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $this->userStats['students_pct'] }}%"></div>
                </div>
            </div>

            {{-- Admins --}}
            <div class="p-3.5 border border-zinc-100 dark:border-zinc-800 rounded-lg bg-zinc-50/50 dark:bg-zinc-800/40 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-500 shrink-0"></span>
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Admins</span>
                    </div>
                    <span class="text-xs text-zinc-400 font-mono">{{ $this->userStats['admins_pct'] }}%</span>
                </div>
                <div class="mt-2 text-xl font-bold text-zinc-900 dark:text-zinc-100">
                    {{ number_format($this->userStats['admins']) }}
                </div>
                <div class="h-1.5 w-full bg-zinc-200 dark:bg-zinc-700 rounded-full mt-2 overflow-hidden">
                    <div class="bg-purple-500 h-full rounded-full" style="width: {{ $this->userStats['admins_pct'] }}%"></div>
                </div>
            </div>

            {{-- Must Reset Password --}}
            <div class="p-3.5 border border-zinc-100 dark:border-zinc-800 rounded-lg bg-amber-50/30 dark:bg-amber-900/10 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-amber-700 dark:text-amber-400">Must Reset Password</span>
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300 text-xs font-bold">!</span>
                </div>
                <div class="mt-2 text-xl font-bold text-amber-800 dark:text-amber-300">
                    {{ number_format($this->userStats['must_reset_password']) }}
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-2">Requires password change</p>
            </div>

            {{-- Unverified Email --}}
            <div class="p-3.5 border border-zinc-100 dark:border-zinc-800 rounded-lg bg-rose-50/30 dark:bg-rose-900/10 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-rose-700 dark:text-rose-400">Unverified Email</span>
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-300 text-xs font-bold">?</span>
                </div>
                <div class="mt-2 text-xl font-bold text-rose-800 dark:text-rose-300">
                    {{ number_format($this->userStats['unverified']) }}
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-2">Pending email verification</p>
            </div>

        </div>

    </div>


    {{-- SECTION 2: MODULES --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 shadow-xs flex flex-col gap-4">
        
        {{-- Section Header & Overall Bar --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Modules</h2>
                    <span class="text-xs text-zinc-500 font-medium">({{ $this->moduleStats['total'] }} total)</span>
                </div>
            </div>

            {{-- Multi-colored Stacked Bar --}}
            <div class="h-3.5 w-full rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden flex shadow-inner">
                <div class="bg-emerald-500 h-full transition-all duration-500" style="width: {{ $this->moduleStats['active_pct'] }}%" title="Active: {{ $this->moduleStats['active'] }} ({{ $this->moduleStats['active_pct'] }}%)"></div>
                <div class="bg-sky-500 h-full transition-all duration-500" style="width: {{ $this->moduleStats['upcoming_pct'] }}%" title="Upcoming: {{ $this->moduleStats['upcoming'] }} ({{ $this->moduleStats['upcoming_pct'] }}%)"></div>
                <div class="bg-slate-400 dark:bg-slate-500 h-full transition-all duration-500" style="width: {{ $this->moduleStats['archived_pct'] }}%" title="Archived: {{ $this->moduleStats['archived'] }} ({{ $this->moduleStats['archived_pct'] }}%)"></div>
            </div>
        </div>

        {{-- Sub-section Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 pt-1">
            
            {{-- Active --}}
            <div class="p-3.5 border border-zinc-100 dark:border-zinc-800 rounded-lg bg-zinc-50/50 dark:bg-zinc-800/40 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Active</span>
                    </div>
                    <span class="text-xs text-zinc-400 font-mono">{{ $this->moduleStats['active_pct'] }}%</span>
                </div>
                <div class="mt-2 text-xl font-bold text-zinc-900 dark:text-zinc-100">
                    {{ number_format($this->moduleStats['active']) }}
                </div>
                <div class="h-1.5 w-full bg-zinc-200 dark:bg-zinc-700 rounded-full mt-2 overflow-hidden">
                    <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $this->moduleStats['active_pct'] }}%"></div>
                </div>
            </div>

            {{-- Upcoming --}}
            <div class="p-3.5 border border-zinc-100 dark:border-zinc-800 rounded-lg bg-zinc-50/50 dark:bg-zinc-800/40 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-sky-500 shrink-0"></span>
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Upcoming</span>
                    </div>
                    <span class="text-xs text-zinc-400 font-mono">{{ $this->moduleStats['upcoming_pct'] }}%</span>
                </div>
                <div class="mt-2 text-xl font-bold text-zinc-900 dark:text-zinc-100">
                    {{ number_format($this->moduleStats['upcoming']) }}
                </div>
                <div class="h-1.5 w-full bg-zinc-200 dark:bg-zinc-700 rounded-full mt-2 overflow-hidden">
                    <div class="bg-sky-500 h-full rounded-full" style="width: {{ $this->moduleStats['upcoming_pct'] }}%"></div>
                </div>
            </div>

            {{-- Archived --}}
            <div class="p-3.5 border border-zinc-100 dark:border-zinc-800 rounded-lg bg-zinc-50/50 dark:bg-zinc-800/40 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-400 shrink-0"></span>
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Archived</span>
                    </div>
                    <span class="text-xs text-zinc-400 font-mono">{{ $this->moduleStats['archived_pct'] }}%</span>
                </div>
                <div class="mt-2 text-xl font-bold text-zinc-900 dark:text-zinc-100">
                    {{ number_format($this->moduleStats['archived']) }}
                </div>
                <div class="h-1.5 w-full bg-zinc-200 dark:bg-zinc-700 rounded-full mt-2 overflow-hidden">
                    <div class="bg-slate-400 dark:bg-slate-500 h-full rounded-full" style="width: {{ $this->moduleStats['archived_pct'] }}%"></div>
                </div>
            </div>

            {{-- Total Enrollments --}}
            <div class="p-3.5 border border-zinc-100 dark:border-zinc-800 rounded-lg bg-zinc-50/50 dark:bg-zinc-800/40 flex flex-col justify-between">
                <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Total Enrollments</span>
                <div class="mt-2 text-xl font-bold text-zinc-900 dark:text-zinc-100">
                    {{ number_format($this->moduleStats['total_enrollments']) }}
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-2">Active student enrollments</p>
            </div>

            {{-- Least Enrolled Module --}}
            <div class="p-3.5 border border-zinc-100 dark:border-zinc-800 rounded-lg bg-zinc-50/50 dark:bg-zinc-800/40 flex flex-col justify-between">
                <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Least Enrolled</span>
                @if ($this->moduleStats['least_enrolled'])
                    <div class="mt-2 min-w-0">
                        <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 truncate" title="{{ $this->moduleStats['least_enrolled']['name'] }}">
                            {{ $this->moduleStats['least_enrolled']['code'] }}
                        </p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">
                            {{ $this->moduleStats['least_enrolled']['name'] }}
                        </p>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 w-fit mt-2">
                        {{ $this->moduleStats['least_enrolled']['count'] }} enrolled
                    </span>
                @else
                    <p class="text-xs text-zinc-400 mt-2">N/A</p>
                @endif
            </div>

            {{-- Most Enrolled Module --}}
            <div class="p-3.5 border border-zinc-100 dark:border-zinc-800 rounded-lg bg-blue-50/40 dark:bg-blue-900/10 flex flex-col justify-between">
                <span class="text-xs font-medium text-blue-700 dark:text-blue-400">Most Enrolled</span>
                @if ($this->moduleStats['most_enrolled'])
                    <div class="mt-2 min-w-0">
                        <p class="text-sm font-semibold text-blue-900 dark:text-blue-200 truncate" title="{{ $this->moduleStats['most_enrolled']['name'] }}">
                            {{ $this->moduleStats['most_enrolled']['code'] }}
                        </p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">
                            {{ $this->moduleStats['most_enrolled']['name'] }}
                        </p>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 w-fit mt-2">
                        {{ $this->moduleStats['most_enrolled']['count'] }} enrolled
                    </span>
                @else
                    <p class="text-xs text-zinc-400 mt-2">N/A</p>
                @endif
            </div>

        </div>

    </div>

</div>
