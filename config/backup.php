<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Backup Path
    |--------------------------------------------------------------------------
    |
    | The default path where backups will be saved. You can override this
    | by setting BACKUP_PATH in your .env file.
    |
    */
    'path' => env('BACKUP_PATH', storage_path('app/backups')),
];
