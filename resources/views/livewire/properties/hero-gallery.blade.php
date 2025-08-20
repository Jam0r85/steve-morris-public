@php
  // $images is already normalized in the component to [['src','alt'], ...]
@endphp

<div
  x-data="{
      images: @js($images),
      idx: $wire.entangle('index'),
      next(){ if(this.images.length) this.idx = (this.idx + 1) % this.images.length },
      prev(){ if(this.images.length) this.idx = (this.idx - 1 + this.images.length) % this.images.length },
  }"
  x-on:keydown.window.left.prevent="prev()"
  x-on:keydown.window.right.prevent="next()"
  class="relative w-full {{ $height }} rounded-2xl overflow-hidden bg-neutral-900"
>
  @if(empty($images))
    <div class="flex items-center justify-center w-full h-full text-white/60">
      No photos available
    </div>
  @else
    <div class="absolute inset-0">
      {{-- Background fill to avoid letterbox look while keeping main image contain --}}
      <img
        :src="images[idx].src"
        alt=""
        class="absolute inset-0 w-full h-full object-cover blur-xl scale-110 opacity-40 pointer-events-none select-none"
        aria-hidden="true"
      />
      {{-- Main image: never cropped --}}
      <img
        :src="images[idx].src"
        :alt="images[idx].alt"
        class="absolute inset-0 w-full h-full object-contain object-center"
        decoding="async"
      />
    </div>

    {{-- Controls --}}
    <button type="button"
            @click="prev"
            class="absolute left-3 top-1/2 -translate-y-1/2 bg-black/45 hover:bg-black/65 text-white p-3 rounded-full backdrop-blur"
            aria-label="Previous photo">‹</button>
    <button type="button"
            @click="next"
            class="absolute right-3 top-1/2 -translate-y-1/2 bg-black/45 hover:bg-black/65 text-white p-3 rounded-full backdrop-blur"
            aria-label="Next photo">›</button>

    {{-- Counter --}}
    <div class="absolute right-3 top-3 bg-black/55 text-white text-xs px-2 py-1 rounded-md"
         x-text="`${idx+1}/${images.length}`"></div>

    {{-- Thumbnails --}}
    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent p-3">
      <div class="flex gap-2 overflow-x-auto">
        <template x-for="(img, i) in images" :key="i">
          <button @click="idx = i"
                  :aria-current="idx === i"
                  class="relative shrink-0 w-24 h-16 md:w-28 md:h-18 rounded-lg overflow-hidden ring-2 transition"
                  :class="idx === i ? 'ring-white' : 'ring-transparent'">
            <img :src="img.src" :alt="img.alt" class="w-full h-full object-cover" loading="lazy" />
            <span class="absolute inset-0" :class="idx === i ? '' : 'bg-black/30'"></span>
          </button>
        </template>
      </div>
    </div>
  @endif
</div>
