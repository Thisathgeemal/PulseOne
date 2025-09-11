@extends('adminDashboard.layout')

@section('content')
<div class="w-full max-w-7xl p-8 bg-white rounded-lg my-4 shadow-md mx-auto">
  <h2 class="text-xl sm:text-2xl font-bold mb-4">Feedback – Monitor & Filter</h2>

  <form method="GET" class="flex flex-wrap gap-2 mb-4">
    <input name="q" value="{{ request('q') }}" placeholder="Search content…" class="border rounded p-2">
    <select name="type" class="border rounded p-2">
      <option value="">All types</option>
      @foreach(['Trainer','Dietitian','System'] as $t)
        <option value="{{ $t }}" @selected(request('type')===$t)>{{ $t }}</option>
      @endforeach
    </select>
    <select name="rate" class="border rounded p-2">
      <option value="">Any rate</option>
      @for($i=1;$i<=5;$i++)
        <option value="{{ $i }}" @selected(request('rate')==$i)>{{ $i }}</option>
      @endfor
    </select>
    <select name="visibility" class="border rounded p-2">
      <option value="">Any visibility</option>
      <option value="1" @selected(request('visibility')==='1')>Visible</option>
      <option value="0" @selected(request('visibility')==='0')>Hidden</option>
    </select>
    <button class="px-4 py-2 bg-gray-800 text-white rounded">Filter</button>
  </form>

  @if(session('success'))
    <div class="mb-4 p-3 bg-green-50 text-green-700 rounded">{{ session('success') }}</div>
  @endif

  <div class="overflow-x-auto">
    <table class="min-w-full bg-white shadow-md rounded-lg border text-base border-gray-200">
      <thead class="bg-gray-800 text-white">
        <tr>
          <th class="py-3 px-4">Date</th>
          <th class="py-3 px-4">From</th>
          <th class="py-3 px-4">Type</th>
          <th class="py-3 px-4">To</th>
          <th class="py-3 px-4">Rate</th>
          <th class="py-3 px-4">Content</th>
          <th class="py-3 px-4">Visible</th>
          <th class="py-3 px-4"></th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $fb)
          <tr class="border-t">
            <td class="py-3 px-4">{{ \Carbon\Carbon::parse($fb->created_at)->format('Y-m-d') }}</td>
            <td class="py-3 px-4">{{ $fb->fromUser->first_name }} {{ $fb->fromUser->last_name }}</td>
            <td class="py-3 px-4">{{ $fb->type }}</td>
            <td class="py-3 px-4">@if($fb->toUser) {{ $fb->toUser->first_name }} {{ $fb->toUser->last_name }} @else System @endif</td>
            <td class="py-3 px-4">{{ $fb->rate ?? '-' }}</td>
            <td class="py-3 px-4">{{ \Illuminate\Support\Str::limit($fb->content, 90) }}</td>
            <td class="py-3 px-4">
              <span class="px-2 py-1 rounded text-sm {{ $fb->is_visible ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                {{ $fb->is_visible ? 'Visible' : 'Hidden' }}
              </span>
            </td>
            <td class="py-3 px-4">
              <form method="POST" action="{{ route('admin.feedback.toggle', $fb->feedback_id) }}" class="inline">
                @csrf @method('PATCH')
                <input type="hidden" name="is_visible" value="{{ $fb->is_visible ? 0 : 1 }}">
                <button class="px-3 py-1 rounded {{ $fb->is_visible ? 'bg-gray-200' : 'bg-green-600 text-white' }}">
                  {{ $fb->is_visible ? 'Hide' : 'Show' }}
                </button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $items->links() }}</div>
</div>
@endsection
