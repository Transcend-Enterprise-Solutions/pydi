<div class="w-full">

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
                <h1 class="text-lg font-bold text-center text-slate-800 dark:text-white">Association Dues</h1>
            </div>

            <!-- Table -->
            <div class="w-full">
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto">
                        <div class="inline-block w-full py-2 align-middle">
                            <div>
                                <div class="overflow-hidden border dark:border-gray-700 rounded-lg">
                                    <div class="flex justify-center pt-4 bg-gray-200 dark:bg-gray-700">
                                        <h1>Association Due for the month of {{ \Carbon\Carbon::parse($month)->format('F Y') }}</h1>
                                    </div>

                                    {{-- Search and Filter --}}
                                    <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700">
                                        <div class="flex flex-wrap items-end justify-between">
                                            {{-- Search Input --}}
                                            <div class="w-full sm:w-1/3 sm:mr-4">
                                                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Search</label>
                                                <input type="text" id="search" wire:model.live="search"
                                                    class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                                                        dark:hover:bg-slate-600 dark:border-slate-600
                                                        dark:text-gray-300 dark:bg-gray-800"
                                                    placeholder="Search homeowner">
                                            </div>
    
                                            <div class="flex gap-2 items-end">
                                                {{-- Month Filter --}}
                                                <div class="w-full sm:w-auto relative mt-4 sm:mt-0">
                                                    <label for="month"
                                                        class="absolute bottom-10 block text-sm font-medium text-gray-700 dark:text-slate-400">Select Month</label>
                                                    <input type="month" id="month" wire:model.live='month'
                                                        class="mt-4 sm:mt-1 px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md 
                                                        dark:hover:bg-slate-600 dark:border-slate-600
                                                        dark:text-gray-300 dark:bg-gray-800">
                                                </div>
                                                <div class="w-full flex">
                                                    <!-- Export to Excel -->
                                                    <div class="relative inline-block text-left">
                                                        <button wire:click="exportPayments"
                                                            class="peer mt-4 sm:mt-1 inline-flex items-center dark:hover:bg-slate-600 dark:border-slate-600
                                                            justify-center px-4 py-1.5 text-sm font-medium tracking-wide 
                                                            text-neutral-800 dark:text-neutral-200 transition-colors duration-200 
                                                            rounded-lg border border-gray-400 hover:bg-gray-300 focus:outline-none"
                                                            type="button" title="Export">
                                                            <img class="flex dark:hidden" src="/images/export-excel.png" width="22" alt="" wire:target="exportPayments" wire:loading.remove>
                                                            <img class="hidden dark:block" src="/images/export-excel-dark.png" width="22" alt="" wire:target="exportPayments" wire:loading.remove>
                                                            <div wire:loading wire:target="exportPayments">
                                                                <div class="spinner-border small text-primary" role="status">
                                                                </div>
                                                            </div>
                                                        </button>                    
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="overflow-x-auto overflow-y-hidden">
                                        <div>
                                            <table class="w-full min-w-full border border-gray-300 dark:border-gray-600">
                                                <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                                                    <tr class="whitespace-nowrap">
                                                        <th scope="col" class="px-5 py-3 text-gray-100 text-sm font-medium text-left uppercase bg-gray-600 dark:bg-gray-600">
                                                            Homeowner
                                                        </th>
                                                        <th scope="col" class="px-5 py-3 text-gray-100 text-sm font-medium text-left uppercase bg-gray-600 dark:bg-gray-600">
                                                            Contact Info
                                                        </th>
                                                        <th scope="col" class="px-5 py-3 text-gray-100 text-sm font-medium text-left uppercase bg-gray-600 dark:bg-gray-600 relative" x-data="{ open: false }">
                                                            <span class="text-green-500 hover:text-green-600 cursor-pointer" title="Filter Status" @click="open = !open"><i class="bi bi-funnel-fill"></i></span> Payment Status
                                                            <div class="text-gray-800 dark:text-gray-50 absolute top-2 -left-20 bg-gray-200 dark:bg-gray-600 shadow-2xl shadow-slate-900 rounded-lg p-2 border border-gray-300 dark:border-gray-700"
                                                                x-show="open" @click.away="open = false">                                                       
                                                                <ul class="space-y-2 text-xs">
                                                                    <li class="flex items-center">
                                                                        <input id="allStatus" type="radio"
                                                                            wire:model.live="paymentStatus"
                                                                            value="all"
                                                                            class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 checked:bg-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                                        <label for="allStatus"
                                                                            class="ml-2 text-gray-900 dark:text-gray-300">
                                                                            All</label>
                                                                    </li>
                                                                    <li class="flex items-center">
                                                                        <input id="paidStatus" type="radio"
                                                                            wire:model.live="paymentStatus"
                                                                            value="paid"
                                                                            class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 checked:bg-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                                        <label for="paidStatus"
                                                                            class="ml-2 text-gray-900 dark:text-gray-300">
                                                                            Paid</label>
                                                                    </li>
                                                                    <li class="flex items-center">
                                                                        <input id="unpaidStatus" type="radio"
                                                                            wire:model.live="paymentStatus"
                                                                            value="unpaid"
                                                                            class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 checked:bg-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                                        <label for="unpaidStatus"
                                                                            class="ml-2 text-gray-900 dark:text-gray-300">
                                                                            Unpaid</label>
                                                                    </li>
                                                                </ul>
                                                            </div>
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
                                                                <div class="w-full">
                                                                    <div class="flex justify-left items-center">
                                                                        @if ($ho->profile_photo_path)
                                                                            <img src="{{ route('profile-photo.file', ['filename' => basename($ho->profile_photo_path)]) }}" 
                                                                                    alt="{{ Auth::user()->name }}" 
                                                                                    class="rounded-full hover:grayscale border border-gray-400" style="width: 50px; height: 50px">
                                                                        @else
                                                                            <img class="rounded-full hover:grayscale border border-gray-400" src="{{ asset('images/blank-profile.png') }}" alt=""  style="width: 50px; height: 50px">
                                                                        @endif
                                                                        <p class="ml-4 text-md" style="line-height: 18px; margin-right: 100px">
                                                                            {{ $ho->first_name }}{{ $ho->middle_name ? (' ' . substr($ho->middle_name, 0, 1) . '.') : '' }} {{ $ho->last_name }} {{ $ho->name_extension }} <br>
                                                                            <span class="text-xs opacity-80" style="line-height: 13px">
                                                                                Blk. {{ $ho->block }} Lot {{ $ho->lot }} - {{ $ho->street }}
                                                                            </span>
                                                                        </p>
                                                                    </div> 
                                                                </div>
                                                            </td>
                                                            <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap bg-white dark:bg-gray-800">
                                                                <i class="bi bi-envelope"></i> &nbsp;{{ $ho->email }} <br>
                                                                <i class="bi bi-telephone"></i> &nbsp;{{ $ho->tel_number }}{{ $ho->mobile_number }}
                                                            </td>
                                                            <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap bg-white dark:bg-gray-800">
                                                                @if($ho->payment_status)
                                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                                        <svg class="mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                                                            <circle cx="4" cy="4" r="3" />
                                                                        </svg>
                                                                        Paid
                                                                    </span>
                                                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                                        {{ \Carbon\Carbon::parse($ho->date_paid)->format('M d, Y') }}
                                                                    </div>
                                                                @else
                                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                                        <svg class="mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                                                            <circle cx="4" cy="4" r="3" />
                                                                        </svg>
                                                                        Unpaid
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td class="px-5 py-4 text-sm font-medium text-center whitespace-nowrap sticky right-0 z-10 bg-white dark:bg-gray-800">
                                                                <div class="relative flex gap-4 justify-center">                                          
                                                                    <button wire:click="toggleEditPayment({{ $ho->userId }})" 
                                                                        class=" text-green-500 hover:text-green-600 dark:text-green-600 
                                                                        dark:hover:text-green-700" title="Edit Status">
                                                                        <i class="bi bi-pencil-fill"></i>
                                                                    </button>
                                                                    @if($ho->payment_status)
                                                                        <button wire:click="viewPayment({{ $ho->userId }})" 
                                                                            class=" text-blue-500 hover:text-blue-600 dark:text-blue-600 pr-2
                                                                            dark:hover:text-blue-700" title="View">
                                                                            <i class="bi bi-eye-fill"></i>
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @if ($homeowners->isEmpty())
                                            <div class="p-4 text-center text-gray-500 dark:text-gray-300 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600" style="margin-top: -1px">
                                                No records!
                                            </div> 
                                        @endif
                                    </div>

                                    <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700">
                                        {{ $homeowners->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>


    {{-- Edit Payment Status Modal --}}
    <x-modal id="paymentModal" maxWidth="2xl" wire:model="editStatus" centered>
        <div class="p-4">
            <div class="mb-4 dark:text-white text-slate-900 font-bold uppercase">
                Edit Payment
                <button @click="show = false" class="float-right focus:outline-none" wire:click='resetVariables'>
                    <i class="fas fa-times"></i>
                </button>
            </div>
            {{-- Form fields --}}
            <form wire:submit.prevent='savePayment'>
                <div class="grid grid-cols-2 gap-4">

                    <div class="col-span-full sm:col-span-1">
                        <label for="homeowner" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Homeowner <span class="text-red-500">*</span></label>
                        <input type="text" id="homeowner" wire:model='homeowner' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700" readonly style="pointer-events: none;">
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="thisMonth" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Monthly Payment For <span class="text-red-500">*</span></label>
                        <input type="month" id="thisMonth" wire:model='thisMonth' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                        @error('thisMonth') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Payment Status <span class="text-red-500">*</span></label>
                        <select id="status" wire:model='status' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            <option value="">Select status</option>
                            <option value="1">Paid</option>
                            <option value="0">Unpaid</option>
                        </select>
                        @error('status') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="paymentMode" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Mode of Payment <span class="text-red-500">*</span></label>
                        <select id="paymentMode" wire:model='paymentMode' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            <option value="">Select payment mode</option>
                            <option value="Cash">Cash</option>
                            <option value="Gcash">Gcash</option>
                            <option value="Maya">Maya</option>
                            <option value="Paypal">Paypal</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                        </select>
                        @error('paymentMode') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="refNumber" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Reference/Reciept Number <span class="text-red-500">*</span></label>
                        <input type="text" id="refNumber" wire:model='refNumber' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="datePaid" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Date Paid <span class="text-red-500">*</span></label>
                        <input type="date" id="datePaid" wire:model='datePaid' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                        @error('datePaid') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>
  

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

    {{-- View Payment Modal --}}
    <x-modal id="viewModal" maxWidth="2xl" wire:model="viewMonthlyPayment" centered>
        <div class="p-4">
            <div class="mb-4 dark:text-white text-slate-900 font-bold uppercase">
                Payment for the month of {{ \Carbon\Carbon::parse($thisMonth)->format('F Y') }}
                <button @click="show = false" class="float-right focus:outline-none" wire:click='resetVariables'>
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="grid grid-cols-2 gap-0 border-t border-l boder-gray-500 dark:border-gray-700">

                <div class="col-span-full sm:col-span-1 border-r border-b boder-gray-500 dark:border-gray-700 p-2">
                    <label for="refNumber" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Name</label>
                    <label for="refNumber" class="block text-md font-medium text-gray-800 dark:text-gray-100">{{ $homeowner }}</label>
                </div>

                <div class="col-span-full sm:col-span-1 border-r border-b boder-gray-500 dark:border-gray-700 p-2">
                    <label for="refNumber" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Month</label>
                    <label for="refNumber" class="block text-md font-medium text-gray-800 dark:text-gray-100">{{ $thisMonth }}</label>
                </div>

                <div class="col-span-full sm:col-span-1 border-r border-b boder-gray-500 dark:border-gray-700 p-2">
                    <label for="refNumber" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Mode of Payment</label>
                    <label for="refNumber" class="block text-md font-medium text-gray-800 dark:text-gray-100">{{ $paymentMode }}</label>
                </div>

                <div class="col-span-full sm:col-span-1 border-r border-b boder-gray-500 dark:border-gray-700 p-2">
                    <label for="refNumber" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Reference/Reciept Number</label>
                    <label for="refNumber" class="block text-md font-medium text-gray-800 dark:text-gray-100">{{ $refNumber }}</label>
                </div>

                <div class="col-span-full sm:col-span-1 border-r border-b boder-gray-500 dark:border-gray-700 p-2">
                    <label for="refNumber" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Date Paid</label>
                    <label for="refNumber" class="block text-md font-medium text-gray-800 dark:text-gray-100">{{ $datePaid }}</label>
                </div>

                <div class="col-span-full sm:col-span-1 border-r border-b boder-gray-500 dark:border-gray-700 p-2">
                    <label for="refNumber" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Received By</label>
                    <label for="refNumber" class="block text-md font-medium text-gray-800 dark:text-gray-100">{{ $receivedBy }}</label>
                </div>


            </div>
            <div class="mt-4 flex justify-end col-span-2">
                <p @click="show = false" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded cursor-pointer" wire:click='resetVariables'>
                    Close
                </p>
            </div>

        </div>
    </x-modal>

</div>
