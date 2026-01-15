<?php

use App\Models\ActivityLog;

if (!function_exists('logActivity')) {
    function logActivity($userRole, $userId, $activityType, $description)
    {
        ActivityLog::create([
            'user_role'  => $userRole,
            'user_id'    => $userId,
            'activity_type' => $activityType,
            'description' => $description,
        ]);
    }
}
