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

            <!-- Upload Form -->
            <div class="flex gap-2 items-center">
                <div x-data="{ open: false }" class="relative inline-block text-left">
                    <button @click="open = !open"
                        class="bg-blue-500 text-white px-3 py-2 rounded hover:bg-blue-600 transition flex items-center gap-2">
                        <i class="bi bi-list"></i> Actions
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 mt-2 w-72 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
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

                            <!-- Download Template -->
                            <li>
                                <button wire:click="$set('showFormatModal', true)"
                                    class="flex items-center w-full px-4 py-2 text-gray-700 hover:bg-gray-100 transition">
                                    <i class="bi bi-filetype-csv text-green-500 mr-2"></i>
                                    Download CSV/XLSX Template
                                </button>
                            </li>

                            @if ($datasetInfo->status !== 'approved')
                                <!-- Upload Data -->
                                <li>
                                    <button wire:click="$set('showImportModal', true)"
                                        class="flex items-center w-full px-4 py-2 text-gray-700 hover:bg-gray-100 transition">
                                        <i class="bi bi-cloud-upload text-indigo-500 mr-2"></i>
                                        Import Data
                                    </button>
                                </li>
                            @endif

                            <!-- Export/Generate Report -->
                            <li>
                                <button wire:click="$set('showExportModal', true)"
                                    class="flex items-center w-full px-4 py-2 text-gray-700 hover:bg-gray-100 transition">
                                    <i class="bi bi-bar-chart-fill text-purple-500 mr-2"></i>
                                    Generate Report
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <a href="{{ route('pydi-datasets') }}"
                    class="bg-blue-500 text-white px-3 py-2 rounded hover:bg-blue-600 transition">
                    <i class="bi bi-skip-backward"></i>
                </a>

            </div>
        </div>

        @include('livewire.user.session-flash')

        <!-- Table -->
        <div class="w-full">
            <table class="table-auto w-full text-left border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">Dimension</th>
                        <th class="px-4 py-2 border">Indicator</th>
                        <th class="px-4 py-2 border">Region</th>
                        <th class="px-4 py-2 border">Sex</th>
                        <th class="px-4 py-2 border">Age</th>
                        <th class="px-4 py-2 border">Value</th>

                        @if (($datasetInfo->status !== 'approved' && $datasetInfo->status !== 'rejected') || $datasetInfo->is_request_edit === 2)
                            <th class="px-4 py-2 border text-center">Actions</th>
                        @endif

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

                            @if (($datasetInfo->status !== 'approved' && $datasetInfo->status !== 'rejected') || $datasetInfo->is_request_edit === 2)
                                <td class="px-4 py-2 border text-center">
                                    <div x-data="{ open: false }" class="relative inline-block text-left">
                                        <!-- Dropdown Trigger -->
                                        <button @click="open = !open"
                                            class="w-8 h-8 flex items-center justify-center bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200 transition"
                                            aria-label="Toggle actions" title="More actions">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>

                                        <!-- Dropdown Menu -->
                                        <div x-show="open" @click.away="open = false" x-transition
                                            class="absolute z-50 right-0 mt-2 w-40 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-md shadow-xl overflow-hidden">
                                            <ul class="text-sm text-gray-700 dark:text-gray-200">
                                                <!-- Edit -->
                                                <li>
                                                    <button wire:click="edit({{ $detail->id }})"
                                                        class="w-full flex items-center gap-2 px-4 py-2 hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                                                        <i class="bi bi-pencil-square"></i> Edit
                                                    </button>
                                                </li>
                                                <!-- Delete -->
                                                <li>
                                                    <button wire:click="confirmDelete({{ $detail->id }})"
                                                        class="w-full flex items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-100 dark:hover:bg-red-700 dark:hover:text-white transition">
                                                        <i class="bi bi-trash"></i> Delete
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

    <!-- Edit and Create Modal -->
    @if ($showModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <h3 class="text-lg font-bold mb-4">
                    {{ $editMode ? 'Edit Dataset Details' : 'Add Dataset Details' }}
                </h3>

                <!-- Dimension -->
                <div class="mb-4">
                    <label class="block text-sm font-medium">Dimension</label>
                    <select wire:model.live="dimension" class="border rounded w-full px-3 py-2">
                        <option value="">Please Select</option>
                        @foreach ($dimensions as $row)
                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                        @endforeach
                    </select>
                    @error('dimension')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Indicator -->
                <div class="mb-4">
                    <label class="block text-sm font-medium">Indicator</label>
                    <select wire:model="indicator" class="border rounded w-full px-3 py-2">
                        <option value="">Please Select</option>
                        @foreach ($indicators as $row)
                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                        @endforeach
                    </select>
                    @error('indicator')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Region -->
                <div class="mb-4">
                    <label class="block text-sm font-medium">Region</label>
                    <select wire:model="region" class="border rounded w-full px-3 py-2">
                        <option value="">Please Select</option>
                        @foreach ($regions as $row)
                            <option value="{{ $row->id }}">{{ $row->region_description }}</option>
                        @endforeach
                    </select>
                    @error('region')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Age and Sex -->
                <div class="mb-4 flex gap-2">
                    <div class="w-1/2">
                        <label class="block text-sm font-medium">Age</label>
                        <input type="text" wire:model="age" class="border rounded w-full px-3 py-2"
                            placeholder="Enter Age">
                        @error('age')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="w-1/2">
                        <label class="block text-sm font-medium">Sex</label>
                        <select wire:model="sex" class="border rounded w-full px-3 py-2">
                            <option value="">Select</option>
                            @foreach ($gender as $row)
                                <option value="{{ $row }}">{{ $row }}</option>
                            @endforeach
                        </select>
                        @error('sex')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Value -->
                <div class="mb-4">
                    <label class="block text-sm font-medium">Value of Indicator</label>
                    <input type="number" wire:model="value" class="border rounded w-full px-3 py-2"
                        placeholder="Enter Value">
                    @error('value')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-2">
                    <button wire:click="$set('showModal', false)" class="px-4 py-2 border rounded">Cancel</button>
                    <button wire:click="save" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 flex items-center gap-2">
                        <span wire:loading.remove wire:target="save">{{ $editMode ? 'Update' : 'Save' }}</span>
                        <span wire:loading wire:target="save" class="flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i> {{ $editMode ? 'Updating...' : 'Saving...' }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Template Modal -->
    @if ($showFormatModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <h3 class="text-lg font-bold mb-4">Download CSV/XLSX Template</h3>

                <!-- Dimension Dropdown -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Dimension</label>
                    <select wire:model="selectedDimension" class="border rounded w-full px-3 py-2">
                        <option value="">-- Select Dimension --</option>
                        @foreach ($dimensions as $dimension)
                            <option value="{{ $dimension->id }}">{{ $dimension->name }}</option>
                        @endforeach
                    </select>
                    @error('selectedDimension')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-2 mt-6">
                    <button wire:click="$set('showFormatModal', false)" class="px-4 py-2 border rounded">
                        Cancel
                    </button>
                    <button wire:click="downloadTemplate" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                        <span wire:loading.remove wire:target="downloadTemplate">Download</span>
                        <span wire:loading wire:target="downloadTemplate">
                            <i class="fas fa-spinner fa-spin"></i> Preparing...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Upload Modal -->
    @if ($showImportModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <h3 class="text-lg font-bold mb-4">Upload Dataset Details</h3>

                <!-- Dimension Dropdown -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Dimension</label>
                    <select wire:model="selectedDimension" class="border rounded w-full px-3 py-2">
                        <option value="">-- Select Dimension --</option>
                        @foreach ($dimensions as $dimension)
                            <option value="{{ $dimension->id }}">{{ $dimension->name }}</option>
                        @endforeach
                    </select>
                    @error('selectedDimension')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <input type="file" wire:model="file" class="border rounded w-full px-3 py-2">
                    @error('file')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-2 mt-6">
                    <!-- Cancel Button -->
                    <button wire:click="$set('showImportModal', false)" class="px-4 py-2 border rounded">
                        Cancel
                    </button>

                    <!-- Upload Button -->
                    <div>
                        {{-- Show Upload button only when file is NOT uploading --}}
                        <span wire:loading.remove wire:target="file">
                            <button wire:click="import"
                                class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="import">Upload</span>
                                <span wire:loading wire:target="import">
                                    <i class="fas fa-spinner fa-spin"></i> Uploading...
                                </span>
                            </button>
                        </span>

                        {{-- Show "Uploading..." state when file IS uploading --}}
                        <span wire:loading wire:target="file">
                            <button class="px-4 py-2 bg-gray-400 text-white rounded" disabled>
                                Please Wait...
                            </button>
                        </span>
                    </div>
                </div>

            </div>
        </div>
    @endif

    <!-- Generate Modal -->
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
                    <button wire:click="$set('showExportModal', false)"
                        class="px-4 py-2 border rounded">Close</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-sm p-6 text-center">
                <h3 class="text-lg font-bold mb-2">Delete Dataset</h3>
                <p class="text-gray-600 mb-4">Are you sure you want to delete this dataset? This action cannot be
                    undone.</p>

                <div class="flex justify-center gap-4">
                    <button wire:click="$set('showDeleteModal', false)" class="px-4 py-2 border rounded">
                        Cancel
                    </button>
                    <button wire:click="delete" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 flex items-center gap-2">
                        <span wire:loading.remove wire:target="delete">Delete</span>
                        <span wire:loading wire:target="delete" class="flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i> Deleting...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
