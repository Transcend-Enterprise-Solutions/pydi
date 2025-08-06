<div>
    <div class="w-full">
        <h2 class="text-xl font-bold mb-3 pt-0">PYDP ({{ $datasetInfo->name }})</h2>

        <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800 overflow-x-visible">
            <div class="flex justify-between items-center mb-4">
                <div class="flex gap-2 items-center">
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

            <div class="w-full">
                <table class="table-auto w-full text-left border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">PYDP Center</th>
                            <th class="px-4 py-2 border">Indicator</th>
                            <th class="px-4 py-2 border">Year Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tableDatas as $row)
                            <tr class="hover:bg-gray-50 align-middle">
                                <td class="px-4 py-2 border text-center text-xs align-middle">
                                    {{ $row->dimension->name }}</td>
                                <td class="px-4 py-2 border text-center text-xs align-middle">
                                    {{ $row->indicator->title }}</td>

                                <td class="px-4 py-2 border align-middle">
                                    @php
                                        $yearData = $row->years->sortBy('year');
                                    @endphp

                                    @if ($yearData->count())
                                        <table class="w-full text-xs border mx-auto">
                                            <thead class="bg-gray-50 text-gray-700">
                                                <tr>
                                                    <th class="border px-2 py-1 text-center">Year</th>
                                                    <th class="border px-2 py-1 text-center">Physical Target</th>
                                                    <th class="border px-2 py-1 text-center">Financial Target</th>
                                                    <th class="border px-2 py-1 text-center">Physical Actual</th>
                                                    <th class="border px-2 py-1 text-center">Financial Actual</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($yearData as $year)
                                                    <tr>
                                                        <td class="border px-2 py-1 text-center">{{ $year->year }}
                                                        </td>
                                                        <td class="border px-2 py-1 text-center">
                                                            {{ $year->target_physical !== null ? number_format($year->target_physical, 2) : '-' }}
                                                        </td>
                                                        <td class="border px-2 py-1 text-center">
                                                            {{ $year->target_financial !== null ? number_format($year->target_financial, 2) : '-' }}
                                                        </td>
                                                        <td class="border px-2 py-1 text-center">
                                                            {{ $year->actual_physical !== null ? number_format($year->actual_physical, 2) : '-' }}
                                                        </td>
                                                        <td class="border px-2 py-1 text-center">
                                                            {{ $year->actual_financial !== null ? number_format($year->actual_financial, 2) : '-' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
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

</div>
