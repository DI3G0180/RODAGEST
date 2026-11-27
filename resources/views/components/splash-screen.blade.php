<div id="rodagest-splash" role="status" aria-live="polite" aria-label="Cargando RODAGEST" style="position:fixed; inset:0; z-index:999999; display:flex; align-items:center; justify-content:center; background:#ffffff; opacity:1; transition:opacity .3s ease, transform .3s ease;">
    <div class="splash-content">
        <div class="splash-emblem"><img src="/images/logo-rodagest.png" alt="Logotipo RODAGEST"></div>
        <h1>RODAGEST</h1>
        <p>Control y Gestión Inteligente de Flotas</p>
        <div class="splash-loader" aria-hidden="true"><span></span></div>
    </div>
</div>

<style>
    #rodagest-splash{color:#083344;font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif}
    #rodagest-splash .splash-content{display:flex;flex-direction:column;align-items:center;padding:24px;text-align:center;animation:splash-reveal .65s cubic-bezier(.2,.8,.2,1) both}
    #rodagest-splash .splash-emblem{display:grid;width:clamp(132px,36vw,176px);height:clamp(132px,36vw,176px);place-items:center;padding:20px;border:1px solid #dbeafe;border-radius:32px;background:#fff;box-shadow:0 18px 55px rgba(8,51,68,.12)}
    #rodagest-splash .splash-emblem img{display:block;width:100%;height:100%;object-fit:contain}
    #rodagest-splash h1{margin:22px 0 0;color:#083344;font-size:clamp(27px,7vw,36px);font-weight:800;letter-spacing:.12em;line-height:1.15}
    #rodagest-splash p{margin:9px 0 0;color:#0E7490;font-size:14px;font-weight:600;letter-spacing:.01em}
    #rodagest-splash .splash-loader{width:148px;height:3px;margin-top:28px;overflow:hidden;border-radius:999px;background:#e2e8f0}
    #rodagest-splash .splash-loader span{display:block;width:0;height:100%;border-radius:inherit;background:#0E7490;animation:splash-load 1.3s ease-out forwards}
    @keyframes splash-reveal{from{opacity:0;transform:translateY(8px) scale(.94)}to{opacity:1;transform:translateY(0) scale(1)}}
    @keyframes splash-load{to{width:100%}}
    @media(prefers-reduced-motion:reduce){#rodagest-splash .splash-content,#rodagest-splash .splash-loader span{animation-duration:.01ms;animation-iteration-count:1}#rodagest-splash .splash-loader span{width:100%}}
</style>

<script>
    (function () {
        function initSplash() {
            var splash = document.getElementById('rodagest-splash');
            if (!splash) return;
            var finished = false;
            var safetyTimer;

            function finish() {
                if (finished) return;
                finished = true;
                splash.style.opacity = '0';
                splash.style.transform = 'translateY(-8px)';
                splash.style.pointerEvents = 'none';
                window.setTimeout(function () {
                    splash.style.display = 'none';
                    splash.setAttribute('aria-hidden', 'true');
                }, 320);
                if (safetyTimer) window.clearTimeout(safetyTimer);
            }

            try {
                var key = 'rodagest-login-splash-seen-v2';
                if (window.sessionStorage.getItem(key) === '1') {
                    finish();
                    return;
                }
                window.sessionStorage.setItem(key, '1');
            } catch (error) {
                // La salida temporizada sigue funcionando si WebView bloquea sessionStorage.
            }

            safetyTimer = window.setTimeout(finish, 1600);
            window.setTimeout(finish, 1300);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSplash, { once: true });
        } else {
            initSplash();
        }
    })();
</script>
