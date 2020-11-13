@extends('voyager::master')

@section('page_title', __('voyager::generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
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
                <div class="col-md-12">
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
                                <label for="nama">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder=""
                                       value="{{ old('nama', $dataTypeContent->nama ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="no_hp">No. Hp</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder=""
                                       value="{{ old('no_hp', $dataTypeContent->no_hp ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="pekerjaan">Pekerjaan</label>
                                <input type="text" class="form-control" id="pekerjaan" name="pekerjaan" placeholder=""
                                       value="{{ old('pekerjaan', $dataTypeContent->pekerjaan ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat">{{ old('alamat', $dataTypeContent->alamat ?? '') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="kelurahan_id">Pilih Domisili</label>
                                <select class="form-control select2" id="kelurahan_id" name="kelurahan_id">
                                    @if(isset($selected_domisili))
                                        <option value="{{$selected_domisili->value}}" selected>{{$selected_domisili->text}}</option>
                                    @endif
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="kelurahan_id">Grup Donatur</label>
                                <select class="form-control select2" id="donatur_group_id" name="donatur_group_id">
                                    @foreach ($donatur_groups as $donatur_group)
                                        <option value="{{$donatur_group->id}}" >{{$donatur_group->donatur_group_name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <input type="hidden" value="{{Auth::user()->id}}" name="added_by_user_id" />

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
    <script>
        $('document').ready(function () {
            $('.toggleswitch').bootstrapToggle();

            $('#kelurahan_id').select2({
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

        });
    </script>
@stop
