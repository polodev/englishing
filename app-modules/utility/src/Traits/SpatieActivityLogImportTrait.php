<?php

namespace Modules\Utility\Traits;

trait SpatieActivityLogImportTrait
{

  // activity logs
  use \Spatie\Activitylog\Traits\LogsActivity;
  protected static $recordEvents = ['updated', 'deleted'];

  public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
  {
    return \Spatie\Activitylog\LogOptions::defaults()
      ->dontSubmitEmptyLogs()
      ->logUnguarded()
      ->logOnlyDirty();
  }
}
