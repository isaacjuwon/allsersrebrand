<?php

use Livewire\Volt\Component;

new class extends Component {

}; ?>
<x-layouts.app :title="__('Home')">
    <div class="flex flex-col lg:flex-row gap-8 w-full max-w-7xl mx-auto px-4 lg:px-0">
        <!-- Main Feed (Left Column) -->
        <div class="flex-1 w-full max-w-2xl mx-auto lg:mx-0">
            <livewire:dashboard.feed />
        </div>

        <!-- Right Sidebar (Trending & Pros) -->
        <div class="hidden lg:block w-80 space-y-6">
            <livewire:challenge.trending-widget />
            @include('partials.pros-widget')
        </div>
    </div>
</x-layouts.app>