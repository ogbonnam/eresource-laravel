<x-filament-panels::page>

    <div class="space-y-6">

        {{-- ========================================================= --}}
        {{-- REVIEW HEADER --}}
        {{-- ========================================================= --}}

        <x-filament::section>
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                <div>
                    <h2 class="text-xl font-semibold text-gray-950 dark:text-white">
                        Question Review
                    </h2>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Review the question, classification, answer and source information before approval.
                    </p>
                </div>

                <div>
                    @php
                        $statusColor = match ($record->status) {
                            'approved' => 'success',
                            'rejected' => 'danger',
                            'pending_review' => 'warning',
                            'draft' => 'gray',
                            default => 'gray',
                        };

                        $statusLabel = match ($record->status) {
                            'pending_review' => 'Pending Review',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                            'draft' => 'Draft',
                            default => str($record->status)->title(),
                        };
                    @endphp

                    <x-filament::badge :color="$statusColor">
                        {{ $statusLabel }}
                    </x-filament::badge>
                </div>

            </div>
        </x-filament::section>


        {{-- ========================================================= --}}
        {{-- QUESTION --}}
        {{-- ========================================================= --}}

        <x-filament::section heading="Question">

            <div class="space-y-4">

                <div>
                    <div class="mb-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                        Question
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-5 text-base leading-7 text-gray-950 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        {!! nl2br(e($record->question)) !!}
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Question Type
                        </div>

                        <div class="mt-1 font-medium">
                            {{ match ($record->question_type) {
                                'multiple_choice' => 'Multiple Choice',
                                'short_answer' => 'Short Answer',
                                'structured' => 'Structured',
                                'matching' => 'Matching',
                                'note_taking' => 'Note Taking',
                                'writing' => 'Writing',
                                'email' => 'Email Writing',
                                'essay' => 'Essay Writing',
                                'true_false' => 'True / False',
                                'fill_blank' => 'Fill in the Blank',
                                default => str($record->question_type)->replace('_', ' ')->title(),
                            } }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Difficulty
                        </div>

                        <div class="mt-1 font-medium">
                            {{ str($record->difficulty)->title() }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Marks
                        </div>

                        <div class="mt-1 font-medium">
                            {{ $record->marks }}
                        </div>
                    </div>

                </div>

            </div>

        </x-filament::section>


        {{-- ========================================================= --}}
        {{-- CLASSIFICATION --}}
        {{-- ========================================================= --}}

        <x-filament::section heading="Classification">

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">

                <div>
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                        Subject
                    </div>

                    <div class="mt-1 font-medium">
                        {{ $record->subject?->name ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                        Class / Level
                    </div>

                    <div class="mt-1 font-medium">
                        {{ $record->schoolClass?->name ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                        Topic
                    </div>

                    <div class="mt-1 font-medium">
                        {{ $record->topic ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                        Subtopic
                    </div>

                    <div class="mt-1 font-medium">
                        {{ $record->subtopic ?? '-' }}
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                        Concept
                    </div>

                    <div class="mt-1 font-medium">
                        {{ $record->concept ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                        Command Word
                    </div>

                    <div class="mt-1 font-medium">
                        {{ $record->command_word ?? '-' }}
                    </div>
                </div>

            </div>

        </x-filament::section>


        {{-- ========================================================= --}}
        {{-- MCQ OPTIONS --}}
        {{-- ========================================================= --}}

        @if ($record->question_type === 'multiple_choice')

            <x-filament::section heading="Answer Options">

                <div class="space-y-3">

                    @forelse ($record->options as $option)

                        <div
                            @class([
                                'rounded-xl border p-4',
                                'border-success-500 bg-success-50 dark:bg-success-950/20' => $option->is_correct,
                                'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800' => ! $option->is_correct,
                            ])
                        >

                            <div class="flex items-start gap-4">

                                <div
                                    @class([
                                        'flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-sm font-bold',
                                        'bg-success-600 text-white' => $option->is_correct,
                                        'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' => ! $option->is_correct,
                                    ])
                                >
                                    {{ $option->label }}
                                </div>

                                <div class="flex-1">

                                    <div class="text-sm leading-6 text-gray-950 dark:text-white">
                                        {!! nl2br(e($option->option_text)) !!}
                                    </div>

                                    @if ($option->is_correct)
                                        <div class="mt-2">
                                            <x-filament::badge color="success">
                                                Correct Answer
                                            </x-filament::badge>
                                        </div>
                                    @endif

                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="rounded-xl border border-warning-200 bg-warning-50 p-4 text-sm text-warning-800">
                            No answer options have been stored for this multiple-choice question.
                        </div>

                    @endforelse

                </div>

            </x-filament::section>

        @endif


        {{-- ========================================================= --}}
        {{-- ANSWER --}}
        {{-- ========================================================= --}}

        <x-filament::section heading="Answer">

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-800">

                @if (filled($record->answer))

                    <div class="whitespace-pre-line text-sm leading-7 text-gray-950 dark:text-white">
                        {{ $record->answer }}
                    </div>

                @else

                    <div class="text-sm italic text-gray-500">
                        No answer has been provided for this question.
                    </div>

                @endif

            </div>

        </x-filament::section>


        {{-- ========================================================= --}}
        {{-- EXPLANATION --}}
        {{-- ========================================================= --}}

        <x-filament::section heading="Explanation">

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-800">

                @if (filled($record->explanation))

                    <div class="whitespace-pre-line text-sm leading-7 text-gray-950 dark:text-white">
                        {{ $record->explanation }}
                    </div>

                @else

                    <div class="text-sm italic text-gray-500">
                        No explanation has been provided.
                    </div>

                @endif

            </div>

        </x-filament::section>


        {{-- ========================================================= --}}
        {{-- SOURCE --}}
        {{-- ========================================================= --}}

        <x-filament::section heading="Source & AI Information">

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">

                <div>
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                        Past Paper
                    </div>

                    <div class="mt-1 font-medium">
                        {{ $record->pastPaper?->title ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                        Original Question
                    </div>

                    <div class="mt-1 font-medium">
                        {{ $record->metadata['question_number'] ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                        Section
                    </div>

                    <div class="mt-1 font-medium">
                        {{ $record->metadata['section'] ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                        Source Type
                    </div>

                    <div class="mt-1 font-medium">
                        {{ str($record->source_type)->replace('_', ' ')->title() }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                        AI Model
                    </div>

                    <div class="mt-1 font-medium">
                        {{ $record->ai_model ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                        Created
                    </div>

                    <div class="mt-1 font-medium">
                        {{ $record->created_at?->format('d M Y H:i') ?? '-' }}
                    </div>
                </div>

            </div>

            @if (filled($record->generation_notes))

                <div class="mt-6">

                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                        Analysis / Generation Notes
                    </div>

                    <div class="mt-2 whitespace-pre-line rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm dark:border-gray-700 dark:bg-gray-800">
                        {{ $record->generation_notes }}
                    </div>

                </div>

            @endif

        </x-filament::section>


        {{-- ========================================================= --}}
        {{-- REVIEW INFORMATION --}}
        {{-- ========================================================= --}}

        @if ($record->reviewed_at)

            <x-filament::section heading="Review Information">

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Reviewed By
                        </div>

                        <div class="mt-1 font-medium">
                            {{ $record->reviewer?->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Reviewed At
                        </div>

                        <div class="mt-1 font-medium">
                            {{ $record->reviewed_at->format('d M Y H:i') }}
                        </div>
                    </div>

                </div>

            </x-filament::section>

        @endif

    </div>

</x-filament-panels::page>