<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Stripe;

class StripeConnectController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret'));
    }

    public function onboard(Team $team): RedirectResponse
    {
        $account = Account::create([
            'type' => 'express',
            'country' => 'SK',
            'email' => auth()->user()->email,
            'capabilities' => [
                'card_payments' => ['requested' => true],
                'transfers' => ['requested' => true],
            ],
            'metadata' => [
                'team_id' => $team->id,
            ],
        ]);

        $team->update(['stripe_connect_account_id' => $account->id]);

        $accountLink = AccountLink::create([
            'account' => $account->id,
            'refresh_url' => route('stripe.connect.onboard', $team),
            'return_url' => route('stripe.connect.callback', ['team_id' => $team->id]),
            'type' => 'account_onboarding',
        ]);

        return redirect($accountLink->url);
    }

    public function callback(Request $request): RedirectResponse
    {
        $team = Team::findOrFail($request->query('team_id'));

        if ($team->stripe_connect_account_id) {
            $account = Account::retrieve($team->stripe_connect_account_id);

            if (! $account->details_submitted) {
                return redirect()->route('stripe.connect.onboard', $team);
            }
        }

        return redirect()->to(
            filament()->getUrl().'/teams/'.$team->id.'/edit',
        );
    }
}
