@extends('front.layout')

@section('title', __('terms'))

@section('content')
    <section class="contact-us who-us container my-5">

        <h2 class="text-center py-5">{{__('contact_us')}}</h2>
        <div class="social">
            <h3 class="animate__animated animate__heartBeat">
                <i class='fab fa-fw fa-whatsapp'></i>
                <a href="https://api.whatsapp.com/send?phone=966590700745">{{__('whatsapp')}}</a>
            </h3>

            <h3 class="animate__animated animate__heartBeat">
                <i class='fab fa-fw fa-twitter'></i>
                <a href="https://mobile.twitter.com/alnaqilsa">{{__('twitter')}}</a>
            </h3>

            <h3 class="animate__animated animate__heartBeat">
                <i class='fab fa-fw fa-instagram'></i>
                <a href="https://www.instagram.com/alnaqilsa/">{{__('instagram')}}</a>
            </h3>

            <h3 class="animate__animated animate__heartBeat">
                <i class='fab fa-fw fa-snapchat-ghost'></i>
                <a href="https://www.snapchat.com/add/alnaqilsa">{{__('snapchat')}}</a>
            </h3>

            <h3 class="animate__animated animate__heartBeat">
                <i class='fas fa-phone'></i>
                <a href="tel:8001240316">8001240316</a>
            </h3>

            <h3 class="animate__animated animate__heartBeat">
                <i class='fas fa-phone'></i>
                <a href="tel:0590700745">0590700745</a>
            </h3>

            <h3 class="animate__animated animate__heartBeat">
                <i class='fas fa-envelope'></i>
                <a href="mailto:info@alnaqil.sa">info@alnaqil.sa</a>
            </h3>




        </div>


    </section>
@endsection
