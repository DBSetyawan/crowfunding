<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$program->program_name}} - Kotakamal</title>
    
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
        <a title="back" href="javascript:history.back()" style="width: auto; height: 33px; margin-right: 20px;">
            <i class="fas fa-arrow-left" class="back-icon"></i>
        </a>
        {{-- <div class="search-bar">
            <span>Cari yang ingin kamu bantu</span>
            <i class="fas fa-search"></i>
        </div> --}}
    </div>
</header>

<main class="content">

    <div class="row card-container" >
        <img src="{{$program->thumbnail}}" class="img-fluid" />
    </div>
    <div class="divider"></div>
    <div class="row card-container" >
        <h1 class="main-title">{{$program->program_name}}</h1>

        <div style="margin-bottom: 8px;width:100%;" class="donation-count-cont">
            <span  class="total-don">{{rupiah($program->total)}}</span>
            <div  class="needed-don">terkumpul dari {{rupiah($program->target_amount)}}</div>
        </div>

        <div class="eifNFc"><div class="fKSicX" style="width:{{isset($program->percentage)?$program->percentage:100}}%;"></div></div>

        <div class="between-space-cont">
            <span><strong>{{$program->count_donations}}</strong> Donasi</span>
            <span><strong> {!! isset($program->days_left) ? $program->days_left : '<i class="fas fa-infinity" style="color: rgb(74, 74, 74);"></i>' !!} </strong> hari lagi</span>
        </div>
    </div>
    <div class="row card-container" >
        

        <div class="subtitle-container">
            <h3 class="subtitle" style="font-weight:600;">Deskripsi</h3>
        </div>

        <p> 
            {!! $program->description !!}
        </p>


        
    </div>

    <div class="row card-container" >
        

        <div class="subtitle-container">
            <h3 class="subtitle" style="font-weight:600;">Donasi Terbaru</h3>
        </div>

        @foreach ($program->latest_donations as $item)
        <div class="donatur-card-container">      
            
            <div class="donatur-card-inner-container">
                <span class="name">{{$item->donatur_name}}</span>
                <span>Donasi <span class="donation">{{rupiah($item->amount)}}</span></span>
                <span class="time">{{$item->created_at}}</span>
            </div>

        </div>   
        @endforeach
        

        
        
    </div>
    @php
    $can_donate = false;

    if($program->type == "program" ){
        $can_donate = true;
    }else if(isset($program->start_date) && isset($program->end_date)){
        $time = time();
        if(strtotime($program->start_date) <= $time && strtotime($program->end_date) >= $time){
            $can_donate = true;
        }
    }

    @endphp
    @if($can_donate)
    <div class="fixed-bottom-bar">
        <a href="{{route('payment.start_payment',['id'=>$program->id,'tipe'=>$program->type])}}"  class="btn btn-block btn-success ">DONASI SEKARANG</a>
    </div>
    @endif
</main>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>
</html>