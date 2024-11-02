<div class="w-full"
x-data="{ 
    selectedTab: '{{ request()->query('mainTab', 'org') }}',
    selectedSubTab: '{{ request()->query('tab', 'bod') }}',
    selectedSubTab2: '{{ request()->query('subTab', 'active') }}',
    activeStatus: {{ request()->query('activeStatus', 1) }},
}" 
x-cloak>

    <style>
        .scrollbar-thin1::-webkit-scrollbar {
                width: 5px;
            }

        .scrollbar-thin1::-webkit-scrollbar-thumb {
            background-color: #c0c0c04b;
        }

        .scrollbar-thin1::-webkit-scrollbar-track {
            background-color: #ffffff23;
        }

        @media (max-width: 1024px){
            .custom-d{
                display: block;
            }
        }

        @media (max-width: 768px){
            .m-scrollable{
                width: 100%;
                overflow-x: scroll;
            }
        }

        @media (min-width:1024px){
            .custom-p{
                padding-bottom: 14px !important;
            }
        }

        @-webkit-keyframes spinner-border {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes spinner-border {
            to {
                transform: rotate(360deg);
            }
        }

        .spinner-border {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            vertical-align: text-bottom;
            border: 2px solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            -webkit-animation: spinner-border .75s linear infinite;
            animation: spinner-border .75s linear infinite;
            color: rgb(0, 255, 42);
        }
    </style>

    <div class="flex justify-center w-full">
        <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800 overflow-x-visible">
            <div class="pb-4 mb-3 pt-4 sm:pt-0">
                <h1 class="text-lg font-bold text-center text-slate-800 dark:text-white">Association Management</h1>
            </div>

            <!-- Table -->
            <div class="w-full">
                <div class="flex flex-col">
                    <div class="flex gap-2 overflow-x-auto -mb-2">
                        <button @click="selectedTab = 'org'" 
                                :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-t-lg': selectedTab === 'org', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': selectedTab !== 'org' }" 
                                class="h-min px-4 pt-2 pb-4 text-sm text-nowrap relative">
                            The Association
                            <span class="px-2 py-1 rounded-lg text-red-500 text-lg absolute -top-2 -right-1 {{ $registering ? '' : 'hidden' }}">•</span>
                        </button>
                        <button @click="selectedTab = 'role'" 
                                :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-t-lg': selectedTab === 'role', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': selectedTab !== 'role' }" 
                                class="h-min px-4 pt-2 pb-4 text-sm text-nowrap">
                            Administrator
                        </button>
                        <button @click="selectedTab = 'settings'" 
                                :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-t-lg': selectedTab === 'settings', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': selectedTab !== 'settings' }" 
                                class="h-min px-4 pt-2 pb-4 text-sm text-nowrap">
                            Settings
                        </button>
                    </div>
                    <div class="-my-2 overflow-x-auto">
                        <div class="inline-block w-full py-2 align-middle">
                            <div>
                                <div class="overflow-hidden border dark:border-gray-700 rounded-lg">
                                    <div x-show="selectedTab === 'org'">
                                        <div class="overflow-x-hidden">
                                            <div class="flex gap-2 overflow-x-auto dark:bg-gray-700 bg-gray-200 rounded-t-lg border-b border-gray-300 dark:border-gray-600">
                                                <button @click="selectedSubTab = 'bod'" 
                                                        :class="{ 'font-bold text-gray-800 dark:text-gray-200 border-b-2 border-gray-800 dark:border-gray-200': selectedSubTab === 'bod', 'text-gray-500 font-medium dark:text-gray-500 dark:hover:text-white hover:text-black': selectedSubTab !== 'bod' }" 
                                                        class="h-min px-4 pt-2 pb-2 text-sm no-wrap">
                                                    BOD
                                                </button>
                                                <button @click="selectedSubTab = 'committees'" 
                                                        :class="{ 'font-bold text-gray-800 dark:text-gray-200 border-b-2 border-gray-800 dark:border-gray-200': selectedSubTab === 'committees', 'text-gray-500 font-medium dark:text-gray-500 dark:hover:text-white hover:text-black': selectedSubTab !== 'committees' }" 
                                                        class="h-min px-4 pt-2 pb-2 text-sm no-wrap">
                                                    Committees
                                                </button>
                                                <button @click="selectedSubTab = 'homeowners'" 
                                                        :class="{ 'font-bold text-gray-800 dark:text-gray-200 border-b-2 border-gray-800 dark:border-gray-200': selectedSubTab === 'homeowners', 'text-gray-500 font-medium dark:text-gray-500 dark:hover:text-white hover:text-black': selectedSubTab !== 'homeowners' }" 
                                                        class="h-min px-4 pt-2 pb-2 text-sm no-wrap relative">
                                                    Homeowners
                                                    <span class="px-2 py-1 rounded-lg text-red-500 text-lg absolute -top-2 -right-2 {{ $registering ? '' : 'hidden' }}">•</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700" x-show="selectedSubTab === 'bod'">
                                            <section class="py-6">
                                                <div class="container flex flex-col items-center justify-center p-4 mx-auto space-y-8 sm:p-10">
                                                    <h1 class="text-4xl font-bold leading-none text-center sm:text-5xl text-gray-500 dark:text-gray-300">Board of Directors</h1>
                                                    <p class="max-w-2xl text-center dark:text-gray-600"></p>
                                                    <div class="flex flex flex-wrap justify-center">
                                                        @foreach ($bods as $bod)
                                                            @if(strtolower((string) $bod->position) == 'president' || strtolower((string) $bod->position) == 'vice president')
                                                                <div class="flex flex-col justify-center m-8 text-center">
                                                                    @if ($bod->profile_photo_path)
                                                                        <img src="{{ route('profile-photo.file', ['filename' => basename($bod->profile_photo_path)]) }}" 
                                                                                alt="{{ $bod->name }}" 
                                                                                class="self-center flex-shrink-0 w-24 h-24 mb-4 rounded-full dark:bg-gray-500 border-4 border-gray-300 dark:border-gray-500 object-cover object-center" style="pointer-events:none">
                                                                    @else
                                                                        <img alt="BOD Photo" class="self-center flex-shrink-0 w-24 h-24 mb-4 bg-center bg-cover rounded-full dark:bg-gray-500 border-4 border-gray-300 dark:border-gray-500" src="/images/blank-profile.png"> 
                                                                    @endif
                                                                    <p class="dark:text-gray-400 text-xs"><i class="bi bi-house-fill"></i> {{ $bod->block ? ('B' . $bod->block) : '' }} {{ $bod->lot ? ('L' . $bod->lot) : '' }}</p>
                                                                    <p class="text-l font-semibold leading-tight">{{ $bod->name ?: 'Vacant' }}</p>
                                                                    <p class="dark:text-gray-400">{{ $bod->position }}
                                                                        @if(Auth::user()->user_role != 'homeowner')
                                                                            <span class="text-xs cursor-pointer hover:text-gray-600"><i class="fas fa-pencil-alt" wire:click="toggleSetPos({{ $bod->id }}, {{ $bod->posId }}, {{ $bod->userId ?: 0 }}, '{{ $bod->position }}')" title="Edit"></i></span>
                                                                        @endif                                                                    </p>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    <div class="flex flex flex-wrap justify-center">
                                                        @foreach ($bods as $bod)
                                                            @if(strtolower((string) $bod->position) != 'board member' && strtolower((string) $bod->position) != 'president' && strtolower((string) $bod->position) != 'vice president')
                                                                <div class="flex flex-col justify-center m-8 text-center">
                                                                    @if ($bod->profile_photo_path)
                                                                        <img src="{{ route('profile-photo.file', ['filename' => basename($bod->profile_photo_path)]) }}" 
                                                                                alt="{{ $bod->name }}" 
                                                                                class="self-center flex-shrink-0 w-24 h-24 mb-4 rounded-full dark:bg-gray-500 border-4 border-gray-300 dark:border-gray-500 object-cover object-center" style="pointer-events:none">
                                                                    @else
                                                                        <img alt="BOD Photo" class="self-center flex-shrink-0 w-24 h-24 mb-4 bg-center bg-cover rounded-full dark:bg-gray-500 border-4 border-gray-300 dark:border-gray-500" src="/images/blank-profile.png"> 
                                                                    @endif
                                                                    <p class="dark:text-gray-400 text-xs"><i class="bi bi-house-fill"></i> {{ $bod->block ? ('B' . $bod->block) : '' }} {{ $bod->lot ? ('L' . $bod->lot) : '' }}</p>
                                                                    <p class="text-l font-semibold leading-tight">{{ $bod->name ?: 'Vacant' }}</p>
                                                                    <p class="dark:text-gray-400">{{ $bod->position == 'Public Relations Officer' || $bod->position == 'public relations officer' || $bod->position == 'PUBLIC RELATIONS OFFICER' ? 'PRO' : $bod->position }}
                                                                        @if(Auth::user()->user_role != 'homeowner')
                                                                            <span class="text-xs cursor-pointer hover:text-gray-600"><i class="fas fa-pencil-alt" wire:click="toggleSetPos({{ $bod->id }}, {{ $bod->posId }}, {{ $bod->userId ?: 0 }}, '{{ $bod->position }}')" title="Edit"></i></span>
                                                                        @endif                                                                    </p>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    <h1 class="text-2xl font-bold leading-none text-center sm:text-5xl text-gray-500 dark:text-gray-300">Board Members</h1>
                                                    <div class="bg-gray-500" style="height: 1px; width: 50%;"></div>
                                                    <div class="flex flex-row flex-wrap justify-center">
                                                        @foreach ($bods as $bod)
                                                            @if(strtolower((string) $bod->position) == 'board member')
                                                                <div class="flex flex-col justify-center m-8 text-center">
                                                                    @if ($bod->profile_photo_path)
                                                                        <img src="{{ route('profile-photo.file', ['filename' => basename($bod->profile_photo_path)]) }}" 
                                                                                alt="{{ $bod->name }}" 
                                                                                class="self-center flex-shrink-0 w-24 h-24 mb-4 rounded-full dark:bg-gray-500 border-4 border-gray-300 dark:border-gray-500 object-cover object-center" style="pointer-events:none">
                                                                    @else
                                                                        <img alt="BOD Photo" class="self-center flex-shrink-0 w-24 h-24 mb-4 bg-center bg-cover rounded-full dark:bg-gray-500 border-4 border-gray-300 dark:border-gray-500" src="/images/blank-profile.png"> 
                                                                    @endif
                                                                    <p class="dark:text-gray-400 text-xs"><i class="bi bi-house-fill"></i> {{ $bod->block ? ('B' . $bod->block) : '' }} {{ $bod->lot ? ('L' . $bod->lot) : '' }}</p>
                                                                    <p class="text-l font-semibold leading-tight">{{ $bod->name ?: 'Vacant' }}</p>
                                                                    <p class="dark:text-gray-400">{{ $bod->position }} 
                                                                        @if(Auth::user()->user_role != 'homeowner')
                                                                            <span class="text-xs cursor-pointer hover:text-gray-600"><i class="fas fa-pencil-alt" wire:click="toggleSetPos({{ $bod->id }}, {{ $bod->posId }}, {{ $bod->userId ?: 0 }}, '{{ $bod->position }}')" title="Edit"></i></span>
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                        <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700" x-show="selectedSubTab === 'committees'">
                                            @foreach ($comms as $committeeName => $users)
                                                <div class="flex justify-between items-center w-full py-1.5 bg-gray-50 dark:bg-gray-800 px-4">
                                                    <div class="flex items-end justify-center">
                                                        <i class="bi bi-building mr-2 text-emerald-500 dark:text-emerald-300 mr-2"></i>
                                                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-300">{{ $committeeName }}</h3>
                                                    </div>
                                                    <button wire:click="exportEmployeesPerUnit(null, {{ $users->first()->id }})"
                                                        class="peer inline-flex items-center justify-center
                                                        text-sm font-medium tracking-wide text-green-500 hover:text-green-600 focus:outline-none"
                                                        title="Export List">
                                                        <img class="flex dark:hidden" src="/images/icons8-xls-export-dark.png" width="18" alt="" 
                                                            wire:loading.remove wire:target="exportEmployeesPerUnit(null, {{ $users->first()->id }})">
                                                        <img class="hidden dark:block" src="/images/icons8-xls-export-light.png" width="18" alt="" 
                                                            wire:loading.remove wire:target="exportEmployeesPerUnit(null, {{ $users->first()->id }})">
                                                        <div wire:loading wire:target="exportEmployeesPerUnit(null, {{ $users->first()->id }})" style="margin-left: 5px">
                                                            <div class="spinner-border small text-primary" role="status">
                                                            </div>
                                                        </div>
                                                    </button>
                                                </div>

                                                <div class="w-full p-4 flex flex-col mb-6 bg-white dark:bg-gray-600">
                                                    <div class="w-full">
                                                        <div class="flex flex flex-wrap justify-left sm:justify-left gap-2">
                                                            @foreach ($users as $officer)
                                                                <div class="flex flex-col justify-center m-8 text-center w-52 p-2 rounded-lg">
                                                                    @if ($officer->profile_photo_path)
                                                                        <img src="{{ route('profile-photo.file', ['filename' => basename($officer->profile_photo_path)]) }}" 
                                                                                alt="{{ $officer->name }}" 
                                                                                class="self-center flex-shrink-0 w-24 h-24 mb-4 rounded-full dark:bg-gray-500 border-4 border-gray-300 dark:border-gray-500 object-cover object-center" style="pointer-events:none">
                                                                    @else
                                                                        <img alt="BOD Photo" class="self-center flex-shrink-0 w-24 h-24 mb-4 bg-center bg-cover rounded-full dark:bg-gray-500 border-4 border-gray-300 dark:border-gray-500" src="/images/blank-profile.png"> 
                                                                    @endif
                                                                    <p class="dark:text-gray-400 text-xs"><i class="bi bi-house-fill"></i> {{ $officer->block ? ('B' . $officer->block) : '' }} {{ $officer->lot ? ('L' . $officer->lot) : '' }}</p>
                                                                    <p class="text-l font-semibold leading-tight">{{ $officer->name ?: 'Vacant' }}</p>
                                                                    <p class="dark:text-gray-400">{{ $officer->position }}
                                                                        @if(Auth::user()->user_role != 'homeowner')
                                                                            <span class="text-xs cursor-pointer hover:text-gray-700"><i class="fas fa-pencil-alt" wire:click="toggleSetPos({{ $officer->id }}, {{ $officer->posId }}, {{ $officer->userId ?: 0 }}, '{{ $officer->position }}')" title="Edit"></i></span>
                                                                        @endif                                                                    
                                                                    </p>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>                                      
                                            @endforeach
                                        </div>
                                        <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700" x-show="selectedSubTab === 'homeowners'">
                                            <div class="mb-6 flex flex-col sm:flex-row items-end justify-between">
                                                <div class="w-full sm:w-1/3 sm:mr-4">
                                                    <label for="search2" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Search</label>
                                                    <input type="text" id="search2" wire:model.live="search2"
                                                        class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 rounded-md dark:border-slate-600
                                                            dark:text-gray-300 dark:bg-gray-800"
                                                        placeholder="Search homeowners' name">
                                                </div>

                                                <div class="w-full sm:w-2/3 flex flex-col sm:flex-row sm:justify-end sm:space-x-4">
                                                    <!-- Export to Excel -->
                                                    <div class="relative inline-block text-left">
                                                        <button wire:click="exportList"
                                                            class="peer mt-4 sm:mt-1 inline-flex items-center dark:hover:bg-slate-600 dark:border-slate-600
                                                            justify-center px-4 py-1.5 text-sm font-medium tracking-wide 
                                                            text-neutral-800 dark:text-neutral-200 transition-colors duration-200 
                                                            rounded-lg border border-gray-400 hover:bg-gray-300 focus:outline-none"
                                                            type="button" title="Export">
                                                            <img class="flex dark:hidden" src="/images/export-excel.png" width="22" alt="" wire:target="exportList" wire:loading.remove>
                                                            <img class="hidden dark:block" src="/images/export-excel-dark.png" width="22" alt="" wire:target="exportList" wire:loading.remove>
                                                            <div wire:loading wire:target="exportList">
                                                                <div class="spinner-border small text-primary" role="status">
                                                                </div>
                                                            </div>
                                                        </button>                    
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="overflow-x-hidden">
                                                <div class="flex gap-2 overflow-x-auto dark:bg-gray-700 bg-gray-200 rounded-t-lg">
                                                    <button @click="selectedSubTab2 = 'active'" 
                                                            wire:click="setActiveStatus(1)"
                                                            :class="{ 'font-bold text-gray-800 dark:text-gray-200 border-b-2 border-gray-800 dark:border-gray-200': selectedSubTab2 === 'active', 'text-gray-500 font-medium dark:text-gray-500 dark:hover:text-white hover:text-black': selectedSubTab2 !== 'active' }" 
                                                            class="h-min px-4 pt-2 pb-2 text-sm no-wrap">
                                                        Active
                                                    </button>
                                                    <button @click="selectedSubTab2 = 'inactive'" 
                                                            wire:click="setActiveStatus(2)"
                                                            :class="{ 'font-bold text-gray-800 dark:text-gray-200 border-b-2 border-gray-800 dark:border-gray-200': selectedSubTab2 === 'inactive', 'text-gray-500 font-medium dark:text-gray-500 dark:hover:text-white hover:text-black': selectedSubTab2 !== 'inactive' }" 
                                                            class="h-min px-4 pt-2 pb-2 text-sm no-wrap">
                                                        Inactive
                                                    </button>
                                                    <button @click="selectedSubTab2 = 'registering'" 
                                                            wire:click="setActiveStatus(0)"
                                                            :class="{ 'font-bold text-gray-800 dark:text-gray-200 border-b-2 border-gray-800 dark:border-gray-200': selectedSubTab2 === 'registering', 'text-gray-500 font-medium dark:text-gray-500 dark:hover:text-white hover:text-black': selectedSubTab2 !== 'registering' }" 
                                                            class="h-min px-4 pt-2 pb-2 text-sm no-wrap">
                                                        Unapproved
                                                        <span class="bg-red-500 px-2 py-1 rounded-lg text-white text-xs {{ $registering ? '' : 'hidden' }}">{{ $registering }}</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="overflow-x-auto">
                                                <div>
                                                    <table class="w-full min-w-full border border-gray-300 dark:border-gray-600">
                                                        <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                                                            <tr class="whitespace-nowrap">
                                                                <th scope="col" class="px-5 py-3 text-gray-100 text-sm font-medium text-left uppercase bg-gray-600 dark:bg-gray-600">
                                                                </th>
                                                                <th scope="col" class="px-5 py-3 text-gray-100 text-sm font-medium text-left uppercase bg-gray-600 dark:bg-gray-600">
                                                                    Name
                                                                </th>
                                                                <th scope="col" class="px-5 py-3 text-gray-100 text-sm font-medium text-left uppercase bg-gray-600 dark:bg-gray-600">
                                                                    House Number
                                                                </th>
                                                                <th scope="col" class="px-5 py-3 text-gray-100 text-sm font-medium text-left uppercase bg-gray-600 dark:bg-gray-600">
                                                                    Street
                                                                </th>
                                                                <th scope="col" class="px-5 py-3 text-gray-100 text-sm font-medium text-center uppercase bg-gray-600 dark:bg-gray-600">
                                                                    Email
                                                                </th>
                                                                <th scope="col" class="px-5 py-3 text-gray-100 text-sm font-medium text-center uppercase bg-gray-600 dark:bg-gray-600">
                                                                    Contact Number
                                                                </th>
                                                                <th class="px-5 py-3 text-gray-100 text-sm font-medium text-center uppercase sticky right-0 z-10 bg-gray-600 dark:bg-gray-600">
                                                                    Action
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-300 dark:divide-gray-600">
                                                            @foreach ($homeowners as $ho)
                                                                <tr class="text-neutral-800 dark:text-neutral-200">
                                                                    <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap bg-white dark:bg-gray-800">
                                                                        <div class="flex justify-center items-center" style="width: 50px; height: 50px">
                                                                            @if ($ho->profile_photo_path)
                                                                                <img src="{{ route('profile-photo.file', ['filename' => basename($ho->profile_photo_path)]) }}" 
                                                                                        alt="{{ Auth::user()->name }}" 
                                                                                        class="w-full h-full rounded-full hover:grayscale" style="pointer-events:none">
                                                                            @else
                                                                                <img class="w-full h-full rounded-full hover:grayscale" src="{{ asset('images/blank-profile.png') }}" alt=""  style="pointer-events:none">
                                                                            @endif
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap bg-white dark:bg-gray-800">
                                                                        {{ $ho->first_name }}{{ $ho->middle_name ? (' ' . substr($ho->middle_name, 0, 1) . '.') : '' }} {{ $ho->last_name }} {{ $ho->name_extension }}
                                                                    </td>
                                                                    <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap bg-white dark:bg-gray-800">
                                                                    Blk. {{ $ho->block }} Lot {{ $ho->lot }}
                                                                    </td>
                                                                    <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap bg-white dark:bg-gray-800">
                                                                        {{ $ho->street }}
                                                                    </td>
                                                                    <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap bg-white dark:bg-gray-800">
                                                                        {{ $ho->email }}
                                                                    </td>
                                                                    <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap bg-white dark:bg-gray-800">
                                                                        {{ $ho->tel_number }} {{ $ho->mobile_number }}
                                                                    </td>
                                                                    <td class="px-5 py-4 text-sm font-medium text-center whitespace-nowrap sticky right-0 z-10 bg-white dark:bg-gray-800">
                                                                        <div class="relative">
                                                                            @if($activeStatus === 1)
                                                                                <button wire:click="toggleApprove({{ $ho->user_id }}, 'deactivate')" 
                                                                                    class=" text-orange-500 hover:text-orange-600 dark:text-orange-600 pr-2
                                                                                    dark:hover:text-orange-700" title="Deactivate">
                                                                                    <i class="fas fa-user-slash"></i>
                                                                                </button>
                                                                            @elseif($activeStatus === 0)
                                                                                <button wire:click="toggleApprove({{ $ho->user_id }}, 'approve')" 
                                                                                    class=" text-green-500 hover:text-green-600 dark:text-green-600 pr-2
                                                                                    dark:hover:text-green-700" title="Approve">
                                                                                    <i class="fas fa-user-check"></i>
                                                                                </button>
                                                                            @else
                                                                                <button wire:click="toggleApprove({{ $ho->user_id }}, 'activate')" 
                                                                                    class=" text-green-500 hover:text-green-600 dark:text-green-600 pr-2
                                                                                    dark:hover:text-green-700" title="Activate">
                                                                                    <i class="fas fa-user-check"></i>
                                                                                </button>
                                                                            @endif
                                                                            <button wire:click="toggleDelete({{ $ho->user_id }}, 'homeowner')" 
                                                                                class=" text-red-500 hover:text-red-600 dark:text-red-600 
                                                                                dark:hover:text-red-700" title="Delete">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                            </div>
                                            @if ($homeowners->isEmpty())
                                                <div class="p-4 text-center text-gray-500 dark:text-gray-300 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600" style="margin-top: -1px">
                                                    No records!
                                                </div> 
                                            @endif
                                            <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700">
                                                {{ $homeowners->links() }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700" x-show="selectedTab === 'role'">
                                        <div class="py-6 flex flex-col sm:flex-row items-end justify-between bg-gray-200 dark:bg-gray-700">
                                            <div class="w-full sm:w-1/3 sm:mr-4">
                                                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Search</label>
                                                <input type="text" id="search" wire:model.live="search"
                                                    class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                                                        dark:hover:bg-slate-600 dark:border-slate-600
                                                        dark:text-gray-300 dark:bg-gray-800"
                                                    placeholder="Search admin name">
                                            </div>
                                            <div class="w-full sm:w-2/3 flex flex-col sm:flex-row sm:justify-end sm:space-x-4">

                                                <div class="w-full sm:w-auto">
                                                    <button wire:click="toggleAddRole" 
                                                        class="mt-4 sm:mt-1 px-2 py-1.5 text-gray-800 rounded-md border border-gray-400 dark:border-slate-600
                                                        hover:bg-green-600 focus:outline-none w-full text-sm
                                                        dark:hover:bg-green-700 dark:text-gray-300 hover:text-white dark:hover:text-white">
                                                        Add Admin
                                                    </button>
                                                </div>
                            
                                                <!-- Export to Excel -->
                                                <div class="relative inline-block text-left">
                                                    <button wire:click="exportRoles"
                                                        class="peer mt-4 sm:mt-1 inline-flex items-center dark:hover:bg-slate-600 dark:border-slate-600
                                                        justify-center px-4 py-1.5 text-sm font-medium tracking-wide 
                                                        text-neutral-800 dark:text-neutral-200 transition-colors duration-200 
                                                        rounded-lg border border-gray-400 hover:bg-gray-300 focus:outline-none"
                                                        type="button" title="Export List">
                                                        <img class="flex dark:hidden" src="/images/export-excel.png" width="22" alt="">
                                                        <img class="hidden dark:block" src="/images/export-excel-dark.png" width="22" alt="">
                                                    </button>                    
                                                </div>
                            
                                            </div>
                                        </div>

                                        <div class="overflow-x-auto">
                                            <div>
                                                <table class="w-full min-w-full border border-gray-300 dark:border-gray-600">
                                                    <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                                                        <tr class="whitespace-nowrap">
                                                            <th scope="col" class="px-5 py-3 text-gray-100 text-sm font-medium text-left uppercase bg-gray-600 dark:bg-gray-600">
                                                                System Admin
                                                            </th>
                                                            <th scope="col" class="px-5 py-3 text-gray-100 text-sm font-medium text-left uppercase bg-gray-600 dark:bg-gray-600">
                                                                Contact Info
                                                            </th>
                                                            <th class="px-5 py-3 text-gray-100 text-sm font-medium text-center uppercase sticky right-0 z-10 bg-gray-600 dark:bg-gray-600">
                                                                Action
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-300 dark:divide-gray-600">
                                                        @foreach ($admins as $ho)
                                                            <tr class="text-neutral-800 dark:text-neutral-200">
                                                                <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap bg-white dark:bg-gray-800">
                                                                    <div class="w-full">
                                                                        <div class="flex justify-left items-center">
                                                                            @if ($ho->profile_photo_path)
                                                                                <img src="{{ route('profile-photo.file', ['filename' => basename($ho->profile_photo_path)]) }}" 
                                                                                        alt="{{ Auth::user()->name }}" 
                                                                                        class="rounded-full hover:grayscale border border-gray-400" style="width: 50px; height: 50px">
                                                                            @else
                                                                                <img class="rounded-full hover:grayscale border border-gray-400" src="{{ asset('images/blank-profile.png') }}" alt=""  style="width: 50px; height: 50px">
                                                                            @endif
                                                                            <p class="ml-4" style="line-height: 18px; margin-right: 100px">
                                                                                {{ $ho->first_name }}{{ $ho->middle_name ? (' ' . substr($ho->middle_name, 0, 1) . '.') : '' }} {{ $ho->last_name }} {{ $ho->name_extension }} <br>
                                                                                <span class="text-xs opacity-80" style="line-height: 13px">
                                                                                    {{ $ho->position }} <br>
                                                                                    Blk. {{ $ho->block }} Lot {{ $ho->lot }} - 
                                                                                    {{ $ho->street }}
                                                                                </span>
                                                                            </p>
                                                                        </div> 
                                                                    </div>
                                                                </td>
                                                                <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap bg-white dark:bg-gray-800">
                                                                    <i class="bi bi-envelope"></i> &nbsp;{{ $ho->email }} <br>
                                                                    <i class="bi bi-telephone"></i> &nbsp;{{ $ho->tel_number }} {{ $ho->mobile_number }}
                                                                </td>
                                                                <td class="px-5 py-4 text-sm font-medium text-center whitespace-nowrap sticky right-0 z-10 bg-white dark:bg-gray-800">
                                                                    <div class="relative">
                                                                        <button wire:click="toggleDelete({{ $ho->userId }}, 'admin')" 
                                                                            class=" text-red-500 hover:text-red-600 dark:text-red-600 
                                                                            dark:hover:text-red-700" title="Delete">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>

                                        @if ($admins->isEmpty())
                                            <div class="p-4 text-center text-gray-500 dark:text-gray-300 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600" style="margin-top: -1px">
                                                No records!
                                            </div> 
                                        @endif
                                        <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700">
                                            {{ $admins->links() }}
                                        </div>
                                    </div>
                                    <div x-show="selectedTab === 'settings'">
                                        <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700">
                                            <div class="mb-6 flex flex-col sm:flex-row items-end justify-between">

                                                <div class="w-full sm:w-1/3 sm:mr-4" x-show="selectedTab === 'settings'">
                                                    <label for="search4" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Search</label>
                                                    <input type="text" id="search4" wire:model.live="search4"
                                                        class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                                                            dark:hover:bg-slate-600 dark:border-slate-600
                                                            dark:text-gray-300 dark:bg-gray-800"
                                                        placeholder="Search committee">
                                                </div>
                                
                                            </div>

                                            <table class="w-full min-w-full">
                                                <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl" style="height: 20px">
                                                    <tr class="whitespace-nowrap">
                                                        <td>
                                                            <div class="flex flex-col md:flex-col lg:flex-row lg:justify-between items-center w-full mb-2">
                                                                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase"><span>BOD | Committees | Positions</span></h3>
                                                                <div>
                                                                    <button wire:click="toggleAddSettings('committee')" 
                                                                        class="peer inline-flex items-center justify-center px-4 py-2 
                                                                        text-sm font-medium tracking-wide text-blue-500 hover:text-blue-600 
                                                                        focus:outline-none" title="Add">
                                                                        <i title="Add" class="fas fa-plus text-green-500"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </thead>
                                            </table>


                                            @foreach ($committees as $comm)

                                                <!-- Committee Header -->
                                                <div class="flex justify-between items-center w-full py-1.5 bg-gray-50 dark:bg-gray-800 px-4">
                                                    <div class="flex items-end">
                                                        <i class="bi bi-building mr-2 text-emerald-500 dark:text-emerald-300 mr-2"></i>
                                                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-300 uppercase">{{ $comm->committee == 'bod' ? 'Board of Directors' : $comm->committee }}</h3>
                                                    </div>
                                                    <div class="relative px-2">
                                                        <button wire:click="toggleEditSettings({{ $comm->id }}, 'committee')" 
                                                            class="peer inline-flex items-center justify-center py-2 lg:mr-2
                                                            text-xs font-medium tracking-wide text-blue-500 hover:text-blue-600 
                                                            focus:outline-none" title="Edit">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </button>
                                                        <button wire:click="toggleDeleteSettings({{ $comm->id }}, 'committee')" 
                                                            class="text-red-600 text-xs hover:text-red-900 dark:text-red-600 
                                                            dark:hover:text-red-900" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                        
                                                <div class="w-full p-4 flex flex-col mb-6 bg-white dark:bg-gray-600">
                                                    <div class="w-full">
                                                        <div class="flex justify-left items-center w-full">
                                                            <h3 class="text-xs font-semibold text-gray-300 dark:text-gray-500">POSITIONS</h3>
                                                            <div class="relative">
                                                                @if($comm->positions->isNotEmpty())
                                                                    <button wire:click="toggleEditPos({{ $comm->id }}, 'position')" 
                                                                        class="peer inline-flex items-center justify-center ml-4 mb-3 
                                                                        text-xs font-medium tracking-wide text-blue-500 hover:text-blue-600 
                                                                        focus:outline-none" title="Edit Positions">
                                                                        <i class="fas fa-pencil-alt" style="font-size: 10px"></i>
                                                                    </button>
                                                                @else
                                                                    <button wire:click="toggleAddPos({{ $comm->id }}, 'position')" 
                                                                        class="text-red-600 text-xs hover:text-red-900 dark:text-red-600 ml-4 mb-1
                                                                        dark:hover:text-red-900" title="Add Position">
                                                                        <i title="Add Position" class="fas fa-plus text-green-500"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <!-- Positions directly under the Office/Division -->
                                                        @if($comm->positions->isNotEmpty())
                                                            <ul class="ml-4 list-disc">
                                                                @foreach ($comm->positions as $position)
                                                                    <li class="text-sm text-gray-500 dark:text-gray-400">{{ $position->position }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                </div>                                       
                                            @endforeach
                                        </div>
                                     </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>




{{-- Delete Modal --}}
<x-modal id="deleteModal" maxWidth="md" wire:model="deleteId" centered>
    <div class="p-4">
        <div class="mb-4 text-slate-900 dark:text-gray-100 font-bold">
            Confirm Deletion
            <button @click="show = false" class="float-right focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">
            Are you sure you want to delete this {{ $deleteMessage }}?
        </label>
        <form wire:submit.prevent='deleteData'>
            <div class="mt-4 flex justify-end col-span-1 sm:col-span-1">
                <button class="mr-2 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    <div wire:loading wire:target="deleteData" style="margin-bottom: 5px;">
                        <div class="spinner-border small text-primary" role="status">
                        </div>
                    </div>
                    Delete
                </button>
                <p @click="show = false" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded cursor-pointer">
                    Cancel
                </p>
            </div>
        </form>
    </div>
</x-modal>

{{-- Approve/Deactivate/Activate Modal --}}
<x-modal id="approveModal" maxWidth="md" wire:model="approveId" centered>
    <div class="p-4">
        <div class="mb-4 text-slate-900 dark:text-gray-100 font-bold">
            Confirm <span class="capitalize">{{ $approveMessage }}</span>
            <button @click="show = false" class="float-right focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">
            Are you sure you want to {{ $approveMessage }} this homeowner?
        </label>
        <form wire:submit.prevent='approveUser'>
            <div class="mt-4 flex justify-end col-span-1 sm:col-span-1">
                <button class="mr-2 text-white font-bold py-2 px-4 rounded
                    {{ $approveMessage != 'deactivate' ? 'bg-green-500 hover:bg-green-700' : 'bg-orange-500 hover:bg-orange-700' }}
                    ">
                    <div wire:loading wire:target="approveUser" style="margin-bottom: 5px;">
                        <div class="spinner-border small text-primary" role="status">
                        </div>
                    </div>
                    <span class="capitalize">{{ $approveMessage }}</span>
                </button>
                <p @click="show = false" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded cursor-pointer">
                    Cancel
                </p>
            </div>
        </form>
    </div>
</x-modal>

{{-- Add and Commitee and Position Modal --}}
<x-modal id="posModal" maxWidth="2xl" wire:model="settings">
    <div class="p-4">
        <div class="mb-4 text-slate-900 dark:text-white font-bold uppercase">
            {{ $add ? 'Add' : 'Edit' }} {{ $data }}
            <button @click="show = false" class="float-right focus:outline-none" wire:click='resetVariables'>
                <i class="fas fa-times"></i>
            </button>
        </div>
        {{-- Form fields --}}
        <form wire:submit.prevent='saveSettings'>
            <div class="grid grid-cols-2 gap-4">
                
                @if($add)
                    @if($data === "committee")
                        <div class="col-span-2 relative">
                            <label for="settings_data" class="block text-sm font-medium text-gray-700 dark:text-slate-400 uppercase">{{ $data }}</label>
                            <input type="text" id="settings_data" wire:model='settings_data' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700" required>
                            @error('settings_data')
                                <span class="text-red-500 text-sm">This field is required!</span>
                            @enderror
                        </div>
                    @endif
                    @if($data === "position")
                        @foreach ($settingsData as $index => $setting)
                            <div class="col-span-2 relative">
                                <label for="settings_data_{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-slate-400 uppercase">{{ $data }}</label>
                                <input type="text" id="settings_data_{{ $index }}" wire:model='settingsData.{{ $index }}.value' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
            
                                <button type="button" wire:click="removeSetting({{ $index }})" class="absolute right-2 top-8 text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i>
                                </button>

                                @error('settingsData.' . $index . '.value')
                                    <span class="text-red-500 text-sm">This field is required!</span>
                                @enderror
                            </div>
                        @endforeach
                        <div class="col-span-2">
                            <button type="button" wire:click="addNewSetting" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Add Another {{ $data }}
                            </button>
                        </div>
                    @endif
                @else
                    @if($data === "committee")
                        <div class="col-span-2 relative">
                            <label for="settings_data" class="block text-sm font-medium text-gray-700 dark:text-slate-400 uppercase">{{ $data }}</label>
                            <input type="text" id="settings_data" wire:model='settings_data' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            @error('settings_data')
                                <span class="text-red-500 text-sm">This field is required!</span>
                            @enderror
                        </div>
                    @endif
                    @if($data === "position")
                        @foreach ($settingsData as $index => $setting)
                            <div class="col-span-2 relative">
                                <label for="settings_data_{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-slate-400 uppercase">{{ $data }}</label>
                                <input type="text" id="settings_data_{{ $index }}" wire:model='settingsData.{{ $index }}.value' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                    
                                <button type="button" wire:click="removeSetting({{ $index }})" class="absolute right-2 top-8 text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                
                                @error('settingsData.' . $index . '.value')
                                    <span class="text-red-500 text-sm">This field is required!</span>
                                @enderror
                            </div>
                        @endforeach
                        <div class="col-span-2">
                            <button type="button" wire:click="addNewSetting" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Add Another {{ $data }}
                            </button>
                        </div>
                    @endif
                @endif

                <div class="mt-4 flex justify-end col-span-2">
                    <button type="submit" class="mr-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        <div wire:loading wire:target="saveSettings" class="spinner-border small text-primary" role="status">
                        </div>
                        Save
                    </button>
                    <button type="button" @click="show = false" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded cursor-pointer" wire:click='resetVariables'>
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-modal>

{{-- Set Position Modal --}}
<x-modal id="posModal" maxWidth="2xl" wire:model="setPos" centered>
    <div class="p-4">
        <div class="mb-4 dark:text-white text-slate-900 font-bold uppercase">
            Set officer for the <span class="text-green-500">{{ $setPos }}</span> position
            <button @click="show = false" class="float-right focus:outline-none" wire:click='resetVariables'>
                <i class="fas fa-times"></i>
            </button>
        </div>
        {{-- Form fields --}}
        <form wire:submit.prevent='savePos'>
            <div class="grid grid-cols-2 gap-4">
                
                <div class="col-span-2 relative">
                    <label for="pos" class="block text-sm font-medium text-gray-700 dark:text-slate-400 uppercase">Homeowner</label>
                    <select name="pos" id="pos" wire:model='pos' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700" required>
                        <option value="">Select homeowner</option>
                        <option value="0" class="text-red-300">Vacant</option>
                        @foreach ($hos as $ho)
                            <option value="{{ $ho->id }}">{{ $ho->name }}</option>
                        @endforeach
                    </select>
                    @error('pos')
                        <span class="text-red-500 text-sm">This field is required!</span>
                    @enderror
                </div>

                <div class="mt-4 flex justify-end col-span-2">
                    <button type="submit" class="mr-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        <div wire:loading wire:target="savePos" class="spinner-border small text-primary" role="status">
                        </div>
                        Save
                    </button>
                    <button type="button" @click="show = false" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded cursor-pointer" wire:click='resetVariables'>
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-modal>

{{-- Add Role Modal --}}
<x-modal id="roleModal" maxWidth="2xl" wire:model="editRole" centered>
    <div class="p-4">
        <div class="mb-4 dark:text-white text-slate-900 font-bold uppercase">
            {{ $addRole ? 'Add' : 'Edit' }} System Admin
            <button @click="show = false" class="float-right focus:outline-none" wire:click='resetVariables'>
                <i class="fas fa-times"></i>
            </button>
        </div>
        {{-- Form fields --}}
        <form wire:submit.prevent='saveRole'>
            <div class="grid grid-cols-2 gap-4">
                
                <div class="col-span-full sm:col-span-1">
                    <label for="userId" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Homeowner <span class="text-red-500">*</span></label>
                    <select id="userId" wire:model='userId' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700"
                        {{ $addRole ? '' : 'disabled' }}>
                        <option value="{{ $userId }}">{{ $name ? $name : 'Select an employee' }}</option>
                        @foreach ($roleHomeowners as $ho)
                            <option value="{{ $ho->id }}">{{ $ho->name }}</option>
                        @endforeach
                    </select>
                    @error('userId') 
                        <span class="text-red-500 text-sm">Please select an employee!</span> 
                    @enderror
                </div>

                <div class="col-span-full sm:col-span-1">
                    <label for="admin_email" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Admin Email <span class="text-red-500">*</span></label>
                    <input type="text" id="admin_email" wire:model='admin_email' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                    @error('admin_email') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                @if($addRole)
                    <div class="col-span-full sm:col-span-1">
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Password <span class="text-red-500">*</span></label>
                        <input type="password" id="password" wire:model='password' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                        @error('password') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="cpassword" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Confirm Password <span class="text-red-500">*</span></label>
                        <input type="password" id="cpassword" wire:model='cpassword' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                        @error('cpassword') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>
                @endif

                <div class="mt-4 flex justify-end col-span-2">
                    <button class="mr-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        <div wire:loading wire:target="saveRole" class="spinner-border small text-primary" role="status">
                        </div>
                        Save
                    </button>
                    <p @click="show = false" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded cursor-pointer" wire:click='resetVariables'>
                        Cancel
                    </p>
                </div>
            </div>
        </form>
    </div>
</x-modal>

</div>

