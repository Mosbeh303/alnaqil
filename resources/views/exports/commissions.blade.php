<table>
    <thead>
    <tr>
        <td class="label ">{{ trans('store') }}</td>
        <td class="label ">{{ trans('shipment_number') }}</td>
        <td class="label ">{{ trans('delivery_fee') }}</td>
        <td class="label ">{{ trans('delivery') }}</td>
        <td class="label ">{{ trans('commission') }} ({{ trans('riyal') }})</td>
        <td class="label ">{{ trans('Status') }}</td>
    </tr>
    </thead>
    <tbody>
        @foreach ($commissions as $commission)
        <tr>
            <td>{{$commission['shipment']['store']}}</td>
            <td>{{$commission['shipment']['number']}}</td>
            <td>{{$commission['shipment']['fees']}}</td>
            <td>{{$commission['shipment']['shipping_date']}}</td>
            <td>

                {{$commission['amount']}}
            </td>

            <td>
                @if ($commission['shipment']['status'] == "100") تم التوصيل
                @else مرتجعة
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
