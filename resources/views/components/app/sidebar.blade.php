<div class="min-w-fit">
    <!-- Sidebar backdrop (mobile only) -->
    <div class="fixed inset-0 bg-slate-900 bg-opacity-30 z-40 lg:hidden lg:z-auto transition-opacity duration-200"
        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'" aria-hidden="true" x-cloak></div>

    <div id="sidebar"
        class="flex flex-col absolute z-40 left-0 top-0 lg:static lg:left-auto lg:top-auto lg:translate-x-0 h-screen overflow-y-scroll lg:overflow-y-auto no-scrollbar w-64 lg:w-20 lg:sidebar-expanded:!w-64 2xl:!w-64 shrink-0 bg-white dark:bg-slate-800 p-4 transition-all duration-200 ease-in-out rounded-3-2xl"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-64'" @click.outside="sidebarOpen = false"
        @keydown.escape.window="sidebarOpen = false">

        <div class="flex justify-left mb-10 ml-1" style="width: 200px; height: 32px;">
            <!-- Logo -->
            <a class="flex items-center" href="{{ route('dashboard') }}">
                <img class="mx-auto block dark:hidden" src="/images/beahoa-logo.png" alt="beahoa logo" width="45">
                <img class="mx-auto hidden dark:block" src="/images/beahoa-logo-white.png" alt="beahoa logo" width="45">
                <span
                    class="text-black dark:text-white ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">BEAHOA Inc.</span>
            </a>
        </div>

        <div class="space-y-8">
            <div>
                <ul class="mt-3">

                    <!-- Admin Tabs ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->

                    @if (Auth::user()->user_role === 'sa' || Auth::user()->user_role === 'admin')

                        <!-- Dashboard -->
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] 
                            @if (in_array(Request::segment(1), ['dashboard'])) {{ 'bg-gray-200 dark:bg-slate-900' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['dashboard']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition 
                            @if (Route::is('dashboard')) {{ '!text-blue-500' }} @endif"
                                href="{{ route('dashboard') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="bi bi-speedometer2 text-slate-400 dark:text-slate-300 mr-3"></i>
                                        <span
                                            class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                            Dashboard
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>

                        <!-- Association -->
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] 
                        @if (in_array(Request::segment(1), ['association'])) {{ 'bg-gray-200 dark:bg-slate-900' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['association']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition 
                            @if (Route::is('association')) {{ '!text-blue-500' }} @endif"
                                href="{{ route('association') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="bi bi-diagram-3 text-slate-400 mr-3"></i>
                                        <span
                                            class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                            Association
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>

                         <!-- Monthly Payments -->
                         <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] 
                         @if (in_array(Request::segment(1), ['monthly-payments'])) {{ 'bg-gray-200 dark:bg-slate-900' }} @endif"
                             x-data="{ open: {{ in_array(Request::segment(1), ['monthly-payments']) ? 1 : 0 }} }">
                             <a class="block text-gray-800 dark:text-gray-100 truncate transition" href="#0"
                                 @click.prevent="sidebarExpanded ? open = !open : sidebarExpanded = true">
                                 <div class="flex items-center justify-between">
                                     <div class="flex items-center">
                                         <i class="bi bi-cash-stack text-slate-400 mr-3"></i>
                                         <span
                                             class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                             Monthly Payments
                                         </span>
                                     </div>
                                     <div class="flex shrink-0 ml-2">
                                         <svg class="lg:hidden lg:sidebar-expanded:inline w-3 h-3 shrink-0 ml-1 fill-current text-slate-400 transition-transform duration-300"
                                             :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                             <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                         </svg>
                                     </div>
                                 </div>
                             </a>

                             <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                 <ul class="pl-9 mt-1 transition-all duration-300 overflow-hidden"
                                     :class="open ? '!block' : 'hidden'">
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-slate-400 hover:text-blue-500 transition duration-150 truncate @if (Route::is('association-dues')) {{ '!text-blue-500' }} @endif"
                                                href="{{ route('association-dues') }}" wire:navigate>
                                                <span class="text-sm font-medium transition-opacity duration-300"
                                                    :class="sidebarExpanded ? 'opacity-100 lg:inline' :
                                                        'opacity-0 lg:hidden'">
                                                    Association Dues
                                                </span>
                                            </a>
                                        </li>
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-slate-400 hover:text-blue-500 transition duration-150 truncate @if (Route::is('water-bills')) {{ '!text-blue-500' }} @endif"
                                                href="{{ route('water-bills') }}" wire:navigate>
                                                <span class="text-sm font-medium transition-opacity duration-300"
                                                    :class="sidebarExpanded ? 'opacity-100 lg:inline' :
                                                        'opacity-0 lg:hidden'">
                                                    Water Bill
                                                </span>
                                            </a>
                                        </li>
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-slate-400 hover:text-blue-500 transition duration-150 truncate @if (Route::is('electric-bills')) {{ '!text-blue-500' }} @endif"
                                                href="{{ route('electric-bills') }}"wire:navigate>
                                                <span class="text-sm font-medium transition-opacity duration-300"
                                                    :class="sidebarExpanded ? 'opacity-100 lg:inline' :
                                                        'opacity-0 lg:hidden'">
                                                    Electric Bill
                                                </span>
                                            </a>
                                        </li>
                                 </ul>
                             </div>
                         </li>

                    @endif

                    <!-- Homeowner Tabs ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------>

                    @if (Auth::user()->user_role === 'homeowner')

                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] 
                            @if (in_array(Request::segment(1), ['home'])) {{ 'bg-gray-200 dark:bg-slate-900' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['home']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition 
                            @if (Route::is('home')) {{ '!text-blue-500' }} @endif"
                                href="{{ route('home') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="bi bi-house-fill text-slate-400 dark:text-slate-300 mr-3"></i>
                                        <span
                                            class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                            Home
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>

                        <!-- Association -->
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] 
                        @if (in_array(Request::segment(1), ['association'])) {{ 'bg-gray-200 dark:bg-slate-900' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['association']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition 
                            @if (Route::is('association')) {{ '!text-blue-500' }} @endif"
                                href="{{ route('association') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="bi bi-diagram-3 text-slate-400 mr-3"></i>
                                        <span
                                            class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                            Association
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endif

                </ul>
            </div>
        </div>

        <!-- Expand / collapse button -->
        <div class="pt-3 hidden lg:inline-flex 2xl:hidden justify-end mt-auto">
            <div class="px-3 py-2">
                <button @click="sidebarExpanded = !sidebarExpanded">
                    <span class="sr-only">Expand / collapse sidebar</span>
                    <svg class="w-6 h-6 fill-current sidebar-expanded:rotate-180" viewBox="0 0 24 24">
                        <path class="text-slate-400"
                            d="M19.586 11l-5-5L16 4.586 23.414 12 16 19.414 14.586 18l5-5H7v-2z" />
                        <path class="text-slate-600" d="M3 23H1V1h2z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>