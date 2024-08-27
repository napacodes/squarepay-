@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @include($activeTemplate . 'partials.buy_sell_list', [
        'countries'      => $countries,
        'advertisements' => $advertisements,
        'cryptos'        => $cryptos,
        'fiatGateways'   => $fiatGateways,
        'type'           => $type,
        'take'           => 20
    ]);
@endsection
