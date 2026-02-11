<?php

return [
    'audit_logs' => env('FEATURE_AUDIT_LOGS', true),
    'customers' => env('FEATURE_CUSTOMERS', true),
    'manufacturers' => env('FEATURE_MANUFACTURERS', true),
    'notifications' => env('FEATURE_NOTIFICATIONS', true),
    'sales_approval' => env('FEATURE_SALES_APPROVAL', true),
    'note_templates' => env('FEATURE_NOTE_TEMPLATES', true),
];
