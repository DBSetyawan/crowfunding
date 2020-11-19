<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Search -  Kotakamal</title>
    
    <link rel="icon" href="{{asset('image/kotakamal-logo.jpg')}}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('fontawesome-free-5.13.1-web/css/all.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('slick-1.8.1/slick/slick.css')}}"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    
</head>
<body>

<header class="mainbar" id="main-bar">
    <div class="inner-cont">
        <a title="back" href="{{url('/')}}" style="width: auto; height: 33px; margin-right: 20px;">
            <i class="fas fa-arrow-left" class="back-icon" style="margin:0 10px 0 10px;"></i>
            <span>Cari</span>
        </a>
        {{-- <div class="search-bar">
            <span>Cari yang ingin kamu bantu</span>
            <i class="fas fa-search"></i>
        </div> --}}
    </div>
</header>


<main class="content" id="main">

<form action="{{route('search')}}" method="GET">
    <div class="row card-container" >
        {{-- <h1 class="main-title col-md-12">Edit Profile</h1> --}}
        {{-- <div class="form-group"> --}}
           
            
                <div class="input-search-container">
                    <input type="hidden" name="category" value="{{isset($category) ? $category->id : ''}}" />
                    <input type="hidden" name="sort" value="{{$sort ? $sort->value : ''}}">
                    <input type="text" class="input-search" placeholder="Ketik Kata Pencaharian" name="keyword" >
                    <button class="search-button" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                    
                </div>
            
            
        {{-- </div> --}}
        
        <div class="top-search-navigation-container">
            <div class="item-button-cont border-right" id="open-type-menu">
                <span class="fmBVXG">
                    <i class="fas fa-project-diagram"></i>
                </span>Tipe
            </div>
            <div class="item-button-cont border-right" id="open-category-menu">
                <span class="fmBVXG">
                    <i class="fas fa-list"></i>
                </span>Kategori
            </div>
            <div class="item-button-cont" id="open-sort-menu">
                <span class="fmBVXG">
                    <i class="fas fa-sort-amount-up-alt"></i>
                </span>Urutan
            </div>
        </div>
        <div class="divider"></div>
        <div class="parameters-container">
            @if($tipe)
            <a href="{{route('search')}}?category={{$category?$category->id:''}}&keyword={{$keyword}}&sort={{$sort?$sort->value:''}}">
                <span class="badge badge-pill badge-secondary"><i class="fas fa-times"></i>Tipe: {{$tipe}}</span>
            </a>
            @endif
            @if(strlen($keyword)>0)
            <a href="{{route('search')}}?tipe={{$tipe?$tipe:''}}&category={{$category?$category->id:''}}&sort={{$sort?$sort->value:''}}">
                <span class="badge badge-pill badge-secondary"><i class="fas fa-times"></i>Kata Kunci: {{$keyword}}</span>
            </a>
            @endif
            @if($category)
                <a href="{{route('search')}}?tipe={{$tipe?$tipe:''}}&keyword={{$keyword}}&sort={{$sort?$sort->value:''}}">
                <span class="badge badge-pill badge-secondary"><i class="fas fa-times"></i>Kategori: {{$category->program_cateogory_name}}</span>
            </a>
            @endif
            @if($sort)
            <a href="{{route('search')}}?tipe={{$tipe?$tipe:''}}&keyword={{$keyword}}&category={{$category?$category->id:''}}">
                <span class="badge badge-pill badge-secondary"><i class="fas fa-times"></i>Urutan: {{$sort->text}}</span>
            </a>
            @endif
        </div>
    </div>

</form>
    <div style="margin:0 15px;">

        @foreach ($programs as $item)
        <div>
            <a href="{{route('program.detail',['id'=>$item->id,'tipe'=>$item->type])}}" style="text-decoration:none;color:inherit;">
                <div class="style__ListContainer-sc-1sl4ulh-0 eDEnsS">
                    <figure class="style__ListFigureCanvas-sc-1sl4ulh-1 Zpwgz">
                        <img alt="{{$item->program_name}}" 
                        src="{{$item->thumbnail}}"  class="style__ListImageCanvas-sc-1sl4ulh-3 cTDNTQ">
                        <div class="style__ListImageContainer-sc-1sl4ulh-2 cROrwh"></div>
                    </figure>
                    <div class="style__ListContent-sc-1sl4ulh-4 eSaNeQ">
                        <span class="cardStyle__CardTitle-rjuxnd-0 pGMPs">{{$item->program_name}}</span>
                        {{-- <div class="cardStyle__CardSubtitle-rjuxnd-1 inzVYl">
                            <span>Darul Funun El-Abbasiyah</span>
                        </div> --}}
                        <div class="fUEXnq">
                            <div class="eifNFc">
                                <div class="fKSicX" style="width:{{isset($item->percentage) ? $item->percentage : 100 }}%;"></div>
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
                                text-align: right;"><strong> {!! isset($item->days_left) ? $item->days_left : '<i class="fas fa-infinity" style="color: rgb(74, 74, 74);"></i>'!!} </strong></span>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div style="height: 1px;
                background: rgb(232, 232, 232);"></div>
            </a>
        </div> 
        @endforeach
        

        
    </div>

</main>





<!-- sort menus -->
<header class="mainbar filter-bar" id="sort-bar" style="display:none;" >
    <div class="inner-cont" style="">
        <a href="#" id="hide-sort" style="width: auto; height: 33px; margin-right: 20px;color:rgb(59, 59, 59);">
            <i class="fas fa-times" class="back-icon" style="margin:10px 10px 10px 10px;"></i>
            <span>Urutkan</span>
        </a>
    </div>
</header>

<main class="content" id="sort-content"  style="display:none;">
    <div class="row card-container" >
        <a href="{{route('search')}}?tipe={{$tipe?$tipe:''}}&sort=date-desc&keyword={{$keyword?$keyword:''}}&category={{$category?$category->id:''}}" style="width:100%;">
            <div class="menus-item">
                <span>Tanggal Terbaru</span>
            </div>
        </a>
        <a href="{{route('search')}}?tipe={{$tipe?$tipe:''}}&sort=date-asc&keyword={{$keyword?$keyword:''}}&category={{$category?$category->id:''}}" style="width:100%;">
            <div class="menus-item">
                <span>Tanggal Terlama</span>
            </div>
        </a>
    </div>
</main>


<!-- category menus -->
<header class="mainbar filter-bar" id="category-bar" style="display:none;" >
    <div class="inner-cont" style="">
        <a href="#" id="hide-category" style="width: auto; height: 33px; margin-right: 20px;color:rgb(59, 59, 59);">
            <i class="fas fa-times" class="back-icon" style="margin:10px 10px 10px 10px;"></i>
            <span>Kategori</span>
        </a>
    </div>
</header>

<main class="content" id="category-content"  style="display:none;">
    <div class="row card-container" >
        @foreach ($categories as $item)
            <a href="{{route('search')}}?tipe={{$tipe?$tipe:''}}&category={{$item->id}}&keyword={{$keyword?$keyword:''}}&sort={{$sort?$sort->value:''}}" style="width:100%;">
                <div class="menus-item">
                    <img src="{{$item->icon}}" alt="{{$item->program_cateogory_name}}" />
                    <span>{{$item->program_cateogory_name}}</span>
                </div>
            </a>
        @endforeach
    </div>
</main>


<!-- type menus -->
<header class="mainbar filter-bar" id="type-bar" style="display:none;" >
    <div class="inner-cont" style="">
        <a href="#" id="hide-type" style="width: auto; height: 33px; margin-right: 20px;color:rgb(59, 59, 59);">
            <i class="fas fa-times" class="back-icon" style="margin:10px 10px 10px 10px;"></i>
            <span>Tipe</span>
        </a>
    </div>
</header>

<main class="content" id="type-content"  style="display:none;">
    <div class="row card-container" >
        <a href="{{route('search')}}?sort={{$sort?$sort->value:''}}&keyword={{$keyword?$keyword:''}}&category={{$category?$category->id:''}}" style="width:100%;">
            <div class="menus-item">
                <span>Semua</span>
            </div>
        </a>
        <a href="{{route('search')}}?tipe=program&sort={{$sort?$sort->value:''}}&keyword={{$keyword?$keyword:''}}&category={{$category?$category->id:''}}" style="width:100%;">
            <div class="menus-item">
                <span>Program</span>
            </div>
        </a>
        <a href="{{route('search')}}?tipe=campaign&sort={{$sort?$sort->value:''}}&keyword={{$keyword?$keyword:''}}&category={{$category?$category->id:''}}" style="width:100%;">
            <div class="menus-item">
                <span>Campaign</span>
            </div>
        </a>
    </div>
</main>



<script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>



<script>
    $( document ).ready(function() {

        $('#open-type-menu').click(function(){
            $('.mainbar').hide();
            $('.content').hide();
            $('#type-bar').show();
            $('#type-content').show();
        });
        $('#hide-type').click(function(){
            $('.mainbar').hide();
            $('.content').hide();
            $('#main-bar').show();
            $('#main').show();
        });

        $('#open-category-menu').click(function(){
            $('.mainbar').hide();
            $('.content').hide();
            $('#category-bar').show();
            $('#category-content').show();
        });
        $('#hide-category').click(function(){
            $('.mainbar').hide();
            $('.content').hide();
            $('#main-bar').show();
            $('#main').show();
        });

        $('#open-sort-menu').click(function(){
            $('.mainbar').hide();
            $('.content').hide();
            $('#sort-bar').show();
            $('#sort-content').show();
        });
        $('#hide-sort').click(function(){
            $('.mainbar').hide();
            $('.content').hide();
            $('#main-bar').show();
            $('#main').show();
        });

    });

</script>
{{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script>
    $('document').ready(function () {
        // $('.toggleswitch').bootstrapToggle();

        $('#kelurahan_id').select2({
        placeholder: "Cari Domisili...",
        minimumInputLength: 1,
        allowClear: true,
        ajax: {
            url: "{{route('auth.get_domisili_json')}}",
            dataType: 'json',
            delay: 250,
            cache: false,
            data: function (params) {
                return {
                    term: params.term,
                    page: params.page || 1,
                };
            },
            processResults: function(data, params) {
                //console.log(data);
                //  NO NEED TO PARSE DATA `processResults` automatically parse it
                //var c = JSON.parse(data);
                console.log(data);
                var page = params.page || 1;
                return {
                    results: $.map(data.results, function (item) { return {id: item.id, text: item.kelurahan+', '+item.kecamatan+', '+item.kabupaten_kota+', '+item.provinsi+', '+item.kd_pos}}),
                    pagination: {
                    // THE `10` SHOULD BE SAME AS `$resultCount FROM PHP, it is the number of records to fetch from table` 
                        more: (page * 10) <= data.count_filtered
                    }
                };
            },              
        }
    });

    });
</script> --}}
</body>
</html>