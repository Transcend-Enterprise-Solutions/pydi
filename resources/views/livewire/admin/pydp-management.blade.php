<div x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" 
     x-init="$watch('darkMode', val => {
         localStorage.setItem('darkMode', val);
         if (val) {
             document.documentElement.classList.add('dark');
         } else {
             document.documentElement.classList.remove('dark');
         }
     });
     if (darkMode) {
         document.documentElement.classList.add('dark');
     }" 
     class="min-h-screen bg-gray-50 dark:bg-gray-800 transition-colors rounded-xl overflow-hidden">
    
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 dark:from-blue-900 dark:to-blue-950 text-white py-12 shadow-lg">
        <div class="mx-auto px-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl md:text-5xl font-bold mb-2">
                        {{ $pageIcon ?? '📋' }} {{ $pageTitle ?? 'PYDP Management' }}
                    </h1>
                    <p class="text-blue-100 text-lg">
                        {{ $pageDescription ?? 'Review submissions and edit requests' }}
                    </p>
                </div>
                <button @click="darkMode = !darkMode" 
                        class="p-3 rounded-lg bg-blue-700 dark:bg-blue-800 hover:bg-blue-800 dark:hover:bg-blue-700 transition-colors"
                        title="Toggle dark mode">
                    <svg x-show="!darkMode" class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                    <svg x-show="darkMode" class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Statistics Cards - Single Row, No Counts -->
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md hover:shadow-lg transition-shadow p-4 border-l-4 border-yellow-500 cursor-pointer" wire:click="$set('filterStatus', 'submitted')">
                <div class="flex items-center justify-between">
                    <p class="text-gray-700 dark:text-gray-300 text-sm font-semibold">Submitted</p>
                    <div class="bg-yellow-100 dark:bg-yellow-900 rounded-full p-2">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md hover:shadow-lg transition-shadow p-4 border-l-4 border-green-500 cursor-pointer" wire:click="$set('filterStatus', 'approved')">
                <div class="flex items-center justify-between">
                    <p class="text-gray-700 dark:text-gray-300 text-sm font-semibold">Approved</p>
                    <div class="bg-green-100 dark:bg-green-900 rounded-full p-2">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md hover:shadow-lg transition-shadow p-4 border-l-4 border-red-500 cursor-pointer" wire:click="$set('filterStatus', 'rejected')">
                <div class="flex items-center justify-between">
                    <p class="text-gray-700 dark:text-gray-300 text-sm font-semibold">Rejected</p>
                    <div class="bg-red-100 dark:bg-red-900 rounded-full p-2">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l-2-2m0 0l-2-2m2 2l2-2m-2 2l-2 2m2-2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md hover:shadow-lg transition-shadow p-4 border-l-4 border-orange-500 cursor-pointer" wire:click="$set('filterStatus', 'edit-requests')">
                <div class="flex items-center justify-between">
                    <p class="text-gray-700 dark:text-gray-300 text-sm font-semibold">Edit Requests</p>
                    <div class="bg-orange-100 dark:bg-orange-900 rounded-full p-2">
                        <svg class="w-5 h-5 text-orange-600 dark:text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compact Filters & Actions - Two Rows -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-3 mb-6">
            <!-- First Row: Filters -->
            <div class="flex gap-2 items-center flex-wrap md:flex-nowrap mb-3">
                <select wire:model.live="filterStatus" 
                        class="flex-1 min-w-[120px] px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="submitted">Submitted</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="edit-requests">Edit Requests</option>
                </select>

                <select wire:model.live="filterAgency" 
                        class="flex-1 min-w-[120px] px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Agencies</option>
                    @forelse($agencies as $agency)
                        <option value="{{ $agency }}">{{ $agency }}</option>
                    @empty
                        <option disabled>No agencies</option>
                    @endforelse
                </select>

                <select wire:model.live="filterUser" 
                        class="flex-1 min-w-[120px] px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Users</option>
                    @forelse($users as $user)
                        <option value="{{ $user->id }}">{{ $user->userData?->first_name }} {{ $user->userData?->last_name }}</option>
                    @empty
                        <option disabled>No users</option>
                    @endforelse
                </select>

                <input type="text" wire:model.live="searchLevel" 
                    placeholder="Search level..." 
                    class="flex-1 min-w-[120px] px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">

                <button wire:click="resetFilters" 
                        class="px-4 py-2 text-sm bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg font-medium transition-colors whitespace-nowrap">
                    🔄 Reset
                </button>
            </div>

            <!-- Second Row: Actions -->
            <div class="flex gap-2 items-center flex-wrap">
                <button wire:click="toggleCheckAll" 
                        class="px-4 py-2 text-sm bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 text-white rounded-lg font-medium transition-colors whitespace-nowrap flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Select All
                </button>

                <button wire:click="generateReport" 
                        class="px-4 py-2 text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors flex items-center gap-1 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Generate Report
                </button>
            </div>

            @if(!empty($selectedLevels))
                <div class="mt-2 p-2 bg-blue-50 dark:bg-blue-900/30 rounded border border-blue-200 dark:border-blue-800">
                    <p class="text-xs text-blue-700 dark:text-blue-300">
                        <strong>{{ count($selectedLevels) }}</strong> {{ $isEditRequestView ? 'indicator(s)' : 'level(s)' }} selected
                    </p>
                </div>
            @endif
        </div>

        <!-- Content Area -->
        @if($isEditRequestView)
            {{-- EDIT REQUESTS VIEW --}}
            @forelse($items as $indicator)
                @php
                    $level = $indicator->level;
                    $editRequestEntry = $indicator->entries->first();
                    $isExpanded = in_array($indicator->id, $expandedIndicators ?? []);
                @endphp

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-4 overflow-hidden border-l-4 border-orange-500">
                    <div class="px-6 py-4">
                        <div class="flex items-start">
                            <!-- Checkbox -->
                            <div class="mr-3 mt-1">
                                <input type="checkbox" 
                                       wire:model.live="selectedLevels" 
                                       value="{{ $indicator->id }}"
                                       class="w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-orange-600 focus:ring-orange-500">
                            </div>

                            <!-- Info (clickable to expand) -->
                            <button type="button" 
                                    wire:click="toggleIndicator({{ $indicator->id }})"
                                    class="flex items-start flex-1 text-left hover:opacity-80 transition-opacity">
                                <div class="mr-3 mt-1">
                                    @if($isExpanded)
                                        <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    @endif
                                </div>

                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-1 flex-wrap">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $indicator->title }}</h3>
                                        <span class="px-2 py-1 bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 text-xs font-semibold rounded-full">
                                            Edit Requested
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                                        <span class="font-semibold">Level:</span> {{ $level->title }} |
                                        <span class="font-semibold">User:</span> {{ $level->user->userData?->first_name }} {{ $level->user->userData?->last_name }} |
                                        <span class="font-semibold">Agency:</span> {{ $level->user->userData?->government_agency ?? 'N/A' }}
                                    </p>
                                    @if($editRequestEntry && $editRequestEntry->edit_request_reason)
                                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                                            <span class="font-semibold">Reason:</span> {{ $editRequestEntry->edit_request_reason }}
                                        </p>
                                    @endif
                                    @if($editRequestEntry && $editRequestEntry->edit_requested_at)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <span class="font-semibold">Requested:</span> {{ $editRequestEntry->edit_requested_at->format('M d, Y H:i') }}
                                        </p>
                                    @endif
                                </div>
                            </button>

                            <!-- Action Buttons -->
                            <div class="flex gap-2 ml-4 flex-shrink-0">
                                <button type="button"
                                        wire:click="openApproveEditModal({{ $indicator->id }})" 
                                        class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors flex items-center gap-1 text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Approve
                                </button>
                                <button type="button"
                                        wire:click="openRejectEditModal({{ $indicator->id }})" 
                                        class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors flex items-center gap-1 text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Reject
                                </button>
                            </div>
                        </div>
                    </div>

                    @if($isExpanded)
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                            @if($indicator->content)
                                <div class="mb-4 p-3 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700">
                                    <h5 class="font-semibold text-sm text-gray-900 dark:text-white mb-2">Indicator Description</h5>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $indicator->content }}</p>
                                </div>
                            @endif

                            <div class="overflow-x-auto">
                                <table class="w-full text-xs bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                    <thead>
                                        <tr class="bg-orange-50 dark:bg-orange-900/30 border-b border-gray-200 dark:border-gray-700">
                                            <th class="px-3 py-2 text-left font-semibold text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-700">Year</th>
                                            <th class="px-3 py-2 text-center font-semibold text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-700">Baseline</th>
                                            <th colspan="3" class="px-3 py-2 text-center font-semibold text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-700">Phys. Target</th>
                                            <th colspan="3" class="px-3 py-2 text-center font-semibold text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-700">Phys. Actual</th>
                                            <th colspan="2" class="px-3 py-2 text-center font-semibold text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-700">Financial</th>
                                            <th class="px-3 py-2 text-left font-semibold text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-700">Remarks</th>
                                            <th class="px-3 py-2 text-center font-semibold text-gray-900 dark:text-white">Status</th>
                                        </tr>
                                        <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600 text-xs text-gray-600 dark:text-gray-400">
                                            <th class="px-3 py-1 border-r border-gray-200 dark:border-gray-700"></th>
                                            <th class="px-3 py-1 border-r border-gray-200 dark:border-gray-700"></th>
                                            <th class="px-3 py-1 text-center border-r border-gray-200 dark:border-gray-700">M</th>
                                            <th class="px-3 py-1 text-center border-r border-gray-200 dark:border-gray-700">F</th>
                                            <th class="px-3 py-1 text-center border-r border-gray-200 dark:border-gray-700">T</th>
                                            <th class="px-3 py-1 text-center border-r border-gray-200 dark:border-gray-700">M</th>
                                            <th class="px-3 py-1 text-center border-r border-gray-200 dark:border-gray-700">F</th>
                                            <th class="px-3 py-1 text-center border-r border-gray-200 dark:border-gray-700">T</th>
                                            <th class="px-3 py-1 text-center border-r border-gray-200 dark:border-gray-700">Allot</th>
                                            <th class="px-3 py-1 text-center border-r border-gray-200 dark:border-gray-700">Spent</th>
                                            <th class="px-3 py-1 border-r border-gray-200 dark:border-gray-700"></th>
                                            <th class="px-3 py-1"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($indicator->entries as $entry)
                                            <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-orange-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <td class="px-3 py-2 font-medium text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-700">{{ $entry->year }}</td>
                                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300 text-center border-r border-gray-200 dark:border-gray-700">{{ $entry->baseline ?? '-' }}</td>
                                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300 text-center border-r border-gray-200 dark:border-gray-700">{{ $entry->physical_target_male ?? '-' }}</td>
                                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300 text-center border-r border-gray-200 dark:border-gray-700">{{ $entry->physical_target_female ?? '-' }}</td>
                                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300 text-center font-medium border-r border-gray-200 dark:border-gray-700">{{ $entry->physical_target_total ?? '-' }}</td>
                                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300 text-center border-r border-gray-200 dark:border-gray-700">{{ $entry->physical_actual_male ?? '-' }}</td>
                                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300 text-center border-r border-gray-200 dark:border-gray-700">{{ $entry->physical_actual_female ?? '-' }}</td>
                                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300 text-center font-medium border-r border-gray-200 dark:border-gray-700">{{ $entry->physical_actual_total ?? '-' }}</td>
                                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300 text-center border-r border-gray-200 dark:border-gray-700">{{ $entry->financial_allotted ?? '-' }}</td>
                                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300 text-center border-r border-gray-200 dark:border-gray-700">{{ $entry->financial_spent ?? '-' }}</td>
                                                <td class="px-3 py-2 text-gray-600 dark:text-gray-400 border-r border-gray-200 dark:border-gray-700">{{ Str::limit($entry->remarks ?? '-', 20) }}</td>
                                                <td class="px-3 py-2 text-center">
                                                    @if($entry->submission_status === 'submitted')
                                                        <span class="inline-block px-2 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 text-xs font-medium rounded-full">Submitted</span>
                                                    @elseif($entry->submission_status === 'approved')
                                                        <span class="inline-block px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs font-medium rounded-full">Approved</span>
                                                    @elseif($entry->submission_status === 'rejected')
                                                        <span class="inline-block px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-xs font-medium rounded-full">Rejected</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="12" class="px-3 py-4 text-center text-gray-500 dark:text-gray-400 text-sm">No entries found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-12 text-center">
                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 text-lg">No edit requests found</p>
                </div>
            @endforelse

        @else
            {{-- SUBMISSIONS VIEW --}}
            @forelse($items as $level)
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow mb-4 overflow-hidden">
                    <div class="px-6 py-4 flex items-center bg-gradient-to-r from-blue-50 dark:from-blue-900/30 to-white dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <!-- Checkbox -->
                        <div class="mr-3">
                            <input type="checkbox" 
                                   wire:model.live="selectedLevels" 
                                   value="{{ $level->id }}"
                                   class="w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                        </div>

                        <!-- Level Header (clickable to expand) -->
                        <button type="button" 
                                wire:click="toggleLevel({{ $level->id }})"
                                class="flex items-center flex-1 text-left hover:opacity-80 transition-opacity">
                            <div class="mr-4">
                                @if(in_array($level->id, $expandedLevels ?? []))
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                @endif
                            </div>

                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $level->title }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $level->user->userData?->first_name }} {{ $level->user->userData?->last_name }}</span>
                                    | {{ $level->user->userData?->government_agency ?? 'N/A' }}
                                    | Indicators: <span class="font-semibold">{{ $level->indicators->count() }}</span>
                                </p>
                                @if($level->content)
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ Str::limit($level->content, 100) }}</p>
                                @endif
                            </div>
                        </button>
                    </div>

                    @if(in_array($level->id, $expandedLevels ?? []))
                        <div class="p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                            @forelse($level->indicators as $indicator)
                                <div class="mb-6 bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <button type="button"
                                            wire:click="toggleIndicator({{ $indicator->id }})"
                                            class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <div class="flex items-center flex-1 text-left">
                                            <div class="mr-3">
                                                @if(in_array($indicator->id, $expandedIndicators ?? []))
                                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-900 dark:text-white">{{ $indicator->title }}</h4>
                                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                                    Submissions: {{ $indicator->entries->count() }}
                                                </p>
                                            </div>
                                        </div>
                                    </button>

                                    @if(in_array($indicator->id, $expandedIndicators ?? []))
                                        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                            @if($indicator->content || $indicator->data_sources || $indicator->frequency)
                                                <div class="mb-4 p-3 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700">
                                                    <h5 class="font-semibold text-sm text-gray-900 dark:text-white mb-2">Details</h5>
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                                        @if($indicator->content)
                                                            <div>
                                                                <p class="font-medium text-gray-700 dark:text-gray-300">Description</p>
                                                                <p class="text-gray-600 dark:text-gray-400">{{ Str::limit($indicator->content, 100) }}</p>
                                                            </div>
                                                        @endif
                                                        @if($indicator->data_sources)
                                                            <div>
                                                                <p class="font-medium text-gray-700 dark:text-gray-300">Data Sources</p>
                                                                <p class="text-gray-600 dark:text-gray-400">{{ $indicator->data_sources }}</p>
                                                            </div>
                                                        @endif
                                                        @if($indicator->frequency)
                                                            <div>
                                                                <p class="font-medium text-gray-700 dark:text-gray-300">Frequency</p>
                                                                <p class="text-gray-600 dark:text-gray-400">{{ $indicator->frequency }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="overflow-x-auto">
                                                <table class="w-full text-xs bg-white dark:bg-gray-800">
                                                    <thead>
                                                        <tr class="bg-blue-50 dark:bg-blue-900 border-b border-gray-200 dark:border-gray-700">
                                                            <th class="px-3 py-2 text-left font-semibold text-gray-900 dark:text-white">Year</th>
                                                            <th class="px-3 py-2 text-center font-semibold text-gray-900 dark:text-white">Baseline</th>
                                                            <th colspan="3" class="px-3 py-2 text-center font-semibold text-gray-900 dark:text-white">Phys. Target</th>
                                                            <th colspan="3" class="px-3 py-2 text-center font-semibold text-gray-900 dark:text-white">Phys. Actual</th>
                                                            <th colspan="2" class="px-3 py-2 text-center font-semibold text-gray-900 dark:text-white">Financial</th>
                                                            <th class="px-3 py-2 text-left font-semibold text-gray-900 dark:text-white">Remarks</th>
                                                            <th class="px-3 py-2 text-center font-semibold text-gray-900 dark:text-white">Status</th>
                                                            <th class="px-3 py-2 text-center font-semibold text-gray-900 dark:text-white">Actions</th>
                                                        </tr>
                                                        <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 text-xs text-gray-600 dark:text-gray-400">
                                                            <th class="px-3 py-1"></th>
                                                            <th class="px-3 py-1"></th>
                                                            <th class="px-3 py-1 text-center">M</th>
                                                            <th class="px-3 py-1 text-center">F</th>
                                                            <th class="px-3 py-1 text-center">T</th>
                                                            <th class="px-3 py-1 text-center">M</th>
                                                            <th class="px-3 py-1 text-center">F</th>
                                                            <th class="px-3 py-1 text-center">T</th>
                                                            <th class="px-3 py-1">Allot</th>
                                                            <th class="px-3 py-1">Spent</th>
                                                            <th class="px-3 py-1"></th>
                                                            <th class="px-3 py-1"></th>
                                                            <th class="px-3 py-1"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($indicator->entries as $entry)
                                                            <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-gray-700/50 transition-colors">
                                                                <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">{{ $entry->year }}</td>
                                                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300 text-center">{{ $entry->baseline ?? '-' }}</td>
                                                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300 text-center">{{ $entry->physical_target_male ?? '-' }}</td>
                                                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300 text-center">{{ $entry->physical_target_female ?? '-' }}</td>
                                                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300 text-center font-medium">{{ $entry->physical_target_total ?? '-' }}</td>
                                                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300 text-center">{{ $entry->physical_actual_male ?? '-' }}</td>
                                                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300 text-center">{{ $entry->physical_actual_female ?? '-' }}</td>
                                                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300 text-center font-medium">{{ $entry->physical_actual_total ?? '-' }}</td>
                                                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300 text-center">{{ $entry->financial_allotted ?? '-' }}</td>
                                                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300 text-center">{{ $entry->financial_spent ?? '-' }}</td>
                                                                <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ Str::limit($entry->remarks ?? '-', 15) }}</td>
                                                                <td class="px-3 py-2 text-center">
                                                                    @if($entry->submission_status === 'submitted')
                                                                        <span class="inline-block px-2 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 text-xs font-medium rounded-full">⏳</span>
                                                                    @elseif($entry->submission_status === 'approved')
                                                                        <span class="inline-block px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs font-medium rounded-full">✓</span>
                                                                    @elseif($entry->submission_status === 'rejected')
                                                                        <span class="inline-block px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-xs font-medium rounded-full">✗</span>
                                                                    @endif
                                                                </td>
                                                                <td class="px-3 py-2 text-center space-x-1">
                                                                    @if($entry->submission_status === 'submitted')
                                                                        <button type="button" wire:click="openApproveModal({{ $entry->id }})" 
                                                                                class="inline-block px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-medium transition-colors">
                                                                            ✓
                                                                        </button>
                                                                        <button type="button" wire:click="openRejectModal({{ $entry->id }})" 
                                                                                class="inline-block px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-medium transition-colors">
                                                                            ✗
                                                                        </button>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="13" class="px-3 py-2 text-center text-gray-500 dark:text-gray-400 text-xs">
                                                                    No submissions for this year range
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400">No indicators in this level</p>
                            @endforelse
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-12 text-center">
                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 text-lg">No levels with {{ $filterStatus }} submissions found</p>
                </div>
            @endforelse
        @endif

        <!-- Pagination -->
        <div class="mt-8">
            {{ $items->links() }}
        </div>

        <!-- MODALS (same as before, just removing report modal) -->

        <!-- Approve Submission Modal -->
        @if($showApproveModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
                    <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 bg-green-50 dark:bg-green-900/30">
                        <h3 class="text-lg font-bold text-green-900 dark:text-green-100">✓ Approve Submission</h3>
                    </div>
                    
                    <div class="p-6">
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Approve this data entry submission?</p>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes (Optional)</label>
                            <textarea wire:model="approvalComments" rows="3"
                                      placeholder="Add any approval notes..."
                                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end gap-3 bg-gray-50 dark:bg-gray-900/30">
                        <button type="button" wire:click="closeModals" 
                                class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="button" wire:click="approveSubmission" 
                                class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                            Approve
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Reject Submission Modal -->
        @if($showRejectModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
                    <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 bg-red-50 dark:bg-red-900/30">
                        <h3 class="text-lg font-bold text-red-900 dark:text-red-100">✗ Reject Submission</h3>
                    </div>
                    
                    <div class="p-6">
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Provide a reason for rejection.</p>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rejection Reason *</label>
                            <textarea wire:model="rejectionReason" rows="4"
                                      placeholder="Explain why this submission is being rejected..."
                                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end gap-3 bg-gray-50 dark:bg-gray-900/30">
                        <button type="button" wire:click="closeModals" 
                                class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="button" wire:click="rejectSubmission" 
                                class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                            Reject
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Approve Edit Request Modal -->
        @if($showApproveEditModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
                    <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 bg-green-50 dark:bg-green-900/30">
                        <h3 class="text-lg font-bold text-green-900 dark:text-green-100">✓ Approve Edit Request</h3>
                    </div>
                    
                    <div class="p-6">
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            This will allow the user to edit all entries for this indicator. The status will be reset to "Draft".
                        </p>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Admin Notes (Optional)</label>
                            <textarea wire:model="approveEditNotes" rows="3"
                                      placeholder="Add any notes for the user..."
                                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end gap-3 bg-gray-50 dark:bg-gray-900/30">
                        <button type="button" wire:click="closeModals" 
                                class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="button" wire:click="approveEditRequest" 
                                class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                            Approve Edit
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Reject Edit Request Modal -->
        @if($showRejectEditModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
                    <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 bg-red-50 dark:bg-red-900/30">
                        <h3 class="text-lg font-bold text-red-900 dark:text-red-100">✗ Reject Edit Request</h3>
                    </div>
                    
                    <div class="p-6">
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            This will reject the edit request. The entries will remain in their current status.
                        </p>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rejection Reason *</label>
                            <textarea wire:model="rejectEditNotes" rows="4"
                                      placeholder="Explain why this edit request is being rejected..."
                                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end gap-3 bg-gray-50 dark:bg-gray-900/30">
                        <button type="button" wire:click="closeModals" 
                                class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="button" wire:click="rejectEditRequest" 
                                class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                            Reject Edit
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>