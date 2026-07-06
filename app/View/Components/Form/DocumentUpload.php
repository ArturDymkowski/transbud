<?php


namespace App\View\Components\Form;

use Illuminate\View\Component;
use Illuminate\View\View;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class DocumentUpload extends Component
{
    public function __construct(
        public string $field,
        public string $label,
        public mixed  $file = null,
        public ?int   $existingMediaId = null,
    )
    {
    }

    public function hasPendingFile(): bool
    {
        return $this->file instanceof TemporaryUploadedFile;
    }

    public function hasExistingMedia(): bool
    {
        return !$this->hasPendingFile() && $this->existingMediaId !== null;
    }

    public function render(): View
    {
        return view('components.form.document-upload');
    }
}
