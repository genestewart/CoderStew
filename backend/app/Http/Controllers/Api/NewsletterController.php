<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsletterController extends Controller
{
    /**
     * Subscribe to newsletter.
     */
    public function subscribe(Request $request): JsonResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:100',
            'preferences' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Check if subscriber already exists
            $existingSubscriber = Newsletter::where('email', $request->email)->first();

            if ($existingSubscriber) {
                if ($existingSubscriber->isActive()) {
                    return response()->json([
                        'message' => 'You are already subscribed to our newsletter.',
                        'data' => [
                            'status' => 'already_subscribed',
                            'email' => $existingSubscriber->email,
                        ],
                    ]);
                } else {
                    // Reactivate existing subscriber
                    $existingSubscriber->resubscribe();
                    
                    // Update Listmonk
                    $this->updateListmonkSubscriber($existingSubscriber);
                    
                    return response()->json([
                        'message' => 'Welcome back! You have been resubscribed to our newsletter.',
                        'data' => [
                            'status' => 'resubscribed',
                            'email' => $existingSubscriber->email,
                        ],
                    ]);
                }
            }

            // Create new subscriber
            $subscriber = Newsletter::create([
                'email' => $request->email,
                'name' => $request->name,
                'status' => 'pending',
                'source' => $request->source ?? 'website',
                'preferences' => $request->preferences ?? [],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Add to Listmonk
            $listmonkId = $this->addToListmonk($subscriber);
            if ($listmonkId) {
                $subscriber->update(['listmonk_subscriber_id' => $listmonkId]);
            }

            // Log the subscription
            Log::info('New newsletter subscription', [
                'subscriber_id' => $subscriber->id,
                'email' => $subscriber->email,
                'source' => $subscriber->source,
            ]);

            // TODO: Send verification email
            // TODO: Add to welcome email sequence

            return response()->json([
                'message' => 'Thank you for subscribing! Please check your email to confirm your subscription.',
                'data' => [
                    'status' => 'pending_verification',
                    'email' => $subscriber->email,
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('Newsletter subscription failed', [
                'error' => $e->getMessage(),
                'email' => $request->email,
            ]);

            return response()->json([
                'message' => 'Sorry, there was an error subscribing to our newsletter. Please try again.',
            ], 500);
        }
    }

    /**
     * Unsubscribe from newsletter.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'token' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $subscriber = Newsletter::where('email', $request->email)->first();

            if (!$subscriber) {
                return response()->json([
                    'message' => 'Email address not found in our newsletter list.',
                ], 404);
            }

            if ($subscriber->isUnsubscribed()) {
                return response()->json([
                    'message' => 'You are already unsubscribed from our newsletter.',
                    'data' => [
                        'status' => 'already_unsubscribed',
                        'email' => $subscriber->email,
                    ],
                ]);
            }

            // Unsubscribe
            $subscriber->unsubscribe();

            // Update Listmonk
            $this->updateListmonkSubscriber($subscriber);

            // Log the unsubscription
            Log::info('Newsletter unsubscription', [
                'subscriber_id' => $subscriber->id,
                'email' => $subscriber->email,
            ]);

            return response()->json([
                'message' => 'You have been successfully unsubscribed from our newsletter.',
                'data' => [
                    'status' => 'unsubscribed',
                    'email' => $subscriber->email,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Newsletter unsubscription failed', [
                'error' => $e->getMessage(),
                'email' => $request->email,
            ]);

            return response()->json([
                'message' => 'Sorry, there was an error unsubscribing. Please try again.',
            ], 500);
        }
    }

    /**
     * Add subscriber to Listmonk.
     */
    private function addToListmonk(Newsletter $subscriber): ?int
    {
        $listmonkUrl = config('services.listmonk.url');
        $username = config('services.listmonk.username');
        $password = config('services.listmonk.password');

        if (!$listmonkUrl || !$username || !$password) {
            Log::warning('Listmonk configuration missing');
            return null;
        }

        try {
            $response = Http::withBasicAuth($username, $password)
                ->post("{$listmonkUrl}/api/subscribers", [
                    'email' => $subscriber->email,
                    'name' => $subscriber->name ?: '',
                    'status' => 'enabled',
                    'lists' => [1], // Default list ID
                    'attribs' => [
                        'source' => $subscriber->source,
                        'subscribed_at' => $subscriber->created_at->toISOString(),
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data']['id'] ?? null;
            }

            Log::error('Listmonk subscription failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

        } catch (\Exception $e) {
            Log::error('Listmonk API error', [
                'error' => $e->getMessage(),
                'email' => $subscriber->email,
            ]);
        }

        return null;
    }

    /**
     * Update subscriber in Listmonk.
     */
    private function updateListmonkSubscriber(Newsletter $subscriber): bool
    {
        if (!$subscriber->listmonk_subscriber_id) {
            return false;
        }

        $listmonkUrl = config('services.listmonk.url');
        $username = config('services.listmonk.username');
        $password = config('services.listmonk.password');

        if (!$listmonkUrl || !$username || !$password) {
            return false;
        }

        try {
            $status = $subscriber->isActive() ? 'enabled' : 'disabled';

            $response = Http::withBasicAuth($username, $password)
                ->put("{$listmonkUrl}/api/subscribers/{$subscriber->listmonk_subscriber_id}", [
                    'email' => $subscriber->email,
                    'name' => $subscriber->name ?: '',
                    'status' => $status,
                    'lists' => [1], // Default list ID
                ]);

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('Listmonk update error', [
                'error' => $e->getMessage(),
                'subscriber_id' => $subscriber->listmonk_subscriber_id,
            ]);
        }

        return false;
    }
}
