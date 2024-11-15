@extends('front.layout')

@section('title', 'تتبع الشحنة')

@push('styles')
    <style>
        .main-content{
            max-width: 850px;
            margin:50px auto;
            border-radius: 5px ;
            font-size: 14px;
            line-height: 10px;
            direction: rtl;
            font-family: 'Tajawal', sans-serif;
            color: #333;
        }

        .main-content .card{
            background-color: rgb(209 213 219);
            color: rgb(55 65 81);
            border-radius: 0.25rem;
            padding: 1rem 1.5rem;
            margin-bottom: 30px;
            border: 0;
            margin-right: 30px;
            position: relative;
            display: flex;
            justify-content: center
        }

        .main-content  .pageTitle{
            margin-bottom: 25px
        }

       .main-content .title{
            font-size: 1.2rem;
            font-weight: 600
        }

        .timeline{
            border-right: 1px solid  rgb(209 213 219);
        }

        .card .dot {
            position: absolute;
            transform: translateX(50%);
            right: -30px;
            width: 15px ;
            height: 15px ;
            border-radius: 50% ;
            background-color: rgb(156 163 175);
            z-index: 10
        }

        .card .line {
            position: absolute;
            right: -30px;
            width: 30px ;
            height: 3px;
            background-color: rgb(209 213 219);

        }

        .card:hover{
            transform: translateY(-8px);
            transition: all .1s ease-in-out
        }


    </style>
@endpush

@section('content')

    <div class="container mt-5 text-dark">
        <div class="row">
            <h4 class="pageTitle col-md-4"> رقم الشحنة: {{$shipment->number}}</h4>
            <h4 class="pageTitle col-md-4 text-md-center"> المرسل: {{$shipment->store->name}}</h4>
            <h4 class="pageTitle col-md-4 text-md-right">
                 محتويات الشحنة:
                 @if ($shipment->details) {{$shipment->details}}
                 @else لا يوجد تفاصيل
                 @endif
            </h4>
        </div>
    </div>


    <div class="main-content px-3">

        <div class="timeline">
            @foreach ($tracks as $track)
                @if ($track['action'] != 'update_phone' && $track['action'] != 'update_warehouse' && $track['action'] != 'update_operator' )
                    <div class="card ">
                        <div class="dot"></div>
                        <div class="line"></div>
                        <p class="date mb-4 mt-2">{{$track['created_at']}}</p>
                        @if($track['action'] == 'update_status')
                            <p class="title"> {{$action[$statuses[$track['status_after_action']]]}} </p>
                        @else
                            <p class="title"> {{$action[$track['action']]}} </p>
                        @endif

                        @if (isset($track['notice']))
                            <p class="description my-2"> {{$track['notice']}} </p>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>

    </div>
@endsection
