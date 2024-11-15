<table>
    <thead>
    <tr>
        <td class="label ">رقم الشحنة</td>
        <td class="label ">رقم الفاتورة</td>
        <td class="label "> التوصيل</td>
        <td class="label"> رسوم التوصيل </td>
        <td class="label">  الدفع عند الاستلام </td>
        <td class="label">نوع الدفع</td>
        <td class="label"> اسم المرسل </td>
        <td class="label">  الحالة </td>
    </tr>
    </thead>
    <tbody>
        @foreach ($shipments as $shipment)
        <tr>
            <td>{{$shipment['number']}}</td>
            <td>SA{{$shipment['invoice']}}</td>
            <td>{{$shipment['shipping_date']}}</td>

            <td>{{$shipment['fees']}}</td>
            <td>
            @if ($shipment['cod'] == 'returned') 0
            @elseif ($shipment['cod'] == 0) دفع مسبق
            @else {{$shipment['cod']}}
            @endif
            </td>
            <td>{{$shipment['payment_method']}}</td>
            <td>{{$shipment['storeName']}}</td>
            <td>
                @if ($shipment['status'] == "100") تم التوصيل
                @else مرتجعة
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
