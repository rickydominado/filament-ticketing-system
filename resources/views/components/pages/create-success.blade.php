@props(['inquiry'])

<x-app-layout>
    <div class="min-h-screen flex justify-center items-center bg-gray-100">
        <div class="w-2/5 px-8 py-12 rounded-lg shadow-lg bg-white text-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-32 h-32 text-green-300 inline mb-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>

            <div class="flex flex-col tracking-wide">
                <h2 class="text-2xl font-semibold">Thank you for submitting your inquiry!</h2>
                <p class="text-base mt-1.5">Your reference number is "<span class="font-semibold text-red-500">{{ $inquiry->id }}</span>".</p>

                <p class="text-sm text-black mt-5">An email has been sent to "<span class="font-semibold">{{ $inquiry->email }}</span>".</p>
                <p class="text-xs text-gray-600 mt-1.5 leading-4">
                    We appreciate your interest and will get back to you as soon as possible.<br>
                    If you have any further questions, please don't hesitate to reach out. Have a great day!
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
