<div class="w-full">
    <div class="w-full flex justify-center">
        <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800 overflow-x-visible">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">PYDI Datasets</h2>
                <div class="flex items-center gap-2">
                    <input type="text" wire:model.live="search" placeholder="Search..."
                        class="w-32 py-1 px-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                    <select wire:model.live="showEntries"
                        class="w-16 py-1 px-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <button wire:click="create()"
                        class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 transition">
                        <i class="bi bi-plus-lg"></i> Add Dataset
                    </button>
                </div>
            </div>

            @include('livewire.user.session-flash')

            <div class="overflow-x-auto">
                <table class="table-auto w-full text-left border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">#</th>
                            <th class="px-4 py-2 border">title</th>
                            <th class="px-4 py-2 border">Description</th>
                            <th class="px-4 py-2 border">Year</th>
                            <th class="px-4 py-2 border">Status</th>
                            <th class="px-4 py-2 border">Date</th>
                            <th class="px-4 py-2 border text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tableDatas as $index => $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border">{{ $tableDatas->firstItem() + $index }}</td>
                                <td class="px-4 py-2 border">{{ $row->name }}</td>
                                <td class="px-4 py-2 border">{{ Str::limit($row->description, 50) }}</td>
                                <td class="px-4 py-2 border">{{ $row->year }}</td>

                                <td class="px-4 py-2 border w-32">
                                    <div class="flex justify-start items-center gap-1">
                                        @if ($row->status === 'approved')
                                            <span
                                                class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Approved</span>
                                        @elseif ($row->status === 'rejected')
                                            <span
                                                class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded">Rejected</span>
                                        @elseif ($row->status === 'needs_revision')
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded">Needs
                                                Revision</span>
                                        @else
                                            <span
                                                class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded">Pending</span>
                                        @endif
                                        <!-- message -->
                                        @if ($row->finalized_at)
                                            <span wire:click="message({{ $row->id }})"
                                                class="inline-flex items-center justify-center w-8 h-8 text-blue-600 rounded-md cursor-pointer hover:bg-blue-200 transition">
                                                <i class="bi bi-chat-left-text"></i>
                                            </span>
                                        @endif

                                        @if ($row->is_submitted && $row->status === 'pending')
                                            <span
                                                class="inline-flex items-center justify-center w-8 h-8 text-blue-600 rounded-md hover:bg-blue-200 transition">
                                                <i class="bi bi-send-check"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-2 border text-sm text-gray-600 text-xs">
                                    <div>
                                        <span class="font-semibold text-gray-800">Created:</span>
                                        {{ $row->created_at->format('M d, Y') }}
                                    </div>
                                    @if ($row->finalized_at)
                                        <div>
                                            <span class="font-semibold text-gray-800">Finalized:</span>
                                            {{ \Carbon\Carbon::parse($row->finalized_at)->format('M d, Y') }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Action Buttons -->
                                <td class="px-4 py-2 border">
                                    <div class="flex justify-center gap-1">
                                        @if ($row->status !== 'approved' && $row->status !== 'rejected')
                                            @if (!$row->is_submitted)
                                                <!-- Send -->
                                                <span wire:click="confirmSend({{ $row->id }})"
                                                    class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-md cursor-pointer hover:bg-blue-200 transition">
                                                    <i class="bi bi-send"></i>
                                                </span>
                                            @endif

                                            <!-- Edit -->
                                            <span wire:click="edit({{ $row->id }})"
                                                class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-md cursor-pointer hover:bg-blue-200 transition">
                                                <i class="bi bi-pencil-square"></i>
                                            </span>
                                        @endif

                                        <!-- Monitor -->
                                        <a href="{{ route('pydi-dataset-details', $row->id) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-md cursor-pointer hover:bg-blue-200 transition">
                                            <i class="bi bi-clipboard-data"></i>
                                        </a>

                                        @if ($row->status !== 'approved' && $row->status !== 'rejected')
                                            <!-- Delete -->
                                            <span wire:click="confirmDelete({{ $row->id }})"
                                                class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-md cursor-pointer hover:bg-blue-200 transition">
                                                <i class="bi bi-trash"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-gray-500">
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
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <h3 class="text-lg font-bold mb-4">
                    {{ $editMode ? 'Edit Dataset' : 'Create New Dataset' }}
                </h3>

                @php $currentYear = now()->year; @endphp

                <div class="mb-3">
                    <label class="block text-sm font-medium">Name</label>
                    <input type="text" wire:model="name" class="border rounded w-full px-3 py-2"
                        placeholder="Enter Name">
                    @error('name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium">Description</label>
                    <textarea wire:model="description" class="border rounded w-full px-3 py-2" placeholder="Enter Description"></textarea>
                    @error('description')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Year</label>
                    <select wire:model.live="year"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        @php
                            $currentYear = date('Y');
                            $startYear = $currentYear - 4;
                            $endYear = $currentYear + 4;
                        @endphp

                        @for ($year = $startYear; $year <= $endYear; $year++)
                            <option value="{{ $year }}" {{ $year == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                    @error('year')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

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

    <!-- Message Modal -->
    @if ($showMessageModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <h3 class="text-lg font-bold mb-4">Feedback</h3>

                <div class="mb-4 text-gray-700">
                    {!! nl2br(e($feedbackMessage)) !!}
                </div>

                <div class="flex justify-end">
                    <button wire:click="$set('showMessageModal', false)"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showConfirmSend)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-sm p-6 text-center">
                <h3 class="text-lg font-bold mb-2">Confirm Send</h3>
                <p class="text-gray-600 mb-4">Are you sure you want to send this dataset?</p>

                <div class="flex justify-center gap-4">
                    <button wire:click="$set('showConfirmSend', false)"
                        class="px-4 py-2 border rounded hover:bg-gray-100">
                        Cancel
                    </button>
                    <button wire:click="sendConfirmed" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 flex items-center gap-2">
                        <span wire:loading.remove wire:target="sendConfirmed">Submit Dataset</span>
                        <span wire:loading wire:target="sendConfirmed" class="flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i> Loanding...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
