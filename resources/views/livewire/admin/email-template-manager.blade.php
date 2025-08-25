<div class="p-6">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-3 sm:p-6">
            <div class="w-full mb-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Email Templates Manager</h2>
                    {{-- <button 
                        wire:click="createTemplate" 
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Create New Template
                    </button> --}}
                </div>

                @if (session()->has('message'))
                    <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('message') }}</span>
                    </div>
                @endif
            </div>

            <!-- Templates List -->
            <div class="rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-600 dark:text-gray-300">
                            @forelse($templates as $template)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        {{ $template->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $template->subject }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button 
                                            wire:click="toggleStatus({{ $template->id }})"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}"
                                        >
                                            {{ $template->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap text-sm font-medium space-x-2">
                                        <button 
                                            wire:click="editTemplate({{ $template->id }})"
                                            class="text-indigo-600 hover:text-indigo-900"
                                        >
                                            Edit
                                        </button>
                                        {{-- <button 
                                            wire:click="deleteTemplate({{ $template->id }})"
                                            onclick="return confirm('Are you sure you want to delete this template?')"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Delete
                                        </button> --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        No templates found. Create your first template!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 flex items-start justify-center bg-black bg-opacity-50 z-50 overflow-y-auto">
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg w-full max-w-2xl my-8">
                <div class="inline-block align-bottom text-left w-full rounded-lg overflow-hidden">
                    <form wire:submit.prevent="saveTemplate">
                        <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-700 dark:text-gray-200 mb-4">
                                        {{ $isEditing ? 'Edit Template' : 'Create New Template' }}
                                    </h3>

                                    <div class="grid grid-cols-1 mb-4">
                                        <div>
                                            <label class="block text-sm font-medium">Template Name</label>
                                            <p class="text-md font-semibold text-gray-700 dark:text-gray-200">{{ $name }}</p>
                                            {{-- <input 
                                                type="text" 
                                                wire:model="name" 
                                                class="mt-1 block dark:bg-slate-700 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                placeholder="e.g., training_invitation"
                                            > --}}
                                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 mb-4">
                                        <div>
                                            <label class="block text-sm font-medium">Subject</label>
                                            <input 
                                                type="text" 
                                                wire:model="subject" 
                                                class="mt-1 block dark:bg-slate-700 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                            @error('subject') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-sm font-medium">Header</label>
                                            <input 
                                                type="text" 
                                                wire:model="header" 
                                                class="mt-1 block w-full dark:bg-slate-700 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                            @error('header') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium">Greetings</label>
                                            <input 
                                                type="text" 
                                                wire:model="greetings" 
                                                class="mt-1 block w-full dark:bg-slate-700 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                            @error('greetings') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2">Message Body (Insert &lt;br&gt; syntax to add new line)</label>
                                        <textarea wire:model="message_body" rows="5"
                                            class="mt-1 block w-full dark:bg-slate-700 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        ></textarea>
                                        
                                        @error('message_body') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium">Footer</label>
                                        <textarea 
                                            wire:model="footer" 
                                            rows="2"
                                            class="mt-1 block w-full rounded-md dark:bg-slate-700 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        ></textarea>
                                        @error('footer') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-sm font-medium">Action Button Text</label>
                                            <input 
                                                type="text" 
                                                wire:model="action_button_text" 
                                                class="mt-1 block w-full dark:bg-slate-700 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                placeholder="e.g., View Training Invitation"
                                            >
                                            @error('action_button_text') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium">Action Button URL</label>
                                            <input 
                                                type="url" 
                                                wire:model="action_button_url" 
                                                class="mt-1 block dark:bg-slate-700 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                placeholder="https://example.com"
                                            >
                                            @error('action_button_url') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                wire:model="is_active" 
                                                class="rounded border-gray-300 dark:bg-slate-700 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                            <span class="ml-2 text-sm">Active</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-slate-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button 
                                type="submit" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                {{ $isEditing ? 'Update' : 'Create' }}
                            </button>
                            <button 
                                type="button" 
                                wire:click="closeModal" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white dark:bg-slate-800 text-base font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>