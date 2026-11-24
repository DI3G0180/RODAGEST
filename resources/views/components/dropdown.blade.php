<div>
    <button @click="isOpen = !isOpen" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-primary hover:bg-secondary transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.75 4.75h12M12 8l-4-4h5.08l-2.42 4A4.96 4.96 0 0112 21a4.96 4.96 0 01-4.08-3.03l-2.42 4H4.75m8.55-9.01L12 2m0 0l-4 4m4-4l4 4"/>
        </svg>
        {{ $label }}
        <svg class="w-4 h-4 ml-2 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7m0 0l-7-7m7 7v11a2 2 0 002-2h-5.586m6.414-4.414a2 2 0 11-2.828 2.828L21.73 11H4.95a2 2 0 01-1.657-3.459l2.308-2.308m0 0l4.384 4.384m-4.384-4.384L12 3v7m0 0l4.384 4.384"/>
        </svg>
    </button>

    @if($children)
    <div class="mt-2 bg-white rounded-lg shadow-xl w-56 absolute z-10 mt-2" @click.outside="isOpen = false">
        @foreach($children as $child)
        <a href="{{ $child['route'] }}" class="block px-4 py-2 text-sm text-[#294D61] hover:bg-secondary hover:text-primary transition">
            {{ $child['label'] }}
        </a>
        @endforeach
    </div>
    @endif
</div>