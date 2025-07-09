<div>
    <section class="px-2 py-12 mx-auto md:px-12 lg:px-32 max-w-7xl">
        <div class="max-w-lg mx-auto md:max-w-xl md:w-full">
            <div class="flex flex-col text-center">
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Registration Form</h1>
            </div>

            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mt-4" role="alert">
                    <span class="block sm:inline">{{ session('message') }} Please Login <a class="text-blue-600" href="{{ route('login') }}">Here</a></span>
                </div>
            @endif

            <div class="p-2 mt-8 border bg-gray-50 rounded-3xl">
                <div class="p-4 md:p-10 bg-white border shadow-lg rounded-2xl">
                    <form wire:submit.prevent="submit">

                        <!-- Personal Information Section -->
                        <div>
                            <h2 class="text-lg font-medium text-gray-500">
                                Personal Information
                            </h2>

                            <div class="mt-6 gap-2 lg:columns-2 sm:columns-1">
                                <div class="w-full">
                                    <label for="first_name" class="text-sm text-gray-700 font-bold">Firstname <span class="text-red-600">*</span></label>
                                    <input type="text" id="first_name" wire:model.live="first_name"
                                        class="w-full h-12 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    @error('first_name')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="w-full">
                                    <label for="middle_name" class="text-sm text-gray-700 font-bold">Middlename</label>
                                    <input type="text" id="middle_name" wire:model.live="middle_name"
                                        class="w-full h-12 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                </div>
                            </div>

                            <div class="mt-4 gap-2 lg:columns-2 sm:columns-1">
                                <div class="w-full">
                                    <label for="last_name" class="text-sm text-gray-700 font-bold">Lastname <span class="text-red-600">*</span></label>
                                    <input type="text" id="last_name" wire:model.live="last_name"
                                        class="w-full h-12 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    @error('last_name')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="w-full">
                                    <label for="name_extension" class="text-sm text-gray-700 font-bold">Name Extension</label>
                                    <select id="name_extension" wire:model.live="name_extension"
                                        class="w-full h-12 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <option value="">None</option>
                                        <option value="Jr.">Jr.</option>
                                        <option value="Sr.">Sr.</option>
                                        <option value="II">II</option>
                                        <option value="III">III</option>
                                        <option value="IV">IV</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Section -->
                        <div class="mt-8">
                            <h2 class="text-lg font-medium text-gray-500">
                                Contact Information
                            </h2>

                            <div class="mt-6 gap-2 lg:columns-2 sm:columns-1">
                                <div class="w-full">
                                    <label for="email" class="text-sm text-gray-700 font-bold">Email <span class="text-red-600">*</span></label>
                                    <input type="email" id="email" wire:model.live="email"
                                        class="w-full h-12 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    @error('email')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="w-full">
                                    <label for="mobile_number" class="text-sm text-gray-700 font-bold">Mobile Number <span class="text-red-600">*</span></label>
                                    <input type="tel" id="mobile_number" wire:model.live="mobile_number"
                                        class="w-full h-12 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    @error('mobile_number')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Address Information Section -->
                        <div class="mt-8">
                            <h2 class="text-lg font-medium text-gray-500">
                                Address Information
                            </h2>

                            <div class="mt-6 gap-2 lg:columns-2 sm:columns-1">
                                <div class="w-full">
                                    <label for="block" class="text-sm text-gray-700 font-bold">Block <span class="text-red-600">*</span></label>
                                    <select id="block" wire:model.live="block"
                                        class="w-full h-12 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <option value="">Select Block</option>
                                        @foreach($blocks as $blockNumber)
                                            <option value="{{ $blockNumber }}">{{ $blockNumber }}</option>
                                        @endforeach
                                    </select>
                                    @error('block') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="w-full">
                                    <label for="lot" class="text-sm text-gray-700 font-bold">Lot <span class="text-red-600">*</span></label>
                                    <select id="lot" wire:model.live="lot"
                                        class="w-full h-12 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <option value="">Select Lot</option>
                                        @if($lots)
                                            @foreach($lots as $lotNumber)
                                                <option value="{{ $lotNumber }}">{{ $lotNumber }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('lot') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="mt-4 gap-2 lg:columns-1 sm:columns-1">
                                <div class="w-full">
                                    <label for="street" class="text-sm text-gray-700 font-bold">Street <span class="text-red-600">*</span></label>
                                    <select id="street" wire:model.live="street"
                                        class="w-full h-12 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                        <option value="">Select Street</option>
                                        <option value="Blackthorn">Blackthorn</option>
                                        <option value="Periwinkle">Periwinkle</option>
                                        <option value="Sunny Sky">Sunny Sky</option>
                                        <option value="Hyacinth">Hyacinth</option>
                                        <option value="Shooting Star">Shooting Star</option>
                                        <option value="Stargazer">Stargazer</option>
                                        <option value="Hawthorn">Hawthorn</option>
                                        <option value="Blue Irish">Blue Irish</option>
                                        <option value="Sweet Amber">Sweet Amber</option>
                                    </select>
                                    @error('street') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Government Information Section -->
                        <div class="mt-8">
                            <h2 class="text-lg font-medium text-gray-500">
                                Government Information
                            </h2>

                            <div class="mt-6 gap-2 lg:columns-2 sm:columns-1">
                                <div class="w-full">
                                    <label for="position_designation" class="text-sm text-gray-700 font-bold">Position/Designation <span class="text-red-600">*</span></label>
                                    <input type="text" id="position_designation" wire:model.live="position_designation" list="positionDesignations"
                                        class="w-full h-12 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    <datalist id="positionDesignations">
                                        @foreach($positions as $position)
                                            <option value="{{ $position }}">
                                        @endforeach
                                    </datalist>
                                    @error('position_designation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="w-full">
                                    <label for="government_agency" class="text-sm text-gray-700 font-bold">Government Agency <span class="text-red-600">*</span></label>
                                    <input type="text" id="government_agency" wire:model.live="government_agency" list="governmentAgencies"
                                        class="w-full h-12 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    <datalist id="governmentAgencies">
                                        @foreach($agencies as $agency)
                                            <option value="{{ $agency }}">
                                        @endforeach
                                    </datalist>
                                    @error('government_agency') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="mt-4 gap-2 lg:columns-2 sm:columns-1">
                                <div class="w-full">
                                    <label for="office_department_division" class="text-sm text-gray-700 font-bold">Office/Department/Division <span class="text-red-600">*</span></label>
                                    <input type="text" id="office_department_division" wire:model.live="office_department_division" list="officeDepartments"
                                        class="w-full h-12 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    <datalist id="officeDepartments">
                                        @foreach($departments as $department)
                                            <option value="{{ $department }}">
                                        @endforeach
                                    </datalist>
                                    @error('office_department_division') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="w-full">
                                    <label for="office_address" class="text-sm text-gray-700 font-bold">Office Address <span class="text-red-600">*</span></label>
                                    <input type="text" id="office_address" wire:model.live="office_address"
                                        class="w-full h-12 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    @error('office_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Account Information Section -->
                        <div class="mt-8">
                            <h2 class="text-lg font-medium text-gray-500">
                                Account Information
                            </h2>

                            <div class="mt-6 gap-2 lg:columns-2 sm:columns-1">
                                <div>
                                    <div class="relative inline-block" x-data="{ tooltip: false }">
                                        <i class="bi bi-info-circle-fill text-blue-700 cursor-pointer"
                                        @mouseenter="tooltip = true" @mouseleave="tooltip = false"></i>
                                        <div x-show="tooltip"
                                            class="absolute left-full top-1/2 transform -translate-y-1/2 ml-2 z-10 w-auto px-4 py-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm dark:bg-gray-700 transition-opacity duration-300"
                                            style="display: none;">
                                            <ul class="list-none space-y-2 whitespace-nowrap">
                                                <li>- At least 8 characters</li>
                                                <li>- One uppercase letter</li>
                                                <li>- One number</li>
                                                <li>- One special character</li>
                                            </ul>
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>
                                    </div>
                                    <label class="text-sm text-gray-700 font-bold" for="password">
                                        Password:
                                        <span class="text-red-600">*</span>
                                    </label>
                                    <input type="password" id="password" wire:model.live="password"
                                        class="w-full h-12 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <div class="relative inline-block" x-data="{ tooltip: false }">
                                        <i class="bi bi-info-circle-fill text-blue-700 cursor-pointer"
                                        @mouseenter="tooltip = true" @mouseleave="tooltip = false"></i>
                                        <div x-show="tooltip"
                                            class="absolute left-full top-1/2 transform -translate-y-1/2 ml-2 z-10 w-auto px-4 py-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm dark:bg-gray-700 transition-opacity duration-300"
                                            style="display: none;">
                                            <ul class="list-none space-y-2 whitespace-nowrap">
                                                <li>- At least 8 characters</li>
                                                <li>- One uppercase letter</li>
                                                <li>- One number</li>
                                                <li>- One special character</li>
                                            </ul>
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>
                                    </div>
                                    <label class="text-sm text-gray-700 font-bold" for="c_password">
                                        Confirm Password:
                                        <span class="text-red-600">*</span>
                                    </label>
                                    <input type="password" id="c_password" wire:model.live="c_password"
                                        class="w-full h-12 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm">
                                    @error('c_password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8 gap-2 lg:columns-1 sm:columns-1">
                            <div class="flex justify-end">
                                <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                                class="inline-flex items-center justify-center w-full h-12 gap-3 px-5 py-3 font-medium text-white bg-blue-700 rounded-xl hover:bg-blue-500 focus:ring-2 focus:ring-offset-2 focus:ring-black">
                                <span wire:loading.remove wire:target="submit">Submit</span>
                                <span wire:loading wire:target="submit">Loading...</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

     <!-- Success Modal -->
     <x-modal id="showModal" maxWidth="2xl" wire:model="showModal" centered>
        <div class="p-4 w-full">
            <div class="w-full max-w-full text-center">
                <h2 class="text-3xl font-semibold mb-6">Congratulations!</h2>
                <p class="mb-6">Your registration was successful. You can now log in.</p>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center px-6 py-3 text-white bg-blue-700 rounded-lg hover:bg-blue-500">
                    Go to Login
                </a>
            </div>
        </div>
    </x-modal>
</div>
