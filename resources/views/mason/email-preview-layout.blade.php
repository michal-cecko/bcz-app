<!DOCTYPE html>
<html lang="sk">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Email Preview</title>
        @masonStyles
        <style>
            @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap');

            body {
                background-color: #f0f0f0;
                color: #555555;
                margin: 0;
                padding: 0;
                font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            }

            #mason-preview-container {
                max-width: 640px;
                margin: 0 auto;
                background-color: #FAFAFA;
                padding: 24px;
                min-height: 100px;
            }

            h1, h2, h3 {
                color: #1A1A1A;
                margin: 0;
            }

            p {
                color: #555555;
                margin: 0;
            }

            a {
                color: #2563eb;
            }

            ul, ol {
                color: #555555;
            }

            /* Mason block styling overrides for email context */
            .mason-block-content {
                padding: 4px 0;
            }

            .mason-block-controls {
                background-color: rgba(0, 0, 0, 0.05) !important;
            }

            .mason-block-controls button {
                color: #666 !important;
            }

            .mason-block-controls button:hover {
                color: #333 !important;
                background-color: rgba(0, 0, 0, 0.1) !important;
            }

            .mason-block.selected {
                outline-color: #2563eb !important;
            }

            .mason-drop-zone.active {
                background-color: rgba(37, 99, 235, 0.1) !important;
                border-color: #2563eb !important;
            }
        </style>
    </head>
    <body>
        @include('mason::iframe-preview-content', ['blocks' => $blocks])
    </body>
</html>
