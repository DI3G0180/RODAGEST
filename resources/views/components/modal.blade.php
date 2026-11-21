<div @click.self="hide()" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden" x-show="isOpen" x-transition:enter="transition ease-out duration-150" x-transition:leave="transition ease-in duration-150">
    <div class="bg-white rounded-xl w-full max-w-md m-6 p-6 shadow-xl transform scale-95" x-transition:enter="transition ease-out duration-150" x-transition:leave="transition ease-in duration-150">
        <h3 class="text-xl font-bold text-primary-800 mb-4">{{ $title }}</h3>
        
        @if($form)
        <form {{ $attrs['method'] || 'POST' }} {{ $attrs['action'] ?? '#' }} class="space-y-4">
            @csrf
            @method($attrs['method'] === 'PUT' || $attrs['method'] === 'PATCH' ? 'PUT' : 'POST')
            
            @foreach($errors->all() as $error)
            <div class="p-3 rounded-lg bg-red-50 text-red-800 text-sm mb-3">
                {{ $error }}
            </div>
            @endforeach
            
            @slot('form-content')
            <!-- Contenido del formulario aquí -->
            @endslot
        </form>
        @endif

        <div class="flex justify-end gap-3 mt-6">
            <button @click="hide()" class="px-4 py-2 rounded-lg text-sm font-medium bg-slate-200 text-slate-700 hover:bg-slate-300 transition">Cancelar</button>
            <button @click="submit()" class="px-4 py-2 rounded-lg text-sm font-medium bg-primary text-white hover:bg-secondary transition">
                {{ $buttonText ?? 'Guardar' }}
            </button>
        </div>
    </div>
</div>
