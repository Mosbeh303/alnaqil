<table>
    <thead>
        <tr>
            <th>رقم الشحنة</th>
            <th>رقم السند</th>
            <th>رسوم التوصيل</th>
            <th> الدفع عند الاستلام</th>
            <th> عمولة cod </th>
            <th>قيمة السند</th>
            <th>التاريخ </th>
            <th>حالة الشحنة</th>
            <th>البيان </th>
            <th>المجموع</th>
            <th>الاجمالي</th>
        </tr>
    </thead>
    <tbody>

        @foreach ($items as $item)
            <tr>
                <td> @if(array_key_exists('number', $item)) {{$item['number']}} @endif</td>
                <td> @if(array_key_exists('id', $item)) {{$item['id']}} @endif</td>
                <td>@if(array_key_exists('fees', $item)) {{$item['fees'] - $item['codFee']}} @endif</td>
                <td>
                    @if(array_key_exists('cod', $item))
                        @if ($item['cod'] == 0) دفع مسبق
                        @else {{$item['cod']}}
                        @endif
                    @endif
                </td>
                <td> @if(array_key_exists('codFee', $item)) {{$item['codFee']}} @endif</td>
                <td> @if(array_key_exists('amount', $item)) {{$item['amount']}} @endif</td>
                <td>{{$item['date']}} </td>
                <td>
                    @if(array_key_exists('status', $item))
                        @if ($item['status'] == "100") تم التوصيل
                        @else مرتجعة
                        @endif
                    @endif
                </td>
                <td> @if(array_key_exists('amount', $item)) {{$item['notice']}} @endif</td>
                <td>@if(array_key_exists('cod', $item)) {{$item['fees'] - $item['cod']}} @endif</td>
                <td>
                    @php
                        if(array_key_exists('id', $item))
                            $sum = number_format((float)$sum, 2, '.', '') + $item['amount'];
                        else
                            $sum =  number_format((float)$sum, 2, '.', '') + ($item['fees'] - $item['cod'] );
                    @endphp

                    {{ number_format((float)$sum, 2, '.', '')}}
                </td>
            </tr>
        @endforeach

    </tbody>
</table>
