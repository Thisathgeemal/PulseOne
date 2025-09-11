@extends('memberDashboard.layout')

@section('content')
<div class="w-full max-w-3xl p-8 bg-white rounded-lg my-4 shadow-md mx-auto">
    <h2 class="text-xl sm:text-2xl font-bold mb-6">Submit Feedback</h2>

    <form method="POST" action="{{ route('member.feedback.store') }}" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium">Type</label>
            <select name="type" id="type" class="mt-1 w-full border rounded p-2">
                <option value="Trainer">Trainer</option>
                <option value="Dietitian">Dietitian</option>
                <option value="System">System</option>
            </select>
            @error('type')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
        </div>

        <div id="personRow">
            <label class="block text-sm font-medium">Select Person</label>
            <select name="to_user_id" class="mt-1 w-full border rounded p-2">
                <optgroup label="Trainers">
                    @foreach($trainers as $u)
                        <option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }}</option>
                    @endforeach
                </optgroup>
                <optgroup label="Dietitians">
                    @foreach($dietitians as $u)
                        <option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }}</option>
                    @endforeach
                </optgroup>
            </select>
            @error('to_user_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
        </div>

        <div>
  <label class="block text-sm font-medium">Rating (1–5)</label>
  <input type="number" min="1" max="5" name="rate" required
         class="mt-1 w-full border rounded p-2" />
  @error('rate')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
</div>


        <div>
            <label class="block text-sm font-medium">Your Feedback</label>
            <textarea name="content" required rows="6" class="mt-1 w-full border rounded p-2"></textarea>
            @error('content')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('member.feedback') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
            <button class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded">Submit</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
  const select = document.getElementById('type');
  const personRow = document.getElementById('personRow');
  function togglePerson(){
    personRow.style.display = (select.value === 'System') ? 'none' : 'block';
  }
  select.addEventListener('change', togglePerson);
  togglePerson();
</script>
@endpush
@endsection
