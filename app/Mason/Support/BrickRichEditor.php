<?php

namespace App\Mason\Support;

use Awcodes\RicherEditor\Plugins\LinkPlugin;
use Awcodes\RicherEditor\Tools\HeadingFiveTool;
use Awcodes\RicherEditor\Tools\HeadingFourTool;
use Awcodes\RicherEditor\Tools\HeadingSixTool;
use Filament\Forms\Components\RichEditor;

class BrickRichEditor
{
    /**
     * Create a RichEditor pre-configured for brick content editing.
     */
    public static function make(string $name): RichEditor
    {
        return RichEditor::make($name)
            ->plugins([
                LinkPlugin::make(),
            ])
            ->tools([
                HeadingFourTool::make(),
                HeadingFiveTool::make(),
                HeadingSixTool::make(),
            ])
            ->toolbarButtons([
                'bold',
                'italic',
                'underline',
                'strike',
                'link',
                'h2',
                'h3',
                'h4',
                'h5',
                'h6',
                'bulletList',
                'orderedList',
                'undo',
                'redo',
            ]);
    }
}
