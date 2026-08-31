<?php

namespace App\Policies;

use App\Models\Resource;
use App\Models\User;

class ResourcePolicy
{
    /**
     * Determine whether the user can view the resource.
     */
    public function view(User $user, Resource $resource): bool
    {
        /*
         * Admins can view everything.
         */
        if ($user->isAdmin()) {
            return true;
        }

        /*
         * Teachers can view resources belonging
         * to courses they own.
         */
        if ($user->isTeacher()) {
            return $resource->course
                && $resource->course->teacher_id === $user->id;
        }

        /*
         * Students can only view resources belonging
         * to courses they are actively enrolled in.
         */
        if ($user->isStudent()) {
            return $resource->course
                && $user->activeCourses()
                    ->whereKey($resource->course_id)
                    ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create resources
     * for a course.
     */
    public function create(User $user, Resource $resource): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isTeacher()
            && $resource->course
            && $resource->course->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can update the resource.
     */
    public function update(User $user, Resource $resource): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isTeacher()
            && $resource->course
            && $resource->course->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can delete the resource.
     */
    public function delete(User $user, Resource $resource): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isTeacher()
            && $resource->course
            && $resource->course->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can publish/unpublish
     * the resource.
     */
    public function publish(User $user, Resource $resource): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isTeacher()
            && $resource->course
            && $resource->course->teacher_id === $user->id;
    }
}