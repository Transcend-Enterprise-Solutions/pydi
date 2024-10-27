<header class="sticky top-0 bg-gray-100/80 dark:bg-[#182235]/80 backdrop-blur-sm border-b border-slate-200 dark:border-slate-700 z-30 shadow-b">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 -mb-px">

            <!-- Header: Left side -->
            <div class="flex">

                <!-- Hamburger button -->
                <button
                    class="text-slate-500 hover:text-slate-600 lg:hidden"
                    @click.stop="sidebarOpen = !sidebarOpen"
                    aria-controls="sidebar"
                    :aria-expanded="sidebarOpen"
                >
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <rect x="4" y="5" width="16" height="2" />
                        <rect x="4" y="11" width="16" height="2" />
                        <rect x="4" y="17" width="16" height="2" />
                    </svg>
                </button>

            </div>

            <!-- Header: Right side -->
            <div class="flex items-center space-x-3">

                <!-- Search Button with Modal -->
                {{-- <x-modal-search /> --}}




                <!-- Info button -->
                <x-dropdown-help align="right" />

                <!-- Notifications button -->
                {{-- <x-dropdown-notifications align="right" /> --}}
                @livewire('notification.notifications-dropdown')

                <!-- Dark mode toggle -->
                <x-theme-toggle />

                <!-- Divider -->
                <hr class="w-px h-6 bg-slate-200 dark:bg-slate-700 border-none" />


                <!-- User button -->
                <x-dropdown-profile align="right"/>

            </div>

        </div>
    </div>
</header>
