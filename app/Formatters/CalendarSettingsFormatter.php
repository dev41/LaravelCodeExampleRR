<?php

namespace App\Formatters;

class CalendarSettingsFormatter extends Formatter
{
    public static function minutesTo12Hours($minutes): string
    {
        $isAM = $minutes < 720;
        $postfix = $isAM ? ' am' : ' pm';

        $hours = ($minutes / 60) % 12;
        $hourMinutes = $minutes % 60;

        if ($minutes === 0 || ($hours === 0 && !$isAM)) {
            $hours = 12;
        }

        if ($hours < 10) {
            $hours = '0' . $hours;
        }
        if ($hourMinutes < 10) {
            $hourMinutes = '0' . $hourMinutes;
        }

        return $hours . ':' . $hourMinutes . $postfix;
    }

    public static function responseSlotsList(array $slotsData, int $length): array
    {
        foreach ($slotsData as $day => $slots) {
            foreach ($slots as $slotKey => $slotMinutes) {
                $slotsData[$day][$slotKey] = self::minutesTo12Hours($slotMinutes) . ' - ' .
                    self::minutesTo12Hours($slotMinutes + $length);
            }
        }

        foreach ($slotsData as $day => $slots) {
            $slotsData[$day] = implode(', ', $slots);
        }

        return $slotsData;
    }

    public static function responseObject(array $settingsData): array
    {
        $settingsData['slots'] = self::responseSlotsList($settingsData['slots'], $settingsData['viewing_length']);

        return [
            'recurring' => [
                'confirmation' => $settingsData['confirmation'] ?? 0,
                'calendarVisibilityLimit' => $settingsData['visibility_limit'] ?? 0,
                'timeLockedBeforeBooking' => $settingsData['before_booking_limit'] ?? 0,
            ],

            'viewings' => [
                'sameTime' => false,
                'length' => $settingsData['viewing_length'] ?? 0,
                'break' => $settingsData['break_between_viewing'] ?? 0,
                'repeat' => $settingsData['repeat_period'] ?? 0,
                'duration' => $settingsData['format_duration'] ?? '',
                'durationNoLimit' => !($settingsData['format_duration'] ?? ''),
                'slots' => $settingsData['slots'] ?? [],
                'days' => $settingsData['days'] ?? [],
            ],
        ];
    }
}
