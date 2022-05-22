<?php

namespace App\Formatters;

use App\Models\CalendarSettings;
use App\Repositories\SearcherMediaFileRepository;

class ViewingEventsFormatter extends Formatter
{
    public static function responseObject(array $eventsData, CalendarSettings $calendarSettings, bool $rsEvents = false): array
    {
        $events = [];

        foreach ($eventsData as $event) {

            if (!isset($events[$event->event_date])) {
                $roomCover = $event->room_cover ? explode('|', $event->room_cover) : null;

                $time = strtotime($event->event_date);
                $hours = date('H', $time);
                $minutes = date('i', $time);
                $eventMinutes = $hours * 60 + $minutes;

                $eventTime = CalendarSettingsFormatter::minutesTo12Hours($eventMinutes) . ' - ' .
                    CalendarSettingsFormatter::minutesTo12Hours($eventMinutes + $calendarSettings->viewing_length);

                $events[$event->event_date] = [
                    'title' => $event->room_title,
                    'date' => $event->event_date,
                    'eventData' => [
                        'time' => $eventTime,
                        'roomId' => $event->room_id,
                        'conflict' => $event->conflict,
                        'roomLetter' => $event->room_letter,
                        'roomTitle' => $event->room_title,
                        'propertyTitle' => $event->property_title,
                        'address' => $event->property_address,
                        'eventType' => $event->event_type,
                        'viewings' => [],
                        'chatLink' => '',
                    ],
                ];

                if ($roomCover) {
                    $events[$event->event_date]['eventData']['roomCover'] = RoomMediaFilesFormatter::resolveImageFilePath($event->room_id, reset($roomCover));
                }
            }

            if (!$rsEvents) {
                $events[$event->event_date]['eventData']['viewings'][] = [
                    'accept' => false,
                    'event_date_ts' => strtotime($event->event_date_ts) * 1000,
                    'event_id' => $event->event_id,
                    'message_id' => $event->message_id,
                    'chat_id' => $event->chat_id,
                    'status' => $event->event_status,
                    'name' => $event->searcher_first_name . ' ' . $event->searcher_last_name,
                    'phone' => $event->searcher_phone,
                    'avatar' => $event->searcher_avatar ?
                        '/' . SearcherMediaFileRepository::getImageFilePath($event->searcher_id, $event->searcher_avatar) : null,
                    'searcherId' => $event->searcher_id,
                ];
            }
        }

        return array_values($events);
    }
}

