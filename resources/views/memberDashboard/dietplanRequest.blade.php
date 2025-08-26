@extends('memberDashboard.layout')

@section('content')
    <div class="w-full max-w-xs md:max-w-7xl p-8 bg-white rounded-lg my-4 text-center shadow-md mx-auto">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold">My Diet Plan Request</h2>
            </div>
            <button onclick="checkHealthAssessmentAndOpenModal()"
                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                + Request Plan
            </button>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto mt-6">
            <table class="min-w-full bg-white shadow-md rounded-lg border text-base border-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left border-b border-gray-300">Dietitian Name</th>
                        <th class="py-3 px-4 text-left border-b border-gray-300">Goal</th>
                        <th class="py-3 px-4 text-left border-b border-gray-300">Current Weight</th>
                        <th class="py-3 px-4 text-left border-b border-gray-300">Target Weight</th>
                        <th class="py-3 px-4 text-left border-b border-gray-300">Timeframe</th>
                        <th class="py-3 px-4 text-left border-b border-gray-300">Description</th>
                        <th class="py-3 px-4 text-left border-b border-gray-300">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $request)
                        <tr class="hover:bg-gray-100 transition duration-150">
                            <td class="py-3 px-4 text-left border-b border-gray-200">
                                {{ $request->dietitian ? $request->dietitian->first_name . ' ' . $request->dietitian->last_name : 'N/A' }}
                            </td>
                            <td class="py-3 px-4 text-left border-b border-gray-200 max-w-[14rem] break-words">
                                {{ $request->goal ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-left border-b border-gray-200">
                                {{ $request->current_weight ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-left border-b border-gray-200">
                                {{ $request->target_weight ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-left border-b border-gray-200">
                                {{ $request->timeframe ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-left border-b border-gray-200">
                                {{ $request->description ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-left border-b border-gray-200">
                                <span
                                    class="{{ $request->status === 'Approved'
                                        ? 'text-green-600 font-semibold'
                                        : ($request->status === 'Rejected'
                                            ? 'text-red-600 font-semibold'
                                            : ($request->status === 'Completed'
                                                ? 'text-blue-600 font-semibold'
                                                : 'text-yellow-600 font-semibold')) }}">
                                    {{ $request->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-4 px-4 text-center text-gray-500">
                                No diet requests available.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $requests->links() }}
        </div>

        <!-- Request Diet Plan Modal -->
        <div id="requestModal" role="dialog" aria-modal="true"
            class="fixed inset-0 flex backdrop-blur-sm bg-white/20 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-[0_0_15px_4px_rgba(241,30,19,0.5)]">
                <h2 class="text-md md:text-3xl font-bold text-center mb-5">Request a Diet Plan</h2>
                @if (isset($healthGoalsDisplay) && $healthGoalsDisplay)
                    <p class="text-sm text-center text-gray-600 mb-4">
                        <i class="fa-solid fa-lightbulb text-yellow-500 mr-1"></i>
                        Goal field is pre-filled from your health assessment
                    </p>
                @endif

                <!-- Form -->
                <form action="{{ route('member.dietplan.request') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Dietitian Selection -->
                    <div>
                        <label for="dietitian_id"
                            class="block text-left text-sm font-medium text-gray-700 items-center gap-2">
                            <i class="fa-solid fa-user-md text-sm mr-1.5 ml-1"></i>
                            Select Dietitian
                        </label>
                        <select id="dietitian_id" name="dietitian_id" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            <option value="" disabled selected>Select Dietitian</option>
                            @foreach ($dietitians as $dietitian)
                                <option value="{{ $dietitian->id }}">{{ $dietitian->first_name }}
                                    {{ $dietitian->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Goal Input (Pre-filled, Editable) -->
                    <div>
                        <label for="goal" class="block text-left text-sm font-medium text-gray-700 items-center gap-2">
                            <i class="fa-solid fa-bullseye text-sm mr-1.5 ml-1"></i>
                            Goal
                        </label>
                        <input type="text" id="goal" name="goal"
                            value="{{ old('goal', $healthGoalsDisplay ?? '') }}"
                            placeholder="e.g., Weight Loss, Muscle Gain, Maintenance" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        @if (isset($healthGoalsDisplay) && $healthGoalsDisplay)
                        @endif
                        <!-- Hidden field for the actual goal value -->
                        <input type="hidden" name="goal_value"
                            value="{{ old('goal_value', $healthGoals ?? 'weight_loss') }}">
                    </div>

                    <!-- Current Weight & Target Weight -->
                    <div class="flex gap-3">
                        <div class="w-1/2 text-left">
                            <label class="block text-sm font-medium text-gray-700">Current Weight (kg)</label>
                            <input type="number" name="current_weight"
                                value="{{ old('current_weight', $currentWeight ?? '') }}" placeholder="e.g. 65"
                                step="0.1" required
                                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                        </div>
                        <div class="w-1/2 text-left">
                            <label class="block text-sm font-medium text-gray-700">Target Weight (kg)</label>
                            <input type="number" name="target_weight" placeholder="e.g. 55" step="0.1"
                                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                        </div>
                    </div>

                    <!-- Timeframe -->
                    <div>
                        <label for="timeframe" class="block text-left text-sm font-medium text-gray-700 items-center gap-2">
                            <i class="fa-solid fa-clock text-sm mr-1.5 ml-1"></i>
                            Timeframe
                        </label>
                        <select id="timeframe" name="timeframe" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            <option value="" disabled selected>Select Timeframe</option>
                            <option value="1 month">1 Month</option>
                            <option value="3 months">3 Months</option>
                            <option value="6 months">6 Months</option>
                            <option value="1 year">1 Year</option>
                        </select>
                    </div>

                    <!-- Special Requirements -->
                    <div class="text-left">
                        <label class="block text-sm font-medium text-gray-700">Special Requirements</label>
                        <textarea name="special_requirements" placeholder="Any special dietary needs, preferences, or other notes..."
                            rows="3"
                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-red-500 focus:border-red-500"></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" onclick="closeRequestModal()"
                            class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded">
                            Cancel
                        </button>
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Health Assessment Required Modal -->
    <div id="healthAssessmentModal" role="dialog" aria-modal="true"
        class="fixed inset-0 flex backdrop-blur-sm bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-[0_0_15px_4px_rgba(241,30,19,0.5)]">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-heart-pulse text-red-500 text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Health Assessment Required</h3>
                <p class="text-gray-600 mb-6">
                    You need to complete your health assessment before requesting a diet plan.
                    This helps our dietitians create a safe and effective plan for you.
                </p>
                <div class="flex gap-3">
                    <button onclick="closeHealthAssessmentModal()"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded">
                        Cancel
                    </button>
                    <a href="{{ route('member.health-assessment') }}"
                        class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-center">
                        Complete Assessment
                    </a>
                </div>
            </div>
        </div>
    </div>

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

        <script>
            function checkHealthAssessmentAndOpenModal() {
                @if ($healthAssessment)
                    openRequestModal();
                @else
                    openHealthAssessmentModal();
                @endif
            }

            function openRequestModal() {
                document.getElementById('requestModal').classList.remove('hidden');
            }

            function closeRequestModal() {
                document.getElementById('requestModal').classList.add('hidden');
            }

            function openHealthAssessmentModal() {
                document.getElementById('healthAssessmentModal').classList.remove('hidden');
            }

            function closeHealthAssessmentModal() {
                document.getElementById('healthAssessmentModal').classList.add('hidden');
            }

            // Update hidden goal_value field based on goal text input
            document.addEventListener('DOMContentLoaded', function() {
                const goalInput = document.getElementById('goal');
                const goalValueInput = document.querySelector('input[name="goal_value"]');

                if (goalInput && goalValueInput) {
                    goalInput.addEventListener('input', function() {
                        const value = this.value.toLowerCase().replace(/\s+/g, '_');
                        // Map common variations to valid values
                        if (value.includes('weight_loss') || value.includes('lose') || value.includes('fat')) {
                            goalValueInput.value = 'weight_loss';
                        } else if (value.includes('muscle') || value.includes('gain') || value.includes(
                                'build')) {
                            goalValueInput.value = 'muscle_gain';
                        } else if (value.includes('maintain') || value.includes('maintenance')) {
                            goalValueInput.value = 'maintenance';
                        } else if (value.includes('athletic') || value.includes('performance') || value
                            .includes('sport')) {
                            goalValueInput.value = 'athletic_performance';
                        } else {
                            // Default to weight_loss if no match
                            goalValueInput.value = 'weight_loss';
                        }
                    });
                }
            });
        </script>
    @endpush

@endsection
