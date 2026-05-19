{{-- =====================================================================
     SYFA Chatbot Widget — v3 · Clean, minimal, and professional
     Included in resources/views/layouts/app.blade.php
     ===================================================================== --}}

@auth
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/marked@9/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify@3/dist/purify.min.js"></script>

<style>
:root {
    --chat-accent: #0f9d9d;
    --chat-accent-strong: #0b7f7a;
    --chat-bg: #f3fbfb;
    --chat-surface: #ffffff;
    --chat-border: #d7ecec;
    --chat-ink: #0f172a;
    --chat-muted: #64748b;
    --chat-danger: #ef4444;
    --chat-radius: 18px;
    --chat-shadow: 0 24px 60px rgba(10, 64, 64, 0.18);
    --chat-font: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
}

#syfa-chat-bubble {
    position: fixed;
    right: 24px;
    bottom: calc(24px + env(safe-area-inset-bottom));
    width: 54px;
    height: 54px;
    border-radius: 16px;
    border: none;
    background: linear-gradient(135deg, #12a7a1, #0b7f7a);
    box-shadow: 0 10px 28px rgba(18, 167, 161, 0.35);
    color: #fff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

#syfa-chat-bubble:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 36px rgba(18, 167, 161, 0.45);
}

#syfa-chat-bubble i { font-size: 22px; }

#syfa-chat-bubble .chat-badge {
    position: absolute;
    top: -3px;
    right: -3px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--chat-danger);
    border: 2px solid #fff;
    display: none;
}

#syfa-chat-window {
    position: fixed;
    right: 24px;
    bottom: 92px;
    width: 380px;
    height: min(620px, 78vh);
    background: var(--chat-surface);
    border-radius: var(--chat-radius);
    box-shadow: var(--chat-shadow);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    opacity: 0;
    transform: translateY(12px) scale(0.98);
    pointer-events: none;
    visibility: hidden;
    transition: opacity 0.25s ease, transform 0.25s ease, visibility 0.25s ease;
    font-family: var(--chat-font);
    z-index: 9998;
}

#syfa-chat-window::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 20% 0%, rgba(18, 167, 161, 0.08), transparent 45%),
        radial-gradient(circle at 90% 20%, rgba(18, 167, 161, 0.06), transparent 40%);
    pointer-events: none;
}

#syfa-chat-window.is-open {
    opacity: 1;
    transform: translateY(0) scale(1);
    pointer-events: auto;
    visibility: visible;
}

.chat-header,
.chat-messages,
.chat-quick,
.chat-input-area,
.chat-footer {
    position: relative;
}

.chat-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    background: linear-gradient(135deg, #12a7a1, #0b7f7a);
    color: #fff;
}

.chat-header-avatar {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.18);
    border: 1px solid rgba(255, 255, 255, 0.28);
    display: flex;
    align-items: center;
    justify-content: center;
}

.chat-header-info h6 {
    margin: 0;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.2px;
}

.chat-header-info small {
    font-size: 11px;
    color: rgba(255, 255, 255, 0.85);
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #7dffd4;
    box-shadow: 0 0 6px #7dffd4;
    display: inline-block;
    animation: pulseDot 2s infinite;
}

@keyframes pulseDot {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
}

.chat-header-actions {
    margin-left: auto;
    display: flex;
    gap: 6px;
}

.chat-header-actions button {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    border: none;
    background: rgba(255, 255, 255, 0.18);
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.15s ease;
}

.chat-header-actions button:hover {
    background: rgba(255, 255, 255, 0.28);
}

.chat-messages {
    flex: 1;
    padding: 16px;
    background: var(--chat-bg);
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 12px;
    scroll-behavior: smooth;
}

.chat-messages::-webkit-scrollbar { width: 4px; }
.chat-messages::-webkit-scrollbar-thumb { background: var(--chat-border); border-radius: 4px; }

.msg-row {
    display: flex;
    align-items: flex-end;
    gap: 8px;
}

.msg-row.user { justify-content: flex-end; }

.msg-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    flex-shrink: 0;
}

.msg-avatar.bot-av {
    background: #12a7a1;
    color: #fff;
}

.msg-avatar.user-av {
    background: #e6f6f6;
    color: var(--chat-accent);
    border: 1px solid var(--chat-border);
}

.msg-bubble {
    max-width: 78%;
    padding: 11px 14px;
    border-radius: 14px;
    font-size: 13px;
    line-height: 1.6;
    word-break: break-word;
}

.msg-bubble.bot {
    background: #fff;
    color: var(--chat-ink);
    border: 1px solid var(--chat-border);
    border-bottom-left-radius: 6px;
    box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
}

.msg-bubble.user {
    background: linear-gradient(135deg, #12a7a1, #0b7f7a);
    color: #fff;
    border-bottom-right-radius: 6px;
    box-shadow: 0 6px 18px rgba(18, 167, 161, 0.26);
}

.msg-bubble.bot.animate-in {
    animation: msgFadeIn 0.3s ease forwards;
}

@keyframes msgFadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

.msg-bubble.bot p { margin: 0 0 6px; }
.msg-bubble.bot p:last-child { margin-bottom: 0; }
.msg-bubble.bot strong { color: var(--chat-accent-strong); }
.msg-bubble.bot ul, .msg-bubble.bot ol { margin: 4px 0 6px 16px; padding: 0; }
.msg-bubble.bot li { margin-bottom: 3px; }
.msg-bubble.bot h3, .msg-bubble.bot h4 { margin: 8px 0 4px; color: var(--chat-accent-strong); font-size: 13px; }
.msg-bubble.bot hr { border: none; border-top: 1px solid var(--chat-border); margin: 8px 0; }
.msg-bubble.bot code { background: #e9f7f7; color: var(--chat-accent-strong); padding: 1px 5px; border-radius: 4px; font-size: 12px; }

.msg-bubble.bot table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
    margin: 8px 0;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 6px 14px rgba(15, 23, 42, 0.06);
}

.msg-bubble.bot thead tr { background: #0f9d9d; color: #fff; }
.msg-bubble.bot th { padding: 7px 10px; text-align: left; font-weight: 600; font-size: 11.5px; white-space: nowrap; }
.msg-bubble.bot td { padding: 6px 10px; border-bottom: 1px solid var(--chat-border); }
.msg-bubble.bot tbody tr:nth-child(even) { background: #f2fbfb; }
.msg-bubble.bot tbody tr:last-child td { border-bottom: none; font-weight: 700; background: #e6f7f7; }

.table-scroll-wrap {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin: 6px 0;
    border-radius: 10px;
}

.table-scroll-wrap::-webkit-scrollbar { height: 4px; }
.table-scroll-wrap::-webkit-scrollbar-thumb { background: var(--chat-border); border-radius: 4px; }
.msg-bubble.bot .table-scroll-wrap table { width: max-content; min-width: 100%; margin: 0; box-shadow: none; }

.typing-indicator {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 11px 14px;
    background: #fff;
    border-radius: 14px;
    border: 1px solid var(--chat-border);
    border-bottom-left-radius: 6px;
    width: fit-content;
}

.typing-indicator span {
    width: 6px;
    height: 6px;
    background: var(--chat-accent);
    border-radius: 50%;
    animation: typingBounce 1.2s infinite;
}

.typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
.typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typingBounce {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.35; }
    30% { transform: translateY(-6px); opacity: 1; }
}

.msg-bubble.bot.streaming::after {
    content: '\25AE';
    animation: blink 0.7s step-end infinite;
    color: var(--chat-accent);
    font-size: 14px;
    margin-left: 2px;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0; }
}

.chat-quick {
    display: flex;
    gap: 8px;
    padding: 10px 14px;
    border-top: 1px solid var(--chat-border);
    background: #fff;
    overflow-x: auto;
    overflow-y: auto;
    max-height: 64px;
    scrollbar-width: none;
}

.chat-quick::-webkit-scrollbar { display: none; }

.chat-quick::-webkit-scrollbar {
    height: 4px;
    width: 4px;
}

.chat-quick::-webkit-scrollbar-thumb {
    background: rgba(15, 157, 157, 0.35);
    border-radius: 999px;
}

.chat-quick { scrollbar-width: thin; scrollbar-color: rgba(15, 157, 157, 0.35) transparent; }

.quick-reply-btn {
    background: #f1fbfb;
    border: 1px solid #bfe4e4;
    color: var(--chat-accent-strong);
    border-radius: 999px;
    padding: 6px 14px;
    font-size: 11.5px;
    font-weight: 600;
    white-space: nowrap;
    cursor: pointer;
    transition: all 0.15s ease;
    flex-shrink: 0;
}

.quick-reply-btn:hover {
    background: var(--chat-accent);
    color: #fff;
    border-color: var(--chat-accent);
    transform: translateY(-1px);
}

.chat-input-area {
    display: flex;
    align-items: flex-end;
    gap: 8px;
    padding: 12px 14px 14px;
    background: rgba(255, 255, 255, 0.95);
    border-top: 1px solid var(--chat-border);
}

.chat-input-area textarea {
    flex: 1;
    border: 1px solid var(--chat-border);
    border-radius: 12px;
    padding: 9px 12px;
    font-size: 13px;
    line-height: 1.5;
    resize: none;
    background: #f8fdfd;
    color: var(--chat-ink);
    outline: none;
    max-height: 90px;
    font-family: var(--chat-font);
    transition: border 0.15s ease, box-shadow 0.15s ease;
}

.chat-input-area textarea:focus {
    border-color: var(--chat-accent);
    box-shadow: 0 0 0 3px rgba(18, 167, 161, 0.12);
}

.chat-send-btn {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    border: none;
    background: var(--chat-accent);
    color: #fff;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    box-shadow: 0 6px 14px rgba(18, 167, 161, 0.3);
}

.chat-send-btn:disabled {
    opacity: 0.45;
    cursor: default;
    box-shadow: none;
}

.chat-send-btn:not(:disabled):hover {
    background: var(--chat-accent-strong);
    transform: translateY(-1px);
}

.chat-footer {
    text-align: center;
    font-size: 10px;
    color: var(--chat-muted);
    padding: 4px 0 6px;
    background: #f8fdfd;
    border-top: 1px solid var(--chat-border);
}

@media (max-width: 640px) {
    #syfa-chat-bubble {
        right: 16px;
        bottom: calc(16px + env(safe-area-inset-bottom));
        width: 50px;
        height: 50px;
        border-radius: 14px;
    }

    #syfa-chat-window {
        left: 16px;
        right: 16px;
        bottom: 82px;
        width: auto;
        height: min(70vh, 520px);
    }
}

@media (prefers-reduced-motion: reduce) {
    #syfa-chat-window,
    #syfa-chat-bubble,
    .msg-bubble.bot.animate-in,
    .typing-indicator span {
        transition: none;
        animation: none;
    }
}

#syfa-chat-bubble { cursor: grab; }
#syfa-chat-bubble.is-dragging { cursor: grabbing; }
</style>

<button id="syfa-chat-bubble" title="SYFA Financial Assistant" aria-label="Buka Chatbot" aria-expanded="false">
    <i class="ti ti-message-chatbot"></i>
    <span class="chat-badge" id="chatbot-badge" aria-hidden="true"></span>
</button>

<div id="syfa-chat-window" role="dialog" aria-label="SYFA Financial Assistant" aria-hidden="true">
    <div class="chat-header">
        <div class="chat-header-avatar"><i class="ti ti-robot"></i></div>
        <div class="chat-header-info">
            <h6>SYFA Financial Assistant</h6>
            <small><span class="status-dot"></span> Aktif — Konsultan Keuangan Digital</small>
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

    <div class="chat-messages" id="chat-messages"></div>

    <div class="chat-quick" id="quick-replies-container">
        <button class="quick-reply-btn" data-msg="Simulasi cicilan pinjaman saya">🧮 Simulasi Cicilan</button>
        <button class="quick-reply-btn" data-msg="Cek status dan jatuh tempo pinjaman saya">📅 Cek Pinjaman</button>
        <button class="quick-reply-btn" data-msg="Cara mengajukan penyesuaian cicilan">🔄 Ajukan Penyesuaian</button>
        <button class="quick-reply-btn" data-msg="Informasi investasi reguler dan khusus">📈 Info Investasi</button>
    </div>

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

    <div class="chat-footer">Powered by SYFA</div>
</div>

<script>
(function () {
    'use strict';

    if (typeof marked !== 'undefined') {
        marked.setOptions({ breaks: true, gfm: true });
    }

    const ROUTE_STREAM = '{{ route("chatbot.stream") }}';
    const ROUTE_CLEAR  = '{{ route("chatbot.clear") }}';
    const CSRF         = '{{ csrf_token() }}';
    const USER_INIT    = '{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}';

    const chatBubble = document.getElementById('syfa-chat-bubble');
    const chatWindow = document.getElementById('syfa-chat-window');
    const messagesEl = document.getElementById('chat-messages');
    const inputEl = document.getElementById('chat-input');
    const sendBtn = document.getElementById('chat-send-btn');
    const closeBtn = document.getElementById('chat-close-btn');
    const resetBtn = document.getElementById('chat-reset-btn');
    const quickReplies = document.getElementById('quick-replies-container');
    const badge = document.getElementById('chatbot-badge');

    const state = {
        isOpen: false,
        isBusy: false,
        dragActive: false,
        dragMoved: false,
        dragStartX: 0,
        dragStartY: 0,
        dragOrigLeft: 0,
        dragOrigTop: 0,
        bubbleLeft: 0,
        bubbleTop: 0,
    };

    const STORAGE_KEY = 'syfa_chat_bubble_pos';
    const GAP = 12;

    function renderMarkdown(text) {
        if (typeof marked === 'undefined') {
            return text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
        }
        let html = marked.parse(text);
        html = html.replace(/<table/g, '<div class="table-scroll-wrap"><table');
        html = html.replace(/<\/table>/g, '</table></div>');
        return typeof DOMPurify !== 'undefined' ? DOMPurify.sanitize(html) : html;
    }

    function scrollBottom(behavior = 'smooth') {
        requestAnimationFrame(() => {
            messagesEl.scrollTo({ top: messagesEl.scrollHeight, behavior });
        });
    }

    function appendMsg(role, text, animate = true) {
        const row = document.createElement('div');
        row.className = 'msg-row ' + role;

        if (role === 'bot') {
            const avatar = document.createElement('div');
            avatar.className = 'msg-avatar bot-av';
            avatar.innerHTML = '<i class="ti ti-robot" style="font-size:13px;"></i>';
            row.appendChild(avatar);
        }

        const bubble = document.createElement('div');
        bubble.className = 'msg-bubble ' + role + (animate && role === 'bot' ? ' animate-in' : '');

        if (role === 'bot') {
            bubble.innerHTML = renderMarkdown(text);
        } else {
            bubble.textContent = text;
        }

        row.appendChild(bubble);

        if (role === 'user') {
            const avatar = document.createElement('div');
            avatar.className = 'msg-avatar user-av';
            avatar.innerHTML = '<span>' + USER_INIT + '</span>';
            row.appendChild(avatar);
        }

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
        avatar.innerHTML = '<i class="ti ti-robot" style="font-size:13px;"></i>';

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

    function appendStreamingBubble() {
        const row = document.createElement('div');
        row.className = 'msg-row bot';

        const avatar = document.createElement('div');
        avatar.className = 'msg-avatar bot-av';
        avatar.innerHTML = '<i class="ti ti-robot" style="font-size:13px;"></i>';

        const bubble = document.createElement('div');
        bubble.className = 'msg-bubble bot streaming';

        row.appendChild(avatar);
        row.appendChild(bubble);
        messagesEl.appendChild(row);
        scrollBottom();
        return bubble;
    }

    function updateStreamingBubble(bubble, text) {
        bubble.textContent = text;
        scrollBottom('auto');
    }

    function finalizeStreamingBubble(bubble, text) {
        bubble.classList.remove('streaming');
        bubble.innerHTML = renderMarkdown(text || 'Maaf, saya tidak bisa menjawab saat ini.');
        scrollBottom();
    }

    function setQuickReplies(replies) {
        quickReplies.innerHTML = '';
        if (!replies || replies.length === 0) return;
        replies.forEach(function (label) {
            const btn = document.createElement('button');
            btn.className = 'quick-reply-btn';
            btn.textContent = label;
            btn.addEventListener('click', function () {
                sendMessage(label);
            });
            quickReplies.appendChild(btn);
        });
        quickReplies.scrollLeft = 0;
    }

    function showWelcome() {
        const company = '{{ Auth::user()->debitur?->nama ?? Auth::user()->name }}';
        const hour = new Date().getHours();
        const greeting = hour < 10 ? 'Selamat pagi' : hour < 15 ? 'Selamat siang' : hour < 18 ? 'Selamat sore' : 'Selamat malam';

        const welcomeText =
            greeting + ', Finance Officer **' + company + '**.\n\n' +
            'Saya **SYFA Financial Assistant**, siap memberikan analisis dan konsultasi keuangan untuk organisasi Anda.\n\n' +
            '**Layanan tersedia:**\n' +
            '- 📅 Status & jatuh tempo pinjaman aktif\n' +
            '- 🧮 Simulasi penyesuaian cicilan (Flat / Anuitas)\n' +
            '- 📈 Informasi portofolio investasi\n' +
            '- 📋 Panduan dokumen & prosedur pengajuan\n\n' +
            'Silakan pilih topik di bawah atau sampaikan pertanyaan Anda.';

        appendMsg('bot', welcomeText, false);
    }

    function setOpen(open) {
        state.isOpen = open;
        chatWindow.classList.toggle('is-open', open);
        chatBubble.setAttribute('aria-expanded', open ? 'true' : 'false');
        chatWindow.setAttribute('aria-hidden', open ? 'false' : 'true');

        if (open) {
            positionChatWindow();
            badge.style.display = 'none';
            if (messagesEl.children.length === 0) {
                showWelcome();
            }
            setTimeout(() => inputEl.focus(), 150);
        }
    }

    function clamp(value, min, max) {
        return Math.max(min, Math.min(value, max));
    }

    function positionChatWindow() {
        if (!state.isOpen) return;
        const vw = window.innerWidth;
        const vh = window.innerHeight;
        const winW = chatWindow.offsetWidth || 380;
        const winH = chatWindow.offsetHeight || 520;

        let left = state.bubbleLeft + (chatBubble.offsetWidth / 2) - (winW / 2);
        let top = state.bubbleTop - winH - GAP;

        if (top < 8) {
            top = state.bubbleTop + chatBubble.offsetHeight + GAP;
        }

        left = clamp(left, 8, vw - winW - 8);
        top = clamp(top, 8, vh - winH - 8);

        chatWindow.style.left = left + 'px';
        chatWindow.style.top = top + 'px';
        chatWindow.style.right = 'auto';
        chatWindow.style.bottom = 'auto';
    }

    function restoreBubblePosition() {
        try {
            const saved = JSON.parse(sessionStorage.getItem(STORAGE_KEY) || 'null');
            if (!saved) return;
            const vw = window.innerWidth;
            const vh = window.innerHeight;
            const bw = chatBubble.offsetWidth || 54;
            const bh = chatBubble.offsetHeight || 54;
            const left = Math.max(8, Math.min(saved.left, vw - bw - 8));
            const top = Math.max(8, Math.min(saved.top, vh - bh - 8));
            chatBubble.style.right = 'auto';
            chatBubble.style.bottom = 'auto';
            chatBubble.style.left = left + 'px';
            chatBubble.style.top = top + 'px';
            state.bubbleLeft = left;
            state.bubbleTop = top;
        } catch (e) { /* ignore */ }
    }

    function saveBubblePosition() {
        try {
            const rect = chatBubble.getBoundingClientRect();
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify({
                left: Math.round(rect.left),
                top: Math.round(rect.top),
            }));
        } catch (e) { /* ignore */ }
    }

    async function sendMessage(text) {
        text = (text !== undefined ? text : inputEl.value).trim();
        if (!text || state.isBusy) return;

        appendMsg('user', text);
        inputEl.value = '';
        inputEl.style.height = 'auto';
        sendBtn.disabled = true;
        state.isBusy = true;
        quickReplies.innerHTML = '';
        showTyping();

        try {
            const resp = await fetch(ROUTE_STREAM, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'text/event-stream',
                },
                body: JSON.stringify({ message: text }),
            });

            hideTyping();

            if (!resp.ok || !resp.body) {
                appendMsg('bot', '⚠️ Maaf, terjadi kesalahan. Silakan coba beberapa saat lagi.');
                state.isBusy = false;
                sendBtn.disabled = inputEl.value.trim() === '';
                return;
            }

            const bubble = appendStreamingBubble();
            let fullText = '';
            const reader = resp.body.getReader();
            const decoder = new TextDecoder();
            let buf = '';

            outer: while (true) {
                const { done, value } = await reader.read();
                if (done) break;

                buf += decoder.decode(value, { stream: true });
                const lines = buf.split('\n');
                buf = lines.pop();

                for (const line of lines) {
                    if (!line.startsWith('data: ')) continue;
                    const raw = line.slice(6).trim();
                    if (!raw) continue;
                    let evt;
                    try { evt = JSON.parse(raw); } catch { continue; }

                    if (evt.token) {
                        fullText += evt.token;
                        updateStreamingBubble(bubble, fullText);
                    }
                    if (evt.done) {
                        finalizeStreamingBubble(bubble, fullText);
                        setQuickReplies(evt.quick_replies || []);
                        break outer;
                    }
                    if (evt.error) {
                        finalizeStreamingBubble(bubble, fullText || '⚠️ Maaf, terjadi gangguan. Silakan coba lagi.');
                        break outer;
                    }
                }
            }

            if (bubble.classList.contains('streaming')) {
                finalizeStreamingBubble(bubble, fullText);
            }
        } catch (e) {
            hideTyping();
            appendMsg('bot', '⚠️ Tidak dapat terhubung ke server. Periksa koneksi internet Anda.');
        }

        state.isBusy = false;
        sendBtn.disabled = inputEl.value.trim() === '';
    }

    closeBtn.addEventListener('click', function () { setOpen(false); });

    chatBubble.addEventListener('pointerdown', function (e) {
        e.preventDefault();
        state.dragActive = true;
        state.dragMoved = false;
        const rect = chatBubble.getBoundingClientRect();
        chatBubble.style.right = 'auto';
        chatBubble.style.bottom = 'auto';
        chatBubble.style.left = rect.left + 'px';
        chatBubble.style.top = rect.top + 'px';
        state.dragStartX = e.clientX;
        state.dragStartY = e.clientY;
        state.dragOrigLeft = rect.left;
        state.dragOrigTop = rect.top;
        state.bubbleLeft = rect.left;
        state.bubbleTop = rect.top;
        chatBubble.classList.add('is-dragging');
    });

    document.addEventListener('pointermove', function (e) {
        if (!state.dragActive) return;
        const dx = e.clientX - state.dragStartX;
        const dy = e.clientY - state.dragStartY;
        if (Math.abs(dx) > 3 || Math.abs(dy) > 3) state.dragMoved = true;
        const vw = window.innerWidth;
        const vh = window.innerHeight;
        const bw = chatBubble.offsetWidth;
        const bh = chatBubble.offsetHeight;
        const nextLeft = clamp(state.dragOrigLeft + dx, 8, vw - bw - 8);
        const nextTop = clamp(state.dragOrigTop + dy, 8, vh - bh - 8);
        chatBubble.style.left = nextLeft + 'px';
        chatBubble.style.top = nextTop + 'px';
        state.bubbleLeft = nextLeft;
        state.bubbleTop = nextTop;
        positionChatWindow();
    });

    document.addEventListener('pointerup', function () {
        if (!state.dragActive) return;
        state.dragActive = false;
        chatBubble.classList.remove('is-dragging');
        if (state.dragMoved) {
            saveBubblePosition();
            return;
        }
        setOpen(!state.isOpen);
    });

    resetBtn.addEventListener('click', async function () {
        if (!confirm('Reset percakapan? Riwayat chat akan dihapus.')) return;
        await fetch(ROUTE_CLEAR, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        messagesEl.innerHTML = '';
        showWelcome();
        setQuickReplies(['🧮 Simulasi Cicilan', '📅 Cek Pinjaman', '🔄 Ajukan Penyesuaian', '📈 Info Investasi']);
    });

    inputEl.addEventListener('input', function () {
        sendBtn.disabled = this.value.trim() === '' || state.isBusy;
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 90) + 'px';
    });

    inputEl.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (!sendBtn.disabled) sendMessage();
        }
    });

    sendBtn.addEventListener('click', function () { sendMessage(); });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && state.isOpen) {
            setOpen(false);
        }
    });

    window.addEventListener('resize', function () {
        const rect = chatBubble.getBoundingClientRect();
        state.bubbleLeft = clamp(rect.left, 8, window.innerWidth - rect.width - 8);
        state.bubbleTop = clamp(rect.top, 8, window.innerHeight - rect.height - 8);
        chatBubble.style.left = state.bubbleLeft + 'px';
        chatBubble.style.top = state.bubbleTop + 'px';
        positionChatWindow();
    });

    document.querySelectorAll('#quick-replies-container .quick-reply-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            sendMessage(this.dataset.msg || this.textContent);
        });
    });

    restoreBubblePosition();
    if (!state.bubbleLeft && !state.bubbleTop) {
        const rect = chatBubble.getBoundingClientRect();
        state.bubbleLeft = rect.left;
        state.bubbleTop = rect.top;
    }
})();
</script>
@endauth
