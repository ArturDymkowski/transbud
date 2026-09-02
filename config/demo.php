<?php

return [

    // How many new records of a given type one IP may create within the decay
    // window before being throttled (fast-flood protection).
    'record_creation' => [
        'max_attempts' => (int) env('DEMO_RECORD_CREATION_MAX_ATTEMPTS', 10),
        'decay_minutes' => (int) env('DEMO_RECORD_CREATION_DECAY_MINUTES', 10),
    ],

    // Hard cap on total rows per model, independent of the throttle above — a
    // backstop against slow, patient accumulation over the hours between resets.
    'max_records' => (int) env('DEMO_MAX_RECORDS', 300),

    // Combined size cap (bytes) for everything stored on the driver_documents/
    // delivery_documents disks, checked before accepting a new upload.
    'max_disk_bytes' => (int) env('DEMO_MAX_DISK_MB', 200) * 1024 * 1024,

];
