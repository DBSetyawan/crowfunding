<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Home -  Kotakamal</title>
    <link rel="icon" href="{{asset('image/kotakamal-logo.jpg')}}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('fontawesome-free-5.13.1-web/css/all.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('slick-1.8.1/slick/slick.css')}}"/>
</head>
<body>
<header class="mainbar">
    <div class="inner-cont home">
        <a id="defaultheader_logo" title="Kotakamal" href="/" style="width: auto; height: 33px; margin-right: 20px;">
            <img src="{{asset('image/kotakamal-logo.jpg')}}" alt="Kitabisa" class="logo">
        </a>
        <a href="{{route('search')}}" style="width:100%;">
            <div class="search-bar">
                <span>Cari yang ingin kamu bantu</span>
                <i class="fas fa-search"></i>
            </div>
        </a>
        
    </div>
</header>

<main class="content">

    <div class="row card-container" >
        <div class="swiper-container" id="promotion-swiper" >
            <div class="swiper-wrapper"  >
                @foreach ($sliders as $item)
                    
                    <div class="swiper-slide"> <a href="{{$item->url}}"><img src="{{$item->image}}" class="image-slider" /></a></div>
                    
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
            {{-- <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div> --}}
            {{-- <div class="swiper-scrollbar"></div> --}}
        </div>
        
    </div>
    <div class="divider"></div>
    <div class="row card-container" >
        <div class="subtitle-container">
            <h3 class="subtitle">Kategori</h3>
            
        </div>

        
            <div class="scrolling-wrapper">

                @foreach ($program_categories as $item)
                    <a href="{{route('search')}}?category={{$item->id}}">
            
                    <div class="style__RoundedButtonWrapper-sc-1s90h4d-1 rIRJZ">
                        <div class="style__RoundedButtonIconWrapper-sc-1s90h4d-2 hZdJFT">
                            <img src="{{$item->icon}}" alt="{{$item->program_cateogory_name}}" class="style__RoundedButtonIcon-sc-1s90h4d-3 nCriu">
                        </div>
                        <p class="style__RoundedButtonText-sc-1s90h4d-4 jDDUUQ">{{$item->program_cateogory_name}}</p>
                    </div>

                </a>
                @endforeach

            </div> 
        
    </div>
    <div class="row card-container" >
        

        <div class="subtitle-container">
            <h3 class="subtitle">Campaign</h3>
            <a href="{{route('search')}}?tipe=campaign">Lihat Lainnya</a>
        </div>

        <div class="scrolling-wrapper">
            @foreach ($campaigns as $item)
                <a href="{{route('program.detail',['id'=>$item->id,'tipe'=>$item->type])}}" class="gSOqSI">
                    <div class="dLdGTW">
                        <div class="jSFrGK">
                            <figure class="falDrR">
                                <img alt="{{$item->program_name}}" 
                                    src="{{$item->thumbnail}}" 
                                class="fNWisl"></figure>
                                <div class="kimAYF">
                                    <div class="cxtvYH">
                                    <span class="pGMPs">{{$item->program_name}}</span>
                                    </div>
                                    {{-- <div class="fUEXnq">
                                        <div class="fCqZVR">
                                            <span >BAITULMAAL MUAMALATasdasdasdasdasdasd MUAMALATasdasdasdasdasdasd MUAMALATasdasdasdasdasdasd</span>
                                        </div>
                                    </div> --}}
                                    <div class="fUEXnq">
                                        <div class="eifNFc">
                                            <div class="fKSicX" style="width:{{$item->percentage}}%"></div>
                                        </div>
                                    </div>
                                    <div class="bottom-card-info-container">
                                        <div type="donationCollected" class="amount-container">
                                            <span>Terkumpul</span>
                                            <span>{{rupiah($item->total)}}</span>
                                        </div>
                
                                        <div style="display: flex;
                                        flex-direction: column;">
                                            <span style="font-weight: normal;
                                            font-size: 10px;
                                            color: rgb(74, 74, 74);">Sisa hari</span>
                                            <span style="font-weight: bold;
                                            font-size: 12px;
                                            text-align: right;"><strong> {{$item->days_left}} </strong></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
            @endforeach
            

        
            
        </div>

        
    </div>


    <div class="row card-container" >
        

        <div class="subtitle-container">
            <h3 class="subtitle">Program</h3>
            <a href="{{route('search')}}?tipe=program">Lihat Lainnya</a>
        </div>

        <div class="scrolling-wrapper">
            @foreach ($programs as $item)
                <a href="{{route('program.detail',['id'=>$item->id,'tipe'=>$item->type])}}" class="gSOqSI">
                    <div class="dLdGTW">
                        <div class="jSFrGK">
                            <figure class="falDrR">
                                <img alt="{{$item->program_name}}" 
                                    src="{{$item->thumbnail}}" 
                                class="fNWisl"></figure>
                                <div class="kimAYF">
                                    <div class="cxtvYH">
                                    <span class="pGMPs">{{$item->program_name}}</span>
                                    </div>
                                    {{-- <div class="fUEXnq">
                                        <div class="fCqZVR">
                                            <span >BAITULMAAL MUAMALATasdasdasdasdasdasd MUAMALATasdasdasdasdasdasd MUAMALATasdasdasdasdasdasd</span>
                                        </div>
                                    </div> --}}
                                    <div class="fUEXnq">
                                        <div class="eifNFc">
                                            <div class="fKSicX" style="width:100%"></div>
                                        </div>
                                    </div>
                                    <div class="bottom-card-info-container">
                                        <div type="donationCollected" class="amount-container">
                                            <span>Terkumpul</span>
                                            <span>{{rupiah($item->total)}}</span>
                                        </div>
                
                                        <div style="display: flex;
                                        flex-direction: column;">
                                            <span style="font-weight: normal;
                                            font-size: 10px;
                                            color: rgb(74, 74, 74);">Sisa hari</span>
                                            <span style="font-weight: bold;
                                            font-size: 12px;
                                            text-align: right;"><strong> <i class="fas fa-infinity" style="color: rgb(74, 74, 74);"></i> </strong></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
            @endforeach
        </div>
    </div>

    
</main>

<nav class="mobile-bottom-nav">
    <div class="mobile-bottom-nav__item mobile-bottom-nav__item--active">
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
    <div class="mobile-bottom-nav__item">
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