<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    public static $availablePermissions = [
        ['title' => 'Users', 'name' => 'users', 'permissions' => ['read', 'write']],
        ['title' => 'User Logins', 'name' => 'logins', 'permissions' => ['read']],
        ['title' => 'Badge Requests', 'name' => 'badge-requests', 'permissions' => ['read', 'write']],
        ['title' => 'Course Containers', 'name' => 'course-containers', 'permissions' => ['read', 'write']],
        ['title' => 'Labels', 'name' => 'labels', 'permissions' => ['read', 'write']],
        ['title' => 'Categories', 'name' => 'categories', 'permissions' => ['read', 'write']],
        ['title' => 'Courses', 'name' => 'courses', 'permissions' => ['read', 'write']],
        ['title' => 'Course Upsells', 'name' => 'course-upsells', 'permissions' => ['read', 'write']],
        ['title' => 'Homepage Settings', 'name' => 'homepage', 'permissions' => ['read', 'write']],
        ['title' => 'Events', 'name' => 'events', 'permissions' => ['read', 'write']],
        ['title' => 'Ads', 'name' => 'ads', 'permissions' => ['read', 'write']],
        ['title' => 'News', 'name' => 'news', 'permissions' => ['read', 'write']],
        ['title' => 'Push Notifications', 'name' => 'push-notifications', 'permissions' => ['read', 'write']],
        ['title' => 'FAQ', 'name' => 'faq', 'permissions' => ['read', 'write']],
        ['title' => 'Feedback', 'name' => 'feedback', 'permissions' => ['read', 'write']],
        ['title' => 'Sendlane', 'name' => 'sendlane', 'permissions' => ['read', 'write']],
        ['title' => 'Tools', 'name' => 'tools', 'permissions' => ['read', 'write']],
        ['title' => 'Stats', 'name' => 'stats', 'permissions' => ['read']],
        ['title' => 'SEO', 'name' => 'seo', 'permissions' => ['read', 'write']],
        ['title' => 'Workflows', 'name' => 'workflows', 'permissions' => ['read', 'write']],
        ['title' => 'User Roles', 'name' => 'user-roles', 'permissions' => ['read', 'write']],
        ['title' => 'General Settings', 'name' => 'general-settings', 'permissions' => ['read', 'write']],
        ['title' => 'Surveys', 'name' => 'surveys', 'permissions' => ['read', 'write']],
        ['title' => 'Test Results', 'name' => 'test-results', 'permissions' => ['read']],
    ];

    protected $table = 'roles';

    protected $guarded = ['id'];

    protected $fillable = ['title', 'permissions'];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function hasAccess($area, $permissions)
    {
        $permissions = is_array($permissions) ? $permissions : [$permissions];

        return array_intersect($permissions, $this->permissions[$area] ?? []) == $permissions;
    }
}
