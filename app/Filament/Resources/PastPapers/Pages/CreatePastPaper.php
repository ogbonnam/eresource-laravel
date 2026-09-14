<?php

namespace App\Filament\Resources\PastPapers\Pages;

use App\Filament\Resources\PastPapers\PastPaperResource;
use App\Services\PastPaperTextExtractor;
use Filament\Resources\Pages\CreateRecord;

class CreatePastPaper extends CreateRecord
{
    protected static string $resource = PastPaperResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uploaded_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        app(PastPaperTextExtractor::class)
            ->process($this->record);
    }
}