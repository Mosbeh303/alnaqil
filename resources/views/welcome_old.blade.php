@extends('front.layout')

@section('title', __('home'))


@push('styles')
<style>
    header {
        height: 100% !important;
        padding-top: 0 !important
    }
</style>
@endpush



@section('hero')
<div class="track w-100">

    <div class="info text-center">
        <h2>{{ __('title') }}</h2>
        <p class="lead">
            {{ __('hero_desc') }} </p>
    </div>

    <form id="tracking-form" action="/tracking">

        <div class="form-row">
            <div class="col-10">
                <input type="text" id="trackNumber" class="form-control" name="number"
                    placeholder="{{ __('add_number') }}" />
            </div>

            <div class="col-2">
                <input id="track-btn" type="submit" value="{{ __('tracking') }}"
                    class="btn btn-warning btn-block font-weight-bold" />
            </div>
        </div>

    </form>
</div>
@endsection






@section('content')

<section class=" primary-section  container my-5">
    <div class="about row justify-content-md-center">
        <div class=" content col-md-8 text-center py-2">
            <div class="header-section">
                <h3 class="title h1-responsive font-weight-bold my-4">{{ __('who_us') }}</h3>
            </div>

            <p class="lead grey-text w-responsive mx-auto mb-5">
                {{ __('who_us_desc') }}

            </p>
        </div>
    </div>
</section>

<section class="primary-section section-services">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-md-10 col-lg-8">
                <div class="header-section">
                    <h2 class="title"> <span>{{ __('services') }}</span></h2>
                    <p class="description lead grey-text w-responsive mx-auto">
                        {{ __('services_desc') }}
                    </p>
                </div>
            </div>
        </div>
        <div class="text-center ">

            <div class="col-md-6 m-auto">
                <div class="single-service">
                    <div class="part-1">
                        <i class="fas fa-stopwatch"></i>
                        <h3 class="title"> {{ __('in_riyadh') }}</h3>
                    </div>
                    <div class="part-2">
                        <p class="description">{{ __('in_riyadh_desc') }}</p>

                    </div>
                </div>
            </div>

            {{-- <div class="col-md-6 ">
                <div class="single-service">
                    <div class="part-1">
                        <i class="fas fa-dolly-flatbed"></i>
                        <h3 class="title">{{ __('out_riyadh') }}</h3>
                    </div>
                    <div class="part-2">
                        <p class="description">{{ __('out_riyadh_desc') }}</p>

                    </div>
                </div>
            </div> --}}


        </div>
    </div>
</section>


<section class="primary-section  text-center my-5 container features">
    <div class=" content py-4">

        <div class="header-section">
            <h2 class="h1-responsive title font-weight-bold  mx-auto my-5">{{ __('you_get') }}</h2>
        </div>
        <div class="text-center row">
            <div class="offset-md-2 col-md-8  text-center ">
                <p class="lead grey-text w-responsive mx-auto mb-5 text-center">{{ __('you_get_desc') }} </p>
            </div>
        </div>

        <div class="row">


            <div class="col-md-4">

                <i class="fas fa-shipping-fast fa-3x red-text"></i>
                <h5 class="font-weight-bold my-4">{{ __('express_delivery') }}</h5>
                <p class="grey-text mb-md-0 mb-5">{{ __('express_delivery_desc') }}
                </p>

            </div>



            <div class="col-md-4">

                <i class="fas fa-money-bill-wave fa-3x cyan-text"></i>
                <h5 class="font-weight-bold my-4">{{ __('cod') }}</h5>
                <p class="grey-text mb-md-0 mb-5">{{ __('cod_desc') }}
                </p>

            </div>



            <div class="col-md-4">

                <i class="fas fa-money-check fa-3x orange-text"></i>
                <h5 class="font-weight-bold my-4">{{ __('weekly_benifits') }}</h5>
                <p class="grey-text mb-0">{{ __('weekly_benifits_desc') }}
                </p>

            </div>

            <div class="col-md-4 offset-md-2 mt-5">

                <i class="fas fa-undo-alt fa-3x orange-text"></i>
                <h5 class="font-weight-bold my-4">{{ __('returned_shipments') }}</h5>
                <p class="grey-text mb-0">{{ __('returned_shipments_desc') }}
                </p>

            </div>

            <div class="col-md-4 mt-5">

                <i class="fas fa-dolly fa-3x orange-text"></i>
                <h5 class="font-weight-bold my-4">{{ __('no_advance') }} </h5>
                <p class="grey-text mb-0">{{ __('no_advance_desc') }}
                </p>

            </div>


        </div>

    </div>

</section>



<section class="section-services py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <h5 class="font-weight-bold  my-5">{{ __('ready?') }}</h5>
            </div>
            <div class="col-md-3 my-5">
                <a class="btn btn-warning btn-block" href="register"> {{ __('ready_desc') }}</a>
            </div>
        </div>
    </div>
</section>

<!--- partners-->

<div class="container py-5 our-partners" style="direction:ltr">
    <h2 class="h1-responsive font-weight-bold  mx-auto my-4">{{ __('partners') }}</h2>
    <section class="customer-logos slider mb-5 text-center">
        @foreach ($partners as $partner)
        <div class="slide">
            @if ($partner->website)
            <a rel="external nofollow" href="{{$partner->website}}" target="_blank">
                <img src="/uploads/{{ $partner->logo }}">
            </a>
            @else
            <img src="/uploads/{{ $partner->logo }}">
            @endif
        </div>
        @endforeach
    </section>
</div>

@endsection







@push('scripts')
<script src="scripts/serializejson.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="scripts/js.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.js"></script>


<script>
    $(document).ready(function() {
            $('.customer-logos').slick({
                slidesToShow: 5,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 1500,
                arrows: false,
                dots: false,
                pauseOnHover: false,
                responsive: [{
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 3
                    }
                }, {
                    breakpoint: 520,
                    settings: {
                        slidesToShow: 1
                    }
                }]
            });
        });
</script>
@endpush