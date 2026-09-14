<?php

namespace App\Filament\Resources\Questions\Pages;

use App\Filament\Resources\Questions\QuestionResource;
use App\Models\Question;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\Page;

class ReviewQuestion extends Page
{
    protected static string $resource = QuestionResource::class;

    protected string $view = 'filament.resources.questions.pages.review-question';

    public Question $record;

    public function mount(Question $record): void
    {
        $this->record = $record->load([
            'subject',
            'schoolClass',
            'pastPaper',
            'options',
            'reviewer',
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [

            EditAction::make()
                ->record($this->record),

            Action::make('approve')
                ->label('Approve Question')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Approve Question')
                ->modalDescription(
                    'This question will become available in the approved question bank.'
                )
                ->modalSubmitActionLabel('Approve Question')
                ->visible(
                    fn (): bool => in_array(
                        $this->record->status,
                        ['pending_review', 'draft'],
                        true
                    )
                )
                ->action(function (): void {
                    $this->record->update([
                        'status' => 'approved',
                        'reviewed_by' => auth()->id(),
                        'reviewed_at' => now(),
                    ]);

                    $this->record->refresh();
                }),

            Action::make('reject')
                ->label('Reject Question')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Reject Question')
                ->modalDescription(
                    'This question will be marked as rejected and will not be available for student practice.'
                )
                ->modalSubmitActionLabel('Reject Question')
                ->visible(
                    fn (): bool => in_array(
                        $this->record->status,
                        ['pending_review', 'draft'],
                        true
                    )
                )
                ->action(function (): void {
                    $this->record->update([
                        'status' => 'rejected',
                        'reviewed_by' => auth()->id(),
                        'reviewed_at' => now(),
                    ]);

                    $this->record->refresh();
                }),

        ];
    }

    public function getTitle(): string
    {
        return 'Review Question';
    }
}