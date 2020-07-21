<div class="checkout-totals">
    <table class="table">
        <tbody>
        <tr>
            <td>Subtotal:</td>
            <td>{{ $cart->displayTotalPrice }}</td>
        </tr>
        <tr>
            <td>Shipping:</td>
            <td>{{ $cart->displayTotalShipping }}</td>
        </tr>
        <tr>
            <td>Tax:</td>
            <td>{{ $cart->displayTotalTax }}</td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <th>Total:</th>
            <th>{{ $cart->displayTotal }}</th>
        </tr>
        </tfoot>
    </table>
</div>