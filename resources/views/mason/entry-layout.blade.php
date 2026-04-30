<!DOCTYPE html>
<html lang="sk">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Mason Entry</title>
        @masonEntryStyles
        @vite(['resources/css/app.css'])
        <style>
            body {
                background-color: #0a0a0a;
                color: #ffffff;
                margin: 0;
                padding: 0;
            }
            #mason-entry-container {
                padding: 40px;
                display: flex;
                flex-direction: column;
                gap: 32px;
            }
            .mason-entry-block-content {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            .mason-entry-block-content > * + * {
                margin-top: 0;
            }
        </style>
    </head>
    <body class="bg-[#0A0A0A] text-white font-sans antialiased">
        @include('mason::iframe-entry-content', ['blocks' => $blocks])
    </body>
</html>
