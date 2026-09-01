    <section class="bg-[#0A0A0A] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-12">
            {{-- Header --}}
            <div class="flex flex-col items-center gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ __('training_detail.form_label') }}</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-4xl tracking-wide">{{ __('training_detail.form_title') }}</h2>
                <p class="text-[#666666] text-base">{{ __('training_detail.form_subtitle') }}</p>
            </div>

            {{-- Form Card --}}
            <div class="w-full max-w-[600px] bg-[#111111] border border-[#222222] p-10">
                <livewire:training-registration-form :training="$training" />
            </div>
        </div>
    </section>
