@extends('front.layout')

@section('title', __('terms'))

@section('content')
    <section class="who-us container my-5">
        <h2 class="text-center py-2">{{__('terms')}}</h2>
        <div class="divider"></div>
        <p class="text-center pt-2 lead animate__animated animate__flipInX">
            {{__('terms_phrase_1')}}<br />
            {{__('terms_phrase_2')}}<br />
            {{__('terms_phrase_3')}}


        <ul class="list-group">
            <ol class="list-group-item">
                {{__('terms_phrase_4')}}
                <ul class="list-group">
                    <li class="list-group-item">
                        {{__('terms_phrase_5')}}
                    </li>
                    <li class="list-group-item">
                        {{__('terms_phrase_6')}}
                    </li>
                    <li class="list-group-item">
                        {{__('terms_phrase_7')}}
                    </li>
                    <li class="list-group-item">
                        {{__('terms_phrase_8')}}
                    </li>
                </ul>
            </ol>

            <ol class="list-group-item">
                {{__('terms_phrase_9')}}
                <ul class="list-group">
                    <li class="list-group-item">
                        {{__('terms_phrase_10')}}
                    </li>
                </ul>
            </ol>



            <ol class="list-group-item">
                {{__('terms_phrase_15')}}
                <ul class="list-group">
                    <li class="list-group-item">
                        {{__('terms_phrase_16')}}</li>
                </ul>
            </ol>

            <ol class="list-group-item">
                {{__('terms_phrase_17')}}
                <ul class="list-group">
                    <li class="list-group-item">
                        {{__('terms_phrase_18')}}
                    </li>
                </ul>
            </ol>
        </ul>

        </p>

    </section>
@endsection
