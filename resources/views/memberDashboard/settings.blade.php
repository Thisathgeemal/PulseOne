@extends('memberDashboard.layout')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md mt-10 relative">
        <h2 class="text-2xl font-bold text-gray-800 mb-8">Edit Profile</h2>

        <!-- Profile Image Preview + Remove -->
        <div class="absolute top-8 right-8 text-right group">
            <label for="profile_image" class="relative cursor-pointer inline-block">
                @if(Auth::user()->profile_image)
                    <img src="{{ asset(Auth::user()->profile_image) }}?v={{ time() }}"
                        alt="Profile"
                        class="w-20 h-20 rounded-full object-cover border-2 border-gray-300" />
                @else
                    <div class="w-20 h-20 rounded-full bg-orange-200 text-orange-800 flex items-center justify-center text-2xl font-bold uppercase border-2 border-gray-300">
                        {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}
                    </div>
                @endif

                <!-- Pencil Icon -->
                <div class="absolute bottom-0 right-0 bg-white rounded-full p-1 border">
                    <i class="fas fa-pen text-sm text-gray-500"></i>
                </div>
            </label>

            <!-- Remove Button (not inside label or form) -->
            @if(Auth::user()->profile_image)
                <form action="{{ route('Member.settings.removeImage') }}" method="POST" class="mt-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-red-500 hover:underline mt-2">Remove Image</button>
                </form>
            @endif
        </div>

        <!-- Main Profile Update Form -->
        <form method="POST" action="{{ route('Member.settings.update') }}" enctype="multipart/form-data" class="space-y-6 pt-4">
            @csrf
            @method('PUT')

            <!-- Hidden file input (inside the form!) -->
            <input type="file" id="profile_image" name="profile_image" class="hidden" />

            <!-- First & Last Name -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6">
                <div>
                    <label class="text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" name="first_name" value="{{ Auth::user()->first_name }}"
                        class="w-full mt-1 border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-red-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" name="last_name" value="{{ Auth::user()->last_name }}"
                        class="w-full mt-1 border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-red-500 focus:outline-none">
                </div>
            </div>

            <!-- Email & Mobile -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ Auth::user()->email }}" disabled
                        class="w-full mt-1 border border-gray-300 rounded px-3 py-2 bg-gray-100 text-gray-600">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Mobile Number</label>
                    <input type="text" name="mobile_number" value="{{ Auth::user()->mobile_number }}"
                        class="w-full mt-1 border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-red-500 focus:outline-none">
                </div>
            </div>

            <!-- Password Toggle -->
    <div class="pt-2">
        <div class="flex justify-between items-center">
            <label class="text-sm font-medium text-gray-700">Change Password</label>
            <button type="button" id="checkCurrentPasswordBtn" class="text-sm text-gray-600 hover:underline flex items-center gap-1">
                <i class="fas fa-pen text-xs"></i> Verify Current Password
            </button>
        </div>

        <div class="mt-4 space-y-4" id="currentPasswordSection">
            <input type="password" name="current_password" id="current_password"
                placeholder="Enter current password"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-red-500 focus:outline-none">

            <div class="flex items-center gap-3" id="checkResult" style="display: none;">
                <span id="checkIcon"></span>
                <span id="checkMessage" class="text-sm"></span>
            </div>

            <button type="button" id="verifyPasswordBtn"
                    class="bg-gray-100 px-4 py-2 text-sm border rounded hover:bg-gray-200">
                Confirm Password
            </button>
        </div>

        <!-- Hidden password fields -->
        <div id="passwordFields" class="space-y-4 hidden mt-6">
            <div>
                <label class="text-sm text-gray-700">New Password</label>
                <input type="password" name="password" placeholder="Enter new password"
                    class="w-full mt-1 border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-red-500 focus:outline-none">
            </div>
            <div>
                <label class="text-sm text-gray-700">Confirm New Password</label>
                <input type="password" name="password_confirmation" placeholder="Confirm new password"
                    class="w-full mt-1 border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-red-500 focus:outline-none">
            </div>
        </div>
    </div>

            <!-- Save Button -->
            <div class="pt-6 flex justify-end gap-4">
                <a href="{{ route('Member.dashboard') }}"
                class="border border-red-500 text-red-500 px-6 py-2 rounded hover:bg-red-50 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-red-500 text-white px-6 py-2 rounded hover:bg-red-600 transition">
                    Save
                </button>
            </div>
        </form>
    </div>

    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js" crossorigin="anonymous"></script>

    <!-- Password toggle logic -->
    <script>
        document.getElementById('togglePassword').addEventListener('click', () => {
            const section = document.getElementById('passwordFields');
            section.classList.toggle('hidden');
        });
    </script>

    <!-- Live image preview -->
    <script>
        const profileInput = document.getElementById('profile_image');

        profileInput?.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            const previewContainer = document.querySelector('label[for="profile_image"]');
            const existingImg = previewContainer.querySelector('img');
            const letterDiv = previewContainer.querySelector('div');

            if (existingImg) {
                existingImg.src = URL.createObjectURL(file);
            } else if (letterDiv) {
                const newImg = document.createElement('img');
                newImg.src = URL.createObjectURL(file);
                newImg.alt = 'Profile';
                newImg.className = 'w-20 h-20 rounded-full object-cover border-2 border-gray-300';
                previewContainer.replaceChild(newImg, letterDiv);
            }
        });
    </script>

    <script>
    document.getElementById('verifyPasswordBtn').addEventListener('click', async () => {
        const password = document.getElementById('current_password').value;
        const icon = document.getElementById('checkIcon');
        const msg = document.getElementById('checkMessage');
        const result = document.getElementById('checkResult');

        result.style.display = 'flex';
        msg.textContent = 'Checking...';
        icon.innerHTML = `<i class="fas fa-spinner fa-spin text-gray-500"></i>`;

        try {
            const res = await fetch("{{ route('Member.settings.checkPassword') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ password })
            });

            const data = await res.json();

            if (data.success) {
                icon.innerHTML = `<i class="fas fa-check-circle text-green-500"></i>`;
                msg.textContent = 'Password confirmed.';
                document.getElementById('passwordFields').classList.remove('hidden');
            } else {
                icon.innerHTML = `<i class="fas fa-times-circle text-red-500"></i>`;
                msg.textContent = 'Incorrect password.';
            }
        } catch (err) {
            icon.innerHTML = `<i class="fas fa-exclamation-triangle text-yellow-500"></i>`;
            msg.textContent = 'Error validating password.';
        }
    });
    </script>

@endsection
