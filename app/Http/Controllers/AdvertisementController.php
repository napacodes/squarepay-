<?php

namespace App\Http\Controllers;

use App\Models\CryptoCurrency;
use App\Models\FiatCurrency;
use App\Models\FiatGateway;

class AdvertisementController extends Controller
{

    public function allAds($type, $crypto, $country = null, $gateway = null, $currency = null, $amount = null)
    {
        $cryptoCurrency = CryptoCurrency::where('code', $crypto)->active()->firstOrFail();
        $query          = adsQuery($cryptoCurrency->id, $type == 'buy' ? 2 : 1);
        $request        = request();

        if ($gateway && $gateway != 'all') {
            $fiatGatewayCheck = FiatGateway::where('slug', $gateway)->active()->firstOrFail();
            $query->where('advertisements.fiat_gateway_id', $fiatGatewayCheck->id);
        }

        if ($currency && $currency != 'all') {
            $fiatCheck = FiatCurrency::where('code', $currency)->active()->firstOrFail();
            $query->where('advertisements.fiat_currency_id', $fiatCheck->id);
        }

        if ($country && $country != 'all') {
            $query->whereHas('user', function ($q) use ($country) {
                $q->active()->where('country_code', $country);
            });
        }

        if ($amount) {
            $query->where('advertisements.min', '<=', $amount)->where('advertisements.max', '>=', $amount);
        }

        if (request()->ajax()) {
            $totalAds = (clone $query)->count();
            $ads      = $query->orderBy('advertisements.id', 'desc')->skip(request()->skip ?? 0)->limit(request()->take ?? 6)->get();
            $html     = view("Template::advertisement.$type", compact('ads'))->render();

            return response()->json(
                [
                    'success' => true,
                    'html'    => $html,
                    'total'   => $totalAds
                ]
            );
        }

        $advertisements = $query->with('user')->orderBy('advertisements.id', 'desc')->paginate(getPaginate());
        $cryptos        = CryptoCurrency::active()->orderBy('name')->get();
        $countries      = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $fiatGateways   = FiatGateway::getGateways();
        $pageTitle      = ucfirst($type) . ' ' . $cryptoCurrency->name;

        return view('Template::advertisement.all', compact('pageTitle', 'advertisements', 'type', 'crypto', 'cryptos', 'fiatGateways', 'countries','request'));
    }
}
