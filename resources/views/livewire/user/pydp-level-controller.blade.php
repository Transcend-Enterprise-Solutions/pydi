<div class="w-full bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-lg font-bold text-gray-900 dark:text-white">PYDP Datasets</h1>
        <div class="flex gap-2">
            <button wire:click="openReportModal"
                class="bg-purple-600 text-sm dark:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-purple-700">
                <i class="bi bi-bar-chart"></i>
                Generate Report
            </button>
            <button wire:click="openDimensionModal"
                class="bg-blue-600 text-sm dark:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Level of Result
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse" style="table-layout: fixed;">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600" style="width: 25%;">
                            Levels of Result
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600" style="width: 50%;">
                            Indicators
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600" style="width: 25%;">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800">
                    @forelse($dimensions as $dimension)
                        @php
                            $indicatorCount = $dimension->indicators->count();
                            $levelStatus = $dimension->indicators->first()?->entries->first()?->submission_status ?? 'draft';
                            $levelEditRequested = $dimension->indicators->first()?->entries->first()?->edit_requested ?? false;
                        @endphp

                        @if ($indicatorCount > 0)
                            @foreach ($dimension->indicators as $index => $indicator)
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    {{-- Levels of Result Column (only on first indicator) --}}
                                    @if ($index === 0)
                                        <td class="px-6 py-4 align-top border-r border-gray-200 dark:border-gray-700" style="width: 25%; vertical-align: top;" rowspan="{{ $indicatorCount + 1 }}">
                                            <div class="space-y-3">
                                                <div>
                                                    <div class="font-semibold text-base text-gray-900 dark:text-white">
                                                        {{ $dimension->title }}
                                                    </div>
                                                    @if ($dimension->content)
                                                        <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                                            {{ $dimension->content }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-2 mt-3 flex-wrap">
                                                    <button wire:click="openDimensionModal({{ $dimension->id }})" class="bg-blue-600 dark:bg-blue-700 text-white px-2 py-1 rounded text-sm hover:bg-blue-700 transition">
                                                        <i class="bi bi-pencil-square mr-1"></i>Edit
                                                    </button>
                                                    @php
                                                        $levelStatus = $dimension->indicators->first()?->entries->first()?->submission_status ?? 'draft';
                                                        $canDeleteLevel = $levelStatus === 'draft';
                                                    @endphp
                                                    @if($canDeleteLevel)
                                                        <button wire:click="confirmAction({{ $dimension->id }}, 'deleteDimension')" class="bg-red-600 dark:bg-red-700 text-white px-2 py-1 rounded text-sm hover:bg-red-700 transition">
                                                            <i class="bi bi-trash mr-1"></i>Delete
                                                        </button>
                                                    @else
                                                        <button disabled class="bg-gray-400 dark:bg-gray-600 text-white px-2 py-1 rounded text-sm cursor-not-allowed opacity-50" title="Cannot delete submitted/approved level of result">
                                                            <i class="bi bi-trash mr-1"></i>Delete
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    @endif

                                    {{-- Indicators Column --}}
                                    <td class="px-6 py-4" style="width: 50%;">
                                        <div class="space-y-2">
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                {{ $indicator->title }}
                                            </div>
                                            <button wire:click="toggleIndicatorDetails({{ $indicator->id }})" class="text-blue-600 dark:text-blue-400 hover:underline text-sm flex items-center gap-1 transition">
                                                @if(in_array($indicator->id, $expandedIndicators))
                                                    Hide Entries
                                                    <i class="bi bi-chevron-up"></i>
                                                @else
                                                    Show Entries
                                                    <i class="bi bi-chevron-down"></i>
                                                @endif
                                            </button>
                                        </div>
                                    </td>

                                    {{-- Actions Column (Edit/Delete per indicator) --}}
                                    <td class="px-6 py-4 text-right" style="width: 25%;">
                                        <div class="flex justify-end gap-1">
                                            <button wire:click="openIndicatorModal({{ $dimension->id }}, {{ $indicator->id }})" class="bg-blue-600 dark:bg-blue-700 text-white px-2 py-1 rounded hover:bg-blue-700 transition" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            @php
                                                $indicatorStatus = $indicator->entries->first()?->submission_status ?? 'draft';
                                                $canDeleteIndicator = $indicatorStatus === 'draft';
                                            @endphp
                                            @if($canDeleteIndicator)
                                                <button wire:click="confirmAction({{ $indicator->id }}, 'deleteIndicator')" class="bg-red-600 dark:bg-red-700 text-white px-2 py-1 rounded hover:bg-red-700 transition" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @else
                                                <button disabled class="bg-gray-400 dark:bg-gray-600 text-white px-2 py-1 rounded cursor-not-allowed opacity-50" title="Cannot delete submitted/approved indicator">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                {{-- Expanded Details Row --}}
                                @if(in_array($indicator->id, $expandedIndicators))
                                    <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                                        <td colspan="3" class="px-6 py-6" style="width: 100%;">
                                            <div class="space-y-6">
                                                {{-- Indicator Details --}}
                                                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                                                    <div class="flex justify-between items-start mb-6">
                                                        <h4 class="font-semibold text-lg text-gray-900 dark:text-white">Indicator Details</h4>
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            @php
                                                                $indicatorStatus = $indicator->entries->first()?->submission_status ?? 'draft';
                                                                $statusColor = match($indicatorStatus) {
                                                                    'submitted' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200',
                                                                    'approved' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
                                                                    'rejected' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200',
                                                                    default => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200',
                                                                };
                                                                $isEditRequested = $indicator->entries->first()?->edit_requested ?? false;
                                                            @endphp
                                                            <span class="px-3 py-1 rounded text-sm font-semibold {{ $statusColor }}">
                                                                {{ ucfirst($indicatorStatus) }}
                                                            </span>
                                                            @if($isEditRequested)
                                                                <span class="px-3 py-1 rounded text-sm font-semibold bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                                    Edit Requested
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                        @if($indicator->content)
                                                            <div>
                                                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $indicator->content }}</p>
                                                            </div>
                                                        @endif
                                                        @if($indicator->data_sources)
                                                            <div>
                                                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Data Sources</label>
                                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $indicator->data_sources }}</p>
                                                            </div>
                                                        @endif
                                                        @if($indicator->frequency)
                                                            <div>
                                                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Frequency</label>
                                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $indicator->frequency }}</p>
                                                            </div>
                                                        @endif
                                                        @if($indicator->responsible)
                                                            <div>
                                                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Responsible</label>
                                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $indicator->responsible }}</p>
                                                            </div>
                                                        @endif
                                                        @if($indicator->validation)
                                                            <div>
                                                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Validation & Reporting</label>
                                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $indicator->validation }}</p>
                                                            </div>
                                                        @endif
                                                        @if($indicator->data_sharing)
                                                            <div>
                                                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Data Sharing</label>
                                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $indicator->data_sharing }}</p>
                                                            </div>
                                                        @endif
                                                        @if($indicator->measurement_unit)
                                                            <div>
                                                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Measurement Unit</label>
                                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $indicator->measurement_unit }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Data Entry Table --}}
                                                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                                                    <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
                                                        <h4 class="font-semibold text-lg text-gray-900 dark:text-white">Data Entries (2023-2028)</h4>
                                                        <div class="flex gap-2 flex-wrap">
                                                            @php
                                                                $indicatorStatus = $indicator->entries->first()?->submission_status ?? 'draft';
                                                                $isEditRequested = $indicator->entries->first()?->edit_requested ?? false;
                                                            @endphp

                                                            {{-- Draft Status: Show Edit All Button --}}
                                                            @if($indicatorStatus === 'draft')
                                                                <button wire:click="toggleEditMode({{ $indicator->id }})" class="bg-yellow-600 dark:bg-yellow-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center gap-2 hover:bg-yellow-700 transition">
                                                                    <i class="bi bi-pencil"></i>
                                                                    {{ isset($editModes[$indicator->id]) && $editModes[$indicator->id] ? 'Lock' : 'Edit All' }}
                                                                </button>

                                                            {{-- Submitted Status: Show Edit Request Button --}}
                                                            @elseif($indicatorStatus === 'submitted' && !$isEditRequested)
                                                                <button wire:click="openEditRequestModal({{ $indicator->id }})" class="bg-blue-600 dark:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center gap-2 hover:bg-blue-700 transition">
                                                                    <i class="bi bi-pencil-fill"></i>Request Edit
                                                                </button>

                                                            {{-- Approved Status: Show Edit Request Button --}}
                                                            @elseif($indicatorStatus === 'approved' && !$isEditRequested)
                                                                <button wire:click="openEditRequestModal({{ $indicator->id }})" class="bg-orange-600 dark:bg-orange-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center gap-2 hover:bg-orange-700 transition">
                                                                    <i class="bi bi-pencil-fill"></i>Request Edit
                                                                </button>

                                                            {{-- Rejected Status: Show Edit Request Button --}}
                                                            @elseif($indicatorStatus === 'rejected' && !$isEditRequested)
                                                                <button wire:click="openEditRequestModal({{ $indicator->id }})" class="bg-red-600 dark:bg-red-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center gap-2 hover:bg-red-700 transition">
                                                                    <i class="bi bi-pencil-fill"></i>Request Edit
                                                                </button>

                                                            {{-- Edit Request Pending: Show Status Badge --}}
                                                            @elseif($isEditRequested)
                                                                <span class="px-4 py-2 rounded text-sm font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 flex items-center gap-2">
                                                                    <i class="bi bi-hourglass-split"></i>Edit Pending Approval
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="overflow-x-auto">
                                                        <table class="w-full text-sm border-collapse" style="table-layout: auto; min-width: 100%;">
                                                            <thead>
                                                                <tr class="bg-gray-100 dark:bg-gray-700 border-b border-gray-300 dark:border-gray-600">
                                                                    <th class="px-2 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600">Year</th>
                                                                    <th class="px-2 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600">Baseline</th>
                                                                    <th class="px-2 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600" colspan="3">Physical Target</th>
                                                                    <th class="px-2 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600" colspan="3">Physical Actual</th>
                                                                    <th class="px-2 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600" colspan="2">Financial</th>
                                                                    <th class="px-2 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600">Remarks</th>
                                                                </tr>
                                                                <tr class="bg-gray-50 dark:bg-gray-800 border-b border-gray-300 dark:border-gray-600">
                                                                    <th class="px-2 py-1 border border-gray-300 dark:border-gray-600"></th>
                                                                    <th class="px-2 py-1 border border-gray-300 dark:border-gray-600"></th>
                                                                    <th class="px-2 py-1 text-xs font-medium border border-gray-300 dark:border-gray-600">M</th>
                                                                    <th class="px-2 py-1 text-xs font-medium border border-gray-300 dark:border-gray-600">F</th>
                                                                    <th class="px-2 py-1 text-xs font-medium border border-gray-300 dark:border-gray-600">T</th>
                                                                    <th class="px-2 py-1 text-xs font-medium border border-gray-300 dark:border-gray-600">M</th>
                                                                    <th class="px-2 py-1 text-xs font-medium border border-gray-300 dark:border-gray-600">F</th>
                                                                    <th class="px-2 py-1 text-xs font-medium border border-gray-300 dark:border-gray-600">T</th>
                                                                    <th class="px-2 py-1 text-xs font-medium border border-gray-300 dark:border-gray-600">Allotted</th>
                                                                    <th class="px-2 py-1 text-xs font-medium border border-gray-300 dark:border-gray-600">Spent</th>
                                                                    <th class="px-2 py-1 border border-gray-300 dark:border-gray-600"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    $entries = $indicator->entries->keyBy('year');
                                                                    $years = range(2023, 2028);
                                                                    $isEditable = isset($editModes[$indicator->id]) && $editModes[$indicator->id];
                                                                    $indicatorStatus = $indicator->entries->first()?->submission_status ?? 'draft';
                                                                    $isEditRequested = $indicator->entries->first()?->edit_requested ?? false;
                                                                    $canEdit = ($indicatorStatus === 'draft') || ($isEditRequested && $isEditable);
                                                                @endphp

                                                                @foreach($years as $year)
                                                                    @php $entry = $entries->get($year); @endphp
                                                                    <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/20 border-b border-gray-200 dark:border-gray-700">
                                                                        <td class="px-2 py-2 font-semibold text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600">{{ $year }}</td>
                                                                        <td class="px-2 py-2 border border-gray-300 dark:border-gray-600"><input type="text" {{ !$canEdit ? 'disabled' : '' }} wire:blur="saveEntry({{ $indicator->id }}, {{ $year }}, 'baseline', $event.target.value)" value="{{ $entry?->baseline }}" class="w-full p-1 text-xs border border-gray-200 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed" /></td>
                                                                        <td class="px-2 py-2 border border-gray-300 dark:border-gray-600"><input type="text" {{ !$canEdit ? 'disabled' : '' }} wire:blur="saveEntry({{ $indicator->id }}, {{ $year }}, 'physical_target_male', $event.target.value)" value="{{ $entry?->physical_target_male }}" class="w-full p-1 text-xs border border-gray-200 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed" /></td>
                                                                        <td class="px-2 py-2 border border-gray-300 dark:border-gray-600"><input type="text" {{ !$canEdit ? 'disabled' : '' }} wire:blur="saveEntry({{ $indicator->id }}, {{ $year }}, 'physical_target_female', $event.target.value)" value="{{ $entry?->physical_target_female }}" class="w-full p-1 text-xs border border-gray-200 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed" /></td>
                                                                        <td class="px-2 py-2 border border-gray-300 dark:border-gray-600"><input type="text" {{ !$canEdit ? 'disabled' : '' }} wire:blur="saveEntry({{ $indicator->id }}, {{ $year }}, 'physical_target_total', $event.target.value)" value="{{ $entry?->physical_target_total }}" class="w-full p-1 text-xs border border-gray-200 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed" /></td>
                                                                        <td class="px-2 py-2 border border-gray-300 dark:border-gray-600"><input type="text" {{ !$canEdit ? 'disabled' : '' }} wire:blur="saveEntry({{ $indicator->id }}, {{ $year }}, 'physical_actual_male', $event.target.value)" value="{{ $entry?->physical_actual_male }}" class="w-full p-1 text-xs border border-gray-200 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed" /></td>
                                                                        <td class="px-2 py-2 border border-gray-300 dark:border-gray-600"><input type="text" {{ !$canEdit ? 'disabled' : '' }} wire:blur="saveEntry({{ $indicator->id }}, {{ $year }}, 'physical_actual_female', $event.target.value)" value="{{ $entry?->physical_actual_female }}" class="w-full p-1 text-xs border border-gray-200 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed" /></td>
                                                                        <td class="px-2 py-2 border border-gray-300 dark:border-gray-600"><input type="text" {{ !$canEdit ? 'disabled' : '' }} wire:blur="saveEntry({{ $indicator->id }}, {{ $year }}, 'physical_actual_total', $event.target.value)" value="{{ $entry?->physical_actual_total }}" class="w-full p-1 text-xs border border-gray-200 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed" /></td>
                                                                        <td class="px-2 py-2 border border-gray-300 dark:border-gray-600"><input type="text" {{ !$canEdit ? 'disabled' : '' }} wire:blur="saveEntry({{ $indicator->id }}, {{ $year }}, 'financial_allotted', $event.target.value)" value="{{ $entry?->financial_allotted }}" class="w-full p-1 text-xs border border-gray-200 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed" /></td>
                                                                        <td class="px-2 py-2 border border-gray-300 dark:border-gray-600"><input type="text" {{ !$canEdit ? 'disabled' : '' }} wire:blur="saveEntry({{ $indicator->id }}, {{ $year }}, 'financial_spent', $event.target.value)" value="{{ $entry?->financial_spent }}" class="w-full p-1 text-xs border border-gray-200 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed" /></td>
                                                                        <td class="px-2 py-2 border border-gray-300 dark:border-gray-600"><input type="text" {{ !$canEdit ? 'disabled' : '' }} wire:blur="saveEntry({{ $indicator->id }}, {{ $year }}, 'remarks', $event.target.value)" value="{{ $entry?->remarks }}" class="w-full p-1 text-xs border border-gray-200 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed" /></td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    {{-- Status and Edit Request Info Box --}}
                                                    @if($indicatorStatus !== 'draft' || $isEditRequested)
                                                        <div class="mt-4 p-4 rounded-lg border {{ $indicatorStatus === 'approved' ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : ($indicatorStatus === 'rejected' ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' : 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800') }}">
                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                                                <div>
                                                                    <span class="font-semibold {{ $indicatorStatus === 'approved' ? 'text-green-800 dark:text-green-200' : ($indicatorStatus === 'rejected' ? 'text-red-800 dark:text-red-200' : 'text-yellow-800 dark:text-yellow-200') }}">
                                                                        📊 Status: {{ ucfirst($indicatorStatus) }}
                                                                    </span>
                                                                    @if($entry->submitted_at)
                                                                        <p class="text-xs {{ $indicatorStatus === 'approved' ? 'text-green-700 dark:text-green-300' : ($indicatorStatus === 'rejected' ? 'text-red-700 dark:text-red-300' : 'text-yellow-700 dark:text-yellow-300') }} mt-2">
                                                                            <i class="bi bi-calendar-event"></i> Submitted: {{ $entry->submitted_at->format('M d, Y H:i') }}
                                                                        </p>
                                                                    @endif
                                                                </div>
                                                                <div>
                                                                    @if($isEditRequested && $entry->edit_requested_at)
                                                                        <span class="font-semibold text-blue-800 dark:text-blue-200">
                                                                            <i class="bi bi-pencil-square"></i> Edit Request Pending
                                                                        </span>
                                                                        <p class="text-xs text-blue-700 dark:text-blue-300 mt-2">
                                                                            <i class="bi bi-calendar-event"></i> Requested: {{ $entry->edit_requested_at->format('M d, Y H:i') }}
                                                                        </p>
                                                                        @if($entry->edit_request_reason)
                                                                            <p class="text-xs text-blue-700 dark:text-blue-300 mt-2">
                                                                                <i class="bi bi-chat-left-text"></i> Reason: {{ $entry->edit_request_reason }}
                                                                            </p>
                                                                        @endif
                                                                    @elseif($entry->admin_notes)
                                                                        <span class="font-semibold text-gray-800 dark:text-gray-200">
                                                                            <i class="bi bi-info-circle"></i> Admin Notes
                                                                        </span>
                                                                        <p class="text-xs text-gray-700 dark:text-gray-300 mt-2">
                                                                            {{ $entry->admin_notes }}
                                                                        </p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach

                            {{-- Add New Indicator Row - Aligned to Indicators Column Only --}}
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <td class="px-6 py-4 border-r border-gray-200 dark:border-gray-700" style="width: 25%;"></td>
                                <td class="px-6 py-4" style="width: 50%;">
                                    <button wire:click="openIndicatorModal({{ $dimension->id }})" class="bg-blue-600 dark:bg-blue-700 text-white px-3 py-2 rounded text-sm font-medium flex items-center gap-1 hover:bg-blue-700 transition">
                                        <i class="bi bi-plus-circle"></i>Add New Indicator
                                    </button>
                                </td>
                                <td class="px-6 py-4" style="width: 25%;"></td>
                            </tr>

                            {{-- Level Actions Row (Submit per level) --}}
                            <tr class="bg-blue-50 dark:bg-blue-900/20 border-b border-gray-200 dark:border-gray-700">
                                <td class="px-6 py-4 border-r border-gray-200 dark:border-gray-700" style="width: 25%;"></td>
                                <td class="px-6 py-4" style="width: 50%;">
                                    <div class="flex gap-2 flex-wrap">
                                        @php
                                            $levelStatus = $dimension->indicators->first()?->entries->first()?->submission_status ?? 'draft';
                                        @endphp

                                        {{-- DRAFT STATUS: Show Submit Button --}}
                                        @if($levelStatus === 'draft')
                                            <button wire:click="openSubmitModal({{ $dimension->id }})" class="bg-green-600 dark:bg-green-700 text-white px-4 py-2 rounded text-sm font-medium flex items-center gap-2 hover:bg-green-700 transition">
                                                <i class="bi bi-check-circle"></i>Submit All Indicators
                                            </button>
                                        @else
                                            <span class="text-gray-600 dark:text-gray-400 text-sm px-4 py-2 font-medium flex items-center gap-2">
                                                <i class="bi bi-info-circle"></i>Request edits per indicator above
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4" style="width: 25%;"></td>
                            </tr>
                        @else
                            {{-- No Indicators Case --}}
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <td class="px-6 py-4 border-r border-gray-200 dark:border-gray-700" style="width: 25%;">
                                    <div class="space-y-3">
                                        <div>
                                            <div class="font-semibold text-base text-gray-900 dark:text-white">
                                                {{ $dimension->title }}
                                            </div>
                                            @if ($dimension->content)
                                                <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                                    {{ $dimension->content }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 mt-3 flex-wrap">
                                            <button wire:click="openDimensionModal({{ $dimension->id }})" class="bg-blue-600 dark:bg-blue-700 text-white px-2 py-1 rounded text-sm hover:bg-blue-700 transition">
                                                <i class="bi bi-pencil-square mr-1"></i>Edit
                                            </button>
                                            <button wire:click="confirmAction({{ $dimension->id }}, 'deleteDimension')" class="bg-red-600 dark:bg-red-700 text-white px-2 py-1 rounded text-sm hover:bg-red-700 transition">
                                                <i class="bi bi-trash mr-1"></i>Delete
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4" style="width: 50%;">
                                    <div class="text-gray-500 dark:text-gray-400 italic">No indicators yet</div>
                                </td>
                                <td class="px-6 py-4 text-right" style="width: 25%;">
                                    <button wire:click="openIndicatorModal({{ $dimension->id }})" class="bg-blue-600 dark:bg-blue-700 text-white px-2 py-1 rounded hover:bg-blue-700 transition">
                                        <i class="bi bi-plus-circle"></i>
                                    </button>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-lg font-medium text-gray-700 dark:text-gray-200">No levels found</p>
                                    <p class="text-sm mt-1">Create your first level to get started</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Report Modal -->
    @if ($showReportModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg mx-4 border border-gray-200 dark:border-gray-700 my-8">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Generate Report</h3>
                        <button wire:click="$set('showReportModal', false)" class="text-gray-500 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="generateReport" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Select Levels of Result to Include *</label>
                            <div class="space-y-3 max-h-80 overflow-y-auto">
                                @forelse($dimensions as $dimension)
                                    <div class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <input type="checkbox" 
                                            wire:model.live="selectedLevels" 
                                            value="{{ $dimension->id }}" 
                                            id="level_{{ $dimension->id }}"
                                            class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                        <label for="level_{{ $dimension->id }}" class="ml-3 flex-1 cursor-pointer">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $dimension->title }}</div>
                                            @if($dimension->content)
                                                <div class="text-xs text-gray-600 dark:text-gray-400">{{ $dimension->content }}</div>
                                            @endif
                                        </label>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">
                                            {{ $dimension->indicators->count() }} indicator(s)
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No levels available</p>
                                @endforelse
                            </div>
                        </div>

                        @if(empty($selectedLevels))
                            <div class="bg-yellow-50 dark:bg-yellow-900/30 p-3 rounded border border-yellow-200 dark:border-yellow-800">
                                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                    <i class="bi bi-exclamation-circle"></i> Select at least one level.
                                </p>
                            </div>
                        @else
                            <div class="bg-blue-50 dark:bg-blue-900/30 p-3 rounded border border-blue-200 dark:border-blue-800">
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    <i class="bi bi-info-circle"></i> Selected: <strong>{{ count($selectedLevels) }}</strong> level(s)
                                </p>
                            </div>
                        @endif

                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" wire:click="$set('showReportModal', false)" class="px-4 py-2 text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancel</button>
                            <button type="submit" {{ empty($selectedLevels) ? 'disabled' : '' }} class="bg-purple-600 dark:bg-purple-700 text-white px-6 py-2 rounded-md flex items-center gap-2 hover:bg-purple-700 dark:hover:bg-purple-800 disabled:opacity-50 disabled:cursor-not-allowed transition">
                                <i class="bi bi-download"></i>Generate & Download
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Submit Modal (Per Level) -->
    @if ($showSubmitModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg mx-4 border border-gray-200 dark:border-gray-700 my-8">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Submit Level of Result</h3>
                        <button wire:click="$set('showSubmitModal', false)" class="text-gray-500 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form wire:submit.prevent="submitLevelWithNotes" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Submission Notes (Optional)</label>
                            <textarea wire:model.defer="submissionNotes" rows="5" placeholder="Add any notes or comments about this submission..." class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"></textarea>
                        </div>
                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" wire:click="$set('showSubmitModal', false)" class="px-4 py-2 text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancel</button>
                            <button type="submit" class="bg-green-600 dark:bg-green-700 text-white px-6 py-2 rounded-md flex items-center gap-2 hover:bg-green-700 dark:hover:bg-green-800 transition">
                                <i class="bi bi-check-circle"></i>Submit All Indicators
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Request Modal (Per Level) -->
    @if ($showEditRequestModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg mx-4 border border-gray-200 dark:border-gray-700 my-8">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Request Edit Level of Result</h3>
                        <button wire:click="$set('showEditRequestModal', false)" class="text-gray-500 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form wire:submit.prevent="requestEditAccessIndicator" class="space-y-4">
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg border border-blue-200 dark:border-blue-800 mb-4">
                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                <i class="bi bi-info-circle mr-2"></i>Please provide a detailed reason for requesting edit access.
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason for Edit Request <span class="text-red-600">*</span></label>
                            <textarea wire:model.defer="editRequestReason" rows="5" placeholder="Please explain why you need to edit this level of result..." class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required></textarea>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Minimum 10 characters required</p>
                        </div>
                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" wire:click="$set('showEditRequestModal', false)" class="px-4 py-2 text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancel</button>
                            <button type="submit" class="bg-blue-600 dark:bg-blue-700 text-white px-6 py-2 rounded-md hover:bg-blue-700 dark:hover:bg-blue-800 transition">Request Edit Access</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Dimension Modal -->
    @if ($showDimensionModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4 border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $editingDimensionId ? 'Edit Level of Result' : 'Add Level of Result' }}</h3>
                        <button wire:click="closeDimensionModal" class="text-gray-500 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form wire:submit.prevent="saveDimension" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Level of Result Title <span class="text-red-600">*</span></label>
                            <input type="text" wire:model.defer="dimensionName" placeholder="e.g., Impact, Output, Activity..." class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                            <textarea wire:model.defer="dimensionDescription" rows="3" placeholder="Describe this level of result..." class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"></textarea>
                        </div>
                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" wire:click="closeDimensionModal" class="px-4 py-2 text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancel</button>
                            <button type="submit" class="bg-blue-600 dark:bg-blue-700 text-white px-6 py-2 rounded-md hover:bg-blue-700 dark:hover:bg-blue-800 transition">{{ $editingDimensionId ? 'Update' : 'Create' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Indicator Modal -->
    @if ($showIndicatorModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl mx-4 border border-gray-200 dark:border-gray-700 my-8">
                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $editingIndicatorId ? 'Edit Indicator' : 'Add Indicator' }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">📋 8 Fields • 2 Required • 6 Optional</p>
                    </div>
                    <button wire:click="closeIndicatorModal" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form Content -->
                <form wire:submit.prevent="saveIndicator" class="px-6 py-4">
                    <!-- Scrollable Fields Container -->
                    <div class="space-y-4 max-h-[65vh] overflow-y-auto pr-2">
                        
                        <!-- REQUIRED FIELDS -->
                        <div class="space-y-3">
                            <!-- Indicator Name -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border-2 border-blue-200 dark:border-blue-800">
                                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                    <span class="text-red-600">*</span> Indicator Name (Required)
                                </label>
                                <input 
                                    type="text" 
                                    wire:model.defer="indicatorName" 
                                    placeholder="e.g., Coverage Rate, Enrollment, Pass Rate..."
                                    required
                                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                />
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">This is the main title of your indicator</p>
                            </div>

                            <!-- Description -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border-2 border-blue-200 dark:border-blue-800">
                                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                    <span class="text-red-600">*</span> Description (Required)
                                </label>
                                <textarea 
                                    wire:model.defer="indicatorDescription" 
                                    rows="2"
                                    placeholder="Explain what this indicator measures and why it's important..."
                                    required
                                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                ></textarea>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">Describe the purpose and meaning of this indicator</p>
                            </div>
                        </div>

                        <!-- OPTIONAL FIELDS SECTION -->
                        <div class="pt-2">
                            <p class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-3">📝 Additional Details (Optional - 6 fields below)</p>
                            
                            <div class="space-y-3">
                                <!-- Data Sources -->
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        📊 Data Sources
                                    </label>
                                    <textarea 
                                        wire:model.defer="indicatorDataSources" 
                                        rows="2"
                                        placeholder="Where does the data come from? (e.g., Ministry records, surveys, etc.)"
                                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    ></textarea>
                                </div>

                                <!-- Frequency -->
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        🔄 Frequency
                                    </label>
                                    <input 
                                        type="text" 
                                        wire:model.defer="indicatorFrequency"
                                        placeholder="e.g., Annual, Quarterly, Monthly, Weekly..."
                                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    />
                                </div>

                                <!-- Responsible -->
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        👤 Responsible
                                    </label>
                                    <textarea 
                                        wire:model.defer="indicatorResponsible" 
                                        rows="2"
                                        placeholder="Who is responsible? (Department, Person, Team)"
                                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    ></textarea>
                                </div>

                                <!-- Validation & Reporting -->
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        ✓ Validation & Reporting
                                    </label>
                                    <textarea 
                                        wire:model.defer="indicatorValidation" 
                                        rows="2"
                                        placeholder="How is data validated? How is it reported?"
                                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    ></textarea>
                                </div>

                                <!-- Data Sharing -->
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        🔐 Data Sharing
                                    </label>
                                    <textarea 
                                        wire:model.defer="indicatorDataSharing" 
                                        rows="2"
                                        placeholder="Who has access? Any restrictions or confidentiality?"
                                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    ></textarea>
                                </div>

                                <!-- Measurement Unit -->
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        📐 Measurement Unit
                                    </label>
                                    <input 
                                        type="text" 
                                        wire:model.defer="indicatorMeasurementUnit"
                                        placeholder="e.g., %, Number, Amount, Score, Count..."
                                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Info Message -->
                        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 p-3 rounded-lg">
                            <p class="text-xs text-amber-800 dark:text-amber-200">
                                💡 <strong>Tip:</strong> Indicator Name and Description are required. Fill the 6 optional fields now or edit them later!
                            </p>
                        </div>
                    </div>

                    <!-- Sticky Footer -->
                    <div class="flex justify-end gap-3 pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                        <button 
                            type="button" 
                            wire:click="closeIndicatorModal" 
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-md transition"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-6 py-2 bg-blue-600 dark:bg-blue-700 text-white hover:bg-blue-700 dark:hover:bg-blue-800 rounded-md font-medium transition"
                        >
                            {{ $editingIndicatorId ? '✓ Update Indicator' : '✓ Create Indicator' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Modal -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-sm p-6 text-center border border-gray-200 dark:border-gray-700">
                <div class="mb-4">
                    <i class="bi bi-exclamation-triangle text-4xl text-red-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Delete {{ $type }}</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">This action cannot be undone. Are you sure you want to continue?</p>
                <div class="flex justify-center gap-4">
                    <button wire:click="$set('showDeleteModal', false)" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancel</button>
                    <button wire:click="confirmDelete" class="px-4 py-2 bg-red-600 dark:bg-red-700 text-white rounded hover:bg-red-700 dark:hover:bg-red-800 transition">Delete</button>
                </div>
            </div>
        </div>
    @endif
</div>