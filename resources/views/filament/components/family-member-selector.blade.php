<div class="mb-4">
    <label class="block text-sm" for="member-{{ $this->getId() }}">{{ __('family.member') }}</label>
    <select id="member-{{ $this->getId() }}" wire:model.live="memberId" class="mt-1 w-full rounded border-gray-300 bg-white text-gray-900 dark:bg-gray-900 dark:text-white">
        <option value="">{{ __('family.choose') }}</option>
        @foreach($this->familyMemberOptions() as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
        @endforeach
    </select>
    @error('memberId') <p class="text-red-500">{{ $message }}</p> @enderror
</div>
