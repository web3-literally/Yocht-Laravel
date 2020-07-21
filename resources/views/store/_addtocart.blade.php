@if($product->isInStock())
    <div class="product-add-to-cart">
        <form action="{{ route('store.cart.item.add', $product->id) }}" method="post" class="form-inline">
            @csrf
            @empty($listing)
                <div class="form-group mb-2">
                    <label for="qty-{{ $product->id }}" class="sr-only">Qty</label>
                    <input id="qty-{{ $product->id }}" type="number" name="qty" min="1" {{--max="{{ $product->stock }}"--}} class="form-control" value="1">
                </div>
            @endempty
            <button type="submit" class="btn btn-primary mb-2">Add To Cart</button>
        </form>
    </div>
@endif