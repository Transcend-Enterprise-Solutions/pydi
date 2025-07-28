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

                                <td class="px-4 py-2 border w-auto">
                                    <div class="flex justify-start items-center">
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
                                            <!-- Message Button with Tooltip -->
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

                                        @if ($row->is_submitted && $row->status === 'pending')
                                            <!-- Send Check Button with Tooltip -->
                                            <div class="relative group inline-flex">
                                                <span
                                                    class="inline-flex items-center justify-center w-8 h-8 text-blue-600 rounded-md hover:bg-blue-200 transition">
                                                    <i class="bi bi-send-check"></i>
                                                </span>
                                                <div
                                                    class="absolute z-10 hidden group-hover:block -top-2 left-1/2 transform -translate-x-1/2 -translate-y-full">
                                                    <div
                                                        class="px-2 py-1 text-xs text-white bg-gray-800 rounded whitespace-nowrap">
                                                        Submitted
                                                        <div
                                                            class="absolute w-2 h-2 bg-gray-800 transform rotate-45 -bottom-1 left-1/2 -translate-x-1/2">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @include('livewire.user.request-statuses')
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
                                                <!-- Submit Button with Tooltip -->
                                                <div class="relative group">
                                                    <span wire:click="confirmSend({{ $row->id }})"
                                                        class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-md cursor-pointer hover:bg-blue-200 transition">
                                                        <i class="bi bi-send-arrow-up"></i>
                                                    </span>
                                                    <div
                                                        class="absolute z-10 hidden group-hover:flex -top-2 left-1/2 transform -translate-x-1/2 -translate-y-full">
                                                        <div
                                                            class="px-2 py-1 text-xs text-white bg-gray-800 rounded whitespace-nowrap">
                                                            Submit for review
                                                            <div
                                                                class="absolute w-2 h-2 bg-gray-800 transform rotate-45 -bottom-1 left-1/2 -translate-x-1/2">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Edit Button with Tooltip -->
                                            <div class="relative group">
                                                <span wire:click="edit({{ $row->id }})"
                                                    class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-md cursor-pointer hover:bg-blue-200 transition">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </span>
                                                <div
                                                    class="absolute z-10 hidden group-hover:flex -top-2 left-1/2 transform -translate-x-1/2 -translate-y-full">
                                                    <div
                                                        class="px-2 py-1 text-xs text-white bg-gray-800 rounded whitespace-nowrap">
                                                        Edit entry
                                                        <div
                                                            class="absolute w-2 h-2 bg-gray-800 transform rotate-45 -bottom-1 left-1/2 -translate-x-1/2">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($row->status === 'approved' && !$row->is_request_edit)
                                            <!-- Request Edit Button (shown only for approved/rejected entries) -->
                                            <div class="relative group">
                                                <span wire:click="requestEdit({{ $row->id }})"
                                                    class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-md cursor-pointer hover:bg-blue-200 transition">
                                                    <i class="bi bi-file-text-fill"></i>
                                                </span>
                                                <div
                                                    class="absolute z-10 hidden group-hover:flex -top-2 left-1/2 transform -translate-x-1/2 -translate-y-full">
                                                    <div
                                                        class="px-2 py-1 text-xs text-white bg-gray-800 rounded whitespace-nowrap">
                                                        Request edit
                                                        <div
                                                            class="absolute w-2 h-2 bg-gray-800 transform rotate-45 -bottom-1 left-1/2 -translate-x-1/2">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Details Button with Tooltip -->
                                        <div class="relative group">
                                            <a href="{{ route('pydi-dataset-details', $row->id) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-md cursor-pointer hover:bg-blue-200 transition">
                                                <i class="bi bi-file-earmark-bar-graph-fill"></i>
                                            </a>
                                            <div
                                                class="absolute z-10 hidden group-hover:flex -top-2 left-1/2 transform -translate-x-1/2 -translate-y-full">
                                                <div
                                                    class="px-2 py-1 text-xs text-white bg-gray-800 rounded whitespace-nowrap">
                                                    View details
                                                    <div
                                                        class="absolute w-2 h-2 bg-gray-800 transform rotate-45 -bottom-1 left-1/2 -translate-x-1/2">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($row->status !== 'approved' && $row->status !== 'rejected')
                                            <!-- Delete Button with Tooltip -->
                                            <div class="relative group">
                                                <span wire:click="confirmDelete({{ $row->id }})"
                                                    class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-md cursor-pointer hover:bg-blue-200 transition">
                                                    <i class="bi bi-trash-fill"></i>
                                                </span>
                                                <div
                                                    class="absolute z-10 hidden group-hover:flex -top-2 left-1/2 transform -translate-x-1/2 -translate-y-full">
                                                    <div
                                                        class="px-2 py-1 text-xs text-white bg-gray-800 rounded whitespace-nowrap">
                                                        Delete entry
                                                        <div
                                                            class="absolute w-2 h-2 bg-gray-800 transform rotate-45 -bottom-1 left-1/2 -translate-x-1/2">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
                    <select wire:model="year"
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
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 text-center">
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Confirm Send</h3>
                <p class="text-gray-600 mb-5">Are you sure you want to send this dataset?</p>

                {{-- Optional file attachment --}}
                <div class="mb-5 text-left">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Optional Attachment</label>
                    <input type="file" wire:model="file" class="border rounded w-full px-3 py-2">
                    @error('file')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-center gap-3">
                    <button wire:click="$set('showConfirmSend', false)"
                        class="px-4 py-2 text-gray-700 border rounded-lg hover:bg-gray-100 transition">
                        Cancel
                    </button>
                    <button wire:click="sendConfirmed" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="sendConfirmed">Submit Dataset</span>
                        <span wire:loading wire:target="sendConfirmed" class="flex items-center gap-1">
                            <i class="fas fa-spinner fa-spin"></i>
                            Loading...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Request Edit Modal -->
    @if ($showRequestEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="w-full max-w-md p-6 mx-4 bg-white rounded-lg shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Request Edit</h3>
                    <button wire:click="$set('showRequestEditModal', false)"
                        class="text-gray-400 hover:text-gray-500">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="mb-4">
                    <p class="text-sm text-gray-600">
                        Are you sure you want to request an edit for this entry?
                        An administrator will review your request.
                    </p>
                </div>

                <div class="flex justify-end space-x-3">
                    <button wire:click="$set('showRequestEditModal', false)"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none">
                        Cancel
                    </button>
                    <button wire:click="confirmRequestEdit" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="confirmRequestEdit">Submit Request</span>
                        <span wire:loading wire:target="confirmRequestEdit" class="flex items-center gap-1">
                            <i class="fas fa-spinner fa-spin"></i>
                            Loading...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
