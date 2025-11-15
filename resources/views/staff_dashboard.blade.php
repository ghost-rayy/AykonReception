@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
<style>
    body {
        background: #f8f9fa;
    }

    .dash-card {
        border: none;
        border-radius: 16px;
        padding: 25px 20px;
        transition: transform .25s ease, box-shadow .25s ease;
        background: #fff;
        text-align: center;
    }
    .dash-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.12);
    }
    .dash-icon {
        font-size: 35px;
        opacity: .85;
        margin-bottom: 5px;
    }
    .section-header {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        border-radius: 16px 16px 0 0;
        padding: 18px;
    }
    .conversation-item {
        cursor: pointer;
        transition: background .2s;
    }
    .conversation-item:hover {
        background: #f1f3f5;
    }
    .badge {
        font-size: 0.75rem;
    }
</style>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="dash-card shadow-sm">
            <div class="dash-icon"><i class="bi bi-calendar-event-fill text-success"></i></div>
            <h6 class="text-muted mb-1">Today's Appointments</h6>
            <h2 class="fw-bold">{{ \App\Models\Appointments::where('user_id', auth()->id())->whereDate('appointment_time', today())->count() }}</h2>
        </div>
    </div>

    <div class="col-md-4">
        <div class="dash-card shadow-sm">
            <div class="dash-icon"><i class="bi bi-clock-fill text-warning"></i></div>
            <h6 class="text-muted mb-1">Upcoming Appointments</h6>
            <h2 class="fw-bold">{{ \App\Models\Appointments::where('user_id', auth()->id())->where('appointment_time', '>', now())->count() }}</h2>
        </div>
    </div>

    <div class="col-md-4">
        <div class="dash-card shadow-sm">
            <div class="dash-icon"><i class="bi bi-check-circle-fill text-primary"></i></div>
            <h6 class="text-muted mb-1">Completed Appointments</h6>
            <h2 class="fw-bold">{{ \App\Models\Appointments::where('user_id', auth()->id())->where('status', 'completed')->count() }}</h2>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Messages Panel --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-chat-dots-fill"></i> Messages</h5>
                <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#newMessageModal">
                    <i class="bi bi-plus-circle">New Message</i>
                </button>
            </div>
            <div class="card-body" style="height: 400px; overflow-y: auto;">
                @php
                    $conversations = \App\Models\Message::where(function($query) {
                        $query->where('sender_id', auth()->id())
                              ->orWhere('receiver_id', auth()->id());
                    })
                    ->with(['sender', 'receiver'])
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->groupBy(function($message) {
                        $ids = [$message->sender_id, $message->receiver_id];
                        sort($ids);
                        return implode('-', $ids);
                    })
                    ->map(function($messages) {
                        $latestMessage = $messages->first();
                        $otherUser = $latestMessage->sender_id === auth()->id() ? $latestMessage->receiver : $latestMessage->sender;
                        return [
                            'other_user' => $otherUser,
                            'latest_message' => $latestMessage,
                            'unread_count' => $messages->where('receiver_id', auth()->id())->where('is_read', false)->count()
                        ];
                    });
                @endphp

                @if($conversations->isEmpty())
                    <p class="text-muted text-center">No conversations yet.</p>
                @else
                    @foreach($conversations as $conversation)
                        <div class="conversation-item d-flex align-items-center p-2 border-bottom" data-user-id="{{ $conversation['other_user']->id }}" data-type="chat" onclick="console.log('Clicked conversation for user {{ $conversation['other_user']->id }}')">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($conversation['other_user']->name) }}&background=007bff&color=fff" class="rounded-circle me-3" width="40" height="40">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <strong class="small">{{ $conversation['other_user']->name }}</strong>
                                    <small class="text-muted">{{ $conversation['latest_message']->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-0 small text-muted">{{ Str::limit($conversation['latest_message']->message, 50) }}</p>
                                @if($conversation['unread_count'] > 0)
                                    <span class="badge bg-danger">{{ $conversation['unread_count'] }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- Notifications Panel --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0"><i class="bi bi-bell-fill"></i> Notifications</h5>
            </div>
            <div class="card-body" style="height: 400px; overflow-y: auto;">
                @php
                    $notifications = \App\Models\Message::where('receiver_id', auth()->id())
                        ->where('is_read', false)
                        ->with('sender')
                        ->orderBy('created_at', 'desc')
                        ->get()
                        ->map(function($message) {
                            return [
                                'type' => 'message',
                                'message' => 'New message from ' . $message->sender->name . ': ' . Str::limit($message->message, 50),
                                'time' => $message->created_at->diffForHumans(),
                            ];
                        });

                    $visitorNotifications = collect(\App\Models\Visitors::where('user_id', auth()->id())
                        ->where('created_at', '>=', now()->subHours(24))
                        ->get()
                        ->map(function($visitor) {
                            return [
                                'type' => 'visitor',
                                'id' => $visitor->id,
                                'message' => 'New visitor: ' . $visitor->name . ' (' . $visitor->phone . ')',
                                'time' => $visitor->created_at->diffForHumans(),
                            ];
                        }));

                    $appointmentNotifications = collect(\App\Models\Appointments::where('user_id', auth()->id())
                        ->where('created_at', '>=', now()->subHours(24))
                        ->get()
                        ->map(function($appointment) {
                            return [
                                'type' => 'appointment',
                                'id' => $appointment->id,
                                'message' => 'Appointment: ' . $appointment->visitor_name . ' at ' . $appointment->appointment_time->format('H:i') . ' (' . ucfirst($appointment->status) . ')',
                                'time' => $appointment->created_at->diffForHumans(),
                            ];
                        }));

                    $visitorNotifications = $visitorNotifications->concat($appointmentNotifications);

                    $notifications = collect($notifications)->concat($visitorNotifications)->sortByDesc('time');
                @endphp

                @if($notifications->isNotEmpty())
                    @foreach($notifications as $notification)
                        <div class="d-flex align-items-center p-3 border-bottom notification-item" data-type="{{ $notification['type'] }}" data-id="{{ $notification['id'] ?? '' }}" style="cursor: pointer;">
                            <div class="flex-shrink-0 me-3">
                                <i class="bi {{ $notification['type'] === 'appointment' ? 'bi-calendar-event-fill text-primary' : ($notification['type'] === 'visitor' ? 'bi-person-fill text-success' : 'bi-chat-dots-fill text-info') }}" style="font-size: 24px;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-1">{{ $notification['message'] }}</p>
                                <small class="text-muted">{{ $notification['time'] }}</small>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted mb-0">No new notifications.</p>
                @endif
            </div>
        </div>
    </div>
</div>



<!-- New Message Modal -->
<div class="modal fade" id="newMessageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-3 shadow-sm">
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
                            @php
                                $users = \App\Models\User::whereIn('role', ['staff', 'receptionist'])->where('id', '!=', auth()->id())->get();
                            @endphp
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
<style>
    .chat-messages {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 10px;
    }
    .message {
        max-width: 70%;
        margin-bottom: 10px;
    }
    .message.sent {
        align-self: flex-end;
    }
    .message.received {
        align-self: flex-start;
    }
    .message-content {
        padding: 8px 12px;
        border-radius: 18px;
        position: relative;
    }
    .message.sent .message-content {
        background: #007bff;
        color: white;
        border-bottom-right-radius: 4px;
    }
    .message.received .message-content {
        background: #f1f3f4;
        color: #333;
        border-bottom-left-radius: 4px;
    }
</style>
<script>
    // Auto scroll messages
    const messagesContainer = document.querySelector('#messagesContainer');
    if(messagesContainer) messagesContainer.scrollTop = messagesContainer.scrollHeight;

    // Handle notification clicks
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function() {
            const type = this.dataset.type;
            const id = this.dataset.id;

            if (type === 'visitor' && id) {
                showVisitorModal(id);
            } else if (type === 'appointment' && id) {
                showAppointmentModal(id);
            }
        });
    });

    // Handle conversation clicks
    document.addEventListener('click', function(e) {
        const conversationItem = e.target.closest('.conversation-item');
        if (conversationItem) {
            const type = conversationItem.dataset.type;
            const userId = conversationItem.dataset.userId;

            console.log('Conversation clicked via event listener:', { type, userId, element: conversationItem });

            if (type === 'chat' && userId) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Calling showChatModal with userId:', userId);
                showChatModal(userId);
            }
        }
    });

    function showVisitorModal(visitorId) {
        fetch(`/visitors/${visitorId}/details`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const modalHtml = `
                <div class="modal fade" id="visitorModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Visitor Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Name:</strong> ${data.name}</p>
                                        <p><strong>Email:</strong> ${data.email || 'N/A'}</p>
                                        <p><strong>Phone:</strong> ${data.phone}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Purpose:</strong> ${data.purpose}</p>
                                        <p><strong>Company:</strong> ${data.company || 'N/A'}</p>
                                        <p><strong>Created:</strong> ${new Date(data.created_at).toLocaleString()}</p>
                                    </div>
                                </div>
                                ${data.notes ? `<div class="mt-3"><strong>Notes:</strong><br>${data.notes}</div>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing modal if present
            const existingModal = document.getElementById('visitorModal');
            if (existingModal) existingModal.remove();

            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('visitorModal'));
            modal.show();
        });
    }

    function showAppointmentModal(appointmentId) {
        fetch(`/appointments/${appointmentId}/details`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const modalHtml = `
                <div class="modal fade" id="appointmentModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Appointment Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Visitor:</strong> ${data.visitor_name}</p>
                                        <p><strong>Phone:</strong> ${data.visitor_phone}</p>
                                        <p><strong>Email:</strong> ${data.visitor ? data.visitor.email : 'N/A'}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Date & Time:</strong> ${new Date(data.appointment_time).toLocaleString()}</p>
                                        <p><strong>Status:</strong> <span class="badge bg-${data.status === 'confirmed' ? 'success' : (data.status === 'pending' ? 'warning' : 'danger')}">${data.status}</span></p>
                                        <p><strong>Purpose:</strong> ${data.purpose || 'N/A'}</p>
                                    </div>
                                </div>
                                ${data.notes ? `<div class="mt-3"><strong>Notes:</strong><br>${data.notes}</div>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing modal if present
            const existingModal = document.getElementById('appointmentModal');
            if (existingModal) existingModal.remove();

            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('appointmentModal'));
            modal.show();
        });
    }

    function showChatModal(userId) {
        console.log('Opening chat modal for user:', userId);

        fetch(`/messages/conversation/${userId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Conversation fetch response:', response);
            return response.json();
        })
        .then(data => {
            console.log('Conversation data:', data);

            const currentUserId = {{ auth()->id() }};
            const otherUser = data.messages.length > 0 ?
                (data.messages[0].sender_id === currentUserId ? data.messages[0].receiver : data.messages[0].sender) :
                { name: 'Unknown User' };

            const messagesHtml = data.messages.map(message => {
                const isSent = message.sender_id === currentUserId;
                const time = new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                return `
                    <div class="message ${isSent ? 'sent' : 'received'}">
                        <div class="message-content">
                            <p class="mb-1">${message.message}</p>
                            <small class="text-muted">${time}</small>
                        </div>
                    </div>
                `;
            }).join('');

            const modalHtml = `
                <div class="modal fade" id="chatModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="bi bi-chat-dots-fill"></i> Chat with ${otherUser.name}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body" style="height: 400px; overflow-y: auto;">
                                <div id="chatMessages" class="chat-messages">
                                    ${messagesHtml || '<p class="text-muted text-center">No messages yet. Start the conversation!</p>'}
                                </div>
                            </div>
                            <div class="modal-footer">
                                <form id="sendMessageForm" class="w-100">
                                    <div class="input-group">
                                        <input type="text" id="messageInput" class="form-control" placeholder="Type your message..." required>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-send-fill"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing modal if present
            const existingModal = document.getElementById('chatModal');
            if (existingModal) existingModal.remove();

            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('chatModal'));
            modal.show();

            // Scroll to bottom
            const chatMessages = document.getElementById('chatMessages');
            if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;

            // Handle message sending
            const sendForm = document.getElementById('sendMessageForm');
            if (sendForm) {
                sendForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const messageInput = document.getElementById('messageInput');
                    const message = messageInput.value.trim();

                    console.log('Sending message:', message);

                    if (message) {
                        fetch('/messages', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                receiver_id: userId,
                                message: message
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Message send response:', data);
                            if (data.success) {
                                messageInput.value = '';
                                // Add new message to chat
                                const chatMessages = document.getElementById('chatMessages');
                                const time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                                const messageHtml = `
                                    <div class="message sent">
                                        <div class="message-content">
                                            <p class="mb-1">${message}</p>
                                            <small class="text-muted">${time}</small>
                                        </div>
                                    </div>
                                `;
                                chatMessages.insertAdjacentHTML('beforeend', messageHtml);
                                chatMessages.scrollTop = chatMessages.scrollHeight;
                            }
                        })
                        .catch(error => {
                            console.error('Error sending message:', error);
                        });
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error fetching conversation:', error);
        });
    }
</script>
@endsection
