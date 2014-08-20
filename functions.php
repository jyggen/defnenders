<?php
function formatBytes($bytes = 0, $decimals = 0)
{
    $quant = [
        'TB' => 1099511627776,
        'GB' => 1073741824,
        'MB' => 1048576,
        'kB' => 1024,
        'B'  => 1,
    ];

    foreach ($quant as $unit => $mag) {
        if (doubleval($bytes) >= $mag) {
            return sprintf('%01.'.$decimals.'f', ($bytes / $mag)).' '.$unit;
        }
    }

    return false;
}
