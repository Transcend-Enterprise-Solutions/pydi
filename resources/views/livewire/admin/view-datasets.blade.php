<div class="w-full">
    <div class="w-full flex justify-center">
        <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800 overflow-x-visible">
            <div class="mb-6">
                <h1 class="text-lg font-bold text-center text-slate-800 dark:text-white">
                    Accomplishment - Review Datasets
                </h1>
            </div>

            <!-- Filters -->
            <div class="mb-6 flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Search PPA or Indicator
                    </label>
                    <input type="text" wire:model.live="searchTerm" placeholder="Search..."
                        class="w-full p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                </div>
                <div class="flex-none">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Status Filter
                    </label>
                    <select wire:model.live="statusFilter"
                        class="w-full p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="needs_revision">Needs Revision</option>
                    </select>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                @php
                    $stats = [
                        'pending' => ['count' => 0, 'color' => 'bg-yellow-100 text-yellow-800'],
                        'approved' => ['count' => 0, 'color' => 'bg-green-100 text-green-800'],
                        'rejected' => ['count' => 0, 'color' => 'bg-red-100 text-red-800'],
                        'needs_revision' => ['count' => 0, 'color' => 'bg-blue-100 text-blue-800'],
                    ];

                    foreach ($accomplishments as $accomplishment) {
                        $stats['pending']['count'] += $accomplishment['pending_count'];
                        $stats['approved']['count'] += $accomplishment['approved_count'];
                        $stats['rejected']['count'] += $accomplishment['rejected_count'];
                        $stats['needs_revision']['count'] += $accomplishment['needs_revision_count'];
                    }
                @endphp

                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 dark:bg-yellow-900/40 rounded-lg">
                            <i class="bi bi-clock text-yellow-600 dark:text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Pending</p>
                            <p class="text-lg font-semibold text-yellow-900 dark:text-yellow-100">
                                {{ $stats['pending']['count'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 dark:bg-green-900/40 rounded-lg">
                            <i class="bi bi-check-circle text-green-600 dark:text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">Approved</p>
                            <p class="text-lg font-semibold text-green-900 dark:text-green-100">
                                {{ $stats['approved']['count'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 dark:bg-red-900/40 rounded-lg">
                            <i class="bi bi-x-circle text-red-600 dark:text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800 dark:text-red-200">Rejected</p>
                            <p class="text-lg font-semibold text-red-900 dark:text-red-100">
                                {{ $stats['rejected']['count'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/40 rounded-lg">
                            <i class="bi bi-arrow-clockwise text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Needs Revision</p>
                            <p class="text-lg font-semibold text-blue-900 dark:text-blue-100">
                                {{ $stats['needs_revision']['count'] }}</p>
                        </div>
                    </div>
                </div>
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
                                        Summary</th>
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
                                            <div class="space-y-1">
                                                @if ($accomplishment['pending_count'] > 0)
                                                    <span
                                                        class="inline-block px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                                        {{ $accomplishment['pending_count'] }} Pending
                                                    </span>
                                                @endif
                                                @if ($accomplishment['approved_count'] > 0)
                                                    <span
                                                        class="inline-block px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                        {{ $accomplishment['approved_count'] }} Approved
                                                    </span>
                                                @endif
                                                @if ($accomplishment['rejected_count'] > 0)
                                                    <span
                                                        class="inline-block px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                                        {{ $accomplishment['rejected_count'] }} Rejected
                                                    </span>
                                                @endif
                                                @if ($accomplishment['needs_revision_count'] > 0)
                                                    <span
                                                        class="inline-block px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                                        {{ $accomplishment['needs_revision_count'] }} Revision
                                                    </span>
                                                @endif
                                            </div>
                                            @if ($accomplishment['last_reviewed'])
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Last: {{ $accomplishment['last_reviewed']->format('M d, Y') }}
                                                </div>
                                            @endif
                                        </td>

                                        @foreach ($availableYears as $year)
                                            @php
                                                $yearData = $accomplishment['years'][$year] ?? null;
                                                $statusColor = $yearData
                                                    ? match ($yearData->status) {
                                                        'pending' => 'border-l-4 border-yellow-400',
                                                        'approved' => 'border-l-4 border-green-400',
                                                        'rejected' => 'border-l-4 border-red-400',
                                                        'needs_revision' => 'border-l-4 border-blue-400',
                                                        default => '',
                                                    }
                                                    : '';
                                            @endphp
                                            <td
                                                class="border border-gray-300 dark:border-gray-600 p-1 text-center {{ $statusColor }}">
                                                <div class="text-xs">
                                                    <div>P: {{ $yearData?->target_physical ?? '-' }}</div>
                                                    <div>F: {{ $yearData?->target_financial ?? '-' }}</div>
                                                </div>
                                            </td>
                                            <td
                                                class="border border-gray-300 dark:border-gray-600 p-1 text-center {{ $statusColor }}">
                                                <div class="text-xs">
                                                    <div>P: {{ $yearData?->actual_physical ?? '-' }}</div>
                                                    <div>F: {{ $yearData?->actual_financial ?? '-' }}</div>
                                                </div>
                                            </td>
                                        @endforeach

                                        <td class="border border-gray-300 dark:border-gray-600 p-2 text-center">
                                            <div class="flex flex-col space-y-1">
                                                <button
                                                    wire:click="openReviewModal('{{ $accomplishment['ppa_name'] }}', {{ $accomplishment['indicator']->id }})"
                                                    class="text-xs px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600"
                                                    title="Review Details">
                                                    <i class="bi bi-eye mr-1"></i>Review
                                                </button>
                                                @if ($accomplishment['pending_count'] > 0)
                                                    <button
                                                        wire:click="bulkApprove('{{ $accomplishment['ppa_name'] }}', {{ $accomplishment['indicator']->id }})"
                                                        class="text-xs px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600"
                                                        title="Approve All">
                                                        <i class="bi bi-check-all mr-1"></i>Approve All
                                                    </button>
                                                @endif
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
                    <p>No accomplishment data found for review.</p>
                </div>
            @endif

            <!-- Review Modal -->
            <x-modal wire:model="showReviewModal" max-width="5xl">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Review Accomplishment Data
                    </h2>

                    @if ($selectedAccomplishment)
                        @php
                            $accomplishment = collect($accomplishments)->firstWhere(function ($item) {
                                return $item['ppa_name'] === $this->selectedAccomplishment['ppa_name'] &&
                                    $item['indicator']->id === $this->selectedAccomplishment['indicator_id'];
                            });
                        @endphp

                        @if ($accomplishment)
                            <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">
                                    PPA: {{ $accomplishment['ppa_name'] }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Indicator: {{ $accomplishment['indicator']->name }}
                                    @if ($accomplishment['indicator']->measurement_unit)
                                        ({{ $accomplishment['indicator']->measurement_unit }})
                                    @endif
                                </p>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full border border-gray-300 dark:border-gray-600">
                                    <thead class="bg-blue-600 text-white">
                                        <tr>
                                            <th class="border border-gray-300 dark:border-gray-600 p-2">Year</th>
                                            <th class="border border-gray-300 dark:border-gray-600 p-2">Target Physical
                                            </th>
                                            <th class="border border-gray-300 dark:border-gray-600 p-2">Target
                                                Financial</th>
                                            <th class="border border-gray-300 dark:border-gray-600 p-2">Actual Physical
                                            </th>
                                            <th class="border border-gray-300 dark:border-gray-600 p-2">Actual
                                                Financial</th>
                                            <th class="border border-gray-300 dark:border-gray-600 p-2">Status</th>
                                            <th class="border border-gray-300 dark:border-gray-600 p-2">Feedback</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($availableYears as $year)
                                            @php
                                                $yearData = $accomplishment['years'][$year] ?? null;
                                            @endphp
                                            <tr class="bg-white dark:bg-gray-800">
                                                <td
                                                    class="border border-gray-300 dark:border-gray-600 p-2 font-medium">
                                                    {{ $year }}
                                                </td>
                                                <td
                                                    class="border border-gray-300 dark:border-gray-600 p-2 text-center">
                                                    {{ $yearData?->target_physical ?? '-' }}
                                                </td>
                                                <td
                                                    class="border border-gray-300 dark:border-gray-600 p-2 text-center">
                                                    {{ $yearData?->target_financial ?? '-' }}
                                                </td>
                                                <td
                                                    class="border border-gray-300 dark:border-gray-600 p-2 text-center">
                                                    {{ $yearData?->actual_physical ?? '-' }}
                                                </td>
                                                <td
                                                    class="border border-gray-300 dark:border-gray-600 p-2 text-center">
                                                    {{ $yearData?->actual_financial ?? '-' }}
                                                </td>
                                                <td class="border border-gray-300 dark:border-gray-600 p-2">
                                                    <select wire:model="reviewData.{{ $year }}.status"
                                                        class="w-full p-1 border rounded text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                                                        <option value="pending">Pending</option>
                                                        <option value="approved">Approved</option>
                                                        <option value="rejected">Rejected</option>
                                                        <option value="needs_revision">Needs Revision</option>
                                                    </select>
                                                </td>
                                                <td class="border border-gray-300 dark:border-gray-600 p-2">
                                                    <textarea wire:model="reviewData.{{ $year }}.feedback" rows="2" placeholder="Add feedback..."
                                                        class="w-full p-1 border rounded text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"></textarea>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endif

                    <div class="mt-6 flex justify-end space-x-4">
                        <button type="button" wire:click="closeReviewModal"
                            class="px-4 py-2 rounded-md bg-gray-600 text-white hover:bg-gray-700">
                            Cancel
                        </button>
                        <button wire:click="submitReview"
                            class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">
                            Submit Review
                        </button>
                    </div>
                </div>
            </x-modal>
        </div>
    </div>
</div>
