<div x-data="{
    isModalOpen: false,
    isEditMode: false,
    confirmingAction: @entangle('confirmingAction'),
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

        <!-- Table -->
        <div class="mt-4 overflow-x-auto text-sm">
            <table class="min-w-full bg-white dark:bg-gray-800 overflow-hidden">
                <thead class="bg-gray-50 dark:bg-gray-700 rounded-xl">
                    <tr class="whitespace-nowrap">
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Email</th>
                        <th class="px-4 py-2 text-left">Contact</th>
                        <th class="px-4 py-2 text-left">Agency</th>
                        <th class="px-4 py-2 text-center">Status</th>
                        <th class="px-4 py-2 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                    <tr class="border-b border-gray-200 dark:border-gray-700 whitespace-nowrap hover:bg-gray-50 dark:hover:bg-gray-700">
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
                    Confirm
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
