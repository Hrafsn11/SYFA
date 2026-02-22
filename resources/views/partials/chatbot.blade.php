{{-- =====================================================================
     SYFA Chatbot Widget - Floating bubble + chat window
     Included in resources/views/layouts/app.blade.php
     ===================================================================== --}}

@auth
{{-- ── Styles ────────────────────────────────────────────────────── --}}
<style>
/* ── Bubble Toggle ── */
#syfa-chat-bubble {
    position: fixed;
    bottom: 28px;
    right: 28px;
    z-index: 9999;
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background: linear-gradient(135deg, #696cff 0%, #9155fd 100%);
    box-shadow: 0 6px 24px rgba(105,108,255,.45);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    transition: transform .2s, box-shadow .2s;
}
#syfa-chat-bubble:hover {
    transform: scale(1.08);
    box-shadow: 0 8px 30px rgba(105,108,255,.6);
}
#syfa-chat-bubble i { color: #fff; font-size: 24px; }
#syfa-chat-bubble .chat-badge {
    position: absolute;
    top: 2px; right: 2px;
    width: 14px; height: 14px;
    border-radius: 50%;
    background: #ff3e1d;
    border: 2px solid #fff;
    display: none;
}

/* ── Chat Window ── */
#syfa-chat-window {
    position: fixed;
    bottom: 100px;
    right: 28px;
    z-index: 9998;
    width: 380px;
    max-width: calc(100vw - 40px);
    height: 560px;
    max-height: calc(100vh - 130px);
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 16px 48px rgba(0,0,0,.18);
    display: none;
    flex-direction: column;
    overflow: hidden;
    animation: chatSlideUp .25s ease;
}
@keyframes chatSlideUp {
    from { opacity:0; transform: translateY(20px) scale(.97); }
    to   { opacity:1; transform: translateY(0)   scale(1);    }
}

/* ── Header ── */
.chat-header {
    background: linear-gradient(135deg, #696cff 0%, #9155fd 100%);
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
}
.chat-header-avatar {
    width: 38px; height: 38px;
    border-radius: 50%;
    background: rgba(255,255,255,.25);
    display: flex; align-items: center; justify-content: center;
}
.chat-header-avatar i { color: #fff; font-size: 18px; }
.chat-header-info h6 { color: #fff; margin: 0; font-size: 14px; font-weight: 600; }
.chat-header-info small { color: rgba(255,255,255,.8); font-size: 11px; }
.chat-header-actions { margin-left: auto; display: flex; gap: 8px; }
.chat-header-actions button {
    background: rgba(255,255,255,.2);
    border: none; color: #fff;
    width: 28px; height: 28px;
    border-radius: 6px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .15s;
}
.chat-header-actions button:hover { background: rgba(255,255,255,.35); }

/* ── Messages Area ── */
.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    background: #f8f7fa;
    display: flex;
    flex-direction: column;
    gap: 10px;
    scroll-behavior: smooth;
}
.chat-messages::-webkit-scrollbar { width: 4px; }
.chat-messages::-webkit-scrollbar-thumb { background: #d0cdff; border-radius: 4px; }

/* ── Message Bubbles ── */
.msg-row { display: flex; align-items: flex-end; gap: 8px; }
.msg-row.user  { flex-direction: row-reverse; }
.msg-avatar {
    width: 28px; height: 28px;
    border-radius: 50%;
    flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700;
}
.msg-avatar.bot-av  { background: linear-gradient(135deg,#696cff,#9155fd); color:#fff; }
.msg-avatar.user-av { background: #e7e6ff; color: #696cff; }

.msg-bubble {
    max-width: 80%;
    padding: 10px 14px;
    border-radius: 16px;
    font-size: 13px;
    line-height: 1.5;
    word-break: break-word;
    white-space: pre-wrap;
}
.msg-bubble.bot  {
    background: #fff;
    border-bottom-left-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,.07);
    color: #433c50;
}
.msg-bubble.user {
    background: linear-gradient(135deg,#696cff,#9155fd);
    color: #fff;
    border-bottom-right-radius: 4px;
}

/* ── Typing Indicator ── */
.typing-indicator {
    display: flex; align-items: center; gap: 4px;
    padding: 10px 14px;
    background: #fff;
    border-radius: 16px;
    border-bottom-left-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,.07);
    width: fit-content;
}
.typing-indicator span {
    width: 7px; height: 7px;
    background: #696cff;
    border-radius: 50%;
    animation: typingBounce 1.2s infinite;
}
.typing-indicator span:nth-child(2) { animation-delay: .2s; }
.typing-indicator span:nth-child(3) { animation-delay: .4s; }
@keyframes typingBounce {
    0%,60%,100% { transform: translateY(0); opacity:.4; }
    30%          { transform: translateY(-6px); opacity:1; }
}

/* ── Quick Replies ── */
.quick-replies {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    padding: 8px 16px;
    background: #f8f7fa;
    border-top: 1px solid #ebebeb;
    flex-shrink: 0;
}
.quick-reply-btn {
    background: #fff;
    border: 1.5px solid #696cff;
    color: #696cff;
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 11.5px;
    cursor: pointer;
    transition: all .15s;
    white-space: nowrap;
    font-weight: 500;
}
.quick-reply-btn:hover {
    background: #696cff;
    color: #fff;
}

/* ── Input Area ── */
.chat-input-area {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 14px;
    background: #fff;
    border-top: 1px solid #ebebeb;
    flex-shrink: 0;
}
.chat-input-area textarea {
    flex: 1;
    border: 1.5px solid #e0dffe;
    border-radius: 12px;
    padding: 8px 12px;
    font-size: 13px;
    resize: none;
    outline: none;
    line-height: 1.4;
    max-height: 80px;
    transition: border .15s;
    color: #433c50;
    background: #fafafa;
}
.chat-input-area textarea:focus { border-color: #696cff; background: #fff; }
.chat-send-btn {
    width: 38px; height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg,#696cff,#9155fd);
    border: none;
    color: #fff;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    transition: opacity .15s;
}
.chat-send-btn:disabled { opacity: .5; cursor: default; }
</style>

{{-- ── Bubble Button ───────────────────────────────────────────── --}}
<button id="syfa-chat-bubble" title="SYFA Assistant" aria-label="Buka Chatbot">
    <i class="ti ti-message-chatbot"></i>
    <span class="chat-badge" id="chatbot-badge"></span>
</button>

{{-- ── Chat Window ─────────────────────────────────────────────── --}}
<div id="syfa-chat-window" role="dialog" aria-label="SYFA Chatbot">
    {{-- Header --}}
    <div class="chat-header">
        <div class="chat-header-avatar"><i class="ti ti-robot"></i></div>
        <div class="chat-header-info">
            <h6>SYFA Assistant</h6>
            <small><span id="chat-status-dot">●</span> Online</small>
        </div>
        <div class="chat-header-actions">
            <button id="chat-reset-btn" title="Reset percakapan">
                <i class="ti ti-refresh" style="font-size:14px;"></i>
            </button>
            <button id="chat-close-btn" title="Tutup">
                <i class="ti ti-x" style="font-size:14px;"></i>
            </button>
        </div>
    </div>

    {{-- Messages --}}
    <div class="chat-messages" id="chat-messages">
        {{-- Welcome message will be injected by JS --}}
    </div>

    {{-- Quick Replies --}}
    <div class="quick-replies" id="quick-replies-container">
        <button class="quick-reply-btn" data-msg="Cek status pinjaman saya">💰 Cek Pinjaman</button>
        <button class="quick-reply-btn" data-msg="Informasi penyesuaian cicilan">🔄 Penyesuaian Cicilan</button>
        <button class="quick-reply-btn" data-msg="Informasi investasi reguler dan khusus">📈 Info Investasi</button>
        <button class="quick-reply-btn" data-msg="Berapa hari lagi jatuh tempo pinjaman saya?">📅 Cek Jatuh Tempo</button>
    </div>

    {{-- Input --}}
    <div class="chat-input-area">
        <textarea
            id="chat-input"
            placeholder="Ketik pertanyaan Anda..."
            rows="1"
            aria-label="Pesan chatbot"
        ></textarea>
        <button class="chat-send-btn" id="chat-send-btn" title="Kirim" disabled>
            <i class="ti ti-send" style="font-size:16px;"></i>
        </button>
    </div>
</div>

{{-- ── Script ───────────────────────────────────────────────────── --}}
<script>
(function () {
    'use strict';

    const ROUTE_MSG   = '{{ route("chatbot.message") }}';
    const ROUTE_CLEAR = '{{ route("chatbot.clear") }}';
    const CSRF        = '{{ csrf_token() }}';
    const USER_INIT   = '{{ substr(Auth::user()->name, 0, 2) }}';

    const bubble       = document.getElementById('syfa-chat-bubble');
    const chatWindow   = document.getElementById('syfa-chat-window');
    const messagesEl   = document.getElementById('chat-messages');
    const inputEl      = document.getElementById('chat-input');
    const sendBtn      = document.getElementById('chat-send-btn');
    const closeBtn     = document.getElementById('chat-close-btn');
    const resetBtn     = document.getElementById('chat-reset-btn');
    const quickReplies = document.getElementById('quick-replies-container');
    const badge        = document.getElementById('chatbot-badge');

    let isOpen       = false;
    let isBusy       = false;
    let hasNewMsg    = false;

    // ── Helpers ──────────────────────────────────────────────────
    function formatText(text) {
        // Bold (**text**)
        text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        // Emoji bullet points - preserve them
        text = text.replace(/^([•\-─])\s/gm, '<span style="color:#696cff">$1</span> ');
        return text;
    }

    function appendMsg(role, text, animate = true) {
        const row = document.createElement('div');
        row.className = 'msg-row ' + role;

        const avatar = document.createElement('div');
        avatar.className = 'msg-avatar ' + (role === 'bot' ? 'bot-av' : 'user-av');
        avatar.innerHTML = role === 'bot'
            ? '<i class="ti ti-robot" style="font-size:14px;"></i>'
            : '<span>' + USER_INIT.toUpperCase() + '</span>';

        const bubble = document.createElement('div');
        bubble.className = 'msg-bubble ' + role;
        bubble.innerHTML = formatText(text);

        if (animate && role === 'bot') {
            bubble.style.opacity = '0';
            bubble.style.transform = 'translateY(6px)';
            bubble.style.transition = 'opacity .3s, transform .3s';
            setTimeout(() => {
                bubble.style.opacity = '1';
                bubble.style.transform = 'translateY(0)';
            }, 10);
        }

        row.appendChild(avatar);
        row.appendChild(bubble);
        messagesEl.appendChild(row);
        scrollBottom();
        return bubble;
    }

    function showTyping() {
        const row = document.createElement('div');
        row.className = 'msg-row bot';
        row.id = 'typing-row';
        const avatar = document.createElement('div');
        avatar.className = 'msg-avatar bot-av';
        avatar.innerHTML = '<i class="ti ti-robot" style="font-size:14px;"></i>';
        const indicator = document.createElement('div');
        indicator.className = 'typing-indicator';
        indicator.innerHTML = '<span></span><span></span><span></span>';
        row.appendChild(avatar);
        row.appendChild(indicator);
        messagesEl.appendChild(row);
        scrollBottom();
    }

    function hideTyping() {
        const row = document.getElementById('typing-row');
        if (row) row.remove();
    }

    function scrollBottom() {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function setQuickReplies(replies) {
        quickReplies.innerHTML = '';
        if (!replies || replies.length === 0) return;
        replies.forEach(function (label) {
            const btn = document.createElement('button');
            btn.className = 'quick-reply-btn';
            btn.textContent = label;
            btn.dataset.msg = label.replace(/^[^\w\s]+\s*/, ''); // strip emoji prefix for message
            btn.addEventListener('click', function () {
                sendMessage(this.textContent);
            });
            quickReplies.appendChild(btn);
        });
    }

    function showWelcome() {
        const name = '{{ Auth::user()->name }}';
        const company = '{{ Auth::user()->debitur?->nama ?? Auth::user()->name }}';
        const now = new Date();
        const hour = now.getHours();
        const greeting = hour < 12 ? 'Selamat pagi' : hour < 15 ? 'Selamat siang' : hour < 18 ? 'Selamat sore' : 'Selamat malam';

        appendMsg('bot',
            greeting + ', ' + name + '! 👋\n\n' +
            'Saya **SYFA Assistant**, siap membantu kebutuhan keuangan **' + company + '**.\n\n' +
            'Saya dapat membantu:\n' +
            '• Cek status & jatuh tempo pinjaman\n' +
            '• Simulasi penyesuaian cicilan (Flat/Anuitas)\n' +
            '• Informasi investasi Reguler & Khusus\n' +
            '• Panduan dokumen & proses pengajuan\n' +
            '• Kalkulasi timeline pencairan\n\n' +
            'Pilih topik di bawah atau ketik pertanyaan Anda:'
        , false);
    }

    // ── Send Message ─────────────────────────────────────────────
    async function sendMessage(text) {
        text = text ? text.trim() : inputEl.value.trim();
        if (!text || isBusy) return;

        appendMsg('user', text);
        inputEl.value = '';
        sendBtn.disabled = true;
        isBusy = true;
        quickReplies.innerHTML = '';
        showTyping();

        try {
            const resp = await fetch(ROUTE_MSG, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ message: text }),
            });

            const data = await resp.json();
            hideTyping();

            if (data.success) {
                appendMsg('bot', data.message);
                setQuickReplies(data.quick_replies || []);
            } else {
                appendMsg('bot', '⚠️ Maaf, terjadi kesalahan. Silakan coba lagi.');
            }
        } catch (e) {
            hideTyping();
            appendMsg('bot', '⚠️ Tidak dapat terhubung. Periksa koneksi internet Anda.');
        }

        isBusy = false;
    }

    // ── Toggle Window ─────────────────────────────────────────────
    bubble.addEventListener('click', function () {
        isOpen = !isOpen;
        chatWindow.style.display = isOpen ? 'flex' : 'none';

        if (isOpen) {
            badge.style.display = 'none';
            hasNewMsg = false;
            // Show welcome if empty
            if (messagesEl.children.length === 0) {
                showWelcome();
            }
            setTimeout(() => inputEl.focus(), 100);
        }
    });

    closeBtn.addEventListener('click', function () {
        isOpen = false;
        chatWindow.style.display = 'none';
    });

    resetBtn.addEventListener('click', async function () {
        if (!confirm('Reset percakapan?')) return;
        await fetch(ROUTE_CLEAR, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        messagesEl.innerHTML = '';
        quickReplies.innerHTML = '';
        showWelcome();
        setQuickReplies([
            '💰 Cek Pinjaman Saya',
            '🔄 Penyesuaian Cicilan',
            '📈 Info Investasi',
            '📅 Cek Jatuh Tempo',
        ]);
    });

    // ── Input handlers ───────────────────────────────────────────
    inputEl.addEventListener('input', function () {
        sendBtn.disabled = this.value.trim() === '' || isBusy;
        // Auto-resize
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 80) + 'px';
    });

    inputEl.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (!sendBtn.disabled) sendMessage();
        }
    });

    sendBtn.addEventListener('click', function () {
        sendMessage();
    });

    // ── Quick reply initial setup ─────────────────────────────────
    document.querySelectorAll('#quick-replies-container .quick-reply-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            sendMessage(this.dataset.msg || this.textContent);
        });
    });
})();
</script>
@endauth
