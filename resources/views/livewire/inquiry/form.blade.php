<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-2/5 my-8 p-5 rounded-lg shadow-lg bg-white">
        <form wire:submit.prevent="store">
            <h2 class="mb-5 text-2xl font-bold tracking-tight">Create Inquiry</h2>

            {{ $this->form }}

            <x-forms.button type="submit" wire:target="store" wire:loading.attr="disabled" loading="Submitting...">
                Submit
            </x-forms.button>
        </form>
    </div>
</div>
