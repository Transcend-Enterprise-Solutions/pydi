<div class="w-full">
    <div class="w-full flex justify-center">
        <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800 overflow-x-visible">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Covered Year (PYDP)</h2>
                <div class="flex items-center gap-2">
                    <input type="text" wire:model.live="search" placeholder="Search..."
                        class="w-52 py-2 px-3 text-sm border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                    <select wire:model.live="showEntries"
                        class="w-16 py-2 px-3 border text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <button wire:click="create()"
                        class="bg-blue-500 text-white text-sm py-2 px-3 rounded hover:bg-blue-600 transition">
                        <i class="bi bi-plus-lg mr-1"></i> Add Indicator
                    </button>
                </div>
            </div>

            @include('livewire.user.session-flash')

            <div class="w-full">
                <table class="table-auto w-full text-left border border-gray-200 dark:border-gray-700">
                    <thead class="bg-gray-100 dark:bg-slate-700">
                        <tr class="uppercase text-xs">
                            <th class="px-4 py-2">#</th>
                            <th class="px-4 py-2">Title</th>
                            <th class="px-4 py-2">Description</th>
                            <th class="px-4 py-2">Covered Year</th>
                            <th class="px-4 py-2">Created At</th>
                            <th class="px-4 py-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tableDatas as $index => $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 text-xs text-gray-700 dark:text-gray-200">
                                <td class="px-4 py-2">{{ $tableDatas->firstItem() + $index }}</td>
                                <td class="px-4 py-2">{{ $row->title }}</td>
                                <td class="px-4 py-2">{{ $row->content }}</td>
                                <td class="px-4 py-2">{{ $row->year_start . '  - ' . $row->year_end }}</td>
                                <td class="px-4 py-2">{{ $row->created_at->format('M d, Y') }}</td>

                                <!-- Action Buttons as Dropdown -->
                                <td class="px-4 py-2 text-center">
                                    <div x-data="{ open: false }" class="relative inline-block text-left">
                                        <!-- Dropdown Trigger -->
                                        <button @click="open = !open"
                                            class="w-8 h-8 flex items-center justify-center bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200 transition"
                                            aria-label="Toggle actions menu" title="More actions">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>

                                        <!-- Dropdown Menu -->
                                        <div x-show="open" @click.away="open = false" x-transition
                                            class="absolute z-50 right-0 mt-2 w-56 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-md shadow-xl overflow-hidden">
                                            <ul
                                                class="text-sm text-gray-700 dark:text-gray-200 divide-y divide-gray-100 dark:divide-slate-700">
                                                <li>
                                                    <button wire:click="edit({{ $row->id }})"
                                                        class="w-full flex items-center gap-2 px-4 py-2 hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                                                        <i class="bi bi-pencil-fill"></i> Edit Details
                                                    </button>
                                                </li>

                                                <li>
                                                    <button wire:click="confirmDelete({{ $row->id }})"
                                                        class="w-full flex items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-100 dark:hover:bg-red-700 dark:hover:text-white transition">
                                                        <i class="bi bi-trash-fill"></i> Delete Year Covered
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-gray-500">
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
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg w-full max-w-md p-6">
                <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100">
                    {{ $editMode ? 'Edit Covered Year' : 'Create New Covered Year' }}
                </h3>

                <div class="mb-3">
                    <label class="block text-sm font-medium">Title</label>
                    <input type="text" wire:model="title" class="border rounded w-full px-3 py-2 dark:bg-gray-700 dark:border-slate-700"
                        placeholder="Enter Title">
                    @error('title')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium">Description</label>
                    <textarea wire:model="description" class="border rounded w-full px-3 py-2 dark:bg-gray-700 dark:border-slate-700" placeholder="Enter Description"></textarea>
                    @error('description')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3 flex gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium">Year Start</label>
                        <input type="text" wire:model="yearStart" class="border rounded w-full px-3 py-2 dark:bg-gray-700 dark:border-slate-700"
                            placeholder="Enter start year">
                        @error('yearStart')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex-1">
                        <label class="block text-sm font-medium">Year End</label>
                        <input type="text" wire:model="yearEnd" class="border rounded w-full px-3 py-2 dark:bg-gray-700 dark:border-slate-700"
                            placeholder="Enter end year">
                        @error('yearEnd')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-5">
                    <button wire:click="$set('showModal', false)" class="px-4 py-2 border rounded dark:border-gray-700">Cancel</button>
                    <button wire:click="save" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 flex items-center gap-2">
                        <span wire:loading.remove wire:target="save">{{ $editMode ? 'Update' : 'Submit' }}</span>
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
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg w-full max-w-sm p-6 text-center">
                <h3 class="text-lg font-bold mb-2">Delete Year Coverd</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4 text-sm">Are you sure you want to delete this year coverd? This action cannot be
                    undone.</p>

                <div class="flex justify-center gap-4">
                    <button wire:click="$set('showDeleteModal', false)" class="px-4 py-2 border rounded dark:border-gray-700">
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
