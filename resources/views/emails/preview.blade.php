<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }} - Náhľad</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', -apple-system, sans-serif; background: #f0f0f0; }
        .toolbar { position: fixed; top: 0; left: 0; right: 0; z-index: 100; display: flex; align-items: center; justify-content: center; gap: 12px; padding: 12px 20px; background: #18181b; border-bottom: 1px solid #27272a; }
        .toolbar span { color: #a1a1aa; font-size: 13px; font-weight: 500; }
        .toggle { position: relative; width: 48px; height: 26px; background: #3f3f46; border-radius: 13px; cursor: pointer; transition: background .2s; }
        .toggle.dark { background: #3b82f6; }
        .toggle::after { content: ''; position: absolute; top: 3px; left: 3px; width: 20px; height: 20px; background: #fff; border-radius: 50%; transition: transform .2s; }
        .toggle.dark::after { transform: translateX(22px); }
        .preview-frame { width: 100%; border: none; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="toolbar">
        <span>Light</span>
        <div class="toggle" id="themeToggle" onclick="toggleTheme()"></div>
        <span>Dark</span>
    </div>
    <iframe id="previewFrame" class="preview-frame"></iframe>
    <script>
        var emailHtml = @json($emailHtml);

        function resizeFrame() {
            var f = document.getElementById('previewFrame');
            f.style.height = (window.innerHeight - 50) + 'px';
        }
        window.addEventListener('resize', resizeFrame);
        resizeFrame();

        var frame = document.getElementById('previewFrame');
        var doc = frame.contentDocument || frame.contentWindow.document;
        doc.open();
        doc.write(emailHtml);
        doc.close();

        function applyTheme(isDark) {
            var frame = document.getElementById('previewFrame');
            var doc = frame.contentDocument || frame.contentWindow.document;
            var body = doc.querySelector('body');
            if (!body) return;

            var content = doc.querySelector('.content-bg');

            if (isDark) {
                body.style.backgroundColor = '#0A0A0A';
                doc.querySelectorAll('.header-bg').forEach(function(el) { el.style.backgroundColor = '#111111'; el.style.borderBottomColor = '#222222'; });
                doc.querySelectorAll('.content-bg').forEach(function(el) { el.style.backgroundColor = '#0D0D0D'; });
                doc.querySelectorAll('.footer-bg').forEach(function(el) { el.style.backgroundColor = '#111111'; el.style.borderTopColor = '#222222'; });
                doc.querySelectorAll('.body-bg').forEach(function(el) { el.style.backgroundColor = '#0A0A0A'; });
                doc.querySelectorAll('.footer-team-name').forEach(function(el) { el.style.color = '#FFFFFF'; });
                doc.querySelectorAll('.footer-contact').forEach(function(el) { el.style.color = '#888888'; });
                doc.querySelectorAll('.footer-divider').forEach(function(el) { el.style.backgroundColor = '#222222'; });
                doc.querySelectorAll('.footer-copyright').forEach(function(el) { el.style.color = '#555555'; });
                doc.querySelectorAll('.powered-divider').forEach(function(el) { el.style.backgroundColor = '#1A1A1A'; });
                doc.querySelectorAll('.powered-text').forEach(function(el) { el.style.color = '#444444'; });
                doc.querySelectorAll('.powered-brand').forEach(function(el) { el.style.color = '#666666'; });
                doc.querySelectorAll('.bcz-logo-light').forEach(function(el) { el.style.display = 'none'; });
                doc.querySelectorAll('.bcz-logo-dark').forEach(function(el) { el.style.display = 'inline'; });
                doc.querySelectorAll('.info-box').forEach(function(el) { el.style.backgroundColor = '#1A1A1A'; });
                doc.querySelectorAll('h1, h2, h3, .heading-text').forEach(function(el) { el.style.color = '#f9fafb'; });
                doc.querySelectorAll('p, .body-text').forEach(function(el) { el.style.color = '#AAAAAA'; });
                doc.querySelectorAll('a').forEach(function(el) {
                    if (el.style.color && el.style.color !== 'rgb(255, 255, 255)') el.style.color = '#60a5fa';
                });
                doc.querySelectorAll('ul, ol, li').forEach(function(el) { el.style.color = '#AAAAAA'; });
                doc.querySelectorAll('hr, .divider-line').forEach(function(el) { el.style.borderTopColor = '#222222'; });
                if (content) {
                    content.querySelectorAll('div').forEach(function(el) { if (el.style.color) el.style.color = '#AAAAAA'; });
                    content.querySelectorAll('span').forEach(function(el) { if (el.style.color) el.style.color = '#AAAAAA'; });
                }
            } else {
                body.style.backgroundColor = '#f0f0f0';
                doc.querySelectorAll('.header-bg').forEach(function(el) { el.style.backgroundColor = '#FFFFFF'; el.style.borderBottomColor = '#E0E0E0'; });
                doc.querySelectorAll('.content-bg').forEach(function(el) { el.style.backgroundColor = '#FAFAFA'; });
                doc.querySelectorAll('.footer-bg').forEach(function(el) { el.style.backgroundColor = '#FFFFFF'; el.style.borderTopColor = '#E0E0E0'; });
                doc.querySelectorAll('.body-bg').forEach(function(el) { el.style.backgroundColor = '#f0f0f0'; });
                doc.querySelectorAll('.footer-team-name').forEach(function(el) { el.style.color = '#1A1A1A'; });
                doc.querySelectorAll('.footer-contact').forEach(function(el) { el.style.color = '#777777'; });
                doc.querySelectorAll('.footer-divider').forEach(function(el) { el.style.backgroundColor = '#E5E5E5'; });
                doc.querySelectorAll('.footer-copyright').forEach(function(el) { el.style.color = '#999999'; });
                doc.querySelectorAll('.powered-divider').forEach(function(el) { el.style.backgroundColor = '#EEEEEE'; });
                doc.querySelectorAll('.powered-text').forEach(function(el) { el.style.color = '#AAAAAA'; });
                doc.querySelectorAll('.powered-brand').forEach(function(el) { el.style.color = '#888888'; });
                doc.querySelectorAll('.bcz-logo-light').forEach(function(el) { el.style.display = 'inline'; });
                doc.querySelectorAll('.bcz-logo-dark').forEach(function(el) { el.style.display = 'none'; });
                doc.querySelectorAll('.info-box').forEach(function(el) { el.style.backgroundColor = '#F3F4F6'; });
                doc.querySelectorAll('h1, h2, h3, .heading-text').forEach(function(el) { el.style.color = '#1A1A1A'; });
                doc.querySelectorAll('p, .body-text').forEach(function(el) { el.style.color = '#555555'; });
                doc.querySelectorAll('a').forEach(function(el) {
                    if (el.style.color && el.style.color !== 'rgb(255, 255, 255)') el.style.color = '#2563eb';
                });
                doc.querySelectorAll('ul, ol, li').forEach(function(el) { el.style.color = '#555555'; });
                doc.querySelectorAll('hr, .divider-line').forEach(function(el) { el.style.borderTopColor = '#E5E5E5'; });
                if (content) {
                    content.querySelectorAll('div').forEach(function(el) { if (el.style.color) el.style.color = '#555555'; });
                    content.querySelectorAll('span').forEach(function(el) { if (el.style.color) el.style.color = '#555555'; });
                }
            }
        }

        function toggleTheme() {
            var toggle = document.getElementById('themeToggle');
            toggle.classList.toggle('dark');
            applyTheme(toggle.classList.contains('dark'));
        }

        // Auto-detect OS dark mode and apply initial theme
        (function() {
            var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (prefersDark) {
                document.getElementById('themeToggle').classList.add('dark');
                applyTheme(true);
            }
        })();
    </script>
</body>
</html>
