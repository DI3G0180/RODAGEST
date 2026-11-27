<div id="rodagest-splash" role="status" aria-live="polite" aria-label="Cargando RODAGEST" class="fixed inset-0 z-[999999] flex items-center justify-center overflow-hidden bg-gradient-to-br from-[#083344] via-[#0E7490] to-[#155E75] text-white" style="position:fixed; inset:0; z-index:999999; opacity:1; transition:opacity .4s ease;">
    <div class="splash-orb splash-orb-one" aria-hidden="true"></div>
    <div class="splash-orb splash-orb-two" aria-hidden="true"></div>
    <div class="splash-wave splash-wave-one" aria-hidden="true"></div>
    <div class="splash-wave splash-wave-two" aria-hidden="true"></div>

    <div class="relative z-10 flex w-full max-w-sm flex-col items-center px-8 text-center">
        <div class="splash-logo-halo grid h-36 w-36 place-items-center rounded-full border border-white/50 bg-white p-5 shadow-[0_0_55px_rgba(255,255,255,.35)] sm:h-40 sm:w-40">
            <img src="/images/logo-rodagest.png" alt="RODAGEST" class="h-full w-full object-contain" style="mix-blend-mode:multiply">
        </div>
        <h1 class="mt-7 text-3xl font-extrabold tracking-[.16em] text-white sm:text-4xl">RODAGEST</h1>
        <p class="mt-2 max-w-xs text-sm font-medium tracking-wide text-white/90 sm:text-base">Sistema Integral de Gestión y Control de Flotas</p>
    </div>
    <div class="absolute bottom-8 left-1/2 h-1.5 w-52 -translate-x-1/2 overflow-hidden rounded-full bg-white/25" aria-hidden="true">
        <div class="splash-progress h-full rounded-full bg-white"></div>
    </div>
</div>

<style>
    #rodagest-splash::before{content:"";position:absolute;inset:0;pointer-events:none;background:radial-gradient(circle at 22% 18%,rgba(255,255,255,.12),transparent 28%),radial-gradient(circle at 82% 76%,rgba(34,211,238,.15),transparent 32%),linear-gradient(135deg,transparent 35%,rgba(255,255,255,.035) 50%,transparent 65%)}
    #rodagest-splash .splash-orb{position:absolute;border:1px solid rgba(255,255,255,.12);border-radius:9999px;background:rgba(255,255,255,.035);pointer-events:none}
    #rodagest-splash .splash-orb-one{width:min(76vw,520px);height:min(76vw,520px);top:-24%;right:-20%;animation:splash-float 7s ease-in-out infinite alternate}
    #rodagest-splash .splash-orb-two{width:min(90vw,680px);height:min(90vw,680px);bottom:-44%;left:-26%;animation:splash-float 9s ease-in-out infinite alternate-reverse}
    #rodagest-splash .splash-wave{position:absolute;left:-15%;width:130%;height:28%;border:1px solid rgba(255,255,255,.1);border-radius:50%;background:rgba(255,255,255,.025);pointer-events:none}
    #rodagest-splash .splash-wave-one{bottom:-18%;transform:rotate(-8deg)}
    #rodagest-splash .splash-wave-two{bottom:-23%;transform:rotate(7deg)}
    #rodagest-splash .splash-logo-halo{animation:splash-enter .65s cubic-bezier(.2,.8,.2,1) both,splash-pulse 2.1s ease-in-out .65s infinite}
    #rodagest-splash .splash-progress{width:0;animation:splash-fill 1.3s ease-out forwards}
    @keyframes splash-enter{from{opacity:0;transform:scale(.72)}to{opacity:1;transform:scale(1)}}
    @keyframes splash-pulse{0%,100%{box-shadow:0 0 34px rgba(255,255,255,.2)}50%{box-shadow:0 0 58px rgba(255,255,255,.42)}}
    @keyframes splash-fill{to{width:100%}}
    @keyframes splash-float{from{transform:translate3d(0,0,0)}to{transform:translate3d(0,18px,0)}}
    @media(prefers-reduced-motion:reduce){#rodagest-splash .splash-logo-halo,#rodagest-splash .splash-orb,#rodagest-splash .splash-progress{animation-duration:.01ms;animation-iteration-count:1}#rodagest-splash .splash-progress{width:100%}}
</style>

<script>
    (function () {
        var splash = document.getElementById('rodagest-splash');
        if (!splash) return;

        var finished = false;
        var failSafe;

        function finish() {
            if (finished) return;
            finished = true;
            splash.style.opacity = '0';
            splash.style.pointerEvents = 'none';
            window.setTimeout(function () {
                splash.style.display = 'none';
                splash.setAttribute('aria-hidden', 'true');
            }, 400);
            if (failSafe) window.clearTimeout(failSafe);
        }

        function startSplash() {
            try {
                var seenKey = 'rodagest-splash-seen-v1';
                if (window.sessionStorage.getItem(seenKey) === '1') {
                    finish();
                    return;
                }
                window.sessionStorage.setItem(seenKey, '1');
            } catch (error) {
                // WebView puede bloquear sessionStorage; el timeout sigue retirando el overlay.
            }
            window.setTimeout(finish, 1400);
        }

        failSafe = window.setTimeout(finish, 1800);
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', startSplash, { once: true });
        } else {
            startSplash();
        }
    })();
</script>
