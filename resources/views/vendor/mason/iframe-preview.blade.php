<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Mason Preview</title>
        @masonStyles
    </head>
    <body>
        @include('mason::iframe-preview-content', ['blocks' => $blocks])
    </body>
</html>
