<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Models\UserActivityLog;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function logActivity($user, $type = '', $action = '', $remarks = '') {
        UserActivityLog::create([
            'user_id' => $user,
            'type' => $type,
            'action' => $action,
            'remarks' => $remarks
        ]);
    }
}
