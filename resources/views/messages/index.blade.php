@extends('layouts.app')

@section('title', 'Messages')

@section('content')

<style>
    .messages-container {
        background: #f8f9fa;
        min-height: calc(100vh - 200px);
        padding: 20px 0;
    }
    
    .conversations-panel {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        height: 600px;
        display: flex;
        flex-direction: column;
    }
    
    .conversations-header {
        padding: 20px;
        border-bottom: 1px solid #e9ecef;
        background: #0d1b2a;
        color: white;
        border-radius: 12px 12px 0 0;
    }
    
    .conversations-list {
        flex: 1;
        overflow-y: auto;
        padding: 0;
    }
    
    .conversation-item {
        padding: 15px 20px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .conversation-item:hover {
        background: #f8f9fa;
    }
    
    .conversation-item.active {
        background: #e7f3ff;
        border-left: 4px solid #0d1b2a;
    }
    
    .conversation-item.unread {
        background: #fff3cd;
        font-weight: 500;
    }
    
    .conversation-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    
    .conversation-info {
        flex: 1;
        min-width: 0;
    }
    
    .conversation-name {
        font-weight: 600;
        color: #212529;
        margin-bottom: 4px;
        font-size: 15px;
    }
    
    .conversation-preview {
        font-size: 13px;
        color: #6c757d;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .conversation-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 6px;
    }
    
    .conversation-time {
        font-size: 12px;
        color: #6c757d;
    }
    
    .unread-badge {
        background: #dc3545;
        color: white;
        border-radius: 12px;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: 600;
        min-width: 20px;
        text-align: center;
    }
    
    .chat-panel {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        height: 600px;
        display: flex;
        flex-direction: column;
    }
    
    .chat-header {
        padding: 20px;
        border-bottom: 1px solid #e9ecef;
        background: #0d1b2a;
        color: white;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        background: #f8f9fa;
    }
    
    .message-bubble {
        margin-bottom: 15px;
        display: flex;
        flex-direction: column;
        max-width: 70%;
    }
    
    .message-bubble.sent {
        align-self: flex-end;
        align-items: flex-end;
    }
    
    .message-bubble.received {
        align-self: flex-start;
        align-items: flex-start;
    }
    
    .message-content {
        padding: 12px 16px;
        border-radius: 18px;
        word-wrap: break-word;
    }
    
    .message-bubble.sent .message-content {
        background: #0d1b2a;
        color: white;
        border-bottom-right-radius: 4px;
    }
    
    .message-bubble.received .message-content {
        background: white;
        color: #212529;
        border-bottom-left-radius: 4px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    
    .message-time {
        font-size: 11px;
        color: #6c757d;
        margin-top: 4px;
        padding: 0 4px;
    }
    
    .chat-input-area {
        padding: 20px;
        border-top: 1px solid #e9ecef;
        background: white;
        border-radius: 0 0 12px 12px;
    }
    
    .chat-input-form {
        display: flex;
        gap: 10px;
    }
    
    .chat-input {
        flex: 1;
        border: 1px solid #dee2e6;
        border-radius: 24px;
        padding: 10px 20px;
        resize: none;
        font-size: 14px;
    }
    
    .chat-input:focus {
        outline: none;
        border-color: #0d1b2a;
        box-shadow: 0 0 0 3px rgba(13, 27, 42, 0.1);
    }
    
    .send-btn {
        background: #0d1b2a;
        color: white;
        border: none;
        border-radius: 50%;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .send-btn:hover {
        background: #1a2f47;
        transform: scale(1.05);
    }
    
    .send-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .empty-chat {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #6c757d;
    }
    
    .empty-chat i {
        font-size: 64px;
        margin-bottom: 16px;
        opacity: 0.3;
    }
    
    .attachment-btn {
        background: #6c757d;
        color: white;
        border: none;
        border-radius: 50%;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .attachment-btn:hover {
        background: #5a6268;
        transform: scale(1.05);
    }
    
    .file-input-wrapper {
        position: relative;
        display: inline-block;
    }
    
    .file-input-wrapper input[type="file"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .attachment-preview {
        margin-top: 10px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .attachment-preview img {
        max-width: 100px;
        max-height: 100px;
        border-radius: 4px;
    }
    
    .attachment-preview .file-info {
        flex: 1;
    }
    
    .attachment-preview .remove-file {
        cursor: pointer;
        color: #dc3545;
        font-size: 18px;
    }
    
    .message-attachment {
        margin-top: 8px;
        padding: 10px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        display: inline-block;
    }
    
    .message-bubble.received .message-attachment {
        background: #f8f9fa;
    }
    
    .message-attachment img {
        max-width: 300px;
        max-height: 300px;
        border-radius: 8px;
        cursor: pointer;
    }
    
    .message-attachment .file-attachment {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px;
        background: white;
        border-radius: 6px;
        text-decoration: none;
        color: #212529;
    }
    
    .message-attachment .file-attachment:hover {
        background: #f8f9fa;
    }
    
    .file-icon {
        font-size: 24px;
    }
</style>

<div class="messages-container">
    <div class="container-fluid">
        <div class="row g-4">
            <!-- Conversations List -->
            <div class="col-md-4">
                <div class="conversations-panel">
                    <div class="conversations-header">
                        <h5 class="mb-0">
                            <i class="bi bi-chat-dots-fill me-2"></i>Conversations
                        </h5>
                    </div>
                    <div class="conversations-list" id="conversationsList">
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
                            })
                            ->sortByDesc(function($conv) {
                                return $conv['latest_message']->created_at;
                            });
                        @endphp

                        @if($conversations->isEmpty())
                            <div class="text-center p-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                                <p>No conversations yet. Start a new conversation!</p>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newMessageModal">
                                    <i class="bi bi-plus-circle me-1"></i>New Message
                                </button>
                            </div>
                        @else
                            @foreach($conversations as $conversation)
                                <div class="conversation-item {{ $conversation['unread_count'] > 0 ? 'unread' : '' }}" 
                                     data-user-id="{{ $conversation['other_user']->id }}"
                                     onclick="selectConversation({{ $conversation['other_user']->id }}, '{{ $conversation['other_user']->name }}')">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($conversation['other_user']->name) }}&background=0d1b2a&color=fff" 
                                         class="conversation-avatar" 
                                         alt="{{ $conversation['other_user']->name }}">
                                    <div class="conversation-info">
                                        <div class="conversation-name">{{ $conversation['other_user']->name }}</div>
                                        <div class="conversation-preview">{{ Str::limit($conversation['latest_message']->message, 50) }}</div>
                                    </div>
                                    <div class="conversation-meta">
                                        <div class="conversation-time">{{ $conversation['latest_message']->created_at->diffForHumans() }}</div>
                                        @if($conversation['unread_count'] > 0)
                                            <span class="unread-badge">{{ $conversation['unread_count'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="p-3 border-top">
                        <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#newMessageModal">
                            <i class="bi bi-plus-circle me-2"></i>New Message
                        </button>
                    </div>
                </div>
            </div>

            <!-- Chat Panel -->
            <div class="col-md-8">
                <div class="chat-panel" id="chatPanel">
                    <div class="chat-header">
                        <h5 class="mb-0" id="chatTitle">
                            <i class="bi bi-chat-dots-fill me-2"></i>Select a conversation
                        </h5>
                    </div>
                    <div class="chat-messages" id="chatMessages">
                        <div class="empty-chat">
                            <i class="bi bi-chat-left-text"></i>
                            <p class="mb-0">Select a conversation to start messaging</p>
                        </div>
                    </div>
                    <div class="chat-input-area" id="chatInputArea" style="display: none;">
                        <form id="messageForm" onsubmit="sendMessage(event)" enctype="multipart/form-data">
                            <div id="attachmentPreview" style="display: none;" class="attachment-preview">
                                <div id="attachmentContent"></div>
                                <span class="remove-file" onclick="removeAttachment()">&times;</span>
                            </div>
                            <div class="chat-input-form">
                                <div class="file-input-wrapper">
                                    <button type="button" class="attachment-btn" onclick="document.getElementById('fileInput').click()" title="Attach file">
                                        <i class="bi bi-paperclip"></i>
                                    </button>
                                    <input type="file" id="fileInput" name="attachment" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar" onchange="handleFileSelect(event)" style="display: none;">
                                </div>
                                <textarea id="messageInput" 
                                          class="chat-input" 
                                          rows="1" 
                                          placeholder="Type your message..." 
                                          onkeydown="handleKeyPress(event)"></textarea>
                                <button type="submit" class="send-btn" id="sendBtn">
                                    <i class="bi bi-send-fill"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
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
            <form id="newMessageForm" onsubmit="sendNewMessage(event)">
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
                        <label for="new_message_text" class="form-label">Message</label>
                        <textarea name="message" id="new_message_text" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="new_message_attachment" class="form-label">Attachment (Optional)</label>
                        <input type="file" name="attachment" id="new_message_attachment" class="form-control" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">
                        <small class="text-muted">Max size: 10MB. Supported: Images, PDF, Word, Excel, Text, ZIP, RAR</small>
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
    let currentUserName = null;
    let messageRefreshInterval = null;

    function selectConversation(userId, userName) {
        currentUserId = userId;
        currentUserName = userName;

        // Update UI
        document.querySelectorAll('.conversation-item').forEach(el => {
            el.classList.remove('active');
            if (el.dataset.userId == userId) {
                el.classList.add('active');
            }
        });

        document.getElementById('chatTitle').innerHTML = `<i class="bi bi-chat-dots-fill me-2"></i>Chat with ${userName}`;
        document.getElementById('chatInputArea').style.display = 'block';
        document.getElementById('messageInput').focus();

        // Load messages
        loadMessages(userId);

        // Start auto-refresh
        if (messageRefreshInterval) {
            clearInterval(messageRefreshInterval);
        }
        messageRefreshInterval = setInterval(() => {
            if (currentUserId) {
                loadMessages(currentUserId, true);
            }
        }, 3000); // Refresh every 3 seconds
    }

    function loadMessages(userId, silent = false) {
        fetch(`/messages/conversation/${userId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('chatMessages');
            
            if (data.messages.length === 0) {
                container.innerHTML = `
                    <div class="empty-chat">
                        <i class="bi bi-chat-left-text"></i>
                        <p class="mb-0">No messages yet. Start the conversation!</p>
                    </div>
                `;
                return;
            }

            // Only update if not silent or if messages changed
            if (!silent || container.children.length === 0) {
                container.innerHTML = '';
                
                data.messages.forEach(message => {
                    const authId = {{ auth()->id() }};
                    const isSent = message.sender_id === authId;
                    const messageDiv = document.createElement('div');
                    messageDiv.className = `message-bubble ${isSent ? 'sent' : 'received'}`;
                    
                    const time = new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    
                    let attachmentHtml = '';
                    if (message.attachment_path) {
                        if (message.attachment_type === 'image') {
                            attachmentHtml = `
                                <div class="message-attachment">
                                    <img src="/storage/${message.attachment_path}" 
                                         alt="${message.attachment_name || 'Image'}" 
                                         onclick="window.open('/storage/${message.attachment_path}', '_blank')"
                                         style="cursor: pointer;">
                                </div>
                            `;
                        } else {
                            const fileSize = message.attachment_size ? formatFileSize(message.attachment_size) : '';
                            attachmentHtml = `
                                <div class="message-attachment">
                                    <a href="/messages/${message.id}/attachment" class="file-attachment" download>
                                        <i class="bi bi-file-earmark file-icon"></i>
                                        <div>
                                            <div style="font-weight: 600;">${escapeHtml(message.attachment_name || 'File')}</div>
                                            ${fileSize ? `<div style="font-size: 12px; color: #6c757d;">${fileSize}</div>` : ''}
                                        </div>
                                    </a>
                                </div>
                            `;
                        }
                    }
                    
                    messageDiv.innerHTML = `
                        ${message.message ? `<div class="message-content">${escapeHtml(message.message)}</div>` : ''}
                        ${attachmentHtml}
                        <div class="message-time">${time}</div>
                    `;
                    
                    container.appendChild(messageDiv);
                    
                    // Mark as read if received
                    if (!isSent && !message.is_read) {
                        markAsRead(message.id);
                    }
                });
                
                container.scrollTop = container.scrollHeight;
            } else {
                // Silent update - just check for new messages
                const lastMessage = data.messages[data.messages.length - 1];
                const authId = {{ auth()->id() }};
                if (lastMessage && lastMessage.receiver_id === authId && !lastMessage.is_read) {
                    // New message received, reload
                    loadMessages(userId, false);
                }
            }
        })
        .catch(error => {
            console.error('Error loading messages:', error);
        });
    }

    let selectedFile = null;

    function handleFileSelect(event) {
        const file = event.target.files[0];
        if (!file) return;
        
        // Check file size (10MB max)
        if (file.size > 10 * 1024 * 1024) {
            alert('File size must be less than 10MB');
            event.target.value = '';
            return;
        }
        
        selectedFile = file;
        const preview = document.getElementById('attachmentPreview');
        const content = document.getElementById('attachmentContent');
        
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                content.innerHTML = `
                    <img src="${e.target.result}" style="max-width: 100px; max-height: 100px; border-radius: 4px;">
                    <div class="file-info">
                        <div style="font-weight: 600;">${escapeHtml(file.name)}</div>
                        <div style="font-size: 12px; color: #6c757d;">${formatFileSize(file.size)}</div>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        } else {
            content.innerHTML = `
                <i class="bi bi-file-earmark" style="font-size: 48px; color: #6c757d;"></i>
                <div class="file-info">
                    <div style="font-weight: 600;">${escapeHtml(file.name)}</div>
                    <div style="font-size: 12px; color: #6c757d;">${formatFileSize(file.size)}</div>
                </div>
            `;
        }
        
        preview.style.display = 'flex';
    }

    function removeAttachment() {
        selectedFile = null;
        document.getElementById('fileInput').value = '';
        document.getElementById('attachmentPreview').style.display = 'none';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    function sendMessage(event) {
        event.preventDefault();
        
        if (!currentUserId) return;
        
        const messageInput = document.getElementById('messageInput');
        const message = messageInput.value.trim();
        
        // At least message or file is required
        if (!message && !selectedFile) return;
        
        const sendBtn = document.getElementById('sendBtn');
        sendBtn.disabled = true;
        
        const formData = new FormData();
        formData.append('receiver_id', currentUserId);
        if (message) {
            formData.append('message', message);
        }
        if (selectedFile) {
            formData.append('attachment', selectedFile);
        }
        
        fetch('/messages', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageInput.value = '';
                messageInput.style.height = 'auto';
                removeAttachment();
                loadMessages(currentUserId, false);
                updateUnreadCount();
            }
            sendBtn.disabled = false;
        })
        .catch(error => {
            console.error('Error sending message:', error);
            sendBtn.disabled = false;
        });
    }

    function sendNewMessage(event) {
        event.preventDefault();
        
        const form = event.target;
        const messageText = document.getElementById('new_message_text').value.trim();
        const attachment = document.getElementById('new_message_attachment').files[0];
        
        // At least message or attachment is required
        if (!messageText && !attachment) {
            alert('Please enter a message or attach a file');
            return;
        }
        
        const formData = new FormData(form);
        
        fetch('/messages', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success || response.ok) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('newMessageModal'));
                modal.hide();
                form.reset();
                
                // Reload page to show new conversation
                window.location.reload();
            } else if (data.error) {
                alert(data.error);
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            alert('Error sending message. Please try again.');
        });
    }

    function markAsRead(messageId) {
        fetch(`/messages/${messageId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(() => {
            updateUnreadCount();
        });
    }

    function handleKeyPress(event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            sendMessage(event);
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function updateUnreadCount() {
        fetch('/messages/unread-count', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('messages-badge');
            if (data.count > 0) {
                if (badge) {
                    badge.textContent = data.count;
                } else {
                    const messagesLink = document.querySelector('a[href="{{ route("messages.index") }}"]');
                    if (messagesLink) {
                        const newBadge = document.createElement('span');
                        newBadge.className = 'badge bg-danger ms-2';
                        newBadge.id = 'messages-badge';
                        newBadge.textContent = data.count;
                        messagesLink.appendChild(newBadge);
                    }
                }
            } else {
                if (badge) {
                    badge.remove();
                }
            }
        });
    }

    // Auto-resize textarea
    document.getElementById('messageInput')?.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Update unread count on page load
    updateUnreadCount();
    setInterval(updateUnreadCount, 10000); // Update every 10 seconds

    // Clean up interval on page unload
    window.addEventListener('beforeunload', function() {
        if (messageRefreshInterval) {
            clearInterval(messageRefreshInterval);
        }
    });
</script>
@endsection
