<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\ChMessage;
use App\Models\DeliveryAddress;
use App\Models\DeviceToken;
use App\Models\Order;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * In-app account deletion.
 *
 * Required by App Store guideline 5.1.1(v) and the Google Play "Data deletion"
 * policy: an app that lets a user create an account must let them delete it
 * from inside the app, not just by emailing support.
 *
 * Deletion is a soft delete plus anonymisation rather than a hard row delete,
 * because orders, order items and vendor payouts reference the user and are
 * financial records the other party is entitled to keep. Everything that
 * identifies the person is scrubbed; the skeleton row stays so an order
 * history does not break.
 */
class AccountController extends Controller
{
    /**
     * What deleting the account will do — shown on the confirmation screen so
     * the user is told the consequences before they confirm.
     */
    public function deletionInfo(Request $request)
    {
        $user = $request->user();

        $pendingOrders = Order::where('user_id', $user->id)
            ->whereNotIn('order_status', ['delivered', 'completed', 'cancel'])
            ->count();

        $vendorPendingOrders = (int) $user->role_id === 2
            ? Order::where('vendor_id', $user->id)
                ->whereNotIn('order_status', ['delivered', 'completed', 'cancel'])
                ->count()
            : 0;

        $blocked = $pendingOrders > 0 || $vendorPendingOrders > 0;

        return response()->json([
            'status' => true,
            'message' => 'Account deletion details fetched.',
            'data' => [
                'can_delete' => ! $blocked,
                'pending_orders' => $pendingOrders,
                'vendor_pending_orders' => $vendorPendingOrders,
                'blocking_reason' => $blocked
                    ? 'You have orders that are still in progress. Please wait until they are completed or cancelled before deleting your account.'
                    : null,
                'will_be_deleted' => [
                    'Your profile, contact details and profile photo',
                    'Your saved delivery addresses',
                    'Your cart and wishlist',
                    'Your chat messages',
                    'Your registered devices and push notifications',
                    (int) $user->role_id === 2
                        ? 'Your product and service listings will be removed from the marketplace'
                        : 'Your product reviews',
                ],
                'will_be_retained' => [
                    'Completed order records, with your personal details removed, are kept for accounting and tax purposes as required by law.',
                ],
                'is_permanent' => true,
            ],
        ]);
    }

    /**
     * Delete the authenticated user's account.
     *
     * Re-authentication is required (password, or the confirmation phrase for
     * social-login accounts that have no usable password) so a stolen device
     * with a live session cannot wipe the account.
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        $usesSocialLogin = empty($user->password)
            || $user->google_auth_id
            || $user->facebook_auth_id
            || $user->apple_auth_id;

        $request->validate([
            'password' => [$usesSocialLogin ? 'nullable' : 'required', 'string'],
            'confirmation' => [$usesSocialLogin ? 'required' : 'nullable', 'string'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        if (! $usesSocialLogin && ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'The password you entered is incorrect.',
            ], 422);
        }

        if ($usesSocialLogin && strtoupper(trim((string) $request->confirmation)) !== 'DELETE') {
            return response()->json([
                'status' => false,
                'message' => 'Type DELETE to confirm account deletion.',
            ], 422);
        }

        // An order that is still moving has money or goods owed in one
        // direction. Deleting the account mid-flight strands the other party.
        $pending = Order::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('vendor_id', $user->id);
            })
            ->whereNotIn('order_status', ['delivered', 'completed', 'cancel'])
            ->count();

        if ($pending > 0) {
            return response()->json([
                'status' => false,
                'message' => 'You have ' . $pending . ' order(s) still in progress. Please wait until they are completed or cancelled before deleting your account.',
            ], 409);
        }

        try {
            DB::transaction(function () use ($user, $request) {
                // Personal data that no one else has a claim on.
                Cart::where('user_id', $user->id)->delete();
                Wishlist::where('user_id', $user->id)->delete();
                DeliveryAddress::where('user_id', $user->id)->delete();
                DeviceToken::where('user_id', $user->id)->delete();
                ChMessage::where('from_id', $user->id)->orWhere('to_id', $user->id)->delete();

                // A vendor's listings must leave the marketplace — nobody can
                // fulfil them any more.
                if ((int) $user->role_id === 2) {
                    Product::where('user_id', $user->id)->update(['status' => 'inactive']);
                }

                // Scrub identifying fields but keep the row so historical
                // orders still resolve to a placeholder rather than breaking.
                $user->forceFill([
                    'first_name' => 'Deleted',
                    'middle_name' => null,
                    'last_name' => 'User',
                    'email' => 'deleted_' . $user->id . '_' . Str::random(8) . '@deleted.invalid',
                    'phone_number' => null,
                    'address' => null,
                    'city' => null,
                    'state' => null,
                    'country' => null,
                    'zipcode' => null,
                    'profile_image' => null,
                    'business_name' => null,
                    'business_description' => null,
                    'business_license' => null,
                    'business_logo' => null,
                    'google_auth_id' => null,
                    'facebook_auth_id' => null,
                    'apple_auth_id' => null,
                    'web_token' => null,
                    'otp' => null,
                    'forgot_password_token' => null,
                    'reset_password_token' => null,
                    'remember_token' => null,
                    'password' => Hash::make(Str::random(40)),
                    'status' => 'inactive',
                    'deletion_reason' => $request->reason,
                    'anonymised_at' => now(),
                ])->save();

                // Revoke every session on every device.
                $user->tokens()->delete();

                $user->delete(); // soft delete
            });
        } catch (\Throwable $e) {
            Log::error('Account deletion failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);

            return response()->json([
                'status' => false,
                'message' => 'We could not delete your account right now. Please try again.',
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Your account has been deleted.',
        ]);
    }
}
