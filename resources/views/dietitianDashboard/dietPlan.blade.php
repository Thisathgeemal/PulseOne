@extends('dietitianDashboard.layout')

@section('content')

    <!-- Header -->
    <div class="bg-[#1E1E1E] text-white px-8 py-6 rounded-lg shadow mb-6 mt-4">
        <h2 class="text-2xl font-bold">Your Diet Plans</h2>
        <p class="text-sm text-gray-300 mt-1">Below are the diet plans you've created for members.</p>
    </div>

    @if ($plans->count())
        <div class="grid gap-6 sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-2">
            @foreach ($plans as $plan)
                <div
                    class="group bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-lg hover:scale-[1.02] transition-all duration-300 p-6 overflow-hidden">
                    <div class="flex justify-between items-start">
                        <div class="flex items-start gap-4">
                            <!-- Avatar with Gradient -->
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg shadow">
                                {{ strtoupper(substr($plan->member->first_name, 0, 1)) }}
                            </div>

                            <!-- Info -->
                            <div class="text-left">
                                <h3 class="text-lg font-bold text-gray-800 mb-1">
                                    {{ $plan->plan_name }}
                                </h3>
                                <p class="text-sm text-gray-600 leading-snug">
                                    <span class="font-semibold text-gray-700">Member:</span> {{ $plan->member->first_name }}
                                    {{ $plan->member->last_name }}<br>
                                    <span class="font-semibold text-gray-700">Duration:</span>
                                    {{ \Carbon\Carbon::parse($plan->start_date)->format('Y-m-d') }} to
                                    {{ \Carbon\Carbon::parse($plan->end_date)->format('Y-m-d') }}
                                </p>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="mt-1">
                            <span
                                class="inline-block text-xs font-semibold px-3 py-1 rounded-full
                                {{ $plan->status === 'Active'
                                    ? 'bg-green-100 text-green-700'
                                    : ($plan->status === 'Cancelled'
                                        ? 'bg-red-100 text-red-700'
                                        : ($plan->status === 'Completed'
                                            ? 'bg-blue-100 text-blue-700'
                                            : 'bg-yellow-100 text-yellow-700')) }}">
                                {{ ucfirst($plan->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div
                        class="mt-5 flex gap-3 justify-end opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <a href="{{ route('dietitian.dietplan.show', $plan->dietplan_id) }}"
                            class="w-[110px] px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition text-center">
                            View
                        </a>
                        <a href="{{ route('dietitian.dietplan.track', $plan->dietplan_id) }}"
                            class="w-[110px] px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition text-center">
                            Track
                        </a>
                        <a href="{{ route('dietitian.dietplan.download', $plan->dietplan_id) }}"
                            class="w-[110px] px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition text-center">
                            Download
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 shadow-md p-6 text-center">
            <h2 class="text-xl font-semibold text-gray-600">No diet plans have been created yet.</h2>
            <p class="text-gray-500">Once you approve a diet request and create a plan, it will appear here.</p>
            <a href="{{ route('dietitian.request') }}"
                class="mt-4 inline-block bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Go to Requests</a>
        </div>
    @endif

    @push('scripts')
        @if (session('success'))
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

        @if (session('error'))
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
