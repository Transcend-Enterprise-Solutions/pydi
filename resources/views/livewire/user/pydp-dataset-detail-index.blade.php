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
                                @if ($datasetInfo->status !== 'approved')
                                    <!-- Manual Input -->
                                    <li>
                                        <button wire:click="create"
                                            class="flex items-center w-full px-4 py-2 text-gray-700 hover:bg-gray-100 transition">
                                            <i class="bi bi-pencil-square text-blue-500 mr-2"></i>
                                            Add Entry (Manual)
                                        </button>
                                    </li>
                                @endif

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

                    <a href="{{ route('pydp-datasets') }}"
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

                            @if (($datasetInfo->status !== 'approved' && $datasetInfo->status !== 'rejected') || $datasetInfo->is_request_edit === 2)
                                <th class="px-4 py-2 border text-center">Actions</th>
                            @endif
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

                                @if (($datasetInfo->status !== 'approved' && $datasetInfo->status !== 'rejected') || $datasetInfo->is_request_edit === 2)
                                    <td class="px-4 py-2 border text-center align-middle">
                                        <div x-data="{ open: false }" class="relative inline-block text-left">
                                            <button @click="open = !open"
                                                class="w-8 h-8 flex items-center justify-center bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200 transition mx-auto"
                                                aria-label="Toggle actions menu" title="More actions">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>

                                            <div x-show="open" @click.away="open = false" x-transition
                                                class="absolute z-50 right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-xl overflow-hidden">
                                                <ul class="text-sm text-gray-700 divide-y divide-gray-100">
                                                    <li>
                                                        <button wire:click="edit({{ $row->id }})"
                                                            class="w-full flex gap-2 px-4 py-2 hover:bg-gray-100 transition">
                                                            <i class="bi bi-pencil-fill"></i> Edit Details
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button wire:click="confirmDelete({{ $row->id }})"
                                                            class="w-full flex gap-2 px-4 py-2 text-red-600 hover:bg-red-100 transition">
                                                            <i class="bi bi-trash-fill"></i> Delete Indicator
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                @endif
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

    <!-- Modal (Used for Create & Edit) -->
    @if ($showModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 overflow-y-auto">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-5xl mx-4 my-8 p-8">
                <h3 class="text-2xl font-bold mb-6">
                    {{ $editMode ? 'Edit Dataset Details' : 'Create New Dataset Details' }}
                </h3>

                {{-- Form Section --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Dimension --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Dimension</label>
                        <select wire:model="dimension" class="border rounded w-full px-3 py-2">
                            <option value="">Please Select</option>
                            @foreach ($dimensions as $row)
                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                            @endforeach
                        </select>
                        @error('dimension')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Indicator --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Indicator</label>
                        <select wire:model="indicator_id" class="border rounded w-full px-3 py-2">
                            <option value="">Please Select</option>
                            @foreach ($indicators as $row)
                                <option value="{{ $row->id }}">{{ $row->title }}</option>
                            @endforeach
                        </select>
                        @error('indicator_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Scrollable Section for Year Inputs --}}
                <div class="max-h-[60vh] overflow-y-auto mt-6 pr-2">
                    @foreach ($yearRange as $year)
                        <div class="border rounded p-4 mb-4 bg-gray-50">
                            <h4 class="text-lg font-semibold mb-3">Year {{ $year }}</h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Physical Target</label>
                                    <input type="number" wire:model="yearData.{{ $year }}.physical_target"
                                        class="border rounded w-full px-3 py-2"
                                        placeholder="Physical target {{ $year }}">
                                    @error("yearData.$year.physical_target")
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Financial Target</label>
                                    <input type="number" wire:model="yearData.{{ $year }}.financial_target"
                                        class="border rounded w-full px-3 py-2"
                                        placeholder="Financial target {{ $year }}">
                                    @error("yearData.$year.financial_target")
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Physical Actual</label>
                                    <input type="number" wire:model="yearData.{{ $year }}.physical_actual"
                                        class="border rounded w-full px-3 py-2"
                                        placeholder="Physical actual {{ $year }}">
                                    @error("yearData.$year.physical_actual")
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Financial Actual</label>
                                    <input type="number" wire:model="yearData.{{ $year }}.financial_actual"
                                        class="border rounded w-full px-3 py-2"
                                        placeholder="Financial actual {{ $year }}">
                                    @error("yearData.$year.financial_actual")
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Footer Buttons --}}
                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="$set('showModal', false)"
                        class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition">Cancel</button>
                    <button wire:click="save" wire:loading.attr="disabled"
                        class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition flex items-center gap-2">
                        <span wire:loading.remove wire:target="save">{{ $editMode ? 'Update' : 'Submit' }}</span>
                        <span wire:loading wire:target="save">
                            <i class="fas fa-spinner fa-spin"></i> {{ $editMode ? 'Updating...' : 'Saving...' }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif



</div>
