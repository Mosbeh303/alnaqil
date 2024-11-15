<table>
    <thead>
    <tr>
        <th>رقم المتجر</th>
        <th>اسم المتجر</th>
        <th>البنك</th>
        <th>اجمالي رسوم التوصيل</th>
        <th>اجمالي الدفع عند الإستلام</th>
        <th>كاش</th>
        <th>شبكة</th>
        <th>سندات القبض  </th>
        <th>سندات الصرف  </th>
        <th> المستحقات</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($stores as $store)
            <tr>
                <td>{{$store['id']}}</td>
                <td>{{$store['name']}}</td>
                <td>{{$store['bank']}}</td>
                <td>{{$store['fees']}}</td>
                <td>{{$store['cod']}}</td>
                <td>{{$store['cash']}}</td>
                <td>{{$store['epayment']}}</td>
                <td>{{$store['receipt']}}</td>
                <td>{{$store['payment']}}</td>
                <td>
                    @if($store['previousDues'] - ($store['cod']   - $store['receipt'] - $store['payment'] - $store['fees']) !== 0)
                        {{ number_format((float) ($store['previousDues'] - ($store['cod']   - $store['receipt'] - $store['payment'] - $store['fees'])), 2, '.', '')  }}
                    @else 0
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
