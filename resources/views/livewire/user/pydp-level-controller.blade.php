<div class="w-full bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-lg font-bold text-gray-900 dark:text-white">PYDP Levels & Indicators Manager</h1>
        <button wire:click="openDimensionModal"
            class="bg-blue-600 dark:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add Level
        </button>
    </div>

    <!-- Main Table -->
    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/3">
                            Levels
                        </th>
                        <th
                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Indicators
                        </th>
                        <th
                            class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-32">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($dimensions as $dimension)
                        @php
                            $indicatorCount = $dimension->indicators->count();
                        @endphp

                        @if ($indicatorCount > 0)
                            @foreach ($dimension->indicators as $index => $indicator)
                                <tr>
                                    <!-- Dimension Column - Only show on first row -->
                                    @if ($index === 0)
                                        <td class="p-6 align-top border-r border-gray-200 dark:border-gray-700"
                                            rowspan="{{ $indicatorCount }}">
                                            <div class="space-y-3">
                                                <div class="font-semibold text-lg text-gray-900 dark:text-white">
                                                    {{ $dimension->title }}
                                                </div>

                                                @if ($dimension->content)
                                                    <div class="text-sm text-gray-600 dark:text-gray-300">
                                                        {{ $dimension->content }}
                                                    </div>
                                                @endif

                                                <div class="flex items-center gap-2 mt-3">
                                                    <button wire:click="openDimensionModal({{ $dimension->id }})"
                                                        class="bg-blue-600 dark:bg-blue-700 text-white px-2 py-1 rounded text-sm">
                                                        <i class="bi bi-pencil-square mr-1"></i>Edit
                                                    </button>
                                                    <button
                                                        wire:click="confirmAction({{ $dimension->id }}, 'deleteDimension')"
                                                        class="bg-red-600 dark:bg-red-700 text-white px-2 py-1 rounded text-sm">
                                                        <i class="bi bi-trash mr-1"></i>Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    @endif


                                    <!-- Indicator Column -->
                                    <td class="px-6 py-4">
                                        <div class="space-y-1">
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                {{ $indicator->title }}
                                            </div>
                                            @if ($indicator->content)
                                                <div class="text-sm text-gray-600 dark:text-gray-300">
                                                    {{ $indicator->content }}
                                                </div>
                                            @endif
                                            @if ($indicator->measurement_unit)
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    Unit: {{ $indicator->measurement_unit }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Actions Column -->
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-1">
                                            <button
                                                wire:click="openIndicatorModal({{ $dimension->id }}, {{ $indicator->id }})"
                                                class="bg-blue-600 dark:bg-blue-700 text-white px-2 py-1 rounded"
                                                title="Edit Indicator">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button wire:click="confirmAction({{ $indicator->id }}, 'deleteIndicator')"
                                                class="bg-red-600 dark:bg-red-700 text-white px-2 py-1 rounded"
                                                title="Delete Indicator">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <!-- Dimension with no indicators -->
                            <tr>
                                <td class="px-6 py-4 border-r border-gray-200 dark:border-gray-700">
                                    <div class="space-y-2">
                                        <div class="font-semibold text-lg text-gray-900 dark:text-white">
                                            {{ $dimension->title }}
                                        </div>
                                        @if ($dimension->content)
                                            <div class="text-sm text-gray-600 dark:text-gray-300">
                                                {{ $dimension->content }}
                                            </div>
                                        @endif
                                        <div class="flex items-center gap-2 mt-3">
                                            <button wire:click="openDimensionModal({{ $dimension->id }})"
                                                class="bg-blue-600 dark:bg-blue-700 text-white px-2 py-1 rounded text-sm">
                                                <i class="bi bi-pencil-square mr-1"></i>Edit
                                            </button>
                                            <button wire:click="confirmAction({{ $dimension->id }}, 'deleteDimension')"
                                                class="bg-red-600 dark:bg-red-700 text-white px-2 py-1 rounded text-sm">
                                                <i class="bi bi-trash mr-1"></i>Delete
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-500 dark:text-gray-400 italic">
                                        No indicators yet
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="openIndicatorModal({{ $dimension->id }})"
                                        class="bg-blue-600 dark:bg-blue-700 text-white px-2 py-1 rounded">
                                        <i class="bi bi-plus-circle"></i>
                                    </button>
                                </td>
                            </tr>
                        @endif

                        <!-- Add Indicator Button Row -->
                        @if ($indicatorCount > 0)
                            <tr class="border-gray-200 dark:border-gray-200">
                                <td class="px-6 py-2 border-r border-gray-200 dark:border-gray-200"></td>
                                <td class="px-6 py-2">
                                    <button wire:click="openIndicatorModal({{ $dimension->id }})"
                                        class="bg-blue-600 dark:bg-blue-700 text-white px-2 py-1 rounded text-sm font-medium flex items-center gap-1">
                                        <i class="bi bi-plus-circle"></i></i>Add New Indicator
                                    </button>
                                </td>
                                <td class="px-6 py-2"></td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-12 w-12 mx-auto mb-4 text-gray-400 dark:text-gray-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-lg font-medium text-gray-700 dark:text-gray-200">No levels found
                                    </p>
                                    <p class="text-sm mt-1">Create your first level to get started</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dimension Modal -->
    @if ($showDimensionModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4 border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $editingDimensionId ? 'Edit Level' : 'Add New Level' }}
                        </h3>
                        <button wire:click="closeDimensionModal" class="text-gray-500 dark:text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveDimension" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name
                                *</label>
                            <input type="text" wire:model.defer="dimensionName"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Enter Name" />
                            @error('dimensionName')
                                <span class="text-red-500 text-sm dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                            <textarea wire:model.defer="dimensionDescription" rows="3"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Enter Description"></textarea>
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" wire:click="closeDimensionModal"
                                class="px-4 py-2 text-gray-600 dark:text-gray-300">
                                Cancel
                            </button>
                            <button type="submit"
                                class="bg-blue-600 dark:bg-blue-700 text-white px-6 py-2 rounded-md">
                                {{ $editingDimensionId ? 'Update' : 'Create' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Indicator Modal -->
    @if ($showIndicatorModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4 border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $editingIndicatorId ? 'Edit Indicator' : 'Add New Indicator' }}
                        </h3>
                        <button wire:click="closeIndicatorModal" class="text-gray-500 dark:text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    @if ($selectedDimensionId)
                        <div
                            class="bg-blue-50 dark:bg-blue-900/30 p-3 rounded-md mb-4 border border-blue-100 dark:border-blue-800">
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                Adding indicator to: <strong
                                    class="font-medium">{{ $dimensions->find($selectedDimensionId)->title ?? 'Unknown' }}</strong>
                            </p>
                        </div>
                    @endif

                    <form wire:submit.prevent="saveIndicator" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name
                                *</label>
                            <input type="text" wire:model.defer="indicatorName"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Enter Name" />
                            @error('indicatorName')
                                <span class="text-red-500 text-sm dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                            <textarea wire:model.defer="indicatorDescription" rows="3"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Enter Description"></textarea>
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" wire:click="closeIndicatorModal"
                                class="px-4 py-2 text-gray-600 dark:text-gray-300">
                                Cancel
                            </button>
                            <button type="submit"
                                class="bg-blue-600 dark:bg-blue-700 text-white px-6 py-2 rounded-md">
                                {{ $editingIndicatorId ? 'Update' : 'Create' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-sm p-6 text-center">
                <h3 class="text-lg font-bold mb-2">Delete {{ $type }}</h3>
                <p class="text-gray-600 mb-4">Are you sure you want to delete this {{ $type }}? This action
                    cannot be
                    undone.</p>

                <div class="flex justify-center gap-4">
                    <button wire:click="$set('showDeleteModal', false)" class="px-4 py-2 border rounded">
                        Cancel
                    </button>
                    <button wire:click="confirmDelete" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 flex items-center gap-2">
                        <span wire:loading.remove wire:target="confirmDelete">Delete</span>
                        <span wire:loading wire:target="confirmDelete" class="flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i> Deleting...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
