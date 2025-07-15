<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Store a new contact form submission.
     */
    public function store(Request $request): JsonResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'type' => 'nullable|string|in:general,project,support,partnership',
            'recaptcha_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Verify reCAPTCHA
        if (!$this->verifyRecaptcha($request->recaptcha_token)) {
            return response()->json([
                'message' => 'reCAPTCHA verification failed',
                'errors' => ['recaptcha_token' => ['Invalid reCAPTCHA token']],
            ], 422);
        }

        try {
            // Create the contact record
            $contact = Contact::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company,
                'subject' => $request->subject,
                'message' => $request->message,
                'type' => $request->type ?? 'general',
                'status' => 'unread',
                'priority' => $this->determinePriority($request),
                'source' => 'website',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Log the contact submission
            Log::info('New contact form submission', [
                'contact_id' => $contact->id,
                'email' => $contact->email,
                'subject' => $contact->subject,
                'type' => $contact->type,
            ]);

            // TODO: Send notification email to admin
            // TODO: Send auto-reply email to user
            // TODO: Add to queue for processing

            return response()->json([
                'message' => 'Thank you for your message! We\'ll get back to you soon.',
                'data' => [
                    'id' => $contact->id,
                    'status' => 'submitted',
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('Contact form submission failed', [
                'error' => $e->getMessage(),
                'email' => $request->email,
                'subject' => $request->subject,
            ]);

            return response()->json([
                'message' => 'Sorry, there was an error submitting your message. Please try again.',
            ], 500);
        }
    }

    /**
     * Verify reCAPTCHA token.
     */
    private function verifyRecaptcha(string $token): bool
    {
        $secretKey = config('services.recaptcha.secret_key');
        
        if (!$secretKey) {
            Log::warning('reCAPTCHA secret key not configured');
            return true; // Allow submission if reCAPTCHA is not configured
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $token,
            ]);

            $result = $response->json();

            if (!$result['success']) {
                Log::warning('reCAPTCHA verification failed', [
                    'error_codes' => $result['error-codes'] ?? [],
                ]);
                return false;
            }

            // Check score for v3 reCAPTCHA (optional)
            if (isset($result['score']) && $result['score'] < 0.5) {
                Log::warning('reCAPTCHA score too low', [
                    'score' => $result['score'],
                ]);
                return false;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification error', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Determine priority based on request content.
     */
    private function determinePriority(Request $request): string
    {
        $urgentKeywords = ['urgent', 'asap', 'emergency', 'critical', 'immediately'];
        $highKeywords = ['important', 'priority', 'deadline', 'soon'];

        $content = strtolower($request->subject . ' ' . $request->message);

        foreach ($urgentKeywords as $keyword) {
            if (str_contains($content, $keyword)) {
                return 'high';
            }
        }

        foreach ($highKeywords as $keyword) {
            if (str_contains($content, $keyword)) {
                return 'medium';
            }
        }

        // Check if it's a partnership or project inquiry
        if (in_array($request->type, ['partnership', 'project'])) {
            return 'medium';
        }

        return 'low';
    }
}
