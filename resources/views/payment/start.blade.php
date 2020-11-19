<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Donasi {{$program->program_name}} -  Kotakamal</title>
    
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
        <div class="title-container">
            <span style="text-align: center;">{{$program->program_name}}</span>
        </div>
    </div>
</header>

<main class="content">

    <div class="row card-container" >
        

        {{-- <div class="subtitle-container">
            <h3 class="subtitle" style="font-weight:600;">Masukan Nominal Donasi</h3> --}}
        {{-- </div> --}}
        
        <div class="input-box" >
            <div class="wrapOtherDonation" >
                <form >
                    <p>Masukan Nominal Donasi</p>
                    <div class="input-container" >
                        <span class="left-item" >Rp</span>
                        <input  name="amount" placeholder="0" type="tel" class="input-donation"  id="donation-amount" />
                    </div>
                    <small id="error-amount" class="form-text text-danger"></small>
                </form>
            </div>
        </div>
        
    </div>
    
    <div class="fixed-bottom-bar">
    <button type="button"  class="btn btn-block btn-primary disabled" id="pay-button">LANJUTKAN PEMBAYARAN</button>
    </div>
{{-- 
    <pre><div id="result-json">JSON result will appear here after payment:<br></div></pre>  --}}

</main>


<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script><script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script>
    $(document).ready(function () {
    $('input.input-donation').keyup(function(event) {
    
    // skip for arrow keys
    if(event.which >= 37 && event.which <= 40) return;
        // format number
        $(this).val(function(index, value) {
        return value
        .replace(/\D/g, "")
        .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        ;
        });
    });
});
    </script>

    
<!-- TODO: Remove ".sandbox" from script src URL for production environment. Also input your client key in "data-client-key" -->
<script src="{{Config::get('app.midtrans_production')?'https://app.midtrans.com/snap/snap.js':'https://app.sandbox.midtrans.com/snap/snap.js'}}" data-client-key="{{$midtrans_client_key}}"></script>
<script type="text/javascript">
$(document).ready(function () {


  document.getElementById('pay-button').onclick = function(){

    var snap_token = "";
    var donation_amount = $('input.input-donation').val().split('.').join("");

    console.log(donation_amount,'asdas');
    if(donation_amount == ""){
        $("#error-amount").text("Donasi Minimal {{rupiah($donasi_minimum)}}");
        return 0;
    }
    if(parseInt(donation_amount) < {{$donasi_minimum}} ){
        $("#error-amount").text("Donasi Minimal {{rupiah($donasi_minimum)}}");
        return 0;
    }
    // SnapToken acquired from previous step
    
    console.log(donation_amount);
    $.get("{{route('payment.get_snaptoken')}}",{donation_id:{{$program->id}},amount:donation_amount}, function(data, status){
        // console.log(data);
        snap.pay(data, {
          // Optional
          onSuccess: function(result){
            /* You may add your own js here, this is just example */ 
            // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
            window.location.href = "{{route('donation')}}";
          },
          // Optional
          onPending: function(result){
            // /* You may add your own js here, this is just example */ document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
            window.location.href = "{{route('donation')}}";
          },
          // Optional
          onError: function(result){
            window.location.href = "{{route('donation')}}";
            // /* You may add your own js here, this is just example */ document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
          }
        });
    });

    
  };
});
</script>
</body>
</html>