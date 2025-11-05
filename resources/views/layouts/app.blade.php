<!DOCTYPE html>
<html lang="{{ session('locale', 'en') }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ env('APP_NAME', 'Courier System') }}</title>
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { min-height: 100vh; background: #f0f2f5; overflow-x: hidden; font-family: 'Segoe UI', sans-serif; }

/* Sidebar */
.sidebar {
    position: fixed; top: 0; left: 0; height: 100%; width: 250px;
    background: linear-gradient(180deg, #1e3c72, #2a5298);
    color: white; transition: all 0.3s ease; padding-top: 60px;
    z-index: 1000; display: flex; flex-direction: column;
}
.sidebar.closed { left: -250px; }
.sidebar a { color: #ffffffcc; text-decoration: none; padding: 10px 20px; display: flex; align-items: center; gap: 10px; border-radius: 8px; transition: all 0.3s; }
.sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.15); transform: translateX(5px); color:#fff; }
.sidebar-header { position: fixed; top:0; left:0; width:250px; height:60px; background: rgba(0,0,0,0.25); display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:14px; text-align:center; padding:0 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.4); color:#fff; text-shadow:0 0 5px #fff,0 0 10px #1e90ff; }
.sidebar-content { margin-top: 80px; }

/* Main content */
.main-content { transition: margin-left 0.3s ease; margin-left: 250px; padding:20px; }
.main-content.expanded { margin-left: 0; }

/* Sidebar toggle */
.toggle-btn { position: fixed; top:80px; left:15px; background:#1e3c72; border:none; color:white; padding:10px 15px; border-radius:8px; cursor:pointer; z-index:1100; }

/* Notifications */
.notification-bell {
    position: fixed; top:15px; right:25px; font-size:24px; color:#1e3c72; cursor:pointer; z-index:1200;
    transition: transform 0.2s;
}
.notification-bell:hover { transform: scale(1.1); }
.notification-bell .count {
    position:absolute; top:-5px; right:-10px; background:red; color:white;
    font-size:12px; font-weight:bold; padding:3px 6px; border-radius:50%; display:inline-block;
}

.notification-dropdown {
    position: fixed; top:60px; right:25px; width:380px; max-height:480px;
    background: rgba(255,255,255,0.8); backdrop-filter: blur(10px);
    border-radius:15px; box-shadow:0 12px 30px rgba(0,0,0,0.25);
    display:none; z-index:1300; overflow-y:auto; padding:10px;
    transition: all 0.3s ease;
}
.notification-dropdown.active { display:block; }

.notification-item {
    border-radius: 12px; padding:10px; margin-bottom:8px;
    transition: all 0.25s ease; cursor:pointer;
    background: rgba(255,255,255,0.7); box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.notification-item:hover { transform: translateX(5px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
.notification-item[data-read="1"] { opacity:0.7; }

.notification-item .header {
    display:flex; justify-content:space-between; align-items:center; margin-bottom:3px;
}
.notification-item .header strong { font-size:14px; }
.notification-item small { font-size:12px; color:gray; }

/* Flash animations */
.flash { animation: flash 0.6s; }
@keyframes flash { 0%,100%{opacity:1}50%{opacity:0.4} }
.flash-item { animation: flash-item 0.6s ease; }
@keyframes flash-item { 0%,100% { background-color: rgba(255,255,255,0.7); } 50% { background-color: #ffe3e3; } }

.language-buttons button { font-weight:bold; }
</style>
</head>
<body>

<!-- Sidebar Toggle -->
<button class="toggle-btn" id="toggleSidebar">☰</button>

<!-- Notification Bell -->
<div class="notification-bell" id="notificationBell">
    🔔
    <span class="count" id="notificationCount">0</span>
</div>

<!-- Language Buttons -->
<div class="language-buttons" style="position: fixed; top: 15px; right: 80px; z-index: 1200; display:flex; gap:5px;">
    <button class="btn btn-sm btn-primary" onclick="setLanguage('en')">EN</button>
    <button class="btn btn-sm btn-success" onclick="setLanguage('fr')">FR</button>
</div>

<!-- Notification Dropdown -->
<div class="notification-dropdown" id="notificationDropdown">
    <div id="notifications">
        @foreach(\App\Models\Notification::latest()->take(20)->get() as $notification)
            <div class="notification-item" data-read="{{ $notification->read_at ? '1' : '0' }}" data-id="{{ $notification->id }}">
                <div class="header">
                    <div class="d-flex gap-2 align-items-center">
                        @if(str_contains($notification->message,'deleted'))<span class="text-danger">❌</span>
                        @elseif(str_contains($notification->message,'updated'))<span class="text-primary">📄</span>
                        @else <span class="text-success">📥</span>@endif
                        <strong>{{ $notification->message }}</strong>
                    </div>
                    <small>{{ $notification->created_at->format('H:i') }}</small>
                </div>
                <small>{{ $notification->created_at->format('M d, Y') }}</small>
            </div>
        @endforeach
    </div>
</div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        KIVU PETROLE ET STOCKAGE <br> LOGISTIQUE / COURIER
    </div>
    <div class="sidebar-content">
        <a href="{{ route('welcome') }}">🏠 {{ __('messages.home') }}</a>
        <a href="{{ route('incoming-letters.index') }}">📥 {{ __('messages.incoming_letters') }}</a>
        <a href="{{ route('outgoing-letters.index') }}">📤 {{ __('messages.outgoing_letters') }}</a>
        <a href="{{ route('incoming-letters.create') }}">➕ {{ __('messages.add_incoming') }}</a>
        <a href="{{ route('outgoing-letters.create') }}">➕ {{ __('messages.add_outgoing') }}</a>
        <hr class="border-light">
        @auth
            <a href="{{ route('dashboard') }}">🧭 {{ __('messages.dashboard') }}</a>
            <form action="{{ route('logout') }}" method="POST" class="mt-3 px-3">
                @csrf
                <button type="submit" class="btn btn-danger w-100">🚪 {{ __('messages.logout') }}</button>
            </form>
        @else
            <div class="px-3 mt-2 d-flex flex-column gap-2 sidebar-btns">
                <a href="{{ route('login') }}" class="btn btn-primary w-100">🔐 {{ __('messages.login') }}</a>
                <a href="{{ route('register') }}" class="btn btn-success w-100">📝 {{ __('messages.register') }}</a>
            </div>
        @endauth
    </div>
</div>

<!-- Main Content -->
<div class="main-content" id="mainContent">
    @yield('content')
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const bell = document.getElementById('notificationBell');
    const dropdown = document.getElementById('notificationDropdown');
    const countEl = document.getElementById('notificationCount');
    const notificationsDiv = document.getElementById('notifications');

    toggleBtn.addEventListener('click', ()=>{ sidebar.classList.toggle('closed'); mainContent.classList.toggle('expanded'); });

    const updateCount = () => {
        const unreadCount = notificationsDiv.querySelectorAll('.notification-item[data-read="0"]').length;
        countEl.textContent = unreadCount;
        countEl.style.display = unreadCount > 0 ? 'inline-block' : 'none';
    };

    const flashBell = () => { bell.classList.add('flash'); setTimeout(()=>bell.classList.remove('flash'),600); };

    const renderNotification = (message, isUnread=true, id=null) => {
        if(id && document.querySelector(`.notification-item[data-id="${id}"]`)) return;
        const div = document.createElement('div');
        div.className='notification-item';
        div.dataset.read=isUnread?"0":"1"; if(id) div.dataset.id=id;
        div.style.opacity=isUnread?'1':'0.7';
        div.innerHTML = `<div class="header">
            <div class="d-flex gap-2 align-items-center">
                <span>${message.includes('deleted')?'❌':message.includes('updated')?'📄':'📥'}</span>
                <strong>${message}</strong>
            </div>
            <small>${new Date().toLocaleTimeString()}</small>
        </div>
        <small>${new Date().toLocaleDateString()}</small>`;
        notificationsDiv.prepend(div);
        if(notificationsDiv.childElementCount>20) notificationsDiv.removeChild(notificationsDiv.lastChild);
        if(isUnread) flashBell();
        updateCount();
    };

    bell.addEventListener('click', async ()=>{
        dropdown.classList.toggle('active');
        if(dropdown.classList.contains('active')){
            const unreadItems = notificationsDiv.querySelectorAll('.notification-item[data-read="0"]');
            if(unreadItems.length>0){
                try{
                    await axios.post('/notifications/read');
                    unreadItems.forEach(item=>{
                        item.dataset.read="1"; item.style.opacity=0.7;
                    });
                    updateCount();
                }catch(err){console.error(err);}
            }
        }
    });

    // Google Translate Language Switch
    window.setLanguage = function(lang) {
        document.cookie = `googtrans=/en/${lang}; path=/`;
        document.cookie = `googtrans=/en/${lang}; domain=${location.hostname}; path=/`;
        location.reload();
    };

    window.Pusher = Pusher;
    window.Echo = new window.Echo({ broadcaster:'pusher', key:"{{ env('PUSHER_APP_KEY') }}", cluster:"{{ env('PUSHER_APP_CLUSTER') }}", forceTLS:true });
    const userId="{{ auth()->id() }}";
    if(userId){ window.Echo.private(`notifications.${userId}`).listen('.LetterActivityEvent',(event)=>{ renderNotification(event.message,true,event.id); if(!dropdown.classList.contains('active')) flashBell(); }); }

    updateCount();
});
</script>

<!-- Google Translate -->
<script type="text/javascript">
function googleTranslateElementInit() {
    new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
}
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

</body>
</html>
