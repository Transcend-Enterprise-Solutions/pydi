<div class="w-full">
    <div class="w-full flex justify-center">
        <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800 overflow-x-visible">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Manage PYDI Datasets</h2>
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
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                @php
                    $stats = [
                        'pending' => ['count' => 0, 'color' => 'bg-yellow-100 text-yellow-800'],
                        'approved' => ['count' => 0, 'color' => 'bg-green-100 text-green-800'],
                        'rejected' => ['count' => 0, 'color' => 'bg-red-100 text-red-800'],
                        'needs_revision' => ['count' => 0, 'color' => 'bg-blue-100 text-blue-800'],
                    ];

                    foreach ($tableDatas as $data) {
                        if (isset($stats[$data->status])) {
                            $stats[$data->status]['count']++;
                        }
                    }
                @endphp

                <!-- Pending -->
                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 dark:bg-yellow-900/40 rounded-lg">
                            <i class="bi bi-clock text-yellow-600 dark:text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Pending</p>
                            <p class="text-lg font-semibold text-yellow-900 dark:text-yellow-100">
                                {{ $stats['pending']['count'] }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Approved -->
                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 dark:bg-green-900/40 rounded-lg">
                            <i class="bi bi-check-circle text-green-600 dark:text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">Approved</p>
                            <p class="text-lg font-semibold text-green-900 dark:text-green-100">
                                {{ $stats['approved']['count'] }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Rejected -->
                <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 dark:bg-red-900/40 rounded-lg">
                            <i class="bi bi-x-circle text-red-600 dark:text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800 dark:text-red-200">Rejected</p>
                            <p class="text-lg font-semibold text-red-900 dark:text-red-100">
                                {{ $stats['rejected']['count'] }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Needs Revision -->
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/40 rounded-lg">
                            <i class="bi bi-arrow-clockwise text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Needs Revision</p>
                            <p class="text-lg font-semibold text-blue-900 dark:text-blue-100">
                                {{ $stats['needs_revision']['count'] }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>


            @include('livewire.user.session-flash')

            <div class="overflow-x-auto">
                <table class="table-auto w-full text-left border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">Account Name</th>
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
                                <td class="px-4 py-2 border">{{ $row->user->name }}</td>
                                <td class="px-4 py-2 border">{{ $row->name }}</td>
                                <td class="px-4 py-2 border">{{ Str::limit($row->description, 50) }}</td>
                                <td class="px-4 py-2 border">{{ $row->year }}</td>

                                <td class="px-4 py-2 border">
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


                                        @if ($row->finalized_at)
                                            <div class="relative group inline-flex">
                                                <span wire:click="message({{ $row->id }})"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-blue-600 rounded-md cursor-pointer hover:bg-blue-200 transition">
                                                    <i class="bi bi-chat-left-text"></i>
                                                </span>
                                                <div
                                                    class="absolute z-10 hidden group-hover:block -top-2 left-1/2 transform -translate-x-1/2 -translate-y-full">
                                                    <div
                                                        class="px-2 py-1 text-xs text-white bg-gray-800 rounded whitespace-nowrap">
                                                        View feedback
                                                        <div
                                                            class="absolute w-2 h-2 bg-gray-800 transform rotate-45 -bottom-1 left-1/2 -translate-x-1/2">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($row->file_path)
                                            <div class="relative group inline-flex">
                                                <a href="{{ Storage::url($row->file_path) }}" target="_blank"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-blue-600 rounded-md cursor-pointer hover:bg-blue-200 transition">
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                </a>
                                                <div
                                                    class="absolute z-10 hidden group-hover:block -top-2 left-1/2 transform -translate-x-1/2 -translate-y-full">
                                                    <div
                                                        class="px-2 py-1 text-xs text-white bg-gray-800 rounded whitespace-nowrap">
                                                        View attached file
                                                        <div
                                                            class="absolute w-2 h-2 bg-gray-800 transform rotate-45 -bottom-1 left-1/2 -translate-x-1/2">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($row->is_request_edit === 1)
                                            <!-- Edit Request Button with Tooltip -->
                                            <div class="relative group inline-flex">
                                                <span wire:click="showEditRequest({{ $row->id }})"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-yellow-600 rounded-md cursor-pointer hover:bg-yellow-100 transition">
                                                    <i class="bi bi-clock-history"></i>
                                                </span>
                                                <div
                                                    class="absolute z-10 hidden group-hover:block -top-2 left-1/2 transform -translate-x-1/2 -translate-y-full">
                                                    <div
                                                        class="px-2 py-1 text-xs text-white bg-gray-800 rounded whitespace-nowrap">
                                                        Pending Edit Request
                                                        <div
                                                            class="absolute w-2 h-2 bg-gray-800 transform rotate-45 -bottom-1 left-1/2 -translate-x-1/2">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                </td>

                                <td class="px-4 py-2 border text-sm text-gray-600">
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
                                    <div class="flex justify-center gap-2">

                                        @if ($row->status !== 'approved' && $row->status !== 'rejected')
                                            <!-- Take Action -->
                                            <span wire:click="openActionModal({{ $row->id }})"
                                                class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-md cursor-pointer hover:bg-blue-200 transition"
                                                title="Take Action">
                                                <i class="bi bi-gear"></i>
                                            </span>
                                            <!-- Edit -->
                                            <span wire:click="edit({{ $row->id }})"
                                                class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-md cursor-pointer hover:bg-blue-200 transition">
                                                <i class="bi bi-pencil-square"></i>
                                            </span>
                                        @endif

                                        <!-- Monitor -->
                                        <a href="{{ route('manage-pydi-dataset-details', $row->id) }}"
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
                    <label class="block text-sm font-medium">Year</label>
                    <select wire:model="year" class="border rounded w-full px-3 py-2">
                        <option value="">Select Year</option>
                        @for ($i = 0; $i < 5; $i++)
                            <option value="{{ $currentYear + $i }}">{{ $currentYear + $i }}</option>
                        @endfor
                    </select>
                    @error('year')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
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

    <!-- Action Confirmation Modal -->
    @if ($showActionModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <h3 class="text-lg font-bold mb-4">Take Action on Dataset</h3>

                <!-- Status Dropdown -->
                <div class="mb-3">
                    <label class="block text-sm font-medium">Status</label>
                    <select wire:model="action_status" class="border rounded w-full px-3 py-2">
                        <option value="">Select Status</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="needs_revision">Needs Revision</option>
                        <option value="pending">Pending</option>
                    </select>
                    @error('action_status')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Feedback -->
                <div class="mb-3">
                    <label class="block text-sm font-medium">Feedback</label>
                    <textarea wire:model="action_feedback" class="border rounded w-full px-3 py-2"
                        placeholder="Leave feedback for the user (optional)..."></textarea>
                    @error('action_feedback')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-2">
                    <button wire:click="$set('showActionModal', false)"
                        class="px-4 py-2 border rounded">Cancel</button>
                    <button wire:click="submitAction" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 flex items-center gap-2">
                        <span wire:loading.remove wire:target="submitAction">Save Action</span>
                        <span wire:loading wire:target="submitAction" class="flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i> Saving...
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

    <!-- Edit Request Approval Modal -->
    @if ($showEditRequestModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="w-full max-w-md p-6 mx-4 bg-white rounded-lg shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Process Edit Request</h3>
                    <button wire:click="$set('showEditRequestModal', false)" title="Close"
                        class="text-gray-400 hover:text-gray-600 transition">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="mb-4">
                    <p class="text-sm text-gray-600">
                        This entry has requested edits. Please review and choose to approve or reject the request.
                    </p>
                </div>

                <div class="flex justify-between gap-3">
                    <button wire:click="processEditRequest('reject')" title="Reject the edit request"
                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none transition">
                        <i class="bi bi-x-circle mr-1"></i> Reject
                    </button>
                    <button wire:click="processEditRequest('approve')" title="Approve the edit request"
                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none transition">
                        <i class="bi bi-check-circle mr-1"></i> Approve
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
