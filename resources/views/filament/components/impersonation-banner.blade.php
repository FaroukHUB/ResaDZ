@if (session()->has('impersonator_id'))
    <div style="background:#b91c1c; color:#fff; padding:10px 16px; display:flex; align-items:center; justify-content:space-between; gap:12px; position:sticky; top:0; z-index:50;">
        <div style="display:flex; align-items:center; gap:10px; font-size:14px;">
            <svg style="width:20px; height:20px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            <span>
                <strong>Mode Admin</strong> — vous êtes connecté en tant que
                <strong>{{ auth()->user()?->loueur?->company_name ?? auth()->user()?->name }}</strong>
            </span>
        </div>
        <a href="{{ route('admin.impersonate.stop') }}"
           style="background:#fff; color:#b91c1c; padding:6px 16px; border-radius:8px; font-weight:700; font-size:13px; text-decoration:none; white-space:nowrap;">
            ← Retour Admin
        </a>
    </div>
@endif
