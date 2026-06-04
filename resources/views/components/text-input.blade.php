@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-300 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm px-3 py-2.5 text-sm']) }}>
