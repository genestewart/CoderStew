<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BookingController extends Controller
{
    /**
     * Get available booking slots.
     */
    public function availability(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', now()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->addDays(30)->format('Y-m-d'));
            $serviceId = $request->get('service_id');

            // Get access token
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                return response()->json([
                    'message' => 'Unable to connect to booking service',
                ], 503);
            }

            // Get availability from Microsoft Bookings
            $availability = $this->getBookingAvailability($accessToken, $startDate, $endDate, $serviceId);

            if ($availability === null) {
                return response()->json([
                    'message' => 'Unable to fetch availability',
                ], 503);
            }

            return response()->json([
                'data' => $availability,
                'meta' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'service_id' => $serviceId,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Booking availability error', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Sorry, there was an error fetching availability. Please try again.',
            ], 500);
        }
    }

    /**
     * Schedule a new booking.
     */
    public function schedule(Request $request): JsonResponse
    {
        $request->validate([
            'service_id' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            // Get access token
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                return response()->json([
                    'message' => 'Unable to connect to booking service',
                ], 503);
            }

            // Create booking in Microsoft Bookings
            $booking = $this->createBooking($accessToken, [
                'serviceId' => $request->service_id,
                'startTime' => $request->start_time,
                'endTime' => $request->end_time,
                'customerName' => $request->customer_name,
                'customerEmail' => $request->customer_email,
                'customerPhone' => $request->customer_phone,
                'notes' => $request->notes,
            ]);

            if (!$booking) {
                return response()->json([
                    'message' => 'Unable to create booking. Please try again.',
                ], 503);
            }

            // Log the booking
            Log::info('New booking created', [
                'booking_id' => $booking['id'] ?? null,
                'customer_email' => $request->customer_email,
                'service_id' => $request->service_id,
                'start_time' => $request->start_time,
            ]);

            return response()->json([
                'message' => 'Your booking has been confirmed! You will receive a confirmation email shortly.',
                'data' => [
                    'booking_id' => $booking['id'] ?? null,
                    'status' => 'confirmed',
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('Booking creation error', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Sorry, there was an error creating your booking. Please try again.',
            ], 500);
        }
    }

    /**
     * Get Microsoft Graph access token.
     */
    private function getAccessToken(): ?string
    {
        $cacheKey = 'microsoft_bookings_token';
        
        // Try to get cached token
        $cachedToken = Cache::get($cacheKey);
        if ($cachedToken) {
            return $cachedToken;
        }

        $clientId = config('services.microsoft.client_id');
        $clientSecret = config('services.microsoft.client_secret');
        $tenantId = config('services.microsoft.tenant_id');

        if (!$clientId || !$clientSecret || !$tenantId) {
            Log::error('Microsoft Bookings configuration missing');
            return null;
        }

        try {
            $response = Http::asForm()->post("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token", [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'scope' => 'https://graph.microsoft.com/.default',
                'grant_type' => 'client_credentials',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $accessToken = $data['access_token'];
                $expiresIn = $data['expires_in'] - 300; // Cache for 5 minutes less than actual expiry

                Cache::put($cacheKey, $accessToken, $expiresIn);
                return $accessToken;
            }

            Log::error('Microsoft token request failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

        } catch (\Exception $e) {
            Log::error('Microsoft token request error', [
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Get booking availability from Microsoft Bookings.
     */
    private function getBookingAvailability(string $accessToken, string $startDate, string $endDate, ?string $serviceId): ?array
    {
        try {
            $businessId = config('services.microsoft.bookings_business_id');
            if (!$businessId) {
                Log::error('Microsoft Bookings business ID not configured');
                return null;
            }

            $url = "https://graph.microsoft.com/v1.0/solutions/bookingBusinesses/{$businessId}/getStaffAvailability";
            
            $payload = [
                'staffIds' => [], // Empty for all staff
                'startTime' => [
                    'dateTime' => $startDate . 'T00:00:00.000Z',
                    'timeZone' => 'UTC',
                ],
                'endTime' => [
                    'dateTime' => $endDate . 'T23:59:59.999Z',
                    'timeZone' => 'UTC',
                ],
            ];

            if ($serviceId) {
                $payload['serviceIds'] = [$serviceId];
            }

            $response = Http::withToken($accessToken)
                ->post($url, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Microsoft Bookings availability request failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

        } catch (\Exception $e) {
            Log::error('Microsoft Bookings availability error', [
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Create a booking in Microsoft Bookings.
     */
    private function createBooking(string $accessToken, array $bookingData): ?array
    {
        try {
            $businessId = config('services.microsoft.bookings_business_id');
            if (!$businessId) {
                Log::error('Microsoft Bookings business ID not configured');
                return null;
            }

            $url = "https://graph.microsoft.com/v1.0/solutions/bookingBusinesses/{$businessId}/appointments";
            
            $payload = [
                'serviceId' => $bookingData['serviceId'],
                'startTime' => [
                    'dateTime' => $bookingData['startTime'],
                    'timeZone' => 'UTC',
                ],
                'endTime' => [
                    'dateTime' => $bookingData['endTime'],
                    'timeZone' => 'UTC',
                ],
                'customers' => [
                    [
                        'name' => $bookingData['customerName'],
                        'emailAddress' => $bookingData['customerEmail'],
                        'phone' => $bookingData['customerPhone'] ?? '',
                        'notes' => $bookingData['notes'] ?? '',
                    ],
                ],
            ];

            $response = Http::withToken($accessToken)
                ->post($url, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Microsoft Bookings appointment creation failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

        } catch (\Exception $e) {
            Log::error('Microsoft Bookings appointment creation error', [
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }
}
