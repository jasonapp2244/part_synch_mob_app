<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Contact / support form.
 *
 * Both stores expect a working support contact for the app listing, and the
 * app's Help screen needs somewhere to post to.
 */
class SupportController extends Controller
{
    public function contact(Request $request)
    {
        $request->validate([
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $user = $request->user();
        $replyTo = $request->email ?: $user->email;
        $inbox = Setting::get('support_email', Setting::get('site_email', config('mail.from.address')));

        $body = "Support request from {$user->first_name} {$user->last_name} (user #{$user->id}, role {$user->role_id})\n"
            . "Reply to: {$replyTo}\n\n"
            . $request->message;

        try {
            Mail::raw($body, function ($mail) use ($inbox, $request, $replyTo) {
                $mail->to($inbox)
                    ->subject('[Support] ' . $request->subject)
                    ->replyTo($replyTo);
            });
        } catch (\Throwable $e) {
            // Mail is best-effort: a failed SMTP handshake should not make the
            // user think their message was rejected, but we must not claim it
            // was delivered either.
            Log::error('Support contact mail failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);

            return response()->json([
                'status' => false,
                'message' => 'We could not send your message right now. Please email us at ' . $inbox . '.',
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Thank you. Our support team will get back to you shortly.',
        ]);
    }
}
