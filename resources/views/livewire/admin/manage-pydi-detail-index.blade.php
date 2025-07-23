<div class="w-full">
    <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800 overflow-x-visible">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">PYDI ({{ $datasetInfo->name }})</h2>

            <!-- Upload Form -->
            <div class="flex gap-2 items-center">
                <input type="text" wire:model.live="search" placeholder="Search..."
                    class="w-32 py-1 px-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                <select wire:model.live="showEntries"
                    class="w-16 py-1 px-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>

                <button wire:click="$set('showExportModal', true)"
                    class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 transition">
                    <i class="bi bi-file-arrow-up"></i> Generate
                </button>

                <button onclick="window.history.back()"
                    class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 transition">
                    <i class="bi bi-skip-backward"></i>
                </button>

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
                        <th class="px-4 py-2 border">Content</th>
                        <th class="px-4 py-2 border">Action</th>
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
                            <td class="px-4 py-2 border">{{ Str::limit($detail->content, 50) }}</td>
                            {{-- <td class="px-4 py-2 border">{{ $detail->created_at->format('M d, Y') }}</td> --}}
                            <!-- Action Buttons -->
                            <td class="px-4 py-2 border">
                                <div class="flex justify-center gap-2">
                                    <!-- Edit -->
                                    <span wire:click="edit({{ $detail->id }})"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-md cursor-pointer hover:bg-blue-200 transition">
                                        <i class="bi bi-pencil-square"></i>
                                    </span>
                                    <!-- Delete -->
                                    <span wire:click="confirmDelete({{ $detail->id }})"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-600 rounded-md cursor-pointer hover:bg-red-200 transition">
                                        <i class="bi bi-trash"></i>
                                    </span>
                                </div>
                            </td>
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

    <!-- Edit Modal -->
    @if ($showModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <h3 class="text-lg font-bold mb-4">Edit Dataset Details</h3>

                <!-- Dimension -->
                <div class="mb-3">
                    <label class="block text-sm font-medium">Dimension</label>
                    <select wire:model.live="edit_dimension" class="border rounded w-full px-3 py-2">
                        <option value="">Please Select</option>
                        @foreach ($dimensions as $row)
                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                        @endforeach
                    </select>
                    @error('edit_dimension')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Indicator -->
                <div class="mb-3">
                    <label class="block text-sm font-medium">Indicator</label>
                    <select wire:model="edit_indicator" class="border rounded w-full px-3 py-2">
                        <option value="">Please Select</option>
                        @foreach ($indicators as $row)
                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                        @endforeach
                    </select>
                    @error('edit_indicator')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Region -->
                <div class="mb-3">
                    <label class="block text-sm font-medium">Region</label>
                    <select wire:model="edit_region" class="border rounded w-full px-3 py-2">
                        <option value="">Please Select</option>
                        @foreach ($regions as $row)
                            <option value="{{ $row->id }}">{{ $row->region_description }}</option>
                        @endforeach
                    </select>
                    @error('edit_region')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Age and Sex -->
                <div class="mb-3 flex gap-2">
                    <div class="w-1/2">
                        <label class="block text-sm font-medium">Age</label>
                        <input type="text" wire:model="edit_age" class="border rounded w-full px-3 py-2"
                            placeholder="Enter Age">
                        @error('edit_age')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="w-1/2">
                        <label class="block text-sm font-medium">Sex</label>
                        <select wire:model="edit_sex" class="border rounded w-full px-3 py-2">
                            <option value="">Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                        @error('edit_sex')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Value of Indicator -->
                <div class="mb-3">
                    <label class="block text-sm font-medium">Value of Indicator</label>
                    <textarea wire:model="edit_content" class="border rounded w-full px-3 py-2" placeholder="Enter Value"></textarea>
                    @error('edit_content')
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
