<div class="w-full">
    <div class="w-full flex justify-center">
        <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800 overflow-x-visible">
            <div class="mb-6 ">
                <h1 class="text-lg font-bold text-center text-slate-800 dark:text-white">Accomplishment - Input
                    Datasets</h1>
            </div>

            <div class="mb-6">
                <button wire:click="openModal"
                    class="text-sm mt-4 sm:mt-1 px-2 py-1.5 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none dark:bg-gray-700 dark:hover:bg-green-600 dark:text-gray-300 dark:hover:text-white whitespace-nowrap">
                    <i class="bi bi-plus-lg mr-1"></i> Add New Accomplishment Data
                </button>
            </div>

            <!-- Accomplishments Table -->
            @if (count($accomplishments) > 0)
                <div class="mb-6">
                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-300 dark:border-gray-600 text-sm">
                            <thead class="bg-blue-600 text-white">
                                <tr>
                                    <th class="border border-gray-300 dark:border-gray-600 p-2" rowspan="2">PPA</th>
                                    <th class="border border-gray-300 dark:border-gray-600 p-2" rowspan="2">Indicator
                                    </th>
                                    <th class="border border-gray-300 dark:border-gray-600 p-2" rowspan="2">Status
                                    </th>
                                    <th class="border border-gray-300 dark:border-gray-600 p-2" colspan="2">2024</th>
                                    <th class="border border-gray-300 dark:border-gray-600 p-2" colspan="2">2025</th>
                                    <th class="border border-gray-300 dark:border-gray-600 p-2" colspan="2">2026</th>
                                    <th class="border border-gray-300 dark:border-gray-600 p-2" colspan="2">2027</th>
                                    <th class="border border-gray-300 dark:border-gray-600 p-2" colspan="2">2028</th>
                                    <th class="border border-gray-300 dark:border-gray-600 p-2" rowspan="2">Actions
                                    </th>
                                </tr>
                                <tr>
                                    @foreach ($availableYears as $year)
                                        <th class="border border-gray-300 dark:border-gray-600 p-1 text-xs">Target</th>
                                        <th class="border border-gray-300 dark:border-gray-600 p-1 text-xs">Actual</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($accomplishments as $accomplishment)
                                    <tr class="bg-white dark:bg-gray-800">
                                        <td class="border border-gray-300 dark:border-gray-600 p-2 font-medium">
                                            {{ $accomplishment['ppa_name'] }}
                                        </td>
                                        <td class="border border-gray-300 dark:border-gray-600 p-2">
                                            {{ $accomplishment['indicator']->name }}
                                            @if ($accomplishment['indicator']->measurement_unit)
                                                <br><span
                                                    class="text-xs text-gray-500">({{ $accomplishment['indicator']->measurement_unit }})</span>
                                            @endif
                                        </td>
                                        <td class="border border-gray-300 dark:border-gray-600 p-2 text-center">
                                            @php
                                                $statusClass = match ($accomplishment['overall_status']) {
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'approved' => 'bg-green-100 text-green-800',
                                                    'rejected' => 'bg-red-100 text-red-800',
                                                    'needs_revision' => 'bg-blue-100 text-blue-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                };
                                                $statusText = match ($accomplishment['overall_status']) {
                                                    'pending' => 'Pending',
                                                    'approved' => 'Approved',
                                                    'rejected' => 'Rejected',
                                                    'needs_revision' => 'Needs Revision',
                                                    default => 'Unknown',
                                                };
                                            @endphp
                                            <span class="px-2 py-1 text-xs rounded-full {{ $statusClass }}">
                                                {{ $statusText }}
                                            </span>
                                            @if ($accomplishment['has_feedback'])
                                                <div class="mt-1">
                                                    <button
                                                        wire:click="showFeedback('{{ $accomplishment['ppa_name'] }}', {{ $accomplishment['indicator']->id }})"
                                                        class="text-blue-500 hover:text-blue-600 text-xs underline">
                                                        View Feedback
                                                    </button>
                                                </div>
                                            @endif
                                        </td>

                                        @foreach ($availableYears as $year)
                                            @php
                                                $yearData = $accomplishment['years'][$year] ?? null;
                                            @endphp
                                            <td class="border border-gray-300 dark:border-gray-600 p-1 text-center">
                                                <div class="text-xs">
                                                    <div>P: {{ $yearData?->target_physical ?? '-' }}</div>
                                                    <div>F: {{ $yearData?->target_financial ?? '-' }}</div>
                                                </div>
                                            </td>
                                            <td class="border border-gray-300 dark:border-gray-600 p-1 text-center">
                                                <div class="text-xs">
                                                    <div>P: {{ $yearData?->actual_physical ?? '-' }}</div>
                                                    <div>F: {{ $yearData?->actual_financial ?? '-' }}</div>
                                                </div>
                                            </td>
                                        @endforeach

                                        <td class="border border-gray-300 dark:border-gray-600 p-2 text-center">
                                            <div class="flex justify-center space-x-1">
                                                <button
                                                    wire:click="editAccomplishment('{{ $accomplishment['ppa_name'] }}', {{ $accomplishment['indicator']->id }})"
                                                    class="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 focus:outline-none"
                                                    title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button
                                                    wire:click="showDeleteConfirmation('{{ $accomplishment['ppa_name'] }}', {{ $accomplishment['indicator']->id }})"
                                                    class="text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 focus:outline-none"
                                                    title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                    <p>No accomplishment data found. Click "Add New Accomplishment Data" to get started.</p>
                </div>
            @endif

            <!-- Add/Edit Modal -->
            <x-modal wire:model="showModal" max-width="4xl">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Input Accomplishment Data
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                PPA (Programs, Projects, and Activities)
                            </label>
                            <input type="text" wire:model="selectedPPA" placeholder="Enter PPA name"
                                class="w-full p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Select Indicator
                            </label>
                            <select wire:model="selectedIndicator" required
                                class="w-full p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                                <option value="">Choose indicator</option>
                                @foreach ($indicators as $indicator)
                                    <option value="{{ $indicator->id }}">
                                        {{ $indicator->name }}
                                        @if ($indicator->measurement_unit)
                                            ({{ $indicator->measurement_unit }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-6">
                            <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-3">
                                Targets and Actual Accomplishments
                            </h3>

                            <div class="overflow-x-auto">
                                <table class="w-full border border-gray-300 dark:border-gray-600">
                                    <thead class="bg-blue-600 text-white">
                                        <tr>
                                            <th class="border border-gray-300 dark:border-gray-600 p-2" rowspan="2">
                                                Year</th>
                                            <th class="border border-gray-300 dark:border-gray-600 p-2" colspan="2">
                                                Target</th>
                                            <th class="border border-gray-300 dark:border-gray-600 p-2" colspan="2">
                                                Actual</th>
                                        </tr>
                                        <tr>
                                            <th class="border border-gray-300 dark:border-gray-600 p-2">Physical</th>
                                            <th class="border border-gray-300 dark:border-gray-600 p-2">Financial</th>
                                            <th class="border border-gray-300 dark:border-gray-600 p-2">Physical</th>
                                            <th class="border border-gray-300 dark:border-gray-600 p-2">Financial</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($availableYears as $year)
                                            <tr class="bg-white dark:bg-gray-800">
                                                <td class="border border-gray-300 dark:border-gray-600 p-2 font-medium">
                                                    {{ $year }}
                                                </td>
                                                <td class="border border-gray-300 dark:border-gray-600 p-1">
                                                    <input type="number"
                                                        wire:model="targets.{{ $year }}.physical"
                                                        step="0.01"
                                                        class="w-full p-1 border-0 focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                                                </td>
                                                <td class="border border-gray-300 dark:border-gray-600 p-1">
                                                    <input type="number"
                                                        wire:model="targets.{{ $year }}.financial"
                                                        step="0.01"
                                                        class="w-full p-1 border-0 focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                                                </td>
                                                <td class="border border-gray-300 dark:border-gray-600 p-1">
                                                    <input type="number"
                                                        wire:model="actuals.{{ $year }}.physical"
                                                        step="0.01"
                                                        class="w-full p-1 border-0 focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                                                </td>
                                                <td class="border border-gray-300 dark:border-gray-600 p-1">
                                                    <input type="number"
                                                        wire:model="actuals.{{ $year }}.financial"
                                                        step="0.01"
                                                        class="w-full p-1 border-0 focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-md">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Alternative: Upload Spreadsheet
                            </h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">
                                You may also upload the accomplished matrix of PPAs in spreadsheet format
                            </p>
                            <input type="file" accept=".xlsx,.xls,.csv"
                                class="w-full p-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-4">
                        <button type="button" wire:click="closeModal"
                            class="px-4 py-2 rounded-md bg-gray-600 text-white hover:bg-gray-700">
                            Cancel
                        </button>
                        <button wire:click="saveAccomplishment"
                            class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">
                            Save Accomplishment
                        </button>
                    </div>
                </div>
            </x-modal>

            <!-- Delete Confirmation Modal -->
            <x-modal wire:model="showDeleteModal" max-width="md">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Confirm Deletion
                    </h2>

                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Are you sure you want to delete this accomplishment data? This action cannot be undone.
                    </p>

                    <div class="flex justify-end space-x-4">
                        <button type="button" wire:click="closeDeleteModal"
                            class="px-4 py-2 rounded-md bg-gray-600 text-white hover:bg-gray-700">
                            Cancel
                        </button>
                        <button wire:click="confirmDelete"
                            class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">
                            Delete
                        </button>
                    </div>
                </div>
            </x-modal>

            <!-- Feedback Modal -->
            <x-modal wire:model="showFeedbackModal" max-width="2xl">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Admin Feedback
                    </h2>

                    @if ($feedbackData)
                        <div class="space-y-4">
                            @foreach ($feedbackData as $data)
                                @if ($data->admin_feedback)
                                    <div class="p-4 border border-gray-200 rounded-lg dark:border-gray-600">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $data->indicator->name }} - {{ $data->year }}
                                                </h4>
                                                <div class="flex items-center mt-1">
                                                    <span
                                                        class="px-2 py-1 text-xs rounded-full {{ $data->status_color }}">
                                                        {{ $data->status_text }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                @if ($data->reviewer)
                                                    By: {{ $data->reviewer->name }}
                                                @endif
                                                @if ($data->reviewed_at)
                                                    <br>{{ $data->reviewed_at->format('M d, Y H:i') }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                                {{ $data->admin_feedback }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-6 flex justify-end">
                        <button type="button" wire:click="closeFeedbackModal"
                            class="px-4 py-2 rounded-md bg-gray-600 text-white hover:bg-gray-700">
                            Close
                        </button>
                    </div>
                </div>
            </x-modal>
        </div>
    </div>
</div>
