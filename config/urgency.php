<?php

return [
    // urgency key => [label, color (hex or tailwind class), priority]
    'levels' => [
        // 'name' is the canonical color name/value stored when user chooses urgency default
        'very_urgent' => ['label' => 'Very Urgent', 'color' => '#DC2626', 'class' => 'bg-red-500', 'priority' => 4, 'name' => 'red'],
        'urgent' => ['label' => 'Urgent', 'color' => '#F97316', 'class' => 'bg-orange-500', 'priority' => 3, 'name' => 'orange'],
        'normal' => ['label' => 'Normal', 'color' => '#F59E0B', 'class' => 'bg-yellow-400', 'priority' => 2, 'name' => 'yellow'],
        'least_urgent' => ['label' => 'Least Urgent', 'color' => '#22C55E', 'class' => 'bg-green-500', 'priority' => 1, 'name' => 'green'],
    ],

    // default ordering for most/least urgent
    'priority_sort' => array_column(array_reverse(array_keys(["very_urgent","urgent","normal","least_urgent"])), null),
];
