<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Mason Entry</title>
        @masonEntryStyles
    </head>
    <body>
        @include('mason::iframe-entry-content', ['blocks' => $blocks])
    </body>
</html>
