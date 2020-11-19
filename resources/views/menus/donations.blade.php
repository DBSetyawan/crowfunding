<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Riwayat Donasi -  Kotakamal</title>
    
    <link rel="icon" href="{{asset('image/kotakamal-logo.jpg')}}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('fontawesome-free-5.13.1-web/css/all.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('slick-1.8.1/slick/slick.css')}}"/>
</head>
<body>
<header class="mainbar">
    <div class="inner-cont">
        <a title="back" href="{{route('donation')}}" style="width: auto; height: 33px; margin-right: 20px;">
            {{-- <i class="fas fa-arrow-left" class="back-icon" style="margin:0 10px 0 10px;"></i> --}}
            <span>Riwayat Donasi</span>
        </a>
    </div>
</header>
<style>
    .donation-item-card{
        display: flex;
        /* flex-direction: row;
        justify-content: flex-start; */
        /* margin: 1em 0px; */
        width: 100%;
        padding: 0px 0;
        /* border-bottom: 0.5px solid #d8d8d8; */
        padding: 1em 0em;
        flex: 1 2 1;
    }

    .donation-image-cont{
        display: flex;
        align-items: center;
        justify-content: center;
    }


    .donation-image-cont img{
        height: auto;
        width: 100px;
        margin: 0 10px 0 0;
    }

    .donation-info-cont{
        /* flex-wrap: wrap; */
        flex-direction:column;
        flex-grow: 2;
    }
    .donation-status-cont{
        /* flex-grow:2; */

    }

    .donation-info-cont .title{
        font-weight: 600;
        font-size: 12px;
        color: #4a4a4a;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        word-break: break-word;
    }

    .donation-info-cont .date{
        line-height: 1.75em;
        font-weight: normal;
        font-size: 10px;
        color: rgba(0, 0, 0, 0.8);
    }

    .donation-info-cont .amount{
        line-height: 1.75em;
        font-weight: bold;
        font-size: 12px;
    }
</style>
<main class="content">

    <div class="row card-container" >
        @foreach ($data as $item)
        <div class="donation-item-card" style="border-bottom: 0.5px solid #d8d8d8;">
            <div class="donation-image-cont">
                <img alt="{{$item->program->program_name}}" src="{{$item->program->thumbnail}}" />
            </div>
            <div class="donation-info-cont">
                <span class="title">{{$item->program->program_name}}</span>
                <div style="flex-direction:column;display:flex;">
                    <span class="amount" >{{rupiah($item->amount)}}</span>
                    <span class="date">{{($item->payment_gateway)?$item->payment_gateway:"-"}}</span> 
                    <span class="date">{{$item->created_at}}</span> 
                </div>
            </div>
            <div class="donation-status-cont" style="flex-direction:column;display:flex;padding:0px 0px;">
                <span class="badge badge-pill badge-primary" style="padding:5px 0px;min-width:75px;">{{ucfirst($item->payment_status)}}</span>
            </div>
        </div>
        @endforeach

       
       @if(count($data) < 1)
       <div style="padding:25px 25px;">
        <img class="img-fluid" src="{{asset('/image/undraw_Tree_swing_646s.png')}}" />
        <p style="text-align:center;">Anda belum pernah melakukan donasi apapun.</p>
    </div>
            
       @endif

    </div>

    
</main>

<nav class="mobile-bottom-nav">
    <div class="mobile-bottom-nav__item">
        <a href="{{url('/')}}">
        <div class="mobile-bottom-nav__item-content">
            <i class="fas fa-home"></i>
            <span>
                Home
            </span>
        </div>		
        </a>
    </div>
    <div class="mobile-bottom-nav__item mobile-bottom-nav__item--active">		
        <a href="{{route('donation')}}">
        <div class="mobile-bottom-nav__item-content">
            <i class="fas fa-hand-holding-usd"></i>
            <span>
                Donasi
            </span>
            
        </div>
        </a>
    </div>
    <div class="mobile-bottom-nav__item ">
        <a href="{{route('profile')}}">
        <div class="mobile-bottom-nav__item-content">
            <i class="fas fa-user"></i>
            <span>
                Profile
            </span>
        </div>		
        </a>
    </div>

</nav>

<!-- slick js start -->
{{-- <script type="text/javascript" src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="{{asset('slick-1.8.1/slick/slick.min.js')}}"></script> --}}
<!-- slick js end -->

<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.css">
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">

<script src="https://unpkg.com/swiper/swiper-bundle.js"></script>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

<script type="module">
    import Swiper from 'https://unpkg.com/swiper/swiper-bundle.esm.browser.min.js'
  
    var mySwiper = new Swiper('#promotion-swiper', {
        // Optional parameters
        direction: 'horizontal',
        loop: true,

        // If we need pagination
        pagination: {
            el: '.swiper-pagination',
        },
        spaceBetween:20,
        scrollbar: {
            hide: true,
        },
        // Navigation arrows
        // navigation: {
        //     nextEl: '.swiper-button-next',
        //     prevEl: '.swiper-button-prev',
        // },

        // And if we need scrollbar
        // scrollbar: {
        //     el: '.swiper-scrollbar',
        // },  

    });
  </script>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>
</html>