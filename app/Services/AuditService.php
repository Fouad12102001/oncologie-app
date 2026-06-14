class AuditService
{
    public static function log(
        $action,
        $module,
        $recordId = null,
        $old = null,
        $new = null
    ){
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'module' => $module,
            'record_id' => $recordId,
            'old_values' => $old,
            'new_values' => $new,
            'ip' => request()->ip()
        ]);
    }
}