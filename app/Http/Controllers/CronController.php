<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\CronJob;
use App\Lib\CurlRequest;
use App\Constants\Status;
use App\Models\CronJobLog;
use App\Models\FiatCurrency;
use App\Models\CryptoCurrency;

class CronController extends Controller
{
    public function cron()
    {
        $general            = gs();
        $general->last_cron = now();
        $general->save();

        $crons = CronJob::with('schedule');

        if (request()->alias) {
            $crons->where('alias', request()->alias);
        } else {
            $crons->where('next_run', '<', now())->where('is_running', Status::YES);
        }
        $crons = $crons->get();
        
        foreach ($crons as $cron) {
            $cronLog              = new CronJobLog();
            $cronLog->cron_job_id = $cron->id;
            $cronLog->start_at    = now();
            if ($cron->is_default) {
                $controller = new $cron->action[0];
                try {
                    $method = $cron->action[1];
                    $controller->$method();
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            } else {
                try {
                    CurlRequest::curlContent($cron->url);
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            }
            $cron->last_run = now();
            $cron->next_run = now()->addSeconds($cron->schedule->interval);
            $cron->save();

            $cronLog->end_at = $cron->last_run;

            $startTime         = Carbon::parse($cronLog->start_at);
            $endTime           = Carbon::parse($cronLog->end_at);
            $diffInSeconds     = $startTime->diffInSeconds($endTime);
            $cronLog->duration = $diffInSeconds;
            $cronLog->save();
        }
        if (request()->target == 'all') {
            $notify[] = ['success', 'Cron executed successfully'];
            return back()->withNotify($notify);
        }
        if (request()->alias) {
            $notify[] = ['success', keyToTitle(request()->alias) . ' executed successfully'];
            return back()->withNotify($notify);
        }
    }

    public function fiatRate()
    {
        try {
            $general      = gs();

            $endpoint     = 'live';
            $accessKey   = $general->fiat_api_key;
            $baseCurrency = 'USD';
            $ch           = curl_init('http://apilayer.net/api/' . $endpoint . '?access_key=' . $accessKey . '&source=' . $baseCurrency);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $json = curl_exec($ch);
            curl_close($ch);
            $exchangeRates = json_decode($json);

            if ($exchangeRates->success == false) {
                $errorMsg = $exchangeRates->error->info;
                echo "$errorMsg";
            } else {
                foreach ($exchangeRates->quotes as $key => $rate) {

                    $curcode  = substr($key, -3);

                    $currency = FiatCurrency::where('code', $curcode)->first();
                    if ($currency) {
                        $currency->rate = $rate;
                        $currency->save();
                    }
                }

                echo "EXECUTED";
            }
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }

    public function cryptoRate()
    {
        try {
            $general = gs();

            $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest';
            $cryptos = CryptoCurrency::pluck('code')->toArray();
            $cryptos = implode(',', $cryptos);

            $parameters = [
                'symbol' => $cryptos,
                'convert' => 'USD',
            ];
            
            $headers = [
                'Accepts: application/json',
                'X-CMC_PRO_API_KEY:' . trim($general->crypto_api_key),
            ];
            $qs      = http_build_query($parameters); // query string encode the parameters
            $request = "{$url}?{$qs}"; // create the request URL
            $curl    = curl_init(); // Get cURL resource
            // Set cURL options
            curl_setopt_array($curl, array(
                CURLOPT_URL            => $request, // set the request URL
                CURLOPT_HTTPHEADER     => $headers, // set the headers
                CURLOPT_RETURNTRANSFER => 1, // ask for raw response instead of bool
            ));
            $response = curl_exec($curl); // Send the request, save the response
            curl_close($curl); // Close request

            $a = json_decode($response);

            if (!isset($a->data)) {
                return 'error';
            }

            $coins = $a->data;

            foreach ($coins as $coin) {

                $currency = CryptoCurrency::where('code', $coin->symbol)->first();
                if ($currency) {
                    $defaultCurrency = 'USD';
                    $currency->rate = $coin->quote->$defaultCurrency->price;
                    $currency->save();
                }
            }

            echo "EXECUTED";
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }

}
