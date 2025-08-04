@extends('dietitianDashboard.layout')

@section('content')

    <!-- Header -->
    <div class="bg-[#1E1E1E] text-white px-8 py-6 rounded-lg shadow mb-6 mt-4">
        <h2 class="text-2xl font-bold">Diet Plan Requests</h2>
        <p class="text-sm text-gray-300 mt-1">You can see the diet requests sent by members and create plans for them.</p>
    </div>

    <!-- Section Background -->
    <div class="grid gap-6 sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-2">
        @forelse ($requests as $req)
            <div class="group bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-lg hover:scale-[1.02] transition-all duration-300 p-6 overflow-hidden">
                <div class="flex justify-between items-start">
                    <div class="flex items-start gap-4">
                        <!-- Avatar with Gradient -->
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg shadow">
                            {{ strtoupper(substr($req->member->first_name, 0, 1)) }}
                        </div>

                        <!-- Info -->
                        <div class="text-left">
                            <h3 class="text-lg font-bold text-gray-800 mb-1">
                                {{ $req->member->first_name }} {{ $req->member->last_name }}
                            </h3>
                            <p class="text-sm text-gray-600">
                                <span class="font-semibold text-gray-700">Plan Goal:</span> {{ $req->description }}
                            </p>
                            <div class="flex gap-4 text-sm text-gray-600 mt-1">
                                <div>
                                    <span class="font-semibold text-gray-700">Height:</span> {{ $req->height ?? 'N/A' }} cm
                                </div>
                                <div>
                                    <span class="font-semibold text-gray-700">Weight:</span> {{ $req->weight ?? 'N/A' }} kg
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="mt-1">
                        <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full
                            {{ $req->status === 'Approved' ? 'bg-green-100 text-green-700' :
                            ($req->status === 'Rejected' ? 'bg-red-100 text-red-700' :
                            'bg-yellow-100 text-yellow-700') }}">
                            {{ ucfirst($req->status) }}
                        </span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-3 flex gap-3 justify-end opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    @if($req->status == 'Pending')
                        <form method="POST" action="{{ route('dietitian.request.update', $req->request_id) }}">
                            @csrf
                            <input type="hidden" name="status" value="Approved">
                            <button class="w-[110px] px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition text-center">
                                Approve
                            </button>
                        </form>
                        <form method="POST" action="{{ route('dietitian.request.update', $req->request_id) }}">
                            @csrf
                            <input type="hidden" name="status" value="Rejected">
                            <button class="w-[110px] px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition text-center">
                                Reject
                            </button>
                        </form>
                    @elseif($req->status == 'Approved')
                        <a href="{{ route('dietitian.request.update', $req->request_id) }}"
                        class="w-[110px] px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition text-center">
                            Create Plan
                        </a>
                    @else
                        <span class="text-sm text-gray-400 italic">No further actions available</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 text-lg font-medium col-span-full bg-white rounded-xl border border-gray-200 shadow-md p-6 mt-3">
                No diet requests assigned yet.
            </div>
        @endforelse
    </div>

    @push('scripts')
        @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
                confirmButtonText: 'OK',
                confirmButtonColor: '#d32f2f'
            });
        </script>
        @endif

        @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "{{ session('error') }}",
                confirmButtonText: 'OK',
                confirmButtonColor: '#d32f2f'
            });
        </script>
        @endif
    @endpush
@endsection
