@extends('layouts.app')

@section('title', 'Messages')
@section('description', 'Manage application text records.')

@section('content')
<section class="surface-card overflow-hidden">
    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 sm:px-6">
        <div>
            <h2 class="text-sm font-semibold text-slate-900">Application records</h2>
            <p class="mt-1 text-xs text-slate-500">{{ count($messages) }} total records</p>
        </div>
        <a href="{{ route('messages.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> New message</a>
    </div>
    @if (count($messages))
        <div class="overflow-x-auto">
            <table class="w-full min-w-[38rem] text-left">
                <thead><tr class="table-heading border-b border-slate-100">
                    <th class="px-5 py-3 font-medium sm:px-6">Key</th>
                    <th class="px-5 py-3 font-medium">Value</th>
                    <th class="px-5 py-3 text-right font-medium sm:px-6">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($messages as $message)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4 text-sm font-medium text-slate-800 sm:px-6">{{ $message->name }}</td>
                            <td class="max-w-md px-5 py-4 text-sm text-slate-600">{{ $message->message }}</td>
                            <td class="px-5 py-4 text-right sm:px-6">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('messages.edit', [$message->id, 'key' => $message->name]) }}" class="icon-button" aria-label="Edit {{ $message->name }}"><x-icon name="pencil" class="h-4 w-4" /></a>
                                    <form action="{{ route('messages.destroy', [$message->id, 'key' => $message->name]) }}" method="POST" onsubmit="return confirm('Delete this message?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="icon-button text-rose-600 hover:bg-rose-50" aria-label="Delete {{ $message->name }}"><x-icon name="trash-2" class="h-4 w-4" /></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="px-6 py-16 text-center">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-500"><x-icon name="mail" class="h-5 w-5" /></span>
            <h3 class="mt-3 text-sm font-semibold text-slate-900">No messages</h3>
            <p class="mt-1 text-sm text-slate-500">Create your first application text record.</p>
        </div>
    @endif
</section>
@endsection