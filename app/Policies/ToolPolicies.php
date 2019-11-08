<?php
namespace App\Policies;

use App\Models\CourseTool;

class ToolPolicies
{
    public function nicheDetective($user)
    {
        return $this->hasAccessTo($user, 'Niche Detective');
    }

    public function launchpad($user)
    {
        return $this->hasAccessTo($user, 'Launchpad');
    }

    public function dlaCreator($user)
    {
        return $this->hasAccessTo($user, 'Digital Lead Academy Creator');
    }

    public function businessBuilder($user)
    {
        return $this->hasAccessTo($user, 'Business Builder');
    }
    
    public function businessBuilderPA($user)
    {
        return $this->hasAccessTo($user, 'Business Builder Publish Academy');
    }
    
    public function businessBuilderDpe($user)
    {
        return $this->hasAccessTo($user, 'Business Builder Digital Profit Engine');
    }

    private function hasAccessTo($user, $toolName)
    {
        if (! $tool = CourseTool::whereToolName($toolName)->first()) {
            return false;
        }

        return (bool) $user->courses()->whereNull('cancelled_at')->find($tool->course_id);
    }
}
