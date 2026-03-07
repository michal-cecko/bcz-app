@if(! empty($images))
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        @foreach($images as $image)
            <img src="{{ Storage::url($image) }}" alt="" class="w-full aspect-square object-cover rounded-lg">
        @endforeach
    </div>
@endif
