@extends('errors.layout')

@section('title', __('Forbidden'))
@section('code', '404')
@section('message')

    <div class="lock"></div>
    <div class="message" style="direction: rtl">


        <svg xmlns="http://www.w3.org/2000/svg" width="150" height="150" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>

        <h2>لا يوجد بوليصة بهذا الرقم!</h2>
        <p>يرجى مراجعة الإدارة إذا كنت تعتقد أن هناك خطأ.</p>
    </div>
@endsection
