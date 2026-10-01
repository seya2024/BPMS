@php
    $heading = $this->getHeading();
    $description = $this->getDescription();
    $hasHeading = filled($heading);
    $hasDescription = filled($description);
@endphp

<x-filament-widgets::widget class="fi-wi-stats-overview">
    <x-filament::section
        :description="$description"
        :heading="$heading"
        class="fi-wi-analytics-stats-ctn"
    >
        {{-- District filter, pinned to the top right of the widget. --}}
        <x-slot name="afterHeader">
            <x-filament::input.wrapper
                inline-prefix
                wire:target="filter"
                class="fi-wi-chart-filter"
            >
                <x-filament::input.select
                    inline-prefix
                    wire:model.live="filter"
                >
                    @foreach ($this->districtFilterOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </x-slot>

        <div class="fi-wi-analytics-stats-body">
            {{ $this->content }}
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
