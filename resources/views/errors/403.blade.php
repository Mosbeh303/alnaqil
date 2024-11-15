@extends('errors::layout')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message')

<div class="lock"></div>
<div class="message" style="direction: rtl">
     <svg xmlns="http://www.w3.org/2000/svg" width="150" height="150" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
      </svg>
  <h2>الوصول إلى هذه الصفحة مقيد</h2>
  <p>يرجى مراجعة الإدارة إذا كنت تعتقد أن هناك خطأ.</p>
</div>
@endsection
