<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex w-full items-center justify-center rounded-xl bg-[#0E7490] px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#083344] focus:outline-none focus:ring-2 focus:ring-[#0E7490] focus:ring-offset-2 sm:w-auto']) }}>
    {{ $slot }}
</button>
