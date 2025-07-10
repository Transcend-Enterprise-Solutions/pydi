<div class="w-full bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">PYDI Data Upload</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Upload your PYDI indicator datasets</p>
        </div>

        @if(!$currentSession)
            <button wire:click="openSessionModal"
                    class="bg-blue-600 dark:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center gap-2 font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Start New Upload Session
            </button>
        @endif
    </div>

    @if($currentSession)
        <!-- Session Info Card -->
        <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-semibold text-blue-900 dark:text-blue-200">Current Upload Session</h3>
                    <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                        <span class="font-medium">{{ $dimensionName }} - {{ $indicatorName }}</span> |
                        ID: {{ $currentSession->id }} |
                        Status: <span class="capitalize">{{ $sessionStatus }}</span> |
                        Records: {{ $recordsCount }}
                    </p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                        Created: {{ $sessionCreatedAt }} |
                        @if($sessionStatus === 'submitted')
                            Submitted: {{ $sessionSubmittedAt }}
                        @endif
                    </p>
                </div>
                <div class="flex gap-2">
                    <button wire:click="exportToCsv"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export CSV
                    </button>
                    <button wire:click="duplicateSession"
                            class="bg-purple-600 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Duplicate
                    </button>
                    <button wire:click="submitSession"
                            class="bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Submit
                    </button>
                    <button wire:click="cancelSession"
                            class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <!-- Add New Record Form -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Add New Record</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Region -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Region *
                    </label>
                    <select wire:model="region"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Region</option>
                        @foreach($regions as $code => $name)
                            <option value="{{ $code }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('region')
                        <span class="text-red-500 text-sm dark:text-red-400">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Sex -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Sex *
                    </label>
                    <select wire:model="sex"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Sex</option>
                        @foreach($sexOptions as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>
                    @error('sex')
                        <span class="text-red-500 text-sm dark:text-red-400">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Age -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Age Group *
                    </label>
                    <select wire:model="age"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Age Group</option>
                        @foreach($ageGroups as $group)
                            <option value="{{ $group }}">{{ $group }}</option>
                        @endforeach
                    </select>
                    @error('age')
                        <span class="text-red-500 text-sm dark:text-red-400">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Value of Indicator -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Value of Indicator *
                    </label>
                    <div class="relative">
                        <input type="number" step="0.01" wire:model="value" placeholder="0.00"
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-12"/>
                        @if($measurementUnit)
                            <span class="absolute right-3 top-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ $measurementUnit }}
                            </span>
                        @endif
                    </div>
                    @error('value')
                        <span class="text-red-500 text-sm dark:text-red-400">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end mt-4">
                <button wire:click="openDataEntryModal"
                        class="bg-green-600 dark:bg-green-700 text-white px-6 py-2 rounded-md font-medium flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add Record
                </button>
            </div>
        </div>

        <!-- Session Data Table -->
        @if(count($sessionRecords) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Session Data ({{ $recordsCount }} records)
                        </h3>
                        <div class="flex gap-2">
                            <button wire:click="exportToCsv"
                                    class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export
                            </button>
                            <button wire:click="submitSession"
                                    class="bg-blue-600 dark:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Submit All
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Region
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Sex
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Age Group
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Value
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($sessionRecords as $record)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $record['region'] }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $record['sex'] }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $record['age'] }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ number_format($record['value'], 2) }}
                                            @if(isset($record['indicator']['measurement_unit']))
                                                {{ $record['indicator']['measurement_unit'] }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full
                                            @if($record['status'] === 'draft') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @elseif($record['status'] === 'submitted') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @elseif($record['status'] === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                            {{ ucfirst($record['status'] ?? 'draft') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-right">
                                        <button wire:click="openDataEntryModal('{{ $record['id'] }}')"
                                                class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3"
                                                title="Edit Record">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </button>
                                        <button wire:click="deleteDataRecord('{{ $record['id'] }}')"
                                                class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                                                title="Remove Record">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-12">
                <div class="text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-xl font-medium text-gray-700 dark:text-gray-200">No records added yet</p>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">Start adding your PYDI indicator data using the form above</p>
                </div>
            </div>
        @endif
    @else
        <!-- No Active Session -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-12">
            <div class="text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                </svg>
                <p class="text-xl font-medium text-gray-700 dark:text-gray-200">Ready to upload PYDI data?</p>
                <p class="text-gray-500 dark:text-gray-400 mt-2 mb-6">Start a new upload session to begin adding your indicator datasets</p>
                <button wire:click="openSessionModal"
                        class="bg-blue-600 dark:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium flex items-center gap-2 mx-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Start New Upload Session
                </button>
            </div>
        </div>
    @endif

    <!-- Session Creation Modal -->
    @if($showSessionModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            New Upload Session
                        </h3>
                        <button wire:click="closeSessionModal" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                            &times;
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Session Name *
                            </label>
                            <input type="text" wire:model="sessionName"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            @error('sessionName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Description
                            </label>
                            <textarea wire:model="sessionDescription" rows="3"
                                      class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
                        </div>

                        <!-- Dimension and Indicator selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Dimension *
                            </label>
                            <select wire:model.live="selectedDimensionId"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">Select Dimension</option>
                                @foreach($dimensions as $dimension)
                                    <option value="{{ $dimension->id }}">{{ $dimension->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedDimensionId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Indicator *
                            </label>
                            <select wire:model="selectedIndicatorId"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">Select Indicator</option>
                                @if($selectedDimensionId)
                                    @foreach($indicators as $indicator)
                                        <option value="{{ $indicator->id }}">{{ $indicator->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('selectedIndicatorId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button wire:click="closeSessionModal"
                                class="px-4 py-2 text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md">
                            Cancel
                        </button>
                        <button wire:click="createSession"
                                class="bg-blue-600 dark:bg-blue-700 text-white px-4 py-2 rounded-md">
                            Create Session
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Data Entry Modal -->
    @if($showDataEntryModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            {{ $editingRecordId ? 'Edit Record' : 'Add New Record' }}
                        </h3>
                        <button wire:click="closeDataEntryModal" class="text-gray-500 dark:text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Region *
                            </label>
                            <select wire:model="region"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Region</option>
                                @foreach($regions as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('region')
                                <span class="text-red-500 text-sm dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Sex *
                            </label>
                            <select wire:model="sex"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Sex</option>
                                @foreach($sexOptions as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            @error('sex')
                                <span class="text-red-500 text-sm dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Age *
                            </label>
                            <select wire:model="age"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Age Group</option>
                                @foreach($ageGroups as $group)
                                    <option value="{{ $group }}">{{ $group }}</option>
                                @endforeach
                            </select>
                            @error('age')
                                <span class="text-red-500 text-sm dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Value of Indicator *
                            </label>
                            <div class="relative">
                                <input type="number" step="0.01" wire:model="value" placeholder="0.00"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-12"/>
                                @if($measurementUnit)
                                    <span class="absolute right-3 top-2 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $measurementUnit }}
                                    </span>
                                @endif
                            </div>
                            @error('value')
                                <span class="text-red-500 text-sm dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button wire:click="closeDataEntryModal"
                                class="px-6 py-2 text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md">
                            Cancel
                        </button>
                        <button wire:click="saveDataRecord"
                                class="bg-blue-600 dark:bg-blue-700 text-white px-6 py-2 rounded-md font-medium">
                            {{ $editingRecordId ? 'Update Record' : 'Add Record' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
