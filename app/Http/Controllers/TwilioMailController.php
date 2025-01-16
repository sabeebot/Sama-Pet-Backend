<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VerificationCode;
use Exception;
use SendGrid\Mail\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TwilioMailController extends Controller
{
    /**
     * Send email using SendGrid
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
    
        $receiverEmail = $request->email;
        $subject = "Email Verification Code";
        $verificationCode = rand(100000, 999999); // Generate a 6-digit code
        $message = "Your verification code is: " . $verificationCode;
    
        try {
            // Create a new SendGrid email object
            $email = new Mail();
            $email->setFrom("samapet.bh@gmail.com", "Sama Pet Care");
            $email->setSubject($subject);
            $email->addTo($receiverEmail);
            $email->addContent("text/plain", $message);
    
            // Fetch the SendGrid API key from the environment
            $sendgridApiKey = config('services.sendgrid.key');
    
            if (!$sendgridApiKey) {
                Log::error('SendGrid API key not found in environment variables.');
                return response()->json(['message' => 'SendGrid API key not found'], 500);
            }
    
            $sendgrid = new \SendGrid($sendgridApiKey);
    
            // Send the email
            $response = $sendgrid->send($email);
    
            // Log the SendGrid response for debugging
            Log::info('SendGrid Response', [
                'statusCode' => $response->statusCode(),
                'body' => $response->body(),
                'headers' => $response->headers()
            ]);
    
            // Check the response status
            if ($response->statusCode() == 202) {
                $expiration = Carbon::now()->addMinutes(5);
    
                // Update or create the verification code record
                VerificationCode::updateOrCreate(
                    ['email' => $receiverEmail],
                    ['code' => $verificationCode, 'expires_at' => $expiration]
                );
    
                Log::info('Verification code sent successfully.', [
                    'verification_code' => $verificationCode,
                    'email' => $receiverEmail,
                ]);
    
                return response()->json(['message' => 'Verification code sent successfully.'], 202);
            } else {
                Log::error('SendGrid email failed.', ['response' => $response->body()]);
                return response()->json(['message' => 'Failed to send email.', 'status_code' => $response->statusCode()], $response->statusCode());
            }
        } catch (\Exception $e) {
            // Log any exceptions for debugging
            Log::error('Exception while sending email', ['exception' => $e->getMessage()]);
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Verify the code
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'verificationCode' => 'required|numeric',
            'email' => 'required|email'
        ]);

        $email = $request->email;
        $providedCode = $request->verificationCode;

        // Retrieve the stored verification code from the database
        $verificationRecord = VerificationCode::where('email', $email)->first();

        if ($verificationRecord) {
            if (Carbon::now()->isAfter($verificationRecord->expires_at)) {
                return response()->json(['message' => 'Verification code has expired.'], 400);
            }

            if ($verificationRecord->code == $providedCode) {
                return response()->json(['message' => 'Verification successful.'], 200);
            } else {
                return response()->json(['message' => 'Invalid verification code.'], 400);
            }
        } else {
            return response()->json(['message' => 'Verification code not found.'], 404);
        }
    }
}
