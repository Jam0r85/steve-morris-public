@props(['count' => 6])

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @for ($i = 0; $i < $count; $i++)
        <div class="animate-pulse rounded-xl overflow-hidden bg-white shadow p-0">
            <div class="h-48 bg-zinc-200"></div>
            <div class="p-4 space-y-3">
                <div class="h-3 w-24 bg-zinc-200 rounded"></div>
                <div class="h-5 bg-zinc-200 rounded"></div>
                <div class="h-4 bg-zinc-200 rounded w-2/3"></div>
                <div class="h-6 bg-zinc-200 rounded w-1/3"></div>
            </div>
        </div>
    @endfor
</div>
