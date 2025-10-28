@php
    $count = App\Models\ShoppingCart::where('user_id', @auth()->user()->id ?? session()->get('randomId'))->count();
@endphp
<a class="add-cart me-2" href="{{ route('shopping.cart') }}">
    <i class="fas fa-shopping-cart">
        <span>{{ $count }}</span>
    </i>
</a>

