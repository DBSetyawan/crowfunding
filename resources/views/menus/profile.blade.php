<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Profile -  Kotakamal</title>
    
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
        <a title="back" href="{{route('profile')}}" style="width: auto; height: 33px; margin-right: 20px;">
            {{-- <i class="fas fa-arrow-left" class="back-icon" style="margin:0 10px 0 10px;"></i> --}}
            <span>Profile</span>
        </a>
    </div>
</header>

<main class="content">

    <div class="row card-container" >
    <div class="profile-info-container">

        <div class="profile-image-container">
            {{-- <img src="{{Config::get('app.admin_asset_base_url').'/'.Auth::user()->avatar}}" class="img-fluid profile-image"/> --}}
        </div>
        <div class="profile-name-container">
            <span class="profile-name">
                {{Auth::user()->name}}
            </span>
            <div style="display:block;">
                <a href="{{route('profile.edit')}}" class="btn btn-outline-primary btn-sm" style="padding:5px 20px;">Edit Profile</a>
            </div>
        </div>
        
        
    

    </div>
    <div style="display:block;padding:0.5em 0em;width:100%;">
        <a href="{{route('auth.logout')}}" class="btn btn-block btn-outline-primary">LOGOUT</a>  
    </div>
    

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
    <div class="mobile-bottom-nav__item">		
        <a href="{{route('donation')}}">
        <div class="mobile-bottom-nav__item-content">
            <i class="fas fa-hand-holding-usd"></i>
            <span>
                Donasi
            </span>
            
        </div>
        </a>
    </div>
    <div class="mobile-bottom-nav__item mobile-bottom-nav__item--active">
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