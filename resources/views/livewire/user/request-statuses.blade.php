<div>
    @if ($row->is_request_edit === 1)
        {{-- Pending --}}
        <div class="relative group inline-flex">
            <span class="inline-flex items-center justify-center w-8 h-8 text-yellow-500 rounded-md">
                <i class="bi bi-clock-history"></i>
            </span>
            <div
                class="absolute z-10 hidden group-hover:block -top-2 left-1/2 transform -translate-x-1/2 -translate-y-full">
                <div class="px-2 py-1 text-xs text-white bg-gray-800 rounded whitespace-nowrap">
                    Edit Request Pending
                    <div class="absolute w-2 h-2 bg-gray-800 transform rotate-45 -bottom-1 left-1/2 -translate-x-1/2">
                    </div>
                </div>
            </div>
        </div>
    @elseif ($row->is_request_edit === 2)
        {{-- Approved --}}
        <div class="relative group inline-flex">
            <span class="inline-flex items-center justify-center w-8 h-8 text-green-500 rounded-md">
                <i class="bi bi-check-circle"></i>
            </span>
            <div
                class="absolute z-10 hidden group-hover:block -top-2 left-1/2 transform -translate-x-1/2 -translate-y-full">
                <div class="px-2 py-1 text-xs text-white bg-gray-800 rounded whitespace-nowrap">
                    Edit Request Approved
                    <div class="absolute w-2 h-2 bg-gray-800 transform rotate-45 -bottom-1 left-1/2 -translate-x-1/2">
                    </div>
                </div>
            </div>
        </div>
    @elseif ($row->is_request_edit === 3)
        {{-- Rejected --}}
        <div class="relative group inline-flex">
            <span class="inline-flex items-center justify-center w-8 h-8 text-red-500 rounded-md">
                <i class="bi bi-x-circle"></i>
            </span>
            <div
                class="absolute z-10 hidden group-hover:block -top-2 left-1/2 transform -translate-x-1/2 -translate-y-full">
                <div class="px-2 py-1 text-xs text-white bg-gray-800 rounded whitespace-nowrap">
                    Edit Request Rejected
                    <div class="absolute w-2 h-2 bg-gray-800 transform rotate-45 -bottom-1 left-1/2 -translate-x-1/2">
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
