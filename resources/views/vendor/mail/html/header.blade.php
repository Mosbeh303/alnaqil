<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
            {{-- <img src="https://laravel.com/img/notification-logo1.png" class="logo" alt="Laravel Logo"> --}}
            <img alt="logo" src="{{asset('images/logo1.png')}}" class="logo" />
            @else
            {{ $slot }}
            @endif
        </a>
    </td>
</tr>