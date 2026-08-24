<?php
namespace App\Http\Resources\Traits;

use Carbon\Carbon;

trait FormatDate
{
    protected function formatDate($date): ?string
    {
        if (empty($date)) {
            return null;
        }

        if ($date instanceof \DateTime) {
            return $date->toISOString();
        }

        if (is_string($date)) {
            try {
                return Carbon::parse($date)->toISOString();
            } catch (\Exception $e) {
                return $date;
            }
        }

        return null;
    }
}