@extends('voyager::master')

@section('page_title', __('voyager::generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
@stop

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://api.mapbox.com/mapbox-gl-js/v1.12.0/mapbox-gl.css" rel="stylesheet" />
    <link
rel="stylesheet"
href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.5.1/mapbox-gl-geocoder.css"
type="text/css"
/>
@stop

@section('content')
    <div class="page-content container-fluid">
        <form class="form-edit-add" role="form"
              action="@if(!is_null($dataTypeContent->getKey())){{ route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) }}@else{{ route('voyager.'.$dataType->slug.'.store') }}@endif"
              method="POST" enctype="multipart/form-data" autocomplete="off">
            <!-- PUT Method if we are editing -->
            @if(isset($dataTypeContent->id))
                {{ method_field("PUT") }}
            @endif
            {{ csrf_field() }}

            <div class="row">
                <div class="col-md-8">
                    <div class="panel panel-bordered">
                    {{-- <div class="panel"> --}}
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="panel-body">
                            <div class="form-group">
                                <label for="name">{{ __('voyager::generic.name') }}</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('voyager::generic.name') }}"
                                       value="{{ old('name', $dataTypeContent->name ?? '') }}">
                            </div>

                            

                            <div class="form-group">
                                <label for="email">{{ __('voyager::generic.email') }}</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('voyager::generic.email') }}"
                                       value="{{ old('email', $dataTypeContent->email ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="password">{{ __('voyager::generic.password') }}</label>
                                @if(isset($dataTypeContent->password))
                                    <br>
                                    <small>{{ __('voyager::profile.password_hint') }}</small>
                                @endif
                                <input type="password" class="form-control" id="password" name="password" value="" autocomplete="new-password">
                            </div>


                            <div class="form-group">
                                <label for="hobi">Hobi</label>
                                <input type="text" class="form-control" id="hobi" name="hobi" placeholder="Hobi"
                                       value="{{ old('hobi', $dataTypeContent->hobi ?? '') }}">
                            </div>


                            <div class="form-group">
                                <label for="tempat_lahir">Tempat Lahir</label>
                                <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" placeholder="tempat_lahir"
                                       value="{{ old('tempat_lahir', $dataTypeContent->tempat_lahir ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="tanggal_lahir">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" placeholder="tanggal_lahir"
                                       value="{{ old('tanggal_lahir', $dataTypeContent->tanggal_lahir ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" placeholder="alamat">{{ old('alamat', $dataTypeContent->alamat ?? '') }}</textarea>
                            </div>

                            
                            <div class="form-group">
                                <label for="urban">Kelurahan</label>
                                {{--  <select class="form-control select2" id="urban" name="urban_id">  --}}
                                    {{--  <select class="form-control" id="urban_id">  --}}
                                     {{--  <div class="col-md-3">  --}}
                                            {{--  <label>Origin</label>  --}}
                                            <div id="urban_id" class="col-xl-12" style="padding: 200px;width: 100%;padding: 12px 10px;"></div>
                                        {{--  </div>  --}}
                                {{--  </select>  --}}
                            </div>

                            <div class="form-group">
                                <label for="pekerjaan">Pekerjaan</label>
                                <input type="text" class="form-control" id="pekerjaan" name="pekerjaan" placeholder="pekerjaan"
                                value="{{ old('pekerjaan', $dataTypeContent->pekerjaan ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="posisi">Posisi</label>
                                <input type="text" class="form-control" id="posisi" name="posisi" placeholder="posisi"
                                value="{{ old('posisi', $dataTypeContent->posisi ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="no_whatsapp">Whatsapp</label>
                                <input type="text" class="form-control" id="no_whatsapp" name="no_whatsapp" placeholder="no_whatsapp"
                                value="{{ old('no_whatsapp', $dataTypeContent->no_whatsapp ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="url_facebook">Whatsapp</label>
                                <input type="text" class="form-control" id="url_facebook" name="url_facebook" placeholder="url_facebook"
                                value="{{ old('url_facebook', $dataTypeContent->url_facebook ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="url_instagram">Link Instagram</label>
                                <input type="text" class="form-control" id="url_instagram" name="url_instagram" placeholder="url_instagram"
                                value="{{ old('url_instagram', $dataTypeContent->url_instagram ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="url_twitter">Link Twitter</label>
                                <input type="text" class="form-control" id="url_twitter" name="url_twitter" placeholder="url_twitter"
                                value="{{ old('url_twitter', $dataTypeContent->url_twitter ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="url_website">Link Website</label>
                                <input type="text" class="form-control" id="url_website" name="url_website" placeholder="url_website"
                                value="{{ old('url_website', $dataTypeContent->url_website ?? '') }}">
                            </div>

                            @can('editRoles', $dataTypeContent)
                                <div class="form-group">
                                    <label for="default_role">{{ __('voyager::profile.role_default') }}</label>
                                    @php
                                        $dataTypeRows = $dataType->{(isset($dataTypeContent->id) ? 'editRows' : 'addRows' )};

                                        $row     = $dataTypeRows->where('field', 'user_belongsto_role_relationship')->first();
                                        $options = $row->details;
                                    @endphp
                                    @include('voyager::formfields.relationship')
                                </div>
                                <div class="form-group">
                                    <label for="additional_roles">{{ __('voyager::profile.roles_additional') }}</label>
                                    @php
                                        $row     = $dataTypeRows->where('field', 'user_belongstomany_role_relationship')->first();
                                        $options = $row->details;
                                    @endphp
                                    @include('voyager::formfields.relationship')
                                </div>
                            @endcan
                            @php
                            if (isset($dataTypeContent->locale)) {
                                $selected_locale = $dataTypeContent->locale;
                            } else {
                                $selected_locale = config('app.locale', 'en');
                            }
                            @endphp
                            <div class="form-group">
                                <label for="locale">{{ __('voyager::generic.locale') }}</label>
                                <select class="form-control select2" id="locale" name="locale">
                                    @foreach (Voyager::getLocales() as $locale)
                                    @if($locale !== "Donatur")
                                    <option value="{{ $locale }}"
                                    {{ ($locale == $selected_locale ? 'selected' : '') }}>{{ $locale }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="panel panel panel-bordered panel-warning">
                        <div class="panel-body">
                            <div class="form-group">
                                @if(isset($dataTypeContent->avatar))
                                    <img src="{{ filter_var($dataTypeContent->avatar, FILTER_VALIDATE_URL) ? $dataTypeContent->avatar : Voyager::image( $dataTypeContent->avatar ) }}" style="width:200px; height:auto; clear:both; display:block; padding:2px; border:1px solid #ddd; margin-bottom:10px;" />
                                @endif
                                <input type="file" data-name="avatar" name="avatar">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary pull-right save">
                {{ __('voyager::generic.save') }}
            </button>   
        </form>

        <iframe id="form_target" name="form_target" style="display:none"></iframe>
        <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post" enctype="multipart/form-data" style="width:0px;height:0;overflow:hidden">
            {{ csrf_field() }}
            <input name="image" id="upload_file" type="file" onchange="$('#my_form').submit();this.value='';">
            <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
        </form>
    </div>
@stop

@section('javascript')
<script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.5.1/mapbox-gl-geocoder.min.js"></script>
<script src="https://api.mapbox.com/mapbox-gl-js/v1.12.0/mapbox-gl.js"></script>
<script src="https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.auto.min.js"></script>
    <script>
        $('document').ready(function () {

    mapboxgl.accessToken = 'pk.eyJ1IjoiZGFuaWVsZWlucyIsImEiOiJja2ZjMWl6aWQwOGk4MnhxMmwwbTh3cTFtIn0.ZUzOVi8FYutY0rqra1s7tQ';
        var geocoder = new MapboxGeocoder({
        accessToken: mapboxgl.accessToken,
        types: 'place, address, poi, postcode, district, neighborhood'
        });
        geocoder.addTo('#urban_id');

     geocoder.on('results', function(results) {
         var textfield = document.createElement("select");
            if(results.query[0]){
                textfield.name = "urban_id";
                textfield.value = results.query[0];

            
            }
        })
            $('.toggleswitch').bootstrapToggle();
            /**
                document.getElementById('aporg').name ="urban"
                document.getElementById('aporg').appendChild(textfield)
            $('#urban').select2({
                placeholder: "Cari Domisili...",
                minimumInputLength: 1,
                allowClear: true,
                ajax: {
                    url: "{{route('domisili.get_json')}}",
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
            */

        });
    </script>
@stop
