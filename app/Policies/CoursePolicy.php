<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Determine whether the user can view the course.
     *
     * Admin:
     * - Can view any course.
     *
     * Teacher:
     * - Can only view courses they own.
     *
     * Student:
     * - Can only view courses they are actively enrolled in.
     */
    public function view(User $user, Course $course): bool
    {
        /*
         * Administrators can view any course.
         */
        if ($user->isAdmin()) {
            return true;
        }

        /*
         * Teachers can only view courses
         * that belong to them.
         */
        if ($user->isTeacher()) {
            return $course->teacher_id === $user->id;
        }

        /*
         * Students can only view courses
         * where they have an active enrollment.
         */
        if ($user->isStudent()) {
            return $user->activeCourses()
                ->whereKey($course->id)
                ->exists();
        }

        /*
         * Unknown roles have no access.
         */
        return false;
    }


    /**
     * Determine whether the user can update the course.
     *
     * Admin:
     * - Can update any course.
     *
     * Teacher:
     * - Can update only their own courses.
     *
     * Student:
     * - Cannot update courses.
     */
    public function update(User $user, Course $course): bool
    {
        /*
         * Administrators can update any course.
         */
        if ($user->isAdmin()) {
            return true;
        }

        /*
         * Teachers can update their own courses.
         */
        if ($user->isTeacher()) {
            return $course->teacher_id === $user->id;
        }

        return false;
    }


    /**
     * Determine whether the user can delete the course.
     *
     * Admin:
     * - Can delete any course.
     *
     * Teacher:
     * - Can delete only their own courses.
     *
     * Student:
     * - Cannot delete courses.
     */
    public function delete(User $user, Course $course): bool
    {
        /*
         * Administrators can delete any course.
         */
        if ($user->isAdmin()) {
            return true;
        }

        /*
         * Teachers can delete their own courses.
         */
        if ($user->isTeacher()) {
            return $course->teacher_id === $user->id;
        }

        return false;
    }


    /**
     * Determine whether the user can create courses.
     *
     * Admin:
     * - Can create courses.
     *
     * Teacher:
     * - Can create courses.
     *
     * Student:
     * - Cannot create courses.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }
}