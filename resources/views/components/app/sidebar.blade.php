<div class="min-w-fit">
    <!-- Sidebar backdrop (mobile only) -->
    <div class="fixed inset-0 bg-slate-900 bg-opacity-30 z-40 lg:hidden lg:z-auto transition-opacity duration-200"
        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'" aria-hidden="true" x-cloak></div>

    <div id="sidebar"
        class="flex flex-col absolute z-40 left-0 top-0 lg:static lg:left-auto lg:top-auto lg:translate-x-0 h-screen overflow-y-scroll lg:overflow-y-auto no-scrollbar w-64 lg:w-20 lg:sidebar-expanded:!w-64 2xl:!w-64 shrink-0 bg-white dark:bg-slate-800 p-4 transition-all duration-200 ease-in-out rounded-3-2xl"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-64'" @click.outside="sidebarOpen = false"
        @keydown.escape.window="sidebarOpen = false">

        <div class="flex items-center ml-3 my-5" style="height: 40px;">
            <!-- Logo -->
            <a href="{{ url('/') }}" class="flex items-center space-x-2  group">
                <img src="/images/nyc_logo.png" alt="NYC Logo" title="Go to Dashboard"
                    class="w-16 h-16 object-contain transition duration-200 group-hover:scale-105">

                <span
                    class="text-lg font-semibold text-black dark:text-white lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 transition-opacity duration-300">
                    NYC - PYDP
                </span>
            </a>
        </div>

        <div class="space-y-5">
            <div>
                <ul class="">
                    <!-- Dashboard Section -->
                    @if (in_array(Auth::user()->user_role, ['sa', 'admin']))
                        @php
                            $adminSections = [
                                'Dashboard' => [
                                    [
                                        'route' => 'dashboard',
                                        'icon' => 'speedometer2',
                                        'label' => 'Dashboard',
                                    ],
                                ],
                                'Settings' => [
                                    [
                                        'route' => 'representatives',
                                        'icon' => 'people',
                                        'label' => 'Representatives',
                                    ],
                                    [
                                        'route' => 'dimension-indicator',
                                        'icon' => 'sliders',
                                        'label' => 'Dimension Indicators',
                                    ],
                                    [
                                        'route' => 'cover-year',
                                        'icon' => 'bi bi-sliders2',
                                        'label' => 'Covered Year (PYDI)',
                                    ],
                                ],
                                'Manage' => [
                                    [
                                        'route' => 'manage-pydp-datasets',
                                        'icon' => 'database',
                                        'label' => 'Manage PYDP Datasets',
                                    ],
                                    [
                                        'route' => 'manage-pydi-datasets',
                                        'icon' => 'bar-chart-line',
                                        'label' => 'Manage PYDI Datasets',
                                    ],
                                ],
                            ];
                        @endphp

                        @foreach ($adminSections as $section => $items)
                            <li
                                class="px-4 pt-4 pb-1 text-xs font-semibold text-gray-500 uppercase tracking-wide dark:text-slate-400">
                                {{ $section }}
                            </li>

                            @foreach ($items as $item)
                                @php
                                    $isActive =
                                        request()->routeIs($item['route']) || request()->is($item['route'] . '*');
                                @endphp

                                <li
                                    class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] {{ $isActive ? 'bg-gray-200 dark:bg-slate-900' : '' }}">
                                    <a href="{{ route($item['route']) }}"
                                        class="flex items-center justify-between block text-gray-800 dark:text-gray-100 transition truncate {{ $isActive ? '!text-blue-500' : '' }}">
                                        <div class="flex items-center">
                                            <i
                                                class="bi bi-{{ $item['icon'] }} text-slate-400 dark:text-slate-300 mr-3 text-lg"></i>
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                                {{ $item['label'] }}
                                            </span>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        @endforeach
                    @endif

                    <!-- User Section -->
                    @if (Auth::user()->user_role === 'user')
                        @php
                            $userSections = [
                                'Dashboard' => [
                                    [
                                        'route' => 'dashboard',
                                        'icon' => 'speedometer2',
                                        'label' => 'Dashboard',
                                    ],
                                ],
                                'Settings' => [
                                    [
                                        'route' => 'pydp-indicators',
                                        'icon' => 'sliders',
                                        'label' => 'PYDP Indicators',
                                    ],
                                ],
                                'Input Datasets' => [
                                    [
                                        'route' => 'pydp-datasets',
                                        'icon' => 'database',
                                        'label' => 'PYDP Datasets',
                                    ],
                                    [
                                        'route' => 'pydi-datasets',
                                        'icon' => 'bar-chart-line',
                                        'label' => 'PYDI Datasets',
                                    ],
                                ],
                            ];
                        @endphp

                        @foreach ($userSections as $section => $items)
                            <li
                                class="px-4 pt-4 pb-1 text-xs font-semibold text-gray-500 uppercase tracking-wide dark:text-slate-400">
                                {{ $section }}
                            </li>

                            @foreach ($items as $item)
                                @php
                                    $isActive =
                                        request()->routeIs($item['route']) || request()->is($item['route'] . '*');
                                @endphp

                                <li
                                    class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] {{ $isActive ? 'bg-gray-200 dark:bg-slate-900' : '' }}">
                                    <a href="{{ route($item['route']) }}"
                                        class="flex items-center justify-between block text-gray-800 dark:text-gray-100 transition truncate {{ $isActive ? '!text-blue-500' : '' }}">
                                        <div class="flex items-center"> {{-- wire:navigate --}}
                                            <i
                                                class="bi bi-{{ $item['icon'] }} text-slate-400 dark:text-slate-300 mr-3 text-lg"></i>
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                                {{ $item['label'] }}
                                            </span>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        @endforeach

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
