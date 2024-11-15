<table class="w-full border border-collapse border-slate-700">
    <thead>
        <tr>
            <th class="label">رقم المتجر</th>
            <th class="label">اسم المتجر</th>
            <th class="label">رقم الفاتورة</th>
            <th class="label">تاريخ الفاتورة</th>
            <th class="label">الرسوم قبل الضريبة (ريال)</th>
            <th class="label">الضريبة (ريال)</th>
            <th class="label">الرسوم شامل الضريبة (ريال)</th>
            <th class="label">الرقم الضريبي</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
            <tr>
                <td>{{$item['storeNumber']}}</td>
                <td>{{$item['storeName']}}</td>
                <td>{{$item['number']}}</td>
                <td>{{$item['date']}}</td>
                <td>{{$item['fees_without_tax']}} </td>
                <td>{{$item['tax']}} </td>
                <td>{{$item['fees']}} </td>
                <td>{{$item['tax_number']}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
