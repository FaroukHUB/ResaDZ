<style>
.pa-fab{position:fixed;bottom:24px;right:24px;width:56px;height:56px;border-radius:50%;border:none;cursor:pointer;z-index:9999;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(0,0,0,0.25);transition:all .3s;background:linear-gradient(135deg,#FF6B2C,#F59E0B)}
.pa-fab:hover{transform:scale(1.08);box-shadow:0 6px 24px rgba(255,107,44,0.4)}
.pa-fab svg{width:28px;height:28px;fill:white;stroke:white}
.pa-fab .pa-close{display:none}
.pa-fab.pa-open .pa-chat-icon{display:none}
.pa-fab.pa-open .pa-close{display:block}
.pa-window{position:fixed;bottom:90px;right:24px;width:380px;max-height:520px;background:white;border-radius:16px;box-shadow:0 8px 40px rgba(0,0,0,0.2);z-index:9998;display:none;flex-direction:column;overflow:hidden}
.pa-window.pa-visible{display:flex}
.pa-header{background:linear-gradient(135deg,#FF6B2C,#F59E0B);padding:16px 20px;color:white;display:flex;align-items:center;gap:12px}
.pa-header-name{font-weight:700;font-size:16px}
.pa-header-sub{font-size:12px;opacity:.85}
.pa-messages{flex:1;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:12px;min-height:250px;max-height:350px}
.pa-msg{max-width:85%;padding:10px 14px;border-radius:12px;font-size:14px;line-height:1.5;word-wrap:break-word}
.pa-msg-bot{background:#f3f4f6;color:#1f2937;align-self:flex-start;border-bottom-left-radius:4px}
.pa-msg-user{background:linear-gradient(135deg,#FF6B2C,#F59E0B);color:white;align-self:flex-end;border-bottom-right-radius:4px}
.pa-input-area{padding:12px 16px;border-top:1px solid #e5e7eb;display:flex;gap:8px}
.pa-input{flex:1;border:1px solid #d1d5db;border-radius:10px;padding:10px 14px;font-size:14px;outline:none;resize:none;font-family:inherit}
.pa-input:focus{border-color:#FF6B2C;box-shadow:0 0 0 2px rgba(255,107,44,0.15)}
.pa-send{width:40px;height:40px;border-radius:10px;border:none;background:linear-gradient(135deg,#FF6B2C,#F59E0B);cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.pa-send:disabled{opacity:.5;cursor:not-allowed}
.pa-send svg{width:18px;height:18px;fill:white}
.pa-typing{display:flex;gap:4px;padding:10px 14px;align-self:flex-start}
.pa-typing span{width:8px;height:8px;background:#d1d5db;border-radius:50%;animation:pa-bounce .6s infinite alternate}
.pa-typing span:nth-child(2){animation-delay:.15s}
.pa-typing span:nth-child(3){animation-delay:.3s}
@keyframes pa-bounce{to{transform:translateY(-6px);background:#FF6B2C}}
@media(max-width:480px){.pa-window{width:calc(100vw - 24px);right:12px;bottom:80px;max-height:70vh}}
</style>

<button type="button" class="pa-fab" id="pa-fab" title="Résabot - Assistant">
    <svg class="pa-chat-icon" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
    <svg class="pa-close" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
</button>

<div class="pa-window" id="pa-window">
    <div class="pa-header">
        <div>
            <div class="pa-header-name">Résabot</div>
            <div class="pa-header-sub">Assistant {{ $panelType === 'admin' ? 'Admin' : ($panelType === 'chauffeur' ? 'Chauffeur' : 'Loueur') }}</div>
        </div>
    </div>
    <div class="pa-messages" id="pa-messages">
        <div class="pa-msg pa-msg-bot">Salam ! Je suis Résabot, ton assistant. Pose-moi n'importe quelle question sur ton espace {{ $panelType === 'admin' ? 'admin' : ($panelType === 'chauffeur' ? 'chauffeur' : 'loueur') }}.</div>
    </div>
    <div class="pa-input-area">
        <input type="text" class="pa-input" id="pa-input" placeholder="Pose ta question..." maxlength="500" autocomplete="off">
        <button type="button" class="pa-send" id="pa-send" disabled>
            <svg viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
        </button>
    </div>
</div>

<script>
(function(){
    const fab=document.getElementById('pa-fab'),win=document.getElementById('pa-window'),
          msgs=document.getElementById('pa-messages'),input=document.getElementById('pa-input'),
          send=document.getElementById('pa-send');
    let history=[],busy=false;
    const panel='{{ $panelType }}';

    fab.addEventListener('click',()=>{
        const open=win.classList.toggle('pa-visible');
        fab.classList.toggle('pa-open',open);
        if(open)input.focus();
    });

    input.addEventListener('input',()=>send.disabled=!input.value.trim()||busy);
    input.addEventListener('keydown',e=>{if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();doSend()}});
    send.addEventListener('click',doSend);

    function addMsg(text,isUser){
        const d=document.createElement('div');
        d.className='pa-msg '+(isUser?'pa-msg-user':'pa-msg-bot');
        d.textContent=text;
        msgs.appendChild(d);
        msgs.scrollTop=msgs.scrollHeight;
    }

    function showTyping(){
        const d=document.createElement('div');
        d.className='pa-typing';d.id='pa-typing';
        d.innerHTML='<span></span><span></span><span></span>';
        msgs.appendChild(d);msgs.scrollTop=msgs.scrollHeight;
    }
    function hideTyping(){document.getElementById('pa-typing')?.remove()}

    async function doSend(){
        const text=input.value.trim();
        if(!text||busy)return;
        busy=true;send.disabled=true;input.value='';
        addMsg(text,true);
        history.push({role:'user',content:text});
        showTyping();

        try{
            const r=await fetch('/api/panel-assistant',{
                method:'POST',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]')?.content||''},
                body:JSON.stringify({message:text,history:history.slice(-8),panel:panel})
            });
            const data=await r.json();
            hideTyping();
            addMsg(data.reply||'Erreur, réessaie.',false);
            history.push({role:'assistant',content:data.reply||''});
        }catch(e){
            hideTyping();
            addMsg('Erreur de connexion. Réessaie.',false);
        }
        busy=false;send.disabled=!input.value.trim();
    }
})();
</script>
