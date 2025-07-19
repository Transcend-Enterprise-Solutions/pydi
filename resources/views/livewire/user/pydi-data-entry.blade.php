<div class="w-full">
    <!-- Header Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-slate-800 shadow-sm p-6 rounded-lg">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-200">PYDI Indicator Data Entry</h1>
            <p class="mt-2 text-sm">Upload, edit, and manage your PYDI indicator datasets</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8">
                <button wire:click="setActiveTab('upload')"
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'upload' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Upload Datasets
                </button>
                <button wire:click="setActiveTab('datasets')"
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'datasets' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    View Datasets
                </button>
            </nav>
        </div>

        <!-- Upload Tab -->
        @if($activeTab === 'upload')
            <div class="space-y-6">
                <!-- Upload Methods -->
                <div class="bg-white dark:bg-slate-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200 mb-4">Choose Upload Method</h3>

                        <!-- Method Selection -->
                        <div class="flex space-x-4 mb-6">
                            <button wire:click="setUploadMethod('manual')"
                                    class="text-sm px-4 hover:bg-blue-700 py-2 rounded-md {{ $uploadMethod === 'manual' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Manual Entry
                            </button>
                            <button wire:click="setUploadMethod('file')"
                                    class="text-sm dark:bg-green-700 dark:hover:bg-green-800 dark:text-gray-200 px-4 py-2 rounded-md {{ $uploadMethod === 'file' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                File Upload
                            </button>
                        </div>

                        <!-- Session Selection -->
                        <div class="mb-6">
                            <label for="session_selection" class="block text-sm font-medium">Session</label>
                            <div class="mt-1 flex space-x-4">
                                <select wire:model.live="selectedSessionId" class="dark:bg-gray-700 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Create New Session</option>
                                    @foreach($activeSessions as $session)
                                        <option value="{{ $session->id }}">{{ $session->session_name }} ({{ $session->dataRecords->count() }} records)</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- New Session Name -->
                        @if(!$selectedSessionId)
                            <div class="mb-6">
                                <label for="session_name" class="block text-sm font-medium">Session Name</label>
                                <input wire:model="sessionName" type="text" id="session_name"
                                       class="dark:bg-gray-700 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="Enter session name">
                                @error('sessionName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <!-- Dimension and Indicator Selection -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="dimension_id" class="block text-sm font-medium">Dimension/Center of Participation</label>
                                <select wire:model="dimensionId" wire:change="loadIndicators"
                                        class="dark:bg-gray-700 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Dimension</option>
                                    @foreach($dimensions as $dimension)
                                        <option value="{{ $dimension->id }}">{{ $dimension->name }}</option>
                                    @endforeach
                                </select>
                                @error('dimensionId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="indicator_id" class="block text-sm font-medium">Indicator (Drop-down)</label>
                                <select wire:model="indicatorId"
                                        class="dark:bg-gray-700 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        {{ !$dimensionId ? 'disabled' : '' }}>
                                    <option value="">Select Indicator</option>
                                    @foreach($indicators as $indicator)
                                        <option value="{{ $indicator->id }}">{{ $indicator->name }}</option>
                                    @endforeach
                                </select>
                                @error('indicatorId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Manual Entry Form -->
                        @if($uploadMethod === 'manual')
                            <div class="border-t pt-6">
                                <h4 class="text-md font-medium text-gray-800 dark:text-gray-300 mb-4">Manual Data Entry</h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label for="region" class="block text-sm font-medium">Region</label>
                                        <select wire:model="region"
                                                class="dark:bg-gray-700 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Select Region</option>
                                            @foreach($regions as $regionOption)
                                                <option value="{{ $regionOption->region_description }}">{{ $regionOption->region_description }}</option>
                                            @endforeach
                                        </select>
                                        @error('region') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="sex" class="block text-sm font-medium">Sex</label>
                                        <select wire:model="sex"
                                                class="dark:bg-gray-700 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Select Sex</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Both">Both</option>
                                        </select>
                                        @error('sex') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="age" class="block text-sm font-medium">Age</label>
                                        <input wire:model="age" type="text"
                                               class="dark:bg-gray-700 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                               placeholder="e.g., 15-24, All Ages">
                                        @error('age') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="value" class="block text-sm font-medium">Value of Indicator</label>
                                        <input wire:model="value" type="number" step="0.0001"
                                               class="dark:bg-gray-700 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                               placeholder="Enter numeric value">
                                        @error('value') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="remarks" class="block text-sm font-medium">Remarks</label>
                                        <input wire:model="remarks" type="text"
                                               class="dark:bg-gray-700 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                               placeholder="Optional remarks">
                                        @error('remarks') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button wire:click="addRecord"
                                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        Add Record
                                    </button>
                                </div>
                            </div>
                        @endif

                        <!-- File Upload Form -->
                        @if($uploadMethod === 'file')
                            <div class="border-t pt-6">
                                <h4 class="text-md font-medium text-gray-900 mb-4">File Upload</h4>

                                <div class="mb-4">
                                    <label for="dataset_file" class="block text-sm font-medium">Dataset File</label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                    <span>Upload a file</span>
                                                    <input wire:model="datasetFile" id="file-upload" type="file" class="sr-only" accept=".csv,.xlsx,.xls">
                                                </label>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500">CSV, XLSX, XLS up to 10MB</p>
                                        </div>
                                    </div>
                                    @error('datasetFile') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                @if($datasetFile)
                                    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-md">
                                        <p class="text-sm text-green-800">File selected: {{ $datasetFile->getClientOriginalName() }}</p>
                                    </div>
                                @endif

                                <div class="flex justify-end">
                                    <button wire:click="uploadFile"
                                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            {{ !$datasetFile ? 'disabled' : '' }}>
                                        Upload File
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Active Sessions -->
                @if($activeSessions->count() > 0)
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Active Sessions</h3>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Session Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Records</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($activeSessions as $session)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $session->session_name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $session->dataRecords->count() }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $session->created_at->format('M d, Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                    <button wire:click="viewSession({{ $session->id }})"
                                                            class="text-indigo-600 hover:text-indigo-900">View</button>
                                                    <button wire:click="submitSession({{ $session->id }})"
                                                            class="text-green-600 hover:text-green-900">Submit</button>
                                                    <button wire:click="cancelSession({{ $session->id }})"
                                                            class="text-red-600 hover:text-red-900">Cancel</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Datasets Tab -->
        @if($activeTab === 'datasets')
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200 mb-4">Your Datasets</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 dark:bg-slate-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Session Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Records</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Submitted</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($allSessions as $session)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $session->session_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $session->status === 'active' ? 'bg-yellow-100 text-yellow-800' :
                                                   ($session->status === 'submitted' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst($session->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $session->total_records }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $session->submitted_at ? $session->submitted_at->format('M d, Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button wire:click="viewSession({{ $session->id }})"
                                                    class="text-indigo-600 hover:text-indigo-900">View</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Session Details Modal -->
        @if($showSessionModal)
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Session Details: {{ $selectedSession->session_name ?? '' }}</h3>
                            <button wire:click="closeSessionModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        @if($selectedSession)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dimension</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Indicator</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Region</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sex</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($selectedSession->dataRecords as $record)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $record->dimension->name ?? 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $record->indicator->name ?? 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $record->region }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $record->sex }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $record->age }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ number_format($record->value, 2) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                        {{ $record->status === 'draft' ? 'bg-gray-100 text-gray-800' :
                                                           ($record->status === 'submitted' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                                        {{ ucfirst($record->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Loading Indicator -->
    {{-- <div wire:loading class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-4 rounded-lg shadow-lg">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
            <p class="mt-2 text-sm text-gray-600">Processing...</p>
        </div>
    </div> --}}

    <!-- Success/Error Messages -->
    @if (session()->has('message'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif
</div>
