<?php

namespace Efemer\Royalty\Notifications;

use Efemer\Finance\Factory\Models\FinanceModel;
use Efemer\Shomadhan\Factory\Models\ActivityLog;
use Efemer\Shomadhan\Notifications\NotificationVia;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FinanceNotify extends Notification implements ShouldQueue {

    use Queueable;

    public $model;
    public $activity;

    public function __construct(FinanceModel $model = null, ActivityLog $activity_log = null) {
        if($model) {
            $model->postRead();
            $this->model = $model;
        }
        $this->activity = $activity_log;
    }

    function via() {
        if ($this->activity) {
            return $this->activity->getValue('notify_via');
        }
        return [ 'slack' ];
    }


}
