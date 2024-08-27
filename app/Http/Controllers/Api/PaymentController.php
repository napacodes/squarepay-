<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\Coinpayments\CoinPaymentHosted;
use App\Models\CryptoCurrency;
use App\Models\CryptoWallet;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function walletGenerate($id)
    {
        $crypto = CryptoCurrency::active()->where('id', $id)->first();

        if (!$crypto) {
            return response()->json([
                'remark' => 'crypto_error',
                'status' => 'error',
                'message' => ['error' => 'Crypto currency not found or disabled'],
            ]);
        }

        $coinPayAcc = gs();
        $cps = new CoinPaymentHosted();
        $cps->Setup($coinPayAcc->private_key, $coinPayAcc->public_key);
        $callbackUrl = route('ipn.crypto');
        $result = $cps->GetCallbackAddress($crypto->code, $callbackUrl);
        if ($result['error'] == 'ok') {
            $newCryptoWallet = new CryptoWallet();
            $newCryptoWallet->user_id = Auth::id();
            $newCryptoWallet->crypto_currency_id = $crypto->id;
            $newCryptoWallet->wallet_address = $result['result']['address'];
            $newCryptoWallet->save();
            return response()->json([
                'remark' => 'wallet_address_generated',
                'status' => 'success',
                'message' => ['success' => 'New Wallet Address Generated Successfully'],
            ]);
        } else {
            return response()->json([
                'remark' => 'coinpayments_error',
                'status' => 'error',
                'message' => ['error' => $result['error']],
            ]);
        }
    }
}
