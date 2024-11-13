<?php

return [
    'resources' => [
        'label' => 'Log',
        'plural_label' => 'Log',
        'navigation_item' => true,
        'navigation_group' => 'Access',
        'navigation_icon' => 'heroicon-o-shield-check',
        'navigation_sort' => 50,
        'default_sort_column' => 'id',
        'default_sort_direction' => 'desc',
        'navigation_count_badge' => false,
        'resource' => \Rmsramos\Activitylog\Resources\ActivitylogResource::class,
    ],
    'datetime_format' => 'd/m/Y H:i:s',
];
