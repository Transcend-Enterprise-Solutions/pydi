<div class="w-full mt-6">
    <h2 class="text-xl font-bold mb-3 pt-0">PYDP ({{ $datasetInfo->name }})</h2>

    <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800 overflow-x-visible">
        <div class="flex justify-between items-center flex-wrap gap-4 mb-4">
            <div class="flex gap-2 flex-wrap items-center">
                <input type="text" wire:model.live="search" placeholder="Search..."
                    class="w-52 py-1 px-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                <select wire:model.live="showEntries"
                    class="w-16 py-1 px-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>

            <!-- Upload Form -->
            <div class="flex gap-2 items-center">
                <div x-data="{ open: false }" class="relative inline-block text-left">
                    <button @click="open = !open"
                        class="bg-blue-500 text-white px-3 py-2 rounded hover:bg-blue-600 transition flex items-center gap-2">
                        <i class="bi bi-list"></i> Actions
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 mt-2 w-52 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                        <ul class="py-2 text-sm">
                            <!-- Export/Generate Report -->
                            <li>
                                <button wire:click="exportDatasetDetails"
                                    class="flex items-center w-full px-4 py-2 text-gray-700 hover:bg-gray-100 transition">
                                    <i class="bi bi-bar-chart-fill text-purple-500 mr-2"></i>
                                    Generate Report
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <a href="{{ route('manage-pydp-datasets') }}"
                    class="bg-blue-500 text-white px-3 py-2 rounded hover:bg-blue-600 transition">
                    <i class="bi bi-skip-backward"></i>
                </a>
            </div>
        </div>

        @include('livewire.user.session-flash')

        <div class="w-full overflow-x-auto">
            <table class="table-auto w-full text-left border border-gray-200 dark:border-gray-700 min-w-max">
                <thead class="bg-gray-100 dark:bg-slate-700 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-2 w-40">PYDP Center</th>
                        <th class="px-4 py-2 w-80">Level</th>
                        <th class="px-4 py-2 w-80">Indicator</th>
                        <th class="px-4 py-2 text-center">Year Data</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tableDatas as $row)
                        <tr class="align-top border-b dark:border-slate-700">
                            <td class="px-4 py-2 text-left text-xs align-top w-48">
                                <div class="break-words leading-tight">
                                    {{ $row->dimension->name }}
                                </div>
                            </td>
                            <td class="px-4 py-2 text-center text-xs align-top w-40">
                                <span class="inline-block px-2 py-1 bg-purple-50 dark:bg-purple-800 dark:text-white text-purple-800 rounded text-xs font-medium">
                                    {{ $row->indicator->level->title ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-left text-xs align-top w-80">
                                <div class="break-words leading-tight whitespace-normal">
                                    {{ $row->indicator->title }}
                                </div>
                            </td>
                            <td class="px-4 py-2 align-middle">
                                @php
                                    $yearData = $row->years->sortBy('year');
                                @endphp

                                @if ($yearData->count())
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-xs mx-auto min-w-max">
                                            <thead class="bg-gray-50 dark:bg-slate-700">
                                                <tr>
                                                    <th class="px-2 py-1 text-center whitespace-nowrap">Year</th>
                                                    <th class="px-2 py-1 text-center whitespace-nowrap">Baseline</th>
                                                    <th class="px-2 py-1 text-center whitespace-nowrap">Physical Target</th>
                                                    <th class="px-2 py-1 text-center whitespace-nowrap">Financial Target</th>
                                                    <th class="px-2 py-1 text-center whitespace-nowrap">Physical Actual</th>
                                                    <th class="px-2 py-1 text-center whitespace-nowrap">Financial Actual</th>
                                                    <th class="px-2 py-1 text-center whitespace-nowrap">Total</th>
                                                    <th class="px-2 py-1 text-center whitespace-nowrap">Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($yearData as $year)
                                                    <tr class="border-b dark:border-slate-700">
                                                        <td class="px-2 py-1 text-center font-medium">
                                                            {{ $year->year }}
                                                        </td>
                                                        <td class="px-2 py-1 text-center">
                                                            <span class="inline-block px-2 py-1 bg-blue-50 text-blue-800 dark:bg-blue-800 dark:text-white rounded text-xs font-medium">
                                                                {{ $year->baseline !== null ? number_format($year->baseline, 2) : '-' }}
                                                            </span>
                                                        </td>
                                                        <td class="px-2 py-1 text-center">
                                                            {{ $year->target_physical !== null ? number_format($year->target_physical, 2) : '-' }}
                                                        </td>
                                                        <td class="px-2 py-1 text-center">
                                                            {{ $year->target_financial !== null ? number_format($year->target_financial, 2) : '-' }}
                                                        </td>
                                                        <td class="px-2 py-1 text-center">
                                                            {{ $year->actual_physical !== null ? number_format($year->actual_physical, 2) : '-' }}
                                                        </td>
                                                        <td class="px-2 py-1 text-center">
                                                            {{ $year->actual_financial !== null ? number_format($year->actual_financial, 2) : '-' }}
                                                        </td>
                                                        <td class="px-2 py-1 text-center">
                                                            <span class="inline-block px-2 py-1 bg-green-50 text-green-800 dark:bg-green-800 dark:text-white rounded text-xs font-medium">
                                                                {{ $year->total !== null ? number_format($year->total, 2) : '-' }}
                                                            </span>
                                                        </td>
                                                        <td class="px-2 py-1 text-left max-w-[150px]">
                                                            @if($year->remarks)
                                                                <div class="truncate text-xs text-gray-700" title="{{ $year->remarks }}">
                                                                    {{ $year->remarks }}
                                                                </div>
                                                            @else
                                                                <span class="text-gray-400">-</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic block text-center">No data</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-gray-500">
                                No records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $tableDatas->links() }}
        </div>
    </div>
</div>