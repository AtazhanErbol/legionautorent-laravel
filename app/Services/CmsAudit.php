<?php

namespace App\Services;

use App\Models\LogEntry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CmsAudit
{
    public static function record(Model $model, int $action): void
    {
        $user = auth()->user();
        if (! $user || $model instanceof LogEntry) {
            return;
        }
        $label = class_basename($model);
        $name = $model->name ?? $model->title ?? $model->username ?? $model->id;
        $fields = array_diff(array_keys($model->getChanges()), ['password', 'remember_token']);
        DB::table('django_admin_log')->insert(['action_time' => now(), 'user_id' => $user->id, 'content_type_id' => method_exists($model, 'contentTypeId') ? $model->contentTypeId() : null, 'object_id' => (string) $model->id, 'object_repr' => mb_substr($label.': '.$name, 0, 200), 'action_flag' => $action, 'change_message' => json_encode(['fields' => array_values($fields)], JSON_UNESCAPED_UNICODE)]);
    }
}
