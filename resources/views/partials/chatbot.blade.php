{{-- =====================================================================
     SYFA Chatbot Widget — v2 · Deep Navy + Soft Lavender + Glassmorphism
     Included in resources/views/layouts/app.blade.php
     ===================================================================== --}}

@auth
{{-- ── marked.js + DOMPurify for safe Markdown rendering ──────────── --}}
<script src="https://cdn.jsdelivr.net/npm/marked@9/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify@3/dist/purify.min.js"></script>

{{-- ── Styles ────────────────────────────────────────────────────── --}}
<style>
:root {
    --c-navy:       #0d8f8f;
    --c-navy-light: #0b7a7a;
    --c-lav:        #13ABAB;
    --c-lav-light:  #5dcece;
    --c-lav-bg:     #e6f7f7;
    --c-surface:    #ffffff;
    --c-bg:         #f4fafa;
    --c-border:     #d0eded;
    --c-text:       #1e2a45;
    --c-muted:      #6b7a99;
    --c-danger:     #ef4444;
    --c-warning:    #f59e0b;
    --c-success:    #10b981;
    --radius-lg:    18px;
    --radius-md:    12px;
    --radius-sm:    8px;
    --shadow-lg:    0 20px 60px rgba(19,171,171,.16);
    --shadow-md:    0 4px 16px rgba(19,171,171,.11);
    --shadow-sm:    0 2px 8px  rgba(19,171,171,.07);
}

/* ── Bubble Toggle ── */
#syfa-chat-bubble {
    position: fixed;
    bottom: 28px;
    right: 28px;
    z-index: 9999;
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background: linear-gradient(135deg, #13ABAB 0%, #0d8f8f 100%);
    box-shadow: 0 6px 24px rgba(19,171,171,.45);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    transition: transform .2s, box-shadow .2s;
}
#syfa-chat-bubble:hover {
    transform: scale(1.08);
    box-shadow: 0 10px 32px rgba(19,171,171,.6);
}
#syfa-chat-bubble i { color: #fff; font-size: 22px; }
#syfa-chat-bubble .chat-badge {
    position: absolute;
    top: 2px; right: 2px;
    width: 14px; height: 14px;
    border-radius: 50%;
    background: var(--c-danger);
    border: 2px solid #fff;
    display: none;
}

/* ── Chat Window ── */
#syfa-chat-window {
    position: fixed;
    bottom: 102px;
    right: 28px;
    z-index: 9998;
    width: 390px;
    max-width: calc(100vw - 40px);
    height: 580px;
    max-height: calc(100vh - 140px);
    background: var(--c-surface);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    display: none;
    flex-direction: column;
    overflow: hidden;
    position: relative;
    animation: chatSlideUp .28s cubic-bezier(.22,.68,0,1.2);
}
@keyframes chatSlideUp {
    from { opacity:0; transform: translateY(24px) scale(.96); }
    to   { opacity:1; transform: translateY(0)    scale(1);   }
}

/* ── Header ── */
.chat-header {
    background: linear-gradient(135deg, #13ABAB 0%, #0d8f8f 100%);
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
    border-bottom: 1px solid rgba(255,255,255,.12);
    border-radius: 18px 18px 0 0;
}
.chat-header-avatar {
    width: 36px; height: 36px;
    border-radius: 10px;
    background: rgba(255,255,255,.2);
    border: 1.5px solid rgba(255,255,255,.3);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.chat-header-avatar i { color: #fff; font-size: 17px; }
.chat-header-info h6 { color: #fff; margin: 0; font-size: 13px; font-weight: 700; letter-spacing:.2px; line-height:1.2; }
.chat-header-info small { color: rgba(255,255,255,.8); font-size: 10.5px; display:flex; align-items:center; gap:5px; margin-top:1px; }
.status-dot { width:7px; height:7px; border-radius:50%; background: #7dffd4; box-shadow:0 0 6px #7dffd4; display:inline-block; animation: pulseDot 2s infinite; }
@keyframes pulseDot { 0%,100%{opacity:1;} 50%{opacity:.4;} }
.chat-header-actions { margin-left: auto; display: flex; gap: 6px; }
.chat-header-actions button {
    background: rgba(255,255,255,.15);
    border: none;
    color: rgba(255,255,255,.9);
    width: 30px; height: 30px;
    border-radius: var(--radius-sm); cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .15s;
    font-size: 13px;
}
.chat-header-actions button:hover { background: rgba(255,255,255,.28); color:#fff; }

/* ── Messages Area ── */
.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    background: var(--c-bg);
    display: flex;
    flex-direction: column;
    gap: 12px;
    scroll-behavior: smooth;
}
.chat-messages::-webkit-scrollbar { width: 4px; }
.chat-messages::-webkit-scrollbar-track { background: transparent; }
.chat-messages::-webkit-scrollbar-thumb { background: var(--c-border); border-radius: 4px; }

/* ── Message Rows ── */
.msg-row { display: flex; align-items: flex-end; gap: 8px; }
.msg-row.user  { flex-direction: row-reverse; }
.msg-avatar {
    width: 30px; height: 30px;
    border-radius: 50%;
    flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700;
}
.msg-avatar.bot-av  { background: #13ABAB; color:#fff; }
.msg-avatar.user-av { background: var(--c-lav-bg); color: var(--c-lav); border: 1.5px solid var(--c-border); }

/* ── Bubbles ── */
.msg-bubble {
    max-width: 82%;
    padding: 11px 15px;
    border-radius: var(--radius-lg);
    font-size: 13px;
    line-height: 1.6;
    word-break: break-word;
}
.msg-bubble.bot {
    background: var(--c-surface);
    border-bottom-left-radius: 4px;
    box-shadow: var(--shadow-sm);
    color: var(--c-text);
}
.msg-bubble.user {
    background: #13ABAB;
    color: #fff;
    border-bottom-right-radius: 4px;
    box-shadow: 0 3px 12px rgba(19,171,171,.3);
}
.msg-bubble.bot.animate-in {
    animation: msgFadeIn .3s ease forwards;
}
@keyframes msgFadeIn {
    from { opacity:0; transform:translateY(8px); }
    to   { opacity:1; transform:translateY(0); }
}
/* Markdown inside bot bubble */
.msg-bubble.bot p { margin: 0 0 6px; }
.msg-bubble.bot p:last-child { margin-bottom: 0; }
.msg-bubble.bot strong { color: #0d8f8f; }
.msg-bubble.bot ul, .msg-bubble.bot ol { margin: 4px 0 6px 16px; padding:0; }
.msg-bubble.bot li { margin-bottom: 3px; }
.msg-bubble.bot h3, .msg-bubble.bot h4 { margin: 8px 0 4px; color: #0d8f8f; font-size: 13px; }
.msg-bubble.bot hr { border: none; border-top: 1px solid var(--c-border); margin: 8px 0; }
.msg-bubble.bot code { background: var(--c-lav-bg); color: var(--c-lav); padding: 1px 5px; border-radius: 4px; font-size: 12px; }
/* Markdown Table */
.msg-bubble.bot table { width:100%; border-collapse:collapse; font-size:12px; margin:8px 0; border-radius:var(--radius-sm); overflow:hidden; box-shadow:var(--shadow-sm); }
.msg-bubble.bot thead tr { background: #13ABAB; color:#fff; }
.msg-bubble.bot th { padding:7px 10px; text-align:left; font-weight:600; white-space:nowrap; font-size:11.5px; }
.msg-bubble.bot td { padding:6px 10px; border-bottom:1px solid var(--c-border); }
.msg-bubble.bot tbody tr:nth-child(even) { background: #f0fdfc; }
.msg-bubble.bot tbody tr:last-child td { border-bottom:none; font-weight:700; background:#e6f7f7; }
.msg-bubble.bot tbody tr:hover { background:#f0fdfc; }

/* ── Typing Indicator ── */
.typing-indicator {
    display: flex; align-items: center; gap: 4px;
    padding: 11px 15px;
    background: var(--c-surface);
    border-radius: var(--radius-lg);
    border-bottom-left-radius: 4px;
    box-shadow: var(--shadow-sm);
    width: fit-content;
}
.typing-indicator span {
    width: 7px; height: 7px;
    background: var(--c-lav);
    border-radius: 50%;
    animation: typingBounce 1.2s infinite;
}
.typing-indicator span:nth-child(2) { animation-delay: .2s; }
.typing-indicator span:nth-child(3) { animation-delay: .4s; }
@keyframes typingBounce {
    0%,60%,100% { transform: translateY(0); opacity:.35; }
    30%          { transform: translateY(-7px); opacity:1; }
}

/* ── Quick Replies — horizontal scroll chips ── */
.quick-replies {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    gap: 6px;
    padding: 8px 14px;
    background: var(--c-bg);
    border-top: 1px solid var(--c-border);
    flex-shrink: 0;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}
.quick-replies::-webkit-scrollbar { display: none; }
.quick-reply-btn {
    background: #f0fafa;
    border: 1.5px solid #b2e5e5;
    color: #13ABAB;
    border-radius: 20px;
    padding: 5px 14px;
    font-size: 11.5px;
    cursor: pointer;
    transition: all .15s;
    white-space: nowrap;
    font-weight: 600;
    flex-shrink: 0;
    box-shadow: var(--shadow-sm);
}
.quick-reply-btn:hover {
    background: #13ABAB;
    color: #fff;
    border-color: #13ABAB;
    box-shadow: 0 3px 10px rgba(19,171,171,.3);
    transform: translateY(-1px);
}

/* ── Input Area — Glassmorphism ── */
.chat-input-area {
    display: flex;
    align-items: flex-end;
    gap: 8px;
    padding: 10px 14px;
    background: rgba(255,255,255,.85);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border-top: 1px solid var(--c-border);
    flex-shrink: 0;
}
.chat-input-area textarea {
    flex: 1;
    border: 1.5px solid var(--c-border);
    border-radius: var(--radius-md);
    padding: 9px 13px;
    font-size: 13px;
    resize: none;
    outline: none;
    line-height: 1.5;
    max-height: 80px;
    transition: border .15s, box-shadow .15s;
    color: var(--c-text);
    background: var(--c-bg);
    font-family: inherit;
}
.chat-input-area textarea:focus {
    border-color: #13ABAB;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(19,171,171,.12);
}
.chat-send-btn {
    width: 38px; height: 38px;
    border-radius: 10px;
    background: #13ABAB;
    border: none;
    color: #fff;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    transition: background .15s, transform .15s, box-shadow .15s;
    box-shadow: 0 3px 10px rgba(19,171,171,.35);
}
.chat-send-btn:not(:disabled):hover { background: #0d8f8f; transform: scale(1.05); box-shadow: 0 5px 16px rgba(19,171,171,.5); }
.chat-send-btn:disabled { opacity: .4; cursor: default; box-shadow: none; transform: none; }

/* ── Footer branding ── */
.chat-footer-brand {
    text-align: center;
    font-size: 10px;
    color: var(--c-muted);
    padding: 4px 0 6px;
    letter-spacing: .3px;
    background: var(--c-bg);
    border-top: 1px solid var(--c-border);
    flex-shrink: 0;
}

/* ── Drag ── */
#syfa-chat-bubble { cursor: grab; }
#syfa-chat-bubble.is-dragging { cursor: grabbing; transform: scale(1.1); box-shadow: 0 12px 36px rgba(19,171,171,.6); }

/* ── Resize handle ── */
#chat-resize-handle {
    position: absolute;
    bottom: 0; right: 0;
    width: 18px; height: 18px;
    cursor: se-resize;
    z-index: 10;
    display: flex; align-items: flex-end; justify-content: flex-end;
    padding: 3px;
    opacity: 0;
    transition: opacity .2s;
}
#syfa-chat-window:hover #chat-resize-handle { opacity: 1; }
#chat-resize-handle::after {
    content: '';
    display: block;
    width: 10px; height: 10px;
    border-right: 2px solid var(--c-lav);
    border-bottom: 2px solid var(--c-lav);
    border-radius: 1px;
}
#syfa-chat-window.is-dragging-or-resizing { transition: none !important; }
</style>

{{-- ── Bubble Button ───────────────────────────────────────────── --}}
<button id="syfa-chat-bubble" title="SYFA Financial Assistant" aria-label="Buka Chatbot">
    <i class="ti ti-message-chatbot"></i>
    <span class="chat-badge" id="chatbot-badge"></span>
</button>

{{-- ── Chat Window ─────────────────────────────────────────────── --}}
<div id="syfa-chat-window" role="dialog" aria-label="SYFA Financial Assistant">
    {{-- Header --}}
    <div class="chat-header" id="chat-drag-handle">
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

    {{-- Messages --}}
    <div class="chat-messages" id="chat-messages"></div>

    {{-- Quick Replies --}}
    <div class="quick-replies" id="quick-replies-container">
        <button class="quick-reply-btn" data-msg="Simulasi cicilan pinjaman saya">🧮 Simulasi Cicilan</button>
        <button class="quick-reply-btn" data-msg="Cek status dan jatuh tempo pinjaman saya">📅 Cek Pinjaman</button>
        <button class="quick-reply-btn" data-msg="Cara mengajukan penyesuaian cicilan">🔄 Ajukan Penyesuaian</button>
        <button class="quick-reply-btn" data-msg="Informasi investasi reguler dan khusus">📈 Info Investasi</button>
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
    <div class="chat-footer-brand">Powered by SYFA</div>
    <div id="chat-resize-handle" title="Ubah ukuran"></div>
</div>

{{-- ── Script ───────────────────────────────────────────────────── --}}
<script>
(function () {
    'use strict';

    // Configure marked for table + linebreak support
    if (typeof marked !== 'undefined') {
        marked.setOptions({ breaks: true, gfm: true });
    }

    const ROUTE_MSG   = '{{ route("chatbot.message") }}';
    const ROUTE_CLEAR = '{{ route("chatbot.clear") }}';
    const CSRF        = '{{ csrf_token() }}';
    const USER_INIT   = '{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}';

    const chatBubble   = document.getElementById('syfa-chat-bubble');
    const chatWindow   = document.getElementById('syfa-chat-window');
    const messagesEl   = document.getElementById('chat-messages');
    const inputEl      = document.getElementById('chat-input');
    const sendBtn      = document.getElementById('chat-send-btn');
    const closeBtn     = document.getElementById('chat-close-btn');
    const resetBtn     = document.getElementById('chat-reset-btn');
    const quickReplies = document.getElementById('quick-replies-container');
    const badge        = document.getElementById('chatbot-badge');

    let isOpen  = false;
    let isBusy  = false;

    // ── Markdown renderer ─────────────────────────────────────────
    function renderMarkdown(text) {
        if (typeof marked === 'undefined') {
            return text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
        }
        const html = marked.parse(text);
        return typeof DOMPurify !== 'undefined' ? DOMPurify.sanitize(html) : html;
    }

    // ── Append message ────────────────────────────────────────────
    function appendMsg(role, text, animate = true) {
        const row = document.createElement('div');
        row.className = 'msg-row ' + role;

        const avatar = document.createElement('div');
        avatar.className = 'msg-avatar ' + (role === 'bot' ? 'bot-av' : 'user-av');
        avatar.innerHTML = role === 'bot'
            ? '<i class="ti ti-robot" style="font-size:13px;"></i>'
            : '<span>' + USER_INIT + '</span>';

        const bubble = document.createElement('div');
        bubble.className = 'msg-bubble ' + role + (animate && role === 'bot' ? ' animate-in' : '');

        if (role === 'bot') {
            bubble.innerHTML = renderMarkdown(text);
        } else {
            bubble.textContent = text;
        }

        row.appendChild(avatar);
        row.appendChild(bubble);
        messagesEl.appendChild(row);
        scrollBottom();
        return bubble;
    }

    // ── Typing indicator ──────────────────────────────────────────
    function showTyping() {
        const row = document.createElement('div');
        row.className = 'msg-row bot';
        row.id = 'typing-row';
        const av = document.createElement('div');
        av.className = 'msg-avatar bot-av';
        av.innerHTML = '<i class="ti ti-robot" style="font-size:13px;"></i>';
        const ind = document.createElement('div');
        ind.className = 'typing-indicator';
        ind.innerHTML = '<span></span><span></span><span></span>';
        row.appendChild(av);
        row.appendChild(ind);
        messagesEl.appendChild(row);
        scrollBottom();
    }

    function hideTyping() {
        const row = document.getElementById('typing-row');
        if (row) row.remove();
    }

    // ── Smooth scroll to bottom ───────────────────────────────────
    function scrollBottom() {
        requestAnimationFrame(() => {
            messagesEl.scrollTo({ top: messagesEl.scrollHeight, behavior: 'smooth' });
        });
    }

    // ── Quick replies ─────────────────────────────────────────────
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

    // ── Welcome message ───────────────────────────────────────────
    function showWelcome() {
        const company = '{{ Auth::user()->debitur?->nama ?? Auth::user()->name }}';
        const hour    = new Date().getHours();
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

    // ── Send message ──────────────────────────────────────────────
    async function sendMessage(text) {
        text = (text !== undefined ? text : inputEl.value).trim();
        if (!text || isBusy) return;

        appendMsg('user', text);
        inputEl.value = '';
        inputEl.style.height = 'auto';
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
                appendMsg('bot', '⚠️ Maaf, terjadi kesalahan. Silakan coba beberapa saat lagi.');
            }
        } catch (e) {
            hideTyping();
            appendMsg('bot', '⚠️ Tidak dapat terhubung ke server. Periksa koneksi internet Anda.');
        }

        isBusy = false;
    }

    // ── Toggle window (handled inside bubble drag section below) ──

    closeBtn.addEventListener('click', function () {
        isOpen = false;
        chatWindow.style.display = 'none';
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

    // ── Input handlers ────────────────────────────────────────────
    inputEl.addEventListener('input', function () {
        sendBtn.disabled = this.value.trim() === '' || isBusy;
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 80) + 'px';
    });

    inputEl.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (!sendBtn.disabled) sendMessage();
        }
    });

    sendBtn.addEventListener('click', function () { sendMessage(); });

    // ── Initial quick reply wiring ────────────────────────────────
    document.querySelectorAll('#quick-replies-container .quick-reply-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            sendMessage(this.dataset.msg || this.textContent);
        });
    });

    // ────────────────────────────────────────────────────────────
    // BUBBLE DRAGGABLE + WINDOW RESIZE + POSITION MEMORY
    // ────────────────────────────────────────────────────────────
    const STORAGE_KEY  = 'syfa_bubble_pos';
    const resizeHandle = document.getElementById('chat-resize-handle');
    const GAP = 12; // px gap between bubble and chat window
    const MIN_W = 280, MIN_H = 380;

    // ── Position chat window above/beside bubble ──
    function positionWindowNearBubble() {
        const br   = chatBubble.getBoundingClientRect();
        const vw   = window.innerWidth;
        const vh   = window.innerHeight;
        const winW = chatWindow.offsetWidth  || 390;
        const winH = chatWindow.offsetHeight || 580;

        // Prefer opening to the top-left of bubble
        let left = br.left + br.width / 2 - winW / 2;
        let top  = br.top - winH - GAP;

        // If not enough space above, open below
        if (top < 8) top = br.bottom + GAP;
        // Clamp horizontally
        left = Math.max(8, Math.min(left, vw - winW - 8));
        // Clamp vertically
        top  = Math.max(8, Math.min(top,  vh - winH - 8));

        chatWindow.style.position = 'fixed';
        chatWindow.style.bottom   = 'auto';
        chatWindow.style.right    = 'auto';
        chatWindow.style.left     = left + 'px';
        chatWindow.style.top      = top  + 'px';
    }

    // ── Restore bubble position from sessionStorage ──
    (function restoreBubble() {
        try {
            const saved = JSON.parse(sessionStorage.getItem(STORAGE_KEY) || 'null');
            if (!saved) return;
            const vw = window.innerWidth, vh = window.innerHeight;
            const bw = chatBubble.offsetWidth  || 60;
            const bh = chatBubble.offsetHeight || 60;
            const bx = Math.max(8, Math.min(saved.bottom !== undefined
                ? vw - bw - saved.right
                : saved.left, vw - bw - 8));
            const by = Math.max(8, Math.min(saved.bottom !== undefined
                ? vh - bh - saved.bottom
                : saved.top, vh - bh - 8));
            chatBubble.style.bottom = 'auto';
            chatBubble.style.right  = 'auto';
            chatBubble.style.top    = by + 'px';
            chatBubble.style.left   = bx + 'px';
        } catch (e) { /* ignore */ }
    })();

    function saveBubblePos() {
        try {
            const rect = chatBubble.getBoundingClientRect();
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify({
                left: Math.round(rect.left),
                top:  Math.round(rect.top),
            }));
        } catch (e) { /* ignore */ }
    }

    // ── BUBBLE DRAG ──
    let dragActive = false, dragMoved = false;
    let dragStartX, dragStartY, dragOrigTop, dragOrigLeft;

    chatBubble.addEventListener('mousedown', function (e) {
        e.preventDefault();
        dragActive = true;
        dragMoved  = false;
        // Ensure bubble uses top/left if it was using bottom/right
        if (!chatBubble.style.top) {
            const rect = chatBubble.getBoundingClientRect();
            chatBubble.style.bottom = 'auto';
            chatBubble.style.right  = 'auto';
            chatBubble.style.top    = rect.top  + 'px';
            chatBubble.style.left   = rect.left + 'px';
        }
        dragStartX  = e.clientX;
        dragStartY  = e.clientY;
        dragOrigTop  = parseFloat(chatBubble.style.top);
        dragOrigLeft = parseFloat(chatBubble.style.left);
        chatBubble.classList.add('is-dragging');
    });

    document.addEventListener('mousemove', function (e) {
        if (!dragActive) return;
        const dx = e.clientX - dragStartX;
        const dy = e.clientY - dragStartY;
        if (Math.abs(dx) > 3 || Math.abs(dy) > 3) dragMoved = true;
        const vw = window.innerWidth, vh = window.innerHeight;
        const bw = chatBubble.offsetWidth, bh = chatBubble.offsetHeight;
        const nx = Math.max(8, Math.min(dragOrigLeft + dx, vw - bw - 8));
        const ny = Math.max(8, Math.min(dragOrigTop  + dy, vh - bh - 8));
        chatBubble.style.left = nx + 'px';
        chatBubble.style.top  = ny + 'px';
        // Move chat window alongside if open
        if (isOpen) positionWindowNearBubble();
    });

    document.addEventListener('mouseup', function () {
        if (!dragActive) return;
        dragActive = false;
        chatBubble.classList.remove('is-dragging');
        if (dragMoved) {
            saveBubblePos();
            if (isOpen) positionWindowNearBubble();
        }
    });

    // ── Touch support for mobile ──
    chatBubble.addEventListener('touchstart', function (e) {
        const t = e.touches[0];
        dragActive = true; dragMoved = false;
        if (!chatBubble.style.top) {
            const rect = chatBubble.getBoundingClientRect();
            chatBubble.style.bottom = 'auto'; chatBubble.style.right = 'auto';
            chatBubble.style.top = rect.top + 'px'; chatBubble.style.left = rect.left + 'px';
        }
        dragStartX = t.clientX; dragStartY = t.clientY;
        dragOrigTop = parseFloat(chatBubble.style.top);
        dragOrigLeft = parseFloat(chatBubble.style.left);
    }, { passive: true });

    document.addEventListener('touchmove', function (e) {
        if (!dragActive) return;
        const t = e.touches[0];
        const dx = t.clientX - dragStartX, dy = t.clientY - dragStartY;
        if (Math.abs(dx) > 5 || Math.abs(dy) > 5) dragMoved = true;
        const vw = window.innerWidth, vh = window.innerHeight;
        const bw = chatBubble.offsetWidth, bh = chatBubble.offsetHeight;
        chatBubble.style.left = Math.max(8, Math.min(dragOrigLeft + dx, vw - bw - 8)) + 'px';
        chatBubble.style.top  = Math.max(8, Math.min(dragOrigTop  + dy, vh - bh - 8)) + 'px';
        if (isOpen) positionWindowNearBubble();
    }, { passive: true });

    document.addEventListener('touchend', function () {
        if (!dragActive) return;
        dragActive = false;
        if (dragMoved) { saveBubblePos(); if (isOpen) positionWindowNearBubble(); }
    });

    // Override toggle to position window near bubble
    // (re-bind click only fires if NOT a drag)
    chatBubble.removeEventListener('click', chatBubble._clickHandler);
    chatBubble._clickHandler = function () {
        if (dragMoved) return; // was a drag, not a tap
        isOpen = !isOpen;
        if (isOpen) {
            chatWindow.style.display = 'flex';
            positionWindowNearBubble();
            badge.style.display = 'none';
            if (messagesEl.children.length === 0) showWelcome();
            setTimeout(() => inputEl.focus(), 150);
        } else {
            chatWindow.style.display = 'none';
        }
    };
    chatBubble.addEventListener('click', chatBubble._clickHandler);

    // ── RESIZE (chat window) ──
    let resizeActive = false, resStartX, resStartY, resOrigW, resOrigH;

    resizeHandle.addEventListener('mousedown', function (e) {
        e.preventDefault(); e.stopPropagation();
        resizeActive = true;
        resStartX = e.clientX; resStartY = e.clientY;
        resOrigW  = chatWindow.offsetWidth; resOrigH  = chatWindow.offsetHeight;
        chatWindow.classList.add('is-dragging-or-resizing');
    });

    document.addEventListener('mousemove', function (e) {
        if (!resizeActive) return;
        const newW = Math.max(MIN_W, resOrigW + (e.clientX - resStartX));
        const newH = Math.max(MIN_H, resOrigH + (e.clientY - resStartY));
        const rect = chatWindow.getBoundingClientRect();
        chatWindow.style.width  = Math.min(newW, window.innerWidth  - rect.left - 8) + 'px';
        chatWindow.style.height = Math.min(newH, window.innerHeight - rect.top  - 8) + 'px';
        chatWindow.style.maxWidth  = 'none';
        chatWindow.style.maxHeight = 'none';
    });

    document.addEventListener('mouseup', function () {
        if (resizeActive) {
            resizeActive = false;
            chatWindow.classList.remove('is-dragging-or-resizing');
        }
    });
})();
</script>
@endauth
