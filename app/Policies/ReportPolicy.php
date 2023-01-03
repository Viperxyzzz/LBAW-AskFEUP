<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Report;

use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
    use HandlesAuthorization;

    /**
     * Check if a user can dismiss reports.
     * Only mods and admins can delete reports
     * @param User $user User to check.
     * @param Report $report Report to check.
     * @return bool True is the user can dismiss the report, false otherwise.
     */
    public function delete(User $user, Report $report)
    {
      return $user->is_mod();
    }
}
