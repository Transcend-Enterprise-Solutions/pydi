<div class="w-full">
    <h2 class="text-xl font-bold mb-3 pt-0">PYDI ({{ $datasetInfo->name }})</h2>

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
            <div class="flex gap-2 items-center">


                <button wire:click="$set('showExportModal', true)"
                    class="bg-blue-500 text-white px-3 py-2 rounded hover:bg-blue-600 transition">
                    <i class="bi bi-bar-chart-fill mr-2"></i>Generate Report
                </button>

                <a href="{{ route('manage-pydi-datasets') }}"
                    class="bg-blue-500 text-white px-3 py-2 rounded hover:bg-blue-600 transition">
                    <i class="bi bi-skip-backward"></i>
                </a>
            </div>
        </div>

        @include('livewire.user.session-flash')

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="table-auto w-full text-left border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">Dimension</th>
                        <th class="px-4 py-2 border">Indicator</th>
                        <th class="px-4 py-2 border">Region</th>
                        <th class="px-4 py-2 border">Sex</th>
                        <th class="px-4 py-2 border">Age</th>
                        <th class="px-4 py-2 border">Value</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($details as $detail)
                        <tr class="text-xs">
                            <td class="px-4 py-2 border">{{ $detail->dimension->name ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $detail->indicator->name ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $detail->region->region_description }}</td>
                            <td class="px-4 py-2 border">{{ $detail->sex }}</td>
                            <td class="px-4 py-2 border">{{ $detail->age }}</td>
                            <td class="px-4 py-2 border">{{ $detail->value }}</td>
                        </tr>
                    @empty

                        <tr>
                            <td colspan="8" class="text-center py-4 text-gray-500">No dataset details found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $details->links() }}
        </div>
    </div>

    <!-- Export Modal -->
    @if ($showExportModal ?? false)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
                <h3 class="text-lg font-bold mb-4">Export Dataset Details</h3>

                <div class="flex flex-col gap-4">
                    <p>Select a format to export your dataset:</p>
                    <div class="flex gap-2">
                        <button wire:click="export('csv')"
                            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Export as CSV</button>
                        <button wire:click="export('xlsx')"
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Export as XLSX</button>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button wire:click="$set('showExportModal', false)" class="px-4 py-2 border rounded">Close</button>
                </div>
            </div>
        </div>
    @endif

</div>
