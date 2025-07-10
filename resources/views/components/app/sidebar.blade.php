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
                <img class="mx-auto" src="/images/nyc_logo.png" alt="beahoa logo" width="45">
                <span
                    class="text-black dark:text-white ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">PYDI</span>
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

                        <!-- Agency Rep List -->
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))]
                        @if (in_array(Request::segment(1), ['representatives'])) {{ 'bg-gray-200 dark:bg-slate-900' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['representatives']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition
                            @if (Route::is('representatives')) {{ '!text-blue-500' }} @endif"
                                href="{{ route('representatives') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="bi bi-person-badge text-slate-400 mr-3"></i>
                                        <span
                                            class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                            Representatives
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>

                        <!-- Dimension Indicator Manager -->
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))]
                        @if (in_array(Request::segment(1), ['dimension-indicator'])) {{ 'bg-gray-200 dark:bg-slate-900' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['dimension-indicator']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition
                            @if (Route::is('dimension-indicator')) {{ '!text-blue-500' }} @endif"
                                href="{{ route('dimension-indicator') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="bi bi-bar-chart text-slate-400 mr-3"></i>
                                        <span
                                            class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                            Dimension Indicators
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>

                    @endif

                    <!-- user tabs------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------>

                    @if (Auth::user()->user_role === 'user')

                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))]
                            @if (in_array(Request::segment(1), ['data-entry'])) {{ 'bg-gray-200 dark:bg-slate-900' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['data-entry']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition
                            @if (Route::is('data-entry')) {{ '!text-blue-500' }} @endif"
                                href="{{ route('data-entry') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="bi bi-house-fill text-slate-400 dark:text-slate-300 mr-3"></i>
                                        <span
                                            class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                            Data Entry
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
