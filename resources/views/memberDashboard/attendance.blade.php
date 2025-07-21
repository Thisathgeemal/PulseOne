<table class="w-full border text-left text-sm">
    <thead>
        <tr class="bg-gray-100">
            <th class="py-2 px-4">Date</th>
            <th class="py-2 px-4">Time In</th>
        </tr>
    </thead>
    <tbody>
        @forelse($attendances as $record)
            <tr class="border-t">
                <td class="py-2 px-4">{{ \Carbon\Carbon::parse($record->date)->format('F d, Y') }}</td>
                <td class="py-2 px-4">{{ \Carbon\Carbon::parse($record->time_in)->format('h:i A') }}</td>
            </tr>
        @empty
            <tr>
                <td class="py-2 px-4 text-center" colspan="2">No attendance records found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
