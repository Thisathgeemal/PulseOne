<?php
namespace App\Livewire;

use App\Events\MessageSent;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Chat extends Component
{
    public $users;
    public $selectedUser;
    public $newMessage;
    public $messages;
    public $loginId;
    public $showNewChat   = false;
    public $search        = '';
    public $searchFilter  = '';
    public $searchResults = [];
    protected $listeners  = ['deleteMessage'];
    public $errorMessage;

    // user list
    public function mount()
    {
        $this->loginId  = Auth::id();
        $this->messages = collect();
    }

    // Load logged in user chat with last chat time
    public function loadUsersWithChat()
    {
        $loginId = Auth::id();

        $chatUserIds = ChatMessage::where(function ($query) use ($loginId) {
            $query->where('sender_id', $loginId)
                ->whereNull('deleted_by_sender_at');
        })
            ->orWhere(function ($query) use ($loginId) {
                $query->where('receiver_id', $loginId)
                    ->whereNull('deleted_by_receiver_at');
            })
            ->select(DB::raw("CASE WHEN sender_id = $loginId THEN receiver_id ELSE sender_id END as user_id"))
            ->distinct()
            ->pluck('user_id');

        // Filter users by search input
        $query = User::whereIn('id', $chatUserIds);

        if ($this->searchFilter !== '') {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->searchFilter . '%')
                    ->orWhere('last_name', 'like', '%' . $this->searchFilter . '%');
            });
        }

        $this->users = $query->get();

        foreach ($this->users as $user) {
            $lastMessage = ChatMessage::where(function ($query) use ($loginId, $user) {
                $query->where('sender_id', $loginId)->where('receiver_id', $user->id)->whereNull('deleted_by_sender_at');
            })
                ->orWhere(function ($query) use ($loginId, $user) {
                    $query->where('sender_id', $user->id)->where('receiver_id', $loginId)->whereNull('deleted_by_receiver_at');
                })
                ->latest('created_at')
                ->first();

            $user->lastMessageTime = $lastMessage ? $lastMessage->created_at : null;
        }

        $this->users = $this->users->sortByDesc(function ($user) {
            return $user->lastMessageTime ?? now();
        })->values();
    }

    // Toggle new chat modal
    public function toggleNewChat()
    {
        $this->showNewChat   = ! $this->showNewChat;
        $this->search        = '';
        $this->searchResults = [];
    }

    // Search users
    public function updatedSearch()
    {
        if (strlen($this->search) > 0) {
            $this->searchResults = User::where('id', '!=', Auth::id())
                ->where(function ($query) {
                    $query->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%");
                })
                ->take(5)
                ->get();
        } else {
            $this->searchResults = [];
        }
    }

    // Selected user chat
    public function selectUser($userId)
    {
        $this->selectedUser = User::find($userId);
        $this->loadMessages();
        $this->showNewChat = false;
    }

    // Load messages
    public function loadMessages()
    {
        $this->messages = ChatMessage::where(function ($query) {
            $query->where('sender_id', Auth::id())
                ->where('receiver_id', $this->selectedUser->id)
                ->whereNull('deleted_by_sender_at');
        })
            ->orWhere(function ($query) {
                $query->where('sender_id', $this->selectedUser->id)
                    ->where('receiver_id', Auth::id())
                    ->whereNull('deleted_by_receiver_at');
            })
            ->orderBy('created_at', 'asc')
            ->get();
    }

    // Send message
    public function sendMessage()
    {
        if (! $this->selectedUser || empty($this->selectedUser->id)) {
            $this->errorMessage = 'Please select a user to send the message.';
            $this->newMessage   = '';
            return;
        }

        if (! $this->newMessage) {
            return;
        }

        $chatMessage = ChatMessage::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $this->selectedUser->id,
            'message'     => $this->newMessage,
        ]);

        $this->messages->push($chatMessage);
        $this->newMessage   = '';
        $this->errorMessage = null;
        broadcast(new MessageSent($chatMessage));
    }

    // Get listeners for the Livewire component
    public function getListeners()
    {
        return [
            "echo-private:chat.{$this->loginId},MessageSent" => 'newChatMessageNotification',
        ];
    }

    // New chat message notification
    public function newChatMessageNotification($message)
    {
        if ($message['sender_id'] === $this->selectedUser->id) {
            $messageObject = ChatMessage::find($message['id']);
            $this->messages->push($messageObject);
        }
    }

    // Delete message
    public function deleteMessage($messageId)
    {
        $message = ChatMessage::find($messageId);

        if (! $message) {
            return;
        }

        if ($message->sender_id !== Auth::id()) {
            return;
        }

        $message->delete();

        $this->messages = $this->messages->filter(function ($msg) use ($messageId) {
            return $msg->id !== $messageId;
        });
    }

    // Close chat
    public function closeChat()
    {
        $this->selectedUser = null;
        $this->messages     = collect();
    }

    // Delete chat from single user
    public function deleteChat()
    {
        if (! $this->selectedUser) {
            return;
        }

        $loginId     = Auth::id();
        $otherUserId = $this->selectedUser->id;

        ChatMessage::where('sender_id', $loginId)
            ->where('receiver_id', $otherUserId)
            ->update(['deleted_by_sender_at' => now()]);

        ChatMessage::where('sender_id', $otherUserId)
            ->where('receiver_id', $loginId)
            ->update(['deleted_by_receiver_at' => now()]);

        $this->closeChat();
        $this->loadUsersWithChat();
    }

    public function render()
    {
        $this->loadUsersWithChat();
        return view('livewire.chat');
    }
}
