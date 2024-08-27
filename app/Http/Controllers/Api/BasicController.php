<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CryptoCurrency;
use App\Models\FiatGateway;
use App\Models\Frontend;
use App\Models\Language;

class BasicController extends Controller
{
    public function language($code)
    {
        $language = Language::where('code', $code)->first();

        if (!$language) {
            return response()->json([
                'remark'=>'language_error',
                'status'=>'error',
                'message'=>['error' => 'Language not found'],
            ]);
        }

        $languages   = Language::get();
        $path        = base_path() . "/resources/lang/$code.json";
        $fileContent = file_get_contents($path);
        $notify[]    = 'Language Details';
        $data        = [
            'languages' => $languages,
            'file'      => $fileContent,
        ];

        return response()->json([
            'remark'  => 'language',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => $data
        ]);
    }

    public function cryptos()
    {
        $cryptos = CryptoCurrency::active()->orderBy('name')->get();
        $notify[] = 'All crypto currencies';

        return response()->json([
            'remark'=>'cryptos',
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[
                'cryptos'=>$cryptos
            ]
        ]);
    }

    public function fiatGateways()
    {
        $fiatGateways = FiatGateway::getGateways();
        $notify[] = 'All fiat gateways';

        return response()->json([
            'remark'=>'fiat_gateways',
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[
                'fiat_gateways'=>$fiatGateways
            ]
        ]);
    }

    public function countries()
    {
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $notify[] = 'All countries';

        return response()->json([
            'remark'=>'countries',
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[
                'countries'=>$countries
            ]
        ]);
    }

    public function adFilter()
    {
        $cryptos      = CryptoCurrency::active()->orderBy('name')->get();
        $fiatGateways = FiatGateway::getGateways();
        $countries    = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $notify[]     = 'Advertisement Filter';

        return response()->json([
            'remark'=>'countries',
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[
                'cryptos'      =>$cryptos,
                'fiat_gateways'=>$fiatGateways,
                'countries'    =>$countries
            ]
        ]);
    }

    public function polices()
    {
        $allPolicy = Frontend::where('data_keys', 'policy_pages.element')->latest()->get();
        $notify[] = 'All company policy';

        return response()->json([
            'remark'=>'company_policy',
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[
                'all_policy'=>$allPolicy
            ]
        ]);
    }
}
