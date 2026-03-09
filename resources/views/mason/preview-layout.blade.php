<!DOCTYPE html>
<html lang="sk">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Mason Preview</title>
        @masonStyles
        @vite(['resources/css/app.css'])
        <style>
            body {
                background-color: #0a0a0a;
                color: #ffffff;
                margin: 0;
                padding: 0;
            }
        </style>
    </head>
    <body class="bg-bcz-dark text-white font-sans antialiased">
        @include('mason::iframe-preview-content', ['blocks' => $blocks])
    </body>
</html>
