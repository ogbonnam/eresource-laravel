<?php

namespace App\Filament\Resources\Courses\Widgets;

use App\Models\Course;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class CourseWeeklyActivityChart extends ChartWidget
{
    /*
    |--------------------------------------------------------------------------
    | Current Course
    |--------------------------------------------------------------------------
    */

    public ?Model $record = null;

    /*
    |--------------------------------------------------------------------------
    | Chart Heading
    |--------------------------------------------------------------------------
    */

    protected ?string $heading = 'Weekly Course Activity';

    /*
    |--------------------------------------------------------------------------
    | Description
    |--------------------------------------------------------------------------
    */

    protected ?string $description =
        'Course activity over the last 8 weeks.';

    /*
    |--------------------------------------------------------------------------
    | Lazy Loading
    |--------------------------------------------------------------------------
    */

    protected static bool $isLazy = false;

    /*
    |--------------------------------------------------------------------------
    | Chart Height
    |--------------------------------------------------------------------------
    */

    protected ?string $maxHeight = '400px';

    /*
    |--------------------------------------------------------------------------
    | Chart Type
    |--------------------------------------------------------------------------
    */

    protected function getType(): string
    {
        return 'bar';
    }

    /*
    |--------------------------------------------------------------------------
    | Chart Data
    |--------------------------------------------------------------------------
    */

    protected function getData(): array
    {
        /** @var Course $course */
        $course = $this->record;

        $labels = [];

        $resources = [];

        $assignments = [];

        $submissions = [];

        $graded = [];

        /*
        |--------------------------------------------------------------------------
        | Last 8 weeks
        |--------------------------------------------------------------------------
        */

        $startOfCurrentWeek = now()
            ->startOfWeek();

        for ($i = 7; $i >= 0; $i--) {

            $weekStart = $startOfCurrentWeek
                ->copy()
                ->subWeeks($i);

            $weekEnd = $weekStart
                ->copy()
                ->endOfWeek();

            /*
            |--------------------------------------------------------------------------
            | Label
            |--------------------------------------------------------------------------
            */

            $labels[] =
                $weekStart->format('M d')
                . ' – ' .
                $weekEnd->format('M d');

            /*
            |--------------------------------------------------------------------------
            | Resources created
            |--------------------------------------------------------------------------
            */

            $resources[] = $course
                ->resources()
                ->whereBetween(
                    'created_at',
                    [
                        $weekStart,
                        $weekEnd,
                    ]
                )
                ->count();

            /*
            |--------------------------------------------------------------------------
            | Assignments created
            |--------------------------------------------------------------------------
            */

            $assignments[] = $course
                ->assignments()
                ->whereBetween(
                    'created_at',
                    [
                        $weekStart,
                        $weekEnd,
                    ]
                )
                ->count();

            /*
            |--------------------------------------------------------------------------
            | Submissions
            |--------------------------------------------------------------------------
            */

            $submissions[] = $course
                ->assignments()
                ->whereHas(
                    'submissions',
                    function ($query) use (
                        $weekStart,
                        $weekEnd
                    ) {
                        $query->whereBetween(
                            'submitted_at',
                            [
                                $weekStart,
                                $weekEnd,
                            ]
                        );
                    }
                )
                ->with([
                    'submissions' => function ($query) use (
                        $weekStart,
                        $weekEnd
                    ) {
                        $query->whereBetween(
                            'submitted_at',
                            [
                                $weekStart,
                                $weekEnd,
                            ]
                        );
                    },
                ])
                ->get()
                ->sum(
                    fn ($assignment) =>
                        $assignment->submissions->count()
                );

            /*
            |--------------------------------------------------------------------------
            | Graded submissions
            |--------------------------------------------------------------------------
            */

            $graded[] = $course
                ->assignments()
                ->whereHas(
                    'submissions',
                    function ($query) use (
                        $weekStart,
                        $weekEnd
                    ) {
                        $query
                            ->where('status', 'graded')
                            ->whereBetween(
                                'graded_at',
                                [
                                    $weekStart,
                                    $weekEnd,
                                ]
                            );
                    }
                )
                ->with([
                    'submissions' => function ($query) use (
                        $weekStart,
                        $weekEnd
                    ) {
                        $query
                            ->where('status', 'graded')
                            ->whereBetween(
                                'graded_at',
                                [
                                    $weekStart,
                                    $weekEnd
                                ]
                            );
                    },
                ])
                ->get()
                ->sum(
                    fn ($assignment) =>
                        $assignment->submissions->count()
                );
        }

        return [

            'datasets' => [

                [
                    'label' => 'Resources',
                    'data' => $resources,
                ],

                [
                    'label' => 'Assignments',
                    'data' => $assignments,
                ],

                [
                    'label' => 'Submissions',
                    'data' => $submissions,
                ],

                [
                    'label' => 'Graded',
                    'data' => $graded,
                ],

            ],

            'labels' => $labels,

        ];
    }
}