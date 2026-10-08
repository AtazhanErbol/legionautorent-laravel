<?php

return [
    'temporary_file_upload' => [
        'rules' => ['file', 'max:20480'],
        'preview_mimes' => ['png', 'gif', 'bmp', 'svg', 'wav', 'mp4', 'mov', 'avi', 'wmv', 'mp3', 'm4a', 'jpg', 'jpeg', 'webp', 'avif'],
    ],
];
