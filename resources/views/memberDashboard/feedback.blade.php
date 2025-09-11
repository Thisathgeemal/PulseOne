@extends('memberDashboard.layout')

@section('content')
<div class="w-full max-w-7xl p-8 bg-white rounded-lg my-4 shadow-md mx-auto">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <h2 class="text-xl sm:text-2xl font-bold">My Feedback</h2>
        <a href="{{ route('member.feedback.create') }}"
           class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">+ New Feedback</a>
    </div>

    @if(session('success'))
        <div class="mt-4 p-3 rounded bg-green-50 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="overflow-x-auto mt-6">
        <table class="min-w-full bg-white shadow-md rounded-lg border text-base border-gray-200">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-3 px-4 text-left">Date</th>
                    <th class="py-3 px-4 text-left">Type</th>
                    <th class="py-3 px-4 text-left">To</th>
                    <th class="py-3 px-4 text-left">Rate</th>
                    <th class="py-3 px-4 text-left">Content</th>
                    <th class="py-3 px-4 text-left">Visible</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $fb)
                    <tr class="border-t">
                        <td class="py-3 px-4">{{ \Carbon\Carbon::parse($fb->created_at)->format('Y-m-d') }}</td>
                        <td class="py-3 px-4">{{ $fb->type }}</td>
                        <td class="py-3 px-4">
                            @if($fb->toUser) {{ $fb->toUser->first_name }} {{ $fb->toUser->last_name }} @else System @endif
                        </td>
                        <td class="py-3 px-4">{{ $fb->rate ?? '-' }}</td>
                        <td class="py-3 px-4">
                            <span title="{{ $fb->content }}">{{ \Illuminate\Support\Str::limit($fb->content, 80) }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 rounded text-sm {{ $fb->is_visible ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $fb->is_visible ? 'Yes' : 'Hidden by Admin' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td class="py-4 px-4" colspan="6">No feedback yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $items->links() }}</div>
</div>
@endsection
