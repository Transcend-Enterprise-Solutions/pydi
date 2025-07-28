   @if (session()->has('success'))
       <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
           x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
           x-transition:leave-end="opacity-0" role="alert"
           class="relative mb-4 p-4 pr-10 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-md shadow-sm">
           <div class="flex items-center">
               <i class="bi bi-check-circle-fill text-green-500 mr-3 text-xl"></i>
               <span class="block sm:inline">{{ session('success') }}</span>
           </div>

           <button type="button" @click="show = false" aria-label="Close notification"
               class="absolute top-3 right-3 text-green-500 hover:text-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 rounded-md">
               <i class="bi bi-x-lg text-lg"></i>
           </button>
       </div>
   @endif

   @if (session()->has('error'))
       <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
           x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
           x-transition:leave-end="opacity-0" role="alert"
           class="relative mb-4 p-4 pr-10 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-md shadow-sm">
           <div class="flex items-center">
               <i class="bi bi-exclamation-triangle-fill text-red-500 mr-3 text-xl"></i>
               <span class="block sm:inline">{{ session('error') }}</span>
           </div>

           <button type="button" @click="show = false" aria-label="Close notification"
               class="absolute top-3 right-3 text-red-500 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 rounded-md">
               <i class="bi bi-x-lg text-lg"></i>
           </button>
       </div>
   @endif
