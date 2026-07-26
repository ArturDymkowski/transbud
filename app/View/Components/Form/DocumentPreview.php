<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;
use Illuminate\View\View;

class DocumentPreview extends Component
{
    public function __construct(
        public string $field,
        public string $label,
        public ?array $existingMediaId = null,
    ) {}

    public function render(): View
    {
        return view('components.form.document-preview');
    }
}
