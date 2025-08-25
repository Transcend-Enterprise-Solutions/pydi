<div>
    <div class="w-full flex justify-center">
        <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800 overflow-x-visible">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">{{ Auth::user()->user_role === 'user' ? 'User logs' : 'System Logs' }}</h2>
                <div class="flex items-center gap-2">
                    <input type="text" wire:model.live="search" placeholder="Search..."
                        class="w-52 py-2 px-3 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                    <select wire:model.live="showEntries"
                        class="w-16 py-2 px-3 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>

            @include('livewire.user.session-flash')

            <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="w-full text-xs overflow-auto">
                    <table class="table-auto w-full text-left">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2">#</th>
                                <th class="px-4 py-2">Account Name</th>
                                <th class="px-4 py-2">Action</th>
                                <th class="px-4 py-2">Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tableDatas as $index => $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                    <td class="px-4 py-2">{{ $tableDatas->firstItem() + $index }}</td>
                                    <td class="px-4 py-2">{{ $row->user->name }}</td>
                                    <td class="px-4 py-2">{{ $row->action }}</td>
                                    <td class="px-4 py-2">
                                        {{ $row->created_at->format('M d, Y h:i A') }}
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
            </div>

            <div class="mt-4">
                {{ $tableDatas->links() }}
            </div>
        </div>
    </div>
</div>
