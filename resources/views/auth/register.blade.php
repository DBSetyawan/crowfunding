<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register -  Kotakamal</title>
    
    <link rel="icon" href="{{asset('image/kotakamal-logo.jpg')}}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('fontawesome-free-5.13.1-web/css/all.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('slick-1.8.1/slick/slick.css')}}"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    
</head>
<body>
<header class="mainbar">
    <div class="inner-cont">
        <a title="back" href="javascript:history.back()" style="width: auto; height: 33px; margin-right: 20px;">
            <i class="fas fa-arrow-left" class="back-icon" style="margin:0 10px 0 10px;"></i>
            <span>Daftar</span>
        </a>
        {{-- <div class="search-bar">
            <span>Cari yang ingin kamu bantu</span>
            <i class="fas fa-search"></i>
        </div> --}}
    </div>
</header>

<main class="content">

    <div class="row card-container" >
        <h1 class="main-title col-md-12">Daftar Akun Baru</h1>
        <form class="standard-form col-md-12" method="POST" action="{{route('auth.register_post')}}">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="exampleInputEmail1">Email</label>
                <input type="email" class="form-control" placeholder="Email" name="email">
                @error('email')
                    <small class="form-text text-danger">{{ $message }}</small>
                @enderror
                
            </div>

            <div class="form-group">
                <label for="exampleInputEmail1">Password</label>
                <input type="password" class="form-control" placeholder="Password" name="password">
                @error('password')
                    <small class="form-text text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="exampleInputEmail1">Repeat Password</label>
                <input type="password" class="form-control" placeholder="Repeat Password" name="password_confirmation">
                @error('password_confirmation')
                    <small class="form-text text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="exampleInputEmail1">Nama</label>
                <input type="text" class="form-control" placeholder="Nama" name="nama">
                @error('nama')
                    <small class="form-text text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="exampleInputEmail1">No. HP</label>
                <input type="text" class="form-control" placeholder="No. HP" name="no_hp">
                @error('no_hp')
                    <small class="form-text text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="exampleInputEmail1">Pekerjaan</label>
                <input type="text" class="form-control" placeholder="Pekerjaan" name="pekerjaan">
                @error('pekerjaan')
                    <small class="form-text text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="exampleInputEmail1">Alamat</label>
                <textarea class="form-control" name="alamat"></textarea>
                @error('alamat')
                    <small class="form-text text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="exampleInputEmail1">Domisili</label>
                <select class="form-control select2" id="kelurahan_id" name="urban_id">
                    @if(isset($selected_domisili))
                        <option value="{{$selected_domisili->value}}" selected>{{$selected_domisili->text}}</option>
                    @endif
                </select>
                @error('urban_id')
                    <small class="form-text text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-block btn-primary">REGISTER</button>

        </form>
    </div>

</main>

<script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
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
</script>
</body>
</html>