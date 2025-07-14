<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">PYDI Data Entry</h1>

                @if(!$currentSession)
                    <button
                        wire:click="showCreateSessionForm"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200"
                    >
                        Create New Dataset
                    </button>
                @endif
            </div>

            <!-- Flash Messages -->
            @if(session()->has('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session()->has('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Create Session Modal -->
            @if($showCreateSession)
                <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="cancelCreateSession">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" wire:click.stop>
                        <div class="mt-3">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Dataset</h3>

                            <div class="mb-4">
                                <label for="sessionName" class="block text-sm font-medium text-gray-700 mb-2">
                                    Dataset Name
                                </label>
                                <input
                                    type="text"
                                    id="sessionName"
                                    wire:model="sessionName"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="Enter dataset name"
                                >
                                @error('sessionName')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="sessionNotes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Notes (Optional)
                                </label>
                                <textarea
                                    id="sessionNotes"
                                    wire:model="sessionNotes"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="Enter notes about this dataset"
                                ></textarea>
                                @error('sessionNotes')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex justify-end space-x-2">
                                <button
                                    wire:click="cancelCreateSession"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-200"
                                >
                                    Cancel
                                </button>
                                <button
                                    wire:click="createSession"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200"
                                >
                                    Create Dataset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Current Session Info -->
            @if($currentSession)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-medium text-blue-900">Current Dataset</h3>
                            <p class="text-blue-700">{{ $currentSession->session_name }}</p>
                            @if($currentSession->notes)
                                <p class="text-sm text-blue-600 mt-1">{{ $currentSession->notes }}</p>
                            @endif
                            <p class="text-sm text-blue-600 mt-1">
                                Created: {{ $currentSession->created_at->format('M d, Y h:i A') }}
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <button
                                wire:click="showAddRowForm"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition duration-200"
                            >
                                Add Row
                            </button>
                            @if(count($rows) > 0)
                                <button
                                    wire:click="submitForReview"
                                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md transition duration-200"
                                >
                                    Submit for Review
                                </button>
                            @endif
                            <button
                                wire:click="cancelSession"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition duration-200"
                                onclick="return confirm('Are you sure you want to cancel this dataset? All data will be lost.')"
                            >
                                Cancel Dataset
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Add/Edit Row Modal -->
                @if($showAddRow)
                    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="cancelAddRow">
                        <div class="relative top-10 mx-auto p-5 border w-4/5 max-w-4xl shadow-lg rounded-md bg-white" wire:click.stop>
                            <div class="mt-3">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">
                                    {{ $editingRowIndex !== null ? 'Edit Row' : 'Add New Row' }}
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Dimension -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Dimension <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            wire:model="newRow.dimension_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        >
                                            <option value="">Select Dimension</option>
                                            @foreach($dimensions as $dimension)
                                                <option value="{{ $dimension->id }}">{{ $dimension->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('newRow.dimension_id')
                                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Indicator -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Indicator <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            wire:model="newRow.indicator_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        >
                                            <option value="">Select Indicator</option>
                                            @foreach($indicators as $indicator)
                                                <option value="{{ $indicator->id }}">{{ $indicator->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('newRow.indicator_id')
                                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Region -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Region <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            wire:model="newRow.region"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        >
                                            <option value="">Select Region</option>
                                            @foreach($regions as $region)
                                                <option value="{{ $region->region_code }}">{{ $region->region_description }}</option>
                                            @endforeach
                                        </select>
                                        @error('newRow.region')
                                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Sex -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Sex <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            wire:model="newRow.sex"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        >
                                            <option value="">Select Sex</option>
                                            @foreach($sexOptions as $sex)
                                                <option value="{{ $sex }}">{{ $sex }}</option>
                                            @endforeach
                                        </select>
                                        @error('newRow.sex')
                                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Age -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Age Group <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            wire:model="newRow.age"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        >
                                            <option value="">Select Age Group</option>
                                            @foreach($ageGroups as $age)
                                                <option value="{{ $age }}">{{ $age }}</option>
                                            @endforeach
                                        </select>
                                        @error('newRow.age')
                                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Value -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Value <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="number"
                                            step="0.0001"
                                            wire:model="newRow.value"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            placeholder="Enter value"
                                        >
                                        @error('newRow.value')
                                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Remarks -->
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Remarks
                                        </label>
                                        <textarea
                                            wire:model="newRow.remarks"
                                            rows="3"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            placeholder="Optional remarks or notes"
                                        ></textarea>
                                        @error('newRow.remarks')
                                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-2 mt-6">
                                    <button
                                        wire:click="cancelAddRow"
                                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-200"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        wire:click="{{ $editingRowIndex !== null ? 'updateRow' : 'addRow' }}"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200"
                                    >
                                        {{ $editingRowIndex !== null ? 'Update Row' : 'Add Row' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Data Table -->
                @if(count($rows) > 0)
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Dimension
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Indicator
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Region
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Sex
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Age
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Value
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Remarks
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($rows as $index => $row)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $row['dimension_name'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $row['indicator_name'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $this->getRegionName($row['region']) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $row['sex'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $row['age'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ number_format($row['value'], 4) }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ $row['remarks'] ?: '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button
                                                    wire:click="editRow({{ $index }})"
                                                    class="text-blue-600 hover:text-blue-900 mr-3"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    wire:click="deleteRow({{ $index }})"
                                                    class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('Are you sure you want to delete this row?')"
                                                >
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-500 text-lg">No data rows added yet.</div>
                        <p class="text-gray-400 mt-2">Click "Add Row" to start adding data to your dataset.</p>
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <div class="text-gray-500 text-xl mb-4">No active dataset found.</div>
                    <p class="text-gray-400 mb-6">Create a new dataset to start entering PYDI data.</p>
                    <button
                        wire:click="showCreateSessionForm"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md transition duration-200"
                    >
                        Create New Dataset
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
