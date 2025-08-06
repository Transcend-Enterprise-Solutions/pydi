<div>
    <div class="w-full flex justify-center">
        <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800 overflow-x-visible">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">PYDP Datasets</h2>
                <div class="flex items-center gap-2">
                    <input type="text" wire:model.live="search" placeholder="Search..."
                        class="w-52 py-2 px-3 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                    <select wire:model.live="showEntries"
                        class="w-16 py-2 px-3 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <button wire:click="create()"
                        class="bg-blue-500 text-white py-2 px-3 rounded hover:bg-blue-600 transition">
                        <i class="bi bi-plus-lg mr-1"></i> Add Dataset
                    </button>
                </div>
            </div>

            @include('livewire.user.session-flash')

            <div class="w-full">
                <table class="table-auto w-full text-left border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">#</th>
                            <th class="px-4 py-2 border">Title</th>
                            <th class="px-4 py-2 border">Description</th>
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
                                <td class="px-4 py-2 border">{{ $row->description }}</td>
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

                                <!-- Action Buttons as Dropdown -->
                                <td class="px-4 py-2 border text-center">
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

                                                @if ($row->status !== 'approved' && $row->status !== 'rejected')
                                                    @if (!$row->is_submitted)
                                                        <li>
                                                            <button wire:click="confirmSend({{ $row->id }})"
                                                                class="w-full flex items-center gap-2 px-4 py-2 hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                                                                <i class="bi bi-send-arrow-up"></i> Submit for Review
                                                            </button>
                                                        </li>
                                                    @endif

                                                    <li>
                                                        <button wire:click="edit({{ $row->id }})"
                                                            class="w-full flex items-center gap-2 px-4 py-2 hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                                                            <i class="bi bi-pencil-fill"></i> Edit Details
                                                        </button>
                                                    </li>
                                                @endif

                                                @if ($row->status === 'approved' && !$row->is_request_edit)
                                                    <li>
                                                        <button wire:click="requestEdit({{ $row->id }})"
                                                            class="w-full flex items-center gap-2 px-4 py-2 hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                                                            <i class="bi bi-file-text-fill"></i> Request Edit
                                                        </button>
                                                    </li>
                                                @endif

                                                <li>
                                                    <a href="{{ route('pydp-dataset-details', $row->id) }}"
                                                        class="w-full flex items-center gap-2 px-4 py-2 hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                                                        <i class="bi bi-file-earmark-bar-graph-fill"></i> Manage
                                                        Datasets
                                                    </a>
                                                </li>

                                                @if ($row->status !== 'approved' && $row->status !== 'rejected')
                                                    <li>
                                                        <button wire:click="confirmDelete({{ $row->id }})"
                                                            class="w-full flex items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-100 dark:hover:bg-red-700 dark:hover:text-white transition">
                                                            <i class="bi bi-trash-fill"></i> Delete Dataset
                                                        </button>
                                                    </li>
                                                @endif
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
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <h3 class="text-lg font-bold mb-4">
                    {{ $editMode ? 'Edit Dataset' : 'Create New Dataset' }}
                </h3>

                <div class="mb-3">
                    <label class="block text-sm font-medium">Title</label>
                    <input type="text" wire:model="title" class="border rounded w-full px-3 py-2"
                        placeholder="Enter Title">
                    @error('title')
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

                <!-- Type -->
                <div class="mb-4">
                    <label class="block text-sm font-medium">Year Covered</label>
                    <select wire:model="type" class="border rounded w-full px-3 py-2">
                        <option value="">Please Select</option>
                        @foreach ($types as $row)
                            <option value="{{ $row->id }}">
                                {{ $row->title . ' | ' . $row->year_start . '- ' . $row->year_end }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end gap-2 pt-5">
                    <button wire:click="$set('showModal', false)" class="px-4 py-2 border rounded">Cancel</button>
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

    <!-- Confirm Submit Modal -->
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
