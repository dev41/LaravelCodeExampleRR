<?php

namespace App\Services;

use App\Formatters\CalendarSettingsFormatter;
use App\Formatters\ViewingEventsFormatter;
use App\Http\Requests\Calendar\CheckSettingsUpdateRequest;
use App\Http\Requests\Calendar\CreateEventRequest;
use App\Http\Requests\Calendar\SettingsUpdateRequest;
use App\Http\Requests\Calendar\UpdateViewingStatusRequest;
use App\Models\CalendarSettings;
use App\Models\CalendarSettingsDays;
use App\Models\Event;
use App\Models\Room;
use App\Models\User;
use App\Repositories\CalendarSettingsRepository;
use App\Repositories\EventRepository;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class CalendarService extends Service
{
    public function settingsUpdate(SettingsUpdateRequest $request): CalendarSettings
    {
        try {
            DB::beginTransaction();

            $calendarSettings = CalendarSettings::find(auth()->id());

            if (!$calendarSettings) {
                $calendarSettings = new CalendarSettings();
                $calendarSettings->id = auth()->id();
            } else {
                CalendarSettingsDays::where(['calendar_setting_id' => $calendarSettings->id])->delete();
            }

            $calendarSettings->fill($request->toArray());
            $calendarSettings->save();

            $days = [];
            foreach ($request->days as $dayData) {
                $days[] = [
                    'calendar_setting_id' => $calendarSettings->id,
                    'day' => array_search(strtolower($dayData['day']), CalendarSettingsDays::DAY_NAMES),
                    'time_from' => $dayData['from'] ?: null,
                    'time_to' => $dayData['to'] ?: null,
                ];
            }

            CalendarSettingsDays::insert($days);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }

        return $calendarSettings;
    }

    public function createEvent(CreateEventRequest $request)
    {
        $room = Room::find($request->roomId);

        /** @var CalendarSettings $settings */
        $settings = CalendarSettings::find($room->creator_id);

        $event = new Event();
        $event->creator_id = $room->creator_id;
        $event->start_at = date('Y-m-d H:i:s', $request->date / 1000);
        $event->user_id = auth()->id();
        $event->room_id = $request->roomId;
        $event->message_id = $request->messageId;
        $event->length = $settings->viewing_length;
        $event->type = Event::TYPE_VIEWING;
        $event->save();

        return $event;
    }

    public function updateViewingStatus(UpdateViewingStatusRequest $request)
    {
        $events = array_combine(array_column($request->events, 'id'), array_column($request->events, 'status'));

        $acceptEvents = array_filter($events, function ($status) {
            return $status != Event::STATUS_CANCELED;
        });

        $cancelEvents = array_filter($events, function ($status) {
            return $status == Event::STATUS_CANCELED;
        });

        if ($acceptEvents) {
            Event::whereIn('id', array_keys($acceptEvents))->update(['status' => Event::STATUS_APPROVED]);
        }
        if ($cancelEvents) {
            Event::whereIn('id', array_keys($cancelEvents))->update(['status' => Event::STATUS_CANCELED]);
        }
    }

    public function getEvents()
    {
        /** @var User $user */
        $user = auth()->user();
        return $user->isSuperUser() ? $this->_getSUEvents($user) : $this->_getRSEvents($user);
    }

    private function _getSUEvents(User $user): array
    {
        $calendarSettings = CalendarSettings::find($user->id);
        $events = EventRepository::getSUViewingEvents($user->id);

        $events = $this->setConflictEvents($events, $user);

        return ViewingEventsFormatter::responseObject($events, $calendarSettings);
    }

    private function _getRSEvents(User $user): array
    {
        $events = EventRepository::getRSViewingEvents($user->id);

        $suEvents = [];
        foreach ($events as $event) {
            if (!isset($suEvents[$event->event_su_id])) {
                $suEvents[$event->event_su_id] = [];
            }

            $suEvents[$event->event_su_id][] = $event;
        }

        $suIds = array_keys($suEvents);

        $calendarSettings = CalendarSettings::whereIn('id', $suIds)->get()->all();

        $formatEvents = [];
        foreach ($calendarSettings as $setting) {
            $formatEvents = array_merge($formatEvents, ViewingEventsFormatter::responseObject($suEvents[$setting->id], $setting, true));
        }

        return $formatEvents;
    }

    public function setConflictEvents(array $events, User $user): array
    {
        $availableSlots = $this->getAvailableSlots($user);
        $slotsTS = $availableSlots['slotsTS'] ?? [];
        /** @var CalendarSettings $settings */
        $settings = $availableSlots['settings'];

        foreach ($events as $event) {

            $event->conflict = Event::CONFLICT_OK;

            if (!in_array(strtotime($event->event_date_ts), $slotsTS)) {
                $event->conflict |= Event::CONFLICT_INCOMPATIBLE_SLOT_START;
            }

            if ($event->event_length !== $settings->viewing_length) {
                $event->conflict |= Event::CONFLICT_INCOMPATIBLE_SLOT_LENGTH;
            }
        }

        return $events;
    }

    public function getAvailableSlots(User $user)
    {
        /** @var CalendarSettings $calendarSettings */
        $calendarSettings = $user->calendarSettings()->first();

        $timeStart = new \DateTime();

        switch ($calendarSettings->before_booking_limit) {
            case CalendarSettings::BEFORE_BOOKING_LIMIT_1HOUR:
                $timeStart->modify('+1 hours');
                break;
            case CalendarSettings::BEFORE_BOOKING_LIMIT_2HOUR:
                $timeStart->modify('+2 hours');
                break;
            case CalendarSettings::BEFORE_BOOKING_LIMIT_4HOUR:
                $timeStart->modify('+4 hours');
                break;
            case CalendarSettings::BEFORE_BOOKING_LIMIT_6HOUR:
                $timeStart->modify('+6 hours');
                break;
            default: throw new InvalidParameterException('SU settings: before_booking_limit');
        }

        $timeEnd = new \DateTime();

        switch ($calendarSettings->visibility_limit) {
            case CalendarSettings::VISIBILITY_LIMIT_1WEEK:
                $timeEnd->modify('+1 week');
                break;
            case CalendarSettings::VISIBILITY_LIMIT_2WEEK:
                $timeEnd->modify('+2 week');
                break;
            case CalendarSettings::VISIBILITY_LIMIT_3WEEK:
                $timeEnd->modify('+3 week');
                break;
            case CalendarSettings::VISIBILITY_LIMIT_4WEEK:
                $timeEnd->modify('+4 week');
                break;
            default: throw new InvalidParameterException('SU settings: visibility_limit');
        }

        $from = clone $timeStart;

        $to = clone $timeStart;
        $to->modify('+1 day')->setTime(0, 0);

        $settingDays = $calendarSettings->days()->get()->toArray();
        $availableWeekSlots = $this->getAvailableSlotsByDays($settingDays, $calendarSettings->viewing_length);

        $availableDaysSlots = [];
        $dates = [];
        $slotsTS = [];

        while ($to->getTimestamp() < $timeEnd->getTimestamp()) {

            $dayNumber = (int) $from->format('N');
            $dayMinutesStart = ($from->format('G') * 60) + (int) $from->format('i');

            $settingsDaySlots = $availableWeekSlots[$dayNumber];

            if (!$settingsDaySlots) {
                $from->modify('+1 day')->setTime(0, 0);
                $to->modify('+1 day');

                continue;
            }

            $daySlots = [
                'date' => $from->getTimestamp(),
                'slots' => [],
            ];

            foreach ($settingsDaySlots as $minutes) {
                if ($dayMinutesStart > $minutes) {
                    continue;
                }

                $daySlots['slots'][] = [
                    'minutes' => $minutes,
                    'value' => CalendarSettingsFormatter::minutesTo12Hours($minutes),
                ];

                $slotsTS[] = $from->getTimestamp() + $minutes * 60;
            }

            if (!empty($daySlots) && !empty($daySlots['slots'])) {
                $availableDaysSlots[] = $daySlots;
                $dates[] = $from->getTimestamp();
            }

            $from->modify('+1 day')->setTime(0, 0);
            $to->modify('+1 day');
        }

        return [
            'dates' => $dates,
            'slots' => $availableDaysSlots,
            'slotsTS' => $slotsTS,
            'settings' => $calendarSettings,
        ];
    }

    public function checkSettingsUpdate(CheckSettingsUpdateRequest $request)
    {
        $slots = $this->getAvailableSlotsByDays($request->days, $request->viewing_length);
        return CalendarSettingsFormatter::responseSlotsList($slots, $request->viewing_length);
    }

    public function getSettings(int $userId): array
    {
        $settingsData = CalendarSettingsRepository::getSettings($userId);
        $settingsData['slots'] = $this->getAvailableSlotsByDays($settingsData['days'], $settingsData['viewing_length']);

        return CalendarSettingsFormatter::responseObject($settingsData);
    }

    public function getAvailableSlotsByDays(array $days, int $length): array
    {
        $availableSlots = [];

        foreach ($days as $day) {

            $day = (array) $day;

            $dayKey = $day['day'];
            $from = (int) ($day['from'] ?? $day['time_from'] ?? 0);
            $to = (int) ($day['to'] ?? $day['time_to'] ?? 0);

            $availableSlots[$dayKey] = [];

            if ((!$from && !$to) || ($from + $length > $to)) {
                continue;
            }

            for ($slotMinute = $from; $slotMinute < $to; $slotMinute += $length) {
                $availableSlots[$dayKey][] = $slotMinute;
            }
        }

        return $availableSlots;
    }
}
