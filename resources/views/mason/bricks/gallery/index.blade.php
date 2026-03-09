@if(! empty($label) || ! empty($title) || ! empty($images))
<section class="bg-bcz-dark py-[100px]">
    <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
    <div class="flex flex-col gap-12">
        @if(! empty($label) || ! empty($title))
            <div class="flex flex-col gap-4">
                @if(! empty($label))
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ brick_trans($label) }}</span>
                    </div>
                @endif
                @if(! empty($title))
                    <h2 class="font-display font-bold text-5xl tracking-wide">{{ brick_trans($title) }}</h2>
                @endif
            </div>
        @endif

        @if(! empty($images))
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 h-[250px] md:h-[320px] lg:h-[400px]">
                @foreach($images as $i => $image)
                    @php $imgUrl = brick_media_url($image['image'] ?? null); @endphp
                    @if($i === 0)
                        <div class="bg-cover bg-center" @if($imgUrl) style="background-image: url('{{ $imgUrl }}')" @endif></div>
                    @elseif($i === 1 || $i === 2)
                        @if($i === 1)
                            <div class="grid grid-rows-2 gap-4">
                        @endif
                        <div class="bg-cover bg-center" @if($imgUrl) style="background-image: url('{{ $imgUrl }}')" @endif></div>
                        @if($i === 2 || $loop->last)
                            </div>
                        @endif
                    @elseif($i === 3 || $i === 4)
                        @if($i === 3)
                            <div class="grid grid-rows-2 gap-4">
                        @endif
                        <div class="bg-cover bg-center" @if($imgUrl) style="background-image: url('{{ $imgUrl }}')" @endif></div>
                        @if($i === 4 || $loop->last)
                            </div>
                        @endif
                    @endif
                @endforeach
            </div>
        @endif
    </div>
    </div>
</section>
@endif
