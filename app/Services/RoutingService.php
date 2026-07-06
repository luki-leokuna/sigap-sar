<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

class RoutingService
{
    public function __construct(private readonly DistanceService $distance) {}

    public function route(float $fromLat, float $fromLng, float $toLat, float $toLng): array
    {
        try {
            $url = sprintf(
                'https://router.project-osrm.org/route/v1/driving/%F,%F;%F,%F',
                $fromLng,
                $fromLat,
                $toLng,
                $toLat,
            );

            $response = Http::timeout(6)->get($url, [
                'overview' => 'full',
                'geometries' => 'geojson',
                'steps' => 'true',
            ]);

            if ($response->ok() && ($response->json('routes.0') !== null)) {
                $route = $response->json('routes.0');
                $rawSteps = $route['legs'][0]['steps'] ?? [];
                $formattedSteps = [];
                foreach ($rawSteps as $step) {
                    $maneuver = $step['maneuver'] ?? [];
                    $formattedSteps[] = [
                        'type' => $maneuver['type'] ?? 'continue',
                        'modifier' => $maneuver['modifier'] ?? 'straight',
                        'location' => $maneuver['location'] ?? null,
                        'name' => !empty($step['name']) ? $step['name'] : 'Jalan Raya',
                        'distance' => (float) ($step['distance'] ?? 0),
                        'duration' => (int) ($step['duration'] ?? 0),
                    ];
                }

                return [
                    'success' => true,
                    'source' => 'osrm',
                    'distance_meters' => (float) $route['distance'],
                    'duration_seconds' => (int) $route['duration'],
                    'geometry' => $route['geometry'],
                    'steps' => $formattedSteps,
                ];
            }
        } catch (Throwable) {
            //
        }

        $distanceMeters = $this->distance->haversineMeters($fromLat, $fromLng, $toLat, $toLng);

        return [
            'success' => false,
            'source' => 'fallback',
            'distance_meters' => $distanceMeters,
            'duration_seconds' => $this->distance->roughDurationSeconds($distanceMeters),
            'geometry' => [
                'type' => 'LineString',
                'coordinates' => [
                    [$fromLng, $fromLat],
                    [$toLng, $toLat],
                ],
            ],
            'steps' => [],
        ];
    }
}
