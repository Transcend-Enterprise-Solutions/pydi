<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'btn bg-indigo-500 hover:bg-indigo-600 text-white whitespace-nowrap flex items-center gap-2']) }}>
    <i class="bi bi-box-arrow-in-right"></i>
    {{ $slot }}
</button>
