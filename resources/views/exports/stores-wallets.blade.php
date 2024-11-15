<table>
    <thead>
    <tr>
        <th>رقم المتجر</th>
        <th>اسم المتجر</th>
        <th>دفع آجل / مسبق</th>
        <th>رسوم التوصيل (المحفظة)</th>
        <th>المدفوع للعميل (المحفظة)</th>
        <th>رصيد معلق   </th>
        <th>إجمالي رصيد المحفظة   </th>
    </tr>
    </thead>
    <tbody>
        @foreach ($stores as $store)
            <tr>
                <td>{{$store['id']}}</td>
                <td>{{$store['name']}}</td>
                <td>
                    @if ($store['postpaid'] == 1) <span>{{ trans('postpaid') }}</span>
                    @else <span> {{trans('prepaid') }}</span>
                    @endif
                </td>
                <td>{{$store['wallet']['fees']}} </td>
                <td>{{$store['wallet']['paid']}} </td>
                <td>{{$store['wallet']['outstandingBalance'] }} </td>
                <td>{{$store['wallet']['balance'] }} </td>
            </tr>
        @endforeach
    </tbody>
</table>
