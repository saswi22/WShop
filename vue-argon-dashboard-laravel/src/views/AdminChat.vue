<template>
  <div class="admin-chat">
    <div class="row g-0 h-100">
      <!-- Left Panel: Chat Room List -->
      <div class="col-md-4 border-end">
        <div class="chat-list-header p-3 bg-white border-bottom">
          <h5 class="mb-0">💬 Live Chat</h5>
          <small class="text-muted">{{ chatRooms.length }} Percakapan</small>
        </div>
        
        <div class="chat-room-list">
          <div 
            v-for="room in chatRooms" 
            :key="room.id"
            @click="selectRoom(room)"
            class="chat-room-item"
            :class="{ 'active': selectedRoom && selectedRoom.id === room.id, 'unread': !room.is_read }"
          >
            <div class="d-flex align-items-start">
              <div class="avatar">
                <i class="fas fa-user"></i>
              </div>
              <div class="flex-grow-1 ms-2">
                <div class="d-flex justify-content-between align-items-start">
                  <h6 class="mb-0">{{ room.visitor_name }}</h6>
                  <small class="text-muted">{{ formatDate(room.last_message_at) }}</small>
                </div>
                <p class="text-muted mb-0 small">{{ room.visitor_email }}</p>
                <p v-if="room.latest_message" class="mb-0 small text-truncate">
                  {{ room.latest_message.message }}
                </p>
                <div class="mt-1">
                  <span v-if="!room.is_read" class="badge bg-primary me-1">Baru</span>
                  <span v-if="room.status === 'active'" class="badge bg-success me-1">✓ Aktif</span>
                  <span v-if="room.status === 'queued'" class="badge bg-warning text-dark me-1">
                    <i class="fas fa-clock"></i> Antrian #{{ room.queue_position }}
                  </span>
                  <span v-if="room.status === 'closed'" class="badge bg-secondary">✗ Ditutup</span>
                </div>
              </div>
            </div>
          </div>
          
          <div v-if="chatRooms.length === 0" class="text-center text-muted p-4">
            <i class="fas fa-comments fa-3x mb-3"></i>
            <p>Belum ada percakapan</p>
          </div>
        </div>
      </div>

      <!-- Right Panel: Chat Messages -->
      <div class="col-md-8">
        <!-- No room selected -->
        <div v-if="!selectedRoom" class="no-chat-selected">
          <i class="fas fa-comments fa-4x text-muted mb-3"></i>
          <h5 class="text-muted">Pilih percakapan untuk mulai chat</h5>
        </div>

        <!-- Chat selected -->
        <div v-else class="chat-container">
          <!-- Chat Header -->
          <div class="chat-header-info p-3 bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h5 class="mb-0">
                  {{ selectedRoom.visitor_name }}
                  <span v-if="selectedRoom.status === 'active'" class="badge bg-success ms-2">Aktif</span>
                  <span v-if="selectedRoom.status === 'queued'" class="badge bg-warning ms-2">Antrian</span>
                  <span v-if="selectedRoom.status === 'closed'" class="badge bg-secondary ms-2">Ditutup</span>
                </h5>
                <p class="mb-0 small text-muted">
                  {{ selectedRoom.visitor_email }}
                  <span v-if="selectedRoom.visitor_phone"> • {{ selectedRoom.visitor_phone }}</span>
                </p>
              </div>
              <div>
                <button 
                  v-if="selectedRoom.status === 'active'"
                  @click="confirmCloseRoom" 
                  class="btn btn-sm btn-outline-warning me-2"
                  title="Tutup chat"
                >
                  <i class="fas fa-check"></i> Selesai
                </button>
                <button 
                  @click="confirmDeleteRoom" 
                  class="btn btn-sm btn-outline-danger"
                  title="Hapus percakapan"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Messages -->
          <div class="chat-messages-area" ref="messageArea">
            <div 
              v-for="message in messages" 
              :key="message.id"
              class="message-item"
              :class="message.sender_type === 'admin' ? 'message-admin' : 'message-user'"
            >
              <div class="message-bubble">
                <div class="message-text">{{ message.message }}</div>
                <div class="message-time">{{ formatTime(message.created_at) }}</div>
              </div>
            </div>
          </div>

          <!-- Message Input -->
          <div class="chat-input-area p-3 bg-white border-top">
            <div class="input-group">
              <input 
                v-model="newMessage"
                type="text"
                class="form-control"
                placeholder="Ketik pesan..."
                @keyup.enter="sendMessage"
                :disabled="sending"
              >
              <button 
                @click="sendMessage" 
                class="btn btn-primary"
                :disabled="sending || !newMessage.trim()"
              >
                <i class="fas fa-paper-plane"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminChat',
  data() {
    return {
      chatRooms: [],
      selectedRoom: null,
      messages: [],
      newMessage: '',
      sending: false,
      pollInterval: null,
      lastRoomCount: 0,
      notificationSound: null
    }
  },
  methods: {
    async loadChatRooms() {
      try {
        const response = await this.$axios.get('/api/v2/chat/rooms');
        const newRooms = response.data.data;
        
        // Deteksi chat baru dan play sound
        if (newRooms.length > this.lastRoomCount) {
          this.playNotificationSound();
        }
        
        this.lastRoomCount = newRooms.length;
        this.chatRooms = newRooms;
        
        // Update unread count in sidebar (if needed)
        const unreadCount = this.chatRooms.filter(room => !room.is_read).length;
        this.$emit('unread-count', unreadCount);
      } catch (error) {
        console.error('Error loading chat rooms:', error);
      }
    },
    
    async selectRoom(room) {
      this.selectedRoom = room;
      await this.loadMessages();
      
      // Mark as read
      if (!room.is_read) {
        await this.markAsRead(room.id);
      }
    },
    
    async loadMessages() {
      if (!this.selectedRoom) return;
      
      try {
        const response = await this.$axios.get(`/api/v2/chat/messages/${this.selectedRoom.id}`);
        this.messages = response.data.data;
        
        this.$nextTick(() => {
          this.scrollToBottom();
        });
      } catch (error) {
        console.error('Error loading messages:', error);
      }
    },
    
    async sendMessage() {
      if (!this.newMessage.trim() || this.sending) return;
      
      this.sending = true;
      try {
        await this.$axios.post('/api/v2/chat/message', {
          room_id: this.selectedRoom.id,
          message: this.newMessage,
          sender_type: 'admin'
        });
        
        this.newMessage = '';
        await this.loadMessages();
        await this.loadChatRooms(); // Update room list
      } catch (error) {
        console.error('Error sending message:', error);
        alert('Gagal mengirim pesan');
      } finally {
        this.sending = false;
      }
    },
    
    async markAsRead(roomId) {
      try {
        await this.$axios.put(`/api/v2/chat/room/${roomId}/read`);
        await this.loadChatRooms(); // Refresh list
      } catch (error) {
        console.error('Error marking as read:', error);
      }
    },
    
    async confirmDeleteRoom() {
      if (!confirm('Yakin ingin menghapus percakapan ini?')) return;
      
      try {
        await this.$axios.delete(`/api/v2/chat/room/${this.selectedRoom.id}`);
        this.selectedRoom = null;
        this.messages = [];
        await this.loadChatRooms();
      } catch (error) {
        console.error('Error deleting room:', error);
        alert('Gagal menghapus percakapan');
      }
    },

    async confirmCloseRoom() {
      if (!confirm('Tutup percakapan ini? Chat berikutnya dari antrian akan diaktifkan.')) return;
      
      try {
        await this.$axios.put(`/api/v2/chat/room/${this.selectedRoom.id}/close`);
        this.selectedRoom = null;
        this.messages = [];
        await this.loadChatRooms();
        alert('Chat ditutup. Pasien berikutnya telah diaktifkan.');
      } catch (error) {
        console.error('Error closing room:', error);
        alert('Gagal menutup chat');
      }
    },
    
    scrollToBottom() {
      if (this.$refs.messageArea) {
        const area = this.$refs.messageArea;
        area.scrollTop = area.scrollHeight;
      }
    },
    
    formatDate(datetime) {
      if (!datetime) return '';
      const date = new Date(datetime);
      const now = new Date();
      const diff = now - date;
      const diffDays = Math.floor(diff / (1000 * 60 * 60 * 24));
      
      if (diffDays === 0) {
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');
        return `${hours}:${minutes}`;
      } else if (diffDays === 1) {
        return 'Kemarin';
      } else if (diffDays < 7) {
        return `${diffDays} hari lalu`;
      } else {
        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
      }
    },
    
    formatTime(datetime) {
      const date = new Date(datetime);
      const hours = date.getHours().toString().padStart(2, '0');
      const minutes = date.getMinutes().toString().padStart(2, '0');
      return `${hours}:${minutes}`;
    },
    
    startPolling() {
      // Poll every 5 seconds for new messages
      this.pollInterval = setInterval(() => {
        this.loadChatRooms();
        if (this.selectedRoom) {
          this.loadMessages();
        }
      }, 5000);
    },
    
    stopPolling() {
      if (this.pollInterval) {
        clearInterval(this.pollInterval);
        this.pollInterval = null;
      }
    },

    playNotificationSound() {
      if (this.notificationSound) {
        this.notificationSound.play().catch(err => {
          console.log('Could not play notification sound:', err);
        });
      }
      
      // Browser notification
      if ('Notification' in window && Notification.permission === 'granted') {
        new Notification('Chat Baru', {
          body: 'Ada pesan baru dari pasien',
          icon: '/favicon.ico'
        });
      }
    }
  },
  
  mounted() {
    this.loadChatRooms();
    this.startPolling();
    
    // Initialize notification sound
    this.notificationSound = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUKjj8LdjHAU1jdXrz4IuBSh+zPLaizsKGGS66emkUBELTKXh7bhkGgU0iM3w1IU3CBxmt+rqmkkSDVCl4e+2ZBwFN4vQ7s+AMQUngMvw2og1CBdks+nln1ARCA==');
    
    // Request notification permission
    if ('Notification' in window && Notification.permission === 'default') {
      Notification.requestPermission();
    }
  },
  
  beforeUnmount() {
    this.stopPolling();
  }
}
</script>

<style scoped>
.admin-chat {
  height: calc(100vh - 200px);
  min-height: 600px;
  background: white;
  border-radius: 8px;
  overflow: hidden;
}

.chat-list-header {
  position: sticky;
  top: 0;
  z-index: 10;
}

.chat-room-list {
  height: calc(100vh - 270px);
  overflow-y: auto;
}

.chat-room-list::-webkit-scrollbar {
  width: 6px;
}

.chat-room-list::-webkit-scrollbar-track {
  background: transparent;
}

.chat-room-list::-webkit-scrollbar-thumb {
  background: #ccc;
  border-radius: 3px;
}

.chat-room-item {
  padding: 16px;
  border-bottom: 1px solid #e9ecef;
  cursor: pointer;
  transition: all 0.2s;
}

.chat-room-item:hover {
  background: #f8f9fa;
}

.chat-room-item.active {
  background: #e7f3ff;
  border-left: 3px solid #5e72e4;
}

.chat-room-item.unread {
  background: #f0f7ff;
  font-weight: 500;
}

.chat-room-item .badge {
  font-size: 10px;
  padding: 3px 7px;
}

.chat-room-item .badge.bg-warning {
  animation: pulse-warning 2s infinite;
}

@keyframes pulse-warning {
  0%, 100% {
    box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
  }
  50% {
    box-shadow: 0 0 0 6px rgba(245, 158, 11, 0);
  }
}

.avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 18px;
  flex-shrink: 0;
}

.no-chat-selected {
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.chat-container {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.chat-header-info {
  flex-shrink: 0;
}

.chat-messages-area {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
  background: #f8f9fa;
  max-height: calc(100vh - 400px);
  min-height: 300px;
}

.chat-messages-area::-webkit-scrollbar {
  width: 6px;
}

.chat-messages-area::-webkit-scrollbar-track {
  background: transparent;
}

.chat-messages-area::-webkit-scrollbar-thumb {
  background: #ccc;
  border-radius: 3px;
}

.message-item {
  margin-bottom: 16px;
  display: flex;
}

.message-user {
  justify-content: flex-start;
}

.message-admin {
  justify-content: flex-end;
}

.message-bubble {
  max-width: 70%;
  padding: 12px 16px;
  border-radius: 16px;
  word-wrap: break-word;
}

.message-user .message-bubble {
  background: white;
  color: #333;
  border-bottom-left-radius: 4px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.message-admin .message-bubble {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-bottom-right-radius: 4px;
}

.message-text {
  font-size: 14px;
  line-height: 1.5;
  margin-bottom: 4px;
}

.message-time {
  font-size: 11px;
  opacity: 0.7;
}

.chat-input-area {
  flex-shrink: 0;
  background: white;
  border-top: 2px solid #e9ecef;
  min-height: 70px;
}

.chat-input-area .input-group {
  gap: 8px;
}

.chat-input-area .form-control {
  border-radius: 20px;
  padding: 10px 16px;
}

.chat-input-area .btn {
  border-radius: 50%;
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
