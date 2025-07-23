@extends('adminDashboard.layout')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md mt-10 relative">

        <!-- Page Title -->
        <h2 class="text-2xl font-bold text-gray-800 mb-8">Edit Profile</h2>


    </div>

    <!-- Password Toggle Logic (Unused in current markup, might remove if not used) -->
    <script>
        document.getElementById('togglePassword')?.addEventListener('click', () => {
            const section = document.getElementById('passwordFields');
            section.classList.toggle('hidden');
        });
    </script>

    <!-- Live Profile Image Preview when file selected -->
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

    <!-- Verify Current Password via AJAX -->
    <script>
        document.getElementById('verifyPasswordBtn').addEventListener('click', async () => {
            const password = document.getElementById('current_password').value;
            const icon = document.getElementById('checkIcon');
            const msg = document.getElementById('checkMessage');
            const result = document.getElementById('checkResult');

            // Show loading state
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
                    // Reveal new password fields
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
