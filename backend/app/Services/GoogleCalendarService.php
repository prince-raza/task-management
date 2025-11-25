<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;

class GoogleCalendarService
{
    protected Calendar $service;

    public function __construct()
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/google-service-account.json'));
        $client->addScope(Calendar::CALENDAR);

        $this->service = new Calendar($client);
    }

    /**
     * Add an event to Google Calendar
     *
     * @param string $summary
     * @param string $description
     * @param string $startDateTime 'YYYY-MM-DDTHH:MM:SS'
     * @param string $endDateTime 'YYYY-MM-DDTHH:MM:SS'
     */
    public function addEvent(string $summary, string $description, string $date)
    {
        $event = new Event([
            'summary' => $summary,
            'description' => $description,
            'start' => [
                'date' => $date,
            ],
            'end' => [
                'date' => $date,
            ],
        ]);

        $calendarId = 'rprinceeme@gmail.com';

        return $this->service->events->insert($calendarId, $event);
    }
}
