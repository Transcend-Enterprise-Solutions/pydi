<div x-data="{
    isModalOpen: false,
    isEditMode: false,
    confirmingAction: @entangle('confirmingAction'),
    openEmailModal: @entangle('openEmailModal'),
    actionType: @entangle('actionType'),
    selectedTab: @entangle('selectedTab')
}" x-cloak>

    <div class="w-full bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md">
        <h1 class="text-lg font-bold text-center text-black dark:text-white mb-6">Agency Representatives</h1>

        <div class="mb-6 flex flex-col sm:flex-row items-end justify-between">
            <div class="w-full sm:w-1/3 sm:mr-4">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                <input type="text" id="search" wire:model.live="search"
                       class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-300 rounded-md
                       dark:border-gray-600 dark:text-gray-300 dark:bg-gray-700
                       focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter representative name">
            </div>

            <div class="w-full sm:w-1/3">
                <label for="statusFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select id="statusFilter" wire:model.live="statusFilter"
                        class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-300 rounded-md
                        dark:border-gray-600 dark:text-gray-300 dark:bg-gray-700
                        focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Status</option>
                    <option value="0">Pending</option>
                    <option value="1">Approved</option>
                    <option value="3">Deactivated</option>
                </select>
            </div>
        </div>

        @include('livewire.user.session-flash')

        <!-- Tabs -->
        <div class="w-full mb-4">
            <div class="flex gap-2 overflow-x-auto border-b border-gray-200 dark:border-gray-700" role="tablist">
                <button wire:click="setTab('all')"
                    :class="{'font-bold text-blue-600 border-b-2 border-blue-600 dark:text-blue-400 dark:border-blue-400': selectedTab === 'all', 'text-gray-500 font-medium dark:text-gray-400': selectedTab !== 'all'}"
                    class="h-min px-4 py-2 text-sm" role="tab">All</button>
                <button wire:click="setTab('active')"
                    :class="{'font-bold text-blue-600 border-b-2 border-blue-600 dark:text-blue-400 dark:border-blue-400': selectedTab === 'active', 'text-gray-500 font-medium dark:text-gray-400': selectedTab !== 'active'}"
                    class="h-min px-4 py-2 text-sm" role="tab">Active</button>
                <button wire:click="setTab('inactive')"
                    :class="{'font-bold text-blue-600 border-b-2 border-blue-600 dark:text-blue-400 dark:border-blue-400': selectedTab === 'inactive', 'text-gray-500 font-medium dark:text-gray-400': selectedTab !== 'inactive'}"
                    class="h-min px-4 py-2 text-sm" role="tab">Inactive</button>
            </div>
        </div>

        <div class="mb-4 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <button wire:click="toggleBulkSelect"
                        class="px-4 py-2 {{ $bulkSelectMode ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white text-sm rounded-md transition-colors">
                    {{ $bulkSelectMode ? 'Cancel Selection' : 'Bulk Select' }}
                </button>
                
                @if($bulkSelectMode && count($selectedUsers) > 0)
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            {{ count($selectedUsers) }} selected
                        </span>
                        <div class="relative">
                             <select wire:model.live="bulkAction" class="text-sm border rounded w-full px-3 py-2 dark:bg-slate-700">
                                <option value="">-- Select Bulk Action --</option>
                                <option value="approve">Approve</option>
                                <option value="reject">Reject</option>
                                <option value="deactivate">Deactivate</option>
                                <option value="email">Send Email</option>
                            </select>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Table -->
        <div class="mt-4 overflow-x-auto text-sm">
            <table class="min-w-full bg-white dark:bg-gray-800 overflow-hidden">
                <thead class="bg-gray-50 dark:bg-gray-700 rounded-xl">
                    <tr class="whitespace-nowrap">
                        @if($bulkSelectMode)
                            <th class="px-4 py-2 text-left w-12">
                                <input type="checkbox" wire:model.live="selectAll"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                        @endif
                        <th class="px-4 py-2 uppercase font-medium text-left">Name</th>
                        <th class="px-4 py-2 uppercase font-medium text-left">Email</th>
                        <th class="px-4 py-2 uppercase font-medium text-left">Contact</th>
                        <th class="px-4 py-2 uppercase font-medium text-left">Agency</th>
                        <th class="px-4 py-2 uppercase font-medium text-center">Status</th>
                        <th class="px-4 py-2 uppercase font-medium text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                    <tr class="border-b border-gray-200 dark:border-gray-700 whitespace-nowrap hover:bg-gray-50 dark:hover:bg-gray-700">
                        @if($bulkSelectMode)
                            <td class="px-4 py-2">
                                <input type="checkbox" wire:model.live="selectedUsers" value="{{ $user->id }}"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                        @endif
                        <td class="px-4 py-2 text-left text-gray-800 dark:text-gray-200">
                            {{ $user->userData->first_name ?? '' }} {{ $user->userData->last_name ?? '' }}
                        </td>
                        <td class="px-4 py-2 text-left text-gray-600 dark:text-gray-300">{{ $user->email }}</td>
                        <td class="px-4 py-2 text-left text-gray-600 dark:text-gray-300">{{ $user->userData->mobile_number ?? '' }}</td>
                        <td class="px-4 py-2 text-left text-gray-600 dark:text-gray-300">{{ $user->userData->government_agency ?? '' }}</td>
                        <td class="px-4 py-2 text-center">
                            @switch($user->active_status)
                                @case(0)
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">Pending</span>
                                    @break
                                @case(1)
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">Approved</span>
                                    @break
                                @case(3)
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Deactivated</span>
                                    @break
                            @endswitch
                        </td>
                        <td class="px-4 py-2 text-center">
                            <div class="flex justify-center space-x-3">
                                @if($user->active_status == 0) {{-- Pending --}}
                                    <button wire:click="confirmAction({{ $user->id }}, 'approve')"
                                            class="text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300"
                                            title="Approve">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                    <button wire:click="confirmAction({{ $user->id }}, 'reject')"
                                            class="text-rose-600 hover:text-rose-800 dark:text-rose-400 dark:hover:text-rose-300"
                                            title="Reject">
                                        <i class="fas fa-times-circle"></i>
                                    </button>

                                @elseif($user->active_status == 1) {{-- Approved --}}
                                    <button wire:click="confirmAction({{ $user->id }}, 'deactivate')"
                                            class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300"
                                            title="Deactivate">
                                        <i class="fas fa-ban"></i>
                                    </button>

                                @elseif($user->active_status == 3) {{-- Deactivated --}}
                                    <button wire:click="confirmAction({{ $user->id }}, 'approve')"
                                            class="text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300"
                                            title="Reactivate">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                @endif

          
                                <button wire:click="confirmAction({{ $user->id }}, 'email')"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                        title="Send Email">
                                    <i class="bi bi-envelope-arrow-up"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">
                            No representatives found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Action Confirmation Modal -->
    <div x-show="confirmingAction"
        class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-40 flex items-center justify-center">
        <div @click.away="confirmingAction = false" x-show="confirmingAction"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4"
            class="relative bg-white dark:bg-gray-800 p-6 mx-auto max-w-lg rounded-2xl shadow-xl">
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200" x-text="
                    actionType === 'approve' ? 'Confirm Approval' :
                    actionType === 'reject' ? 'Confirm Rejection' :
                    'Confirm Deactivation'
                "></h3>
                <button @click="confirmingAction = false"
                    class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <p class="text-sm text-gray-700 dark:text-gray-300" x-text="
                actionType === 'approve' ? 'Are you sure you want to approve this user?' :
                actionType === 'reject' ? 'Are you sure you want to reject and permanently delete this user?' :
                'Are you sure you want to deactivate this user?'
            "></p>
            <div class="mt-4 flex justify-end space-x-3">
                <button @click="confirmingAction = false"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300
                    dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                    Cancel
                </button>
                <button wire:click="updateStatus"
                    @click="confirmingAction = false"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500
                    dark:bg-blue-700 dark:hover:bg-blue-600">
                    <div wire:loading wire:target="updateStatus" style="margin-right: 5px">
                        <div class="spinner-border small text-primary" role="status">
                        </div>
                    </div>
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <!-- Bulk Action Confirmation Modal -->
    <div x-data="{ confirmingBulkAction: @entangle('confirmingBulkAction'), bulkActionType: @entangle('bulkActionType') }" 
        x-show="confirmingBulkAction"
        class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-40 flex items-center justify-center">
        <div @click.away="confirmingBulkAction = false" 
            x-show="confirmingBulkAction"
            x-transition:enter="transition ease-out duration-300" 
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0" 
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0" 
            x-transition:leave-end="opacity-0 translate-y-4"
            class="relative bg-white dark:bg-gray-800 p-6 mx-auto max-w-lg rounded-2xl shadow-xl">
            
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200" x-text="
                    bulkActionType === 'approve' ? 'Confirm Bulk Approval' :
                    bulkActionType === 'reject' ? 'Confirm Bulk Rejection' :
                    'Confirm Bulk Deactivation'
                "></h3>
                <button @click="confirmingBulkAction = false"
                    class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="mb-4">
                <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                    You have selected <strong>{{ count($selectedUsers) }}</strong> user(s).
                </p>
                <p class="text-sm text-gray-700 dark:text-gray-300" x-text="
                    bulkActionType === 'approve' ? 'Are you sure you want to approve all selected users?' :
                    bulkActionType === 'reject' ? 'Are you sure you want to reject and permanently delete all selected users? This action cannot be undone.' :
                    'Are you sure you want to deactivate all selected users?'
                "></p>
            </div>
            
            <!-- Warning for reject action -->
            <div x-show="bulkActionType === 'reject'" 
                class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm font-medium text-red-800 dark:text-red-200">
                        Warning: This will permanently delete all user data!
                    </span>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button @click="confirmingBulkAction = false"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300
                    dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 transition-colors">
                    Cancel
                </button>
                <button wire:click="executeBulkAction"
                    @click="confirmingBulkAction = false"
                    class="px-4 py-2 rounded-md text-white focus:outline-none focus:ring-2 transition-colors"
                    :class="bulkActionType === 'reject' ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' : 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500'">
                    <div wire:loading wire:target="executeBulkAction" style="margin-right: 5px">
                        <div class="spinner-border small text-primary" role="status">
                        </div>
                    </div>
                    <span x-text="
                        bulkActionType === 'approve' ? 'Approve All' :
                        bulkActionType === 'reject' ? 'Delete All' :
                        'Deactivate All'
                    "></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Action Email Modal -->
    <div x-show="openEmailModal"
        class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-40 flex items-center justify-center">
        <div x-show="openEmailModal"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4"
            class="relative bg-white dark:bg-gray-800 p-6 mx-auto w-full max-w-xl rounded-2xl shadow-xl">
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">Send an Email</h3>
                <button wire:click="resetAction"
                    class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium">Subject</label>
                <select wire:model="email_subject" class="border rounded w-full px-3 py-2 dark:bg-slate-700">
                    <option value="">-- Select --</option>
                    <option value="agency_submission_reminder_notif">Reminder for PYDI or PYDP Submission</option>
                </select>
                @error('email_subject')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mt-4 flex justify-end space-x-3">
                <button wire:click="resetAction"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300
                    dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                    Cancel
                </button>
                <button wire:click="sendEmail"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500
                    dark:bg-blue-700 dark:hover:bg-blue-600">
                    <div wire:loading wire:target="sendEmail" style="margin-right: 5px">
                        <div class="spinner-border small text-primary" role="status">
                        </div>
                    </div>
                    Send Email
                </button>
            </div>
        </div>
    </div>

    <!-- Bulk Email Modal -->
    <div x-data="{ openBulkEmailModal: @entangle('openBulkEmailModal') }" x-show="openBulkEmailModal"
        class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-40 flex items-center justify-center">
        <div x-show="openBulkEmailModal"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4"
            class="relative bg-white dark:bg-gray-800 p-6 mx-auto w-full max-w-xl rounded-2xl shadow-xl">
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">Send Bulk Email</h3>
                <button wire:click="resetAction"
                    class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="mb-3">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    You are about to send emails to <strong>{{ count($selectedUsers) }}</strong> selected users.
                </p>
                <label class="block text-sm font-medium">Email Subject</label>
                <select wire:model="email_subject" class="border rounded w-full px-3 py-2 dark:bg-slate-700">
                    <option value="">-- Select --</option>
                    <option value="agency_submission_reminder_notif">Reminder on PYDI or PYDP Submission</option>
                </select>
                @error('email_subject')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mt-4 flex justify-end space-x-3">
                <button wire:click="resetAction"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300
                    dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                    Cancel
                </button>
                <button wire:click="sendBulkEmail"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500
                    dark:bg-green-700 dark:hover:bg-green-600">
                    <div wire:loading wire:target="sendBulkEmail" style="margin-right: 5px">
                        <div class="spinner-border small text-primary" role="status">
                        </div>
                    </div>
                    Send Email
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('swal', (event) => {
            Swal.fire({
                title: event.title,
                text: event.text,
                icon: event.icon,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#374151'
            });
        });
    });
</script>
@endpush
