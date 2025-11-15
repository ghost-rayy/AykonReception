@extends('layouts.app')

@section('title', 'Messages')

@section('content')

<div class="row">
    <!-- Conversations List -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-chat-dots-fill"></i> Conversations</h5>
            </div>
            <div class="card-body p-0" id="conversationsList">
                @php
                    $conversations = $messages->groupBy(function($message) {
                        $ids = [$message->sender_id, $message->receiver_id];
                        sort($ids);
                        return implode('-', $ids);
                    })
                    ->map(function($msgs) {
                        $latestMessage = $msgs->first();
                        $otherUser = $latestMessage->sender_id === auth()->id() ? $latestMessage->receiver : $latestMessage->sender;
                        return [
                            'other_user' => $otherUser,
                            'latest_message' => $latestMessage,
                            'unread_count' => $msgs->where('receiver_id', auth()->id())->where('is_read', false)->count()
                        ];
                    });
                @endphp

                @if($conversations->isEmpty())
                    <p class="text-muted text-center p-3">No conversations yet.</p>
                @else
                    @foreach($conversations as $conversation)
                        <div class="p-3 border-bottom conversation-item {{ $conversation['unread_count'] > 0 ? 'bg-light' : '' }}" data-user-id="{{ $conversation['other_user']->id }}" style="cursor: pointer;">
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($conversation['other_user']->name) }}&background=007bff&color=fff" class="rounded-circle me-3" width="40" height="40">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <strong>{{ $conversation['other_user']->name }}</strong>
                                        <small class="text-muted">{{ $conversation['latest_message']->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-0 small text-muted">{{ Str::limit($conversation['latest_message']->message, 40) }}</p>
                                    @if($conversation['unread_count'] > 0)
                                        <span class="badge bg-danger mt-1">{{ $conversation['unread_count'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- Messages -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0" id="chatTitle"><i class="bi bi-chat-dots-fill"></i> Select a conversation</h5>
                <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#newMessageModal">
                    <i class="bi bi-plus-circle"></i> New Message
                </button>
            </div>
            <div class="card-body" id="messagesContainer" style="height: 500px; overflow-y: auto; display: none;">
                <!-- Messages will be loaded here via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- New Message Modal -->
<div class="modal fade" id="newMessageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send New Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('messages.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="receiver_id" class="form-label">To</label>
                        <select name="receiver_id" id="receiver_id" class="form-select" required>
                            <option value="">Select recipient</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ ucfirst($user->role) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea name="message" id="message" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let currentUserId = null;

    // Handle conversation selection
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.querySelector('strong').textContent;

            // Update UI
            document.querySelectorAll('.conversation-item').forEach(el => el.classList.remove('bg-primary', 'text-white'));
            this.classList.add('bg-primary', 'text-white');
            document.getElementById('chatTitle').innerHTML = `<i class="bi bi-chat-dots-fill"></i> Chat with ${userName}`;

            // Load messages for this conversation
            loadMessages(userId);
            currentUserId = userId;
        });
    });

    function loadMessages(userId) {
        fetch(`/messages/conversation/${userId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('messagesContainer');
            container.innerHTML = '';

            if (data.messages.length === 0) {
                container.innerHTML = '<p class="text-muted text-center">No messages in this conversation yet.</p>';
            } else {
                data.messages.forEach(message => {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = `message-item mb-3 p-3 rounded ${message.sender_id === {{ auth()->id() }} ? 'bg-primary text-white ms-auto' : 'bg-light'}`;
                    messageDiv.style.maxWidth = '70%';
                    if (message.sender_id === {{ auth()->id() }}) {
                        messageDiv.style.marginLeft = 'auto';
                    }

                    messageDiv.innerHTML = `
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>${message.sender.name}</strong>
                                <small class="d-block">${new Date(message.created_at).toLocaleString()}</small>
                            </div>
                            ${message.receiver_id === {{ auth()->id() }} && !message.is_read ? '<span class="badge bg-danger">New</span>' : ''}
                        </div>
                        <p class="mb-0 mt-2">${message.message}</p>
                    `;

                    container.appendChild(messageDiv);

                    // Mark as read if received
                    if (message.receiver_id === {{ auth()->id() }} && !message.is_read) {
                        markAsRead(message.id);
                    }
                });
            }

            container.style.display = 'block';
            container.scrollTop = container.scrollHeight;
        });
    }

    function markAsRead(messageId) {
        fetch(`/messages/${messageId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        });
    }

    // Auto scroll to bottom when messages load
    document.getElementById('messagesContainer').scrollTop = document.getElementById('messagesContainer').scrollHeight;
</script>
@endsection
