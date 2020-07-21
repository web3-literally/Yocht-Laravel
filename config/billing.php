<?php

return [
    'vessel' => [
        // Cost in USD
        'free_vessels_count' => env('FREE_VESSELS', 1),
        'extra_vessel_cost' => 15,
        'free_tenders_count' => env('FREE_TENDERS', 1),
        'extra_tender_cost' => 5,
        'free_crew_members_count' => env('FREE_CREW_MEMBERS', 5),
        'extra_crew_team_cost' => 10,
        'extra_view_private_job_details_cost' => 3,
        'free_service_areas_count' => env('FREE_SERVICE_AREAS', 15)
    ]
];