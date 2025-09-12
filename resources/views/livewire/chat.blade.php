<div class="w-full max-w-xs md:max-w-7xl p-8 bg-white rounded-lg my-4 text-center shadow-md mx-auto animate-fade-in"
    x-data>
    <div class="flex h-[600px] border rounded-2xl shadow-sm overflow-hidden bg-white">
        <!-- Left Sidebar -->
        <div class="w-1/3 border-r border-gray-200 flex flex-col bg-gray-50">

            <!-- Header -->
            <div class="p-5 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Chat</h2>
                <button wire:click="toggleNewChat"
                    class="text-black rounded-full shadow-md hover:scale-110 transition transform duration-200 ease-in-out"
                    aria-label="Start new chat" title="Start new chat">
                    <i class="fas fa-edit fa-lg"></i>
                </button>
            </div>

            <!-- New Chat Modal -->
            <div id="newChatModal" role="dialog" aria-modal="true"
                class="fixed inset-0 backdrop-blur-sm bg-white/20 {{ $showNewChat ? 'flex' : 'hidden' }} items-center justify-center z-50">

                <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-[0_0_15px_4px_rgba(241,30,19,0.5)]">
                    <h2 class="text-xl md:text-2xl font-bold text-center mb-4">Start New Chat</h2>

                    <!-- Search Input -->
                    <input type="text" wire:model.live="search" placeholder="Search users..."
                        class="w-full px-3 py-2 mb-3 rounded-lg border border-gray-300 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400" />

                    <!-- Search Results -->
                    @if (!empty($searchResults))
                        <div class="border rounded-lg overflow-hidden">
                            @foreach ($searchResults as $user)
                                <div wire:click="selectUser({{ $user->id }})"
                                    class="p-3 flex items-center gap-3 hover:bg-blue-100 cursor-pointer">
                                    <div
                                        class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center font-semibold">
                                        {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                    </div>
                                    <div class="truncate">
                                        <p class="font-medium">{{ $user->first_name }} {{ $user->last_name }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @elseif(strlen($search) > 0)
                        <p class="text-sm text-gray-500 mt-2">No users found.</p>
                    @endif

                    <!-- Modal Buttons -->
                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded"
                            wire:click="toggleNewChat">Cancel</button>
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div class="p-3.5 border-b border-gray-200">
                <input type="text" wire:model.live="searchFilter" placeholder="Search your chat"
                    class="w-full px-3 py-2 rounded-lg border border-gray-300 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400" />
            </div>

            <!-- Users List -->
            <div
                class="flex-1 overflow-y-auto divide-y divide-gray-200 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                @foreach ($users as $user)
                    <div wire:click="{{ 'selectUser(' . $user->id . ')' }}"
                        class="flex items-center gap-3 p-4 cursor-pointer hover:bg-blue-100 transition rounded-r-lg
                    {{ $selectedUser && $selectedUser->id === $user->id ? 'bg-blue-50' : '' }}">
                        <div
                            class="w-12 h-12 rounded-full flex items-center justify-center select-none overflow-hidden">
                            @if (!empty($user->profile_image))
                                <!-- Show profile_image -->
                                <img src="{{ asset($user->profile_image) }}?v={{ time() }}"
                                    alt="{{ $user->first_name }}" class="w-full h-full object-cover" />
                            @else
                                <!-- Show first letter fallback -->
                                <div
                                    class="bg-gray-300 text-gray-600 font-semibold text-lg flex items-center justify-center w-full h-full">
                                    {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                </div>
                            @endif
                        </div>


                        <div class="flex flex-col truncate">
                            <span class="text-gray-900 font-medium truncate text-left">
                                {{ $user->first_name }} {{ $user->last_name }}
                            </span>
                            <span class="text-sm text-gray-500 truncate text-left">
                                {{ $user->lastMessageTime ? \Carbon\Carbon::parse($user->lastMessageTime)->format('h:i A') : '' }}
                            </span>
                        </div>

                        @if (!empty($unreadCounts[$user->id]) && $unreadCounts[$user->id] > 0)
                            <span
                                class="ml-auto bg-red-600 text-white text-xs font-semibold px-2 py-1 rounded-full select-none">
                                {{ $unreadCounts[$user->id] }}
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Chat Right Section -->
        <div class="w-2/3 flex flex-col bg-white">
            <!-- Header -->
            @if ($selectedUser)
                <div class="flex items-center justify-between p-3.5 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 rounded-full flex items-center justify-center select-none overflow-hidden">
                            @if (!empty($selectedUser->profile_image))
                                <!-- Show profile_image -->
                                <img src="{{ asset($selectedUser->profile_image) }}?v={{ time() }}"
                                    alt="{{ $selectedUser->first_name }}" class="w-full h-full object-cover" />
                            @else
                                <!-- Show first letter fallback -->
                                <div
                                    class="bg-gray-300 text-gray-600 font-semibold text-lg flex items-center justify-center w-full h-full">
                                    {{ strtoupper(substr($selectedUser->first_name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-md text-left font-semibold text-gray-900">
                                {{ $selectedUser->first_name }} {{ $selectedUser->last_name }}
                            </h3>
                            <p class="text-xs text-gray-500">{{ $selectedUser->email }}</p>
                        </div>
                    </div>

                    {{-- icons --}}
                    <div x-data="{ open: false }" class="relative inline-block text-left">
                        <button @click="open = !open" title="Chat Options"
                            class="hover:text-blue-600 focus:outline-none">
                            <i class="fa-solid fa-ellipsis-vertical mr-4 text-black"></i>
                        </button>

                        <!-- Dropdown menu -->
                        <div x-show="open" @click.outside="open = false" x-transition
                            class="origin-top-right absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                            <div class="py-1">
                                <button wire:click="closeChat" @click="open = false"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                    Close Chat
                                </button>
                                <button wire:click="deleteChat" @click="open = false"
                                    class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 w-full text-left">
                                    Delete Chat
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div
                    class="p-6 text-center select-none border-b border-gray-200 
                            {{ $errorMessage ? 'text-red-500 text-sm' : 'text-gray-400' }}">
                    {{ $errorMessage ?: 'Select a user to start chatting.' }}
                </div>
            @endif

            <!-- Messages -->
            <div id="chatInbox" class="flex-1 w-full p-6 overflow-y-auto space-y-4 bg-gray-50"
                style="scroll-behavior: smooth;">
                @foreach ($messages as $message)
                    <div wire:poll.2s
                        class="flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}"
                        wire:key="message-{{ $message->id }}"
                        @if ($message->sender_id === Auth::id()) oncontextmenu="event.preventDefault(); if(confirm('Delete this message?')) { @this.deleteMessage({{ $message->id }}); }"
                            style="cursor: context-menu;" @endif>
                        <div
                            class="max-w-[80%] px-5 py-2 rounded-3xl {{ $message->sender_id === Auth::id() ? 'bg-blue-600 text-white shadow-lg' : 'bg-gray-200 text-gray-800 shadow-sm' }}">
                            {{ $message->message }}

                            {{-- Time + Tick container --}}
                            @if ($message->sender_id === Auth::id())
                                <div class="flex justify-end items-center mt-1 space-x-1 text-xs">
                                    <span>{{ $message->created_at->format('H:i') }}</span>

                                    <!-- Tick SVG always visible -->
                                    <svg class="w-6 h-4 {{ $message->is_read ? 'text-white' : 'text-white/70' }}"
                                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 16 16">
                                        <!-- First tick -->
                                        <path d="M2 8l3 3 7-7" />
                                        <!-- Second tick slightly offset -->
                                        @if ($message->is_read)
                                            <path d="M8 8l3 3 7-7" />
                                        @endif
                                    </svg>
                                </div>
                            @else
                                {{-- For received messages, just show time --}}
                                <div class="text-xs mt-1 text-gray-400 text-left">
                                    {{ $message->created_at->format('H:i') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Input -->
            <form wire:submit.prevent="sendMessage"
                class="p-5 border-t border-gray-200 bg-white flex items-center gap-4">
                <input wire:model="newMessage" type="text" placeholder="Type your message..."
                    class="flex-1 border border-gray-300 rounded-full px-5 py-3 text-sm placeholder-gray-400 focus:ring-2 focus:ring-blue-400 focus:outline-none transition" />
                <button type="submit"
                    class="bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-6 py-3 rounded-full shadow-md transition">
                    Send
                </button>
            </form>
        </div>

    </div>
</div>
