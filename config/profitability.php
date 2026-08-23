<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Margin color thresholds
    |--------------------------------------------------------------------------
    |
    | Percentage thresholds used to color-code the margin badge on delivery
    | profitability views. Below "warning" => red (loss), between "warning"
    | and "good" => amber, at or above "good" => green.
    |
    */

    'margin_thresholds' => [
        'good' => 10.0,
        'warning' => 0.0,
    ],

];
