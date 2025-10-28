@php
    $currencies = getCurrencies();
@endphp
@if($currencies->count() > 1)
    <div class="language_switcher ms-2 ms-sm-3">
        @php
            $currentCurrencyCode = session('user_currency', gs('cur_text'));
            $currentCurrency = $currencies->where('code', $currentCurrencyCode)->first();
            
            // Fallback to default currency if current currency not found
            if (!$currentCurrency) {
                $currentCurrency = getDefaultCurrency();
                $currentCurrencyCode = $currentCurrency->code;
            }
        @endphp
        <div class="language_switcher__caption">
            <span class="icon">
                <i class="las la-coins"></i>
            </span>
            <span class="text">{{ $currentCurrency->code }}</span>
        </div>
        <div class="language_switcher__list">
            @foreach ($currencies as $currency)
                <div class="language_switcher__item @if($currentCurrencyCode == $currency->code) selected @endif" data-value="{{ $currency->code }}">
                    <a href="{{ route('currency.switch', $currency->code) }}" class="thumb">
                        <span class="icon">
                            <i class="las la-coins"></i>
                        </span>
                        <span class="text">{{ $currency->symbol }} {{ $currency->code }}</span>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

@endif