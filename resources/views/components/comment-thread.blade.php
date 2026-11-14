@props(['comments', 'action'])

<div class="rounded-2xl border border-slate-200 bg-white">
    <div class="border-b border-slate-200 px-6 py-4 sm:px-8">
        <h2 class="font-semibold">{{ __('comments.title') }}</h2>
    </div>

    @forelse ($comments as $comment)
        <div class="flex gap-3 border-b border-slate-100 px-6 py-4 last:border-0 sm:px-8">
            <span class="flex size-8 shrink-0 items-center justify-center rounded-full text-xs font-semibold
                         {{ $comment->fromCustomer() ? 'bg-slate-200 text-slate-700' : 'bg-navy-100 text-navy-700' }}">
                {{ $comment->initials() }}
            </span>
            <div class="min-w-0">
                <p class="text-sm font-medium">
                    {{ $comment->authorName() }}
                    <span class="ml-1 text-xs font-normal text-slate-400">
                        {{ $comment->created_at->isoFormat('D MMM YYYY HH:mm') }}
                    </span>
                </p>
                <p class="mt-1 whitespace-pre-line text-sm text-slate-600">{{ $comment->body }}</p>
            </div>
        </div>
    @empty
        <p class="px-6 py-6 text-sm text-slate-500 sm:px-8">{{ __('comments.empty') }}</p>
    @endforelse

    <form method="POST" action="{{ $action }}" class="border-t border-slate-200 px-6 py-5 sm:px-8">
        @csrf

        <label for="body" class="mb-1.5 block text-sm font-medium">{{ __('comments.write') }}</label>
        <textarea id="body" name="body" rows="3" required placeholder="{{ __('comments.placeholder') }}"
                  class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm
                         focus:border-navy-500 focus:outline-none focus:ring-2 focus:ring-navy-500/20">{{ old('body') }}</textarea>

        @error('body')
            <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
        @enderror

        <button type="submit"
                class="mt-3 rounded-lg bg-navy-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-navy-500">
            {{ __('comments.send') }}
        </button>
    </form>
</div>
