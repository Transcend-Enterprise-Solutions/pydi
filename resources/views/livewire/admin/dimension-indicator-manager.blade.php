<div class="w-full bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dimension & Indicators Manager</h1>
        <button wire:click="openDimensionModal"
            class="bg-blue-600 text-sm dark:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add Dimension
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
                            Dimension
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
                                        <td class="px-6 py-4 align-top border-r border-gray-200 dark:border-gray-700"
                                            rowspan="{{ $indicatorCount }}">
                                            <div class="space-y-3 text-center">
                                                <div class="flex justify-center mb-4">
                                                    @if ($dimension->image)
                                                        <img src="{{ $dimension->image }}" alt="{{ $dimension->name }}"
                                                            class="w-full max-w-[160px] object-cover" />
                                                    @endif
                                                </div>

                                                <div class="font-semibold text-lg text-gray-900 dark:text-white">
                                                    {{ $dimension->name }}
                                                </div>

                                                @if ($dimension->description)
                                                    <div class="text-sm text-gray-600 dark:text-gray-300">
                                                        {{ $dimension->description }}
                                                    </div>
                                                @endif

                                                <div class="flex items-center justify-center gap-2 mt-3">
                                                    <button wire:click="openDimensionModal({{ $dimension->id }})"
                                                        class="text-blue-600 dark:text-blue-400 text-sm font-medium">
                                                        Edit Dimension
                                                    </button>
                                                    <button
                                                        wire:click="confirmAction({{ $dimension->id }}, 'deleteDimension')"
                                                        class="text-red-600 dark:text-red-400 text-sm font-medium">
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    @endif


                                    <!-- Indicator Column -->
                                    <td class="px-6 py-4">
                                        <div class="space-y-1">
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                {{ $indicator->name }}
                                            </div>
                                            @if ($indicator->description)
                                                <div class="text-sm text-gray-600 dark:text-gray-300">
                                                    {{ $indicator->description }}
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
                                        <div class="flex justify-end gap-2">
                                            <button
                                                wire:click="openIndicatorModal({{ $dimension->id }}, {{ $indicator->id }})"
                                                class="text-yellow-600 dark:text-yellow-400" title="Edit Indicator">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path
                                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                </svg>
                                            </button>
                                            <button wire:click="confirmAction({{ $indicator->id }}, 'deleteIndicator')"
                                                class="text-red-600 dark:text-red-400" title="Delete Indicator">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
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
                                            {{ $dimension->name }}
                                        </div>
                                        @if ($dimension->description)
                                            <div class="text-sm text-gray-600 dark:text-gray-300">
                                                {{ $dimension->description }}
                                            </div>
                                        @endif
                                        <div class="flex items-center gap-2 mt-3">
                                            <button wire:click="openDimensionModal({{ $dimension->id }})"
                                                class="text-blue-600 dark:text-blue-400 text-sm font-medium">
                                                Edit Dimension
                                            </button>
                                            <button wire:click="confirmAction({{ $dimension->id }}, 'deleteDimension')"
                                                class="text-red-600 dark:text-red-400 text-sm font-medium">
                                                Delete
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
                                        class="bg-green-600 dark:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                        Add Indicator
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
                                        class="text-green-600 dark:text-green-600 text-sm font-medium flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Add Another Indicator
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
                                    <p class="text-lg font-medium text-gray-700 dark:text-gray-200">No dimensions found
                                    </p>
                                    <p class="text-sm mt-1">Create your first dimension to get started</p>
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
                            {{ $editingDimensionId ? 'Edit Dimension' : 'Add New Dimension' }}
                        </h3>
                        <button wire:click="closeDimensionModal" class="text-gray-500 dark:text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
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
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                            @error('dimensionName')
                                <span class="text-red-500 text-sm dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                            <textarea wire:model.defer="dimensionDescription" rows="3"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
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
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4 border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $editingIndicatorId ? 'Edit Indicator' : 'Add New Indicator' }}
                        </h3>
                        <button wire:click="closeIndicatorModal" class="text-gray-500 dark:text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    @if ($selectedDimensionId)
                        <div class="bg-blue-50 dark:bg-blue-900/30 p-3 rounded-md mb-4 border border-blue-100 dark:border-blue-800">
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                Adding indicator to: <strong class="font-medium">{{ $dimensions->find($selectedDimensionId)->name ?? 'Unknown' }}</strong>
                            </p>
                        </div>
                    @endif

                    <form wire:submit.prevent="saveIndicator" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name *</label>
                            <input type="text" wire:model.defer="indicatorName"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                            @error('indicatorName')
                                <span class="text-red-500 text-sm dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                            <textarea wire:model.defer="indicatorDescription" rows="3"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Measurement Unit</label>
                            <select wire:model.defer="indicatorUnit"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Measurement Unit</option>
                                <option value="frequency">Frequency or Count</option>
                                <option value="percentage">Percentage or Rate</option>
                            </select>
                            @error('indicatorUnit')
                                <span class="text-red-500 text-sm dark:text-red-400">{{ $message }}</span>
                            @enderror
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
</div>
