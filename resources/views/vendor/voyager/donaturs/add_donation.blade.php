
@extends('voyager::master')

{{-- @section('page_title', __('voyager::generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular')) --}}

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-plus"></i>
        Add Donation
    </h1>
@stop

@section('content')
    <div class="page-content container-fluid">


        <form class="form-edit-add" role="form"
            action="{{route('donaturs.store_donation')}}"
            method="POST" enctype="multipart/form-data" autocomplete="off">

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
                            <h3>Donasi</h3>

                            <input type="hidden" name="donatur_id" value="{{$donatur->id}}" />

                            <div class="form-group">
                                <label for="program_id">Pilih Program</label>
                                <select class="form-control select2" id="program_id" name="program_id" required>
                                    @foreach ($programs as $program)
                                <option value="{{$program->id}}">{{$program->program_name}}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="form-group">
                                <label for="amount">Jumlah Donasi (Rp)</label>
                                <input type="number" class="form-control" id="amount" name="amount" placeholder="" value="" min="1" required>
                            </div>



                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="panel panel-bordered">
                    <div class="panel-body">
                        <h3>Data Donatur</h3>

                        
                        <div class="row">
                            <div class="col-md-4">
                                <span>Nama</span>
                            </div>
                            <div class="col-md-8">
                                <span>{{$donatur->nama}}</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <span>No. Hp</span>
                            </div>
                            <div class="col-md-8">
                                <span>{{$donatur->no_hp}}</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <span>Pekerjaan</span>
                            </div>
                            <div class="col-md-8">
                                <span>{{$donatur->pekerjaan}}</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <span>Alamat</span>
                            </div>
                            <div class="col-md-8">
                                <span>{{$donatur->alamat}}</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <span>Domisili</span>
                            </div>
                            <div class="col-md-8">
                                <span>KODEPOS:{{$donatur->kelurahan->kd_pos}}, Kel. {{$donatur->kelurahan->kelurahan}}, Kec. {{$donatur->kelurahan->kecamatan->kecamatan}}, Kota/Kab. {{$donatur->kelurahan->kecamatan->kabkot->kabupaten_kota}}, Prov. {{$donatur->kelurahan->kecamatan->kabkot->provinsi->provinsi}}
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary pull-right save">
                Add Donation
            </button>
        </form>
    </div>
@stop

@section('javascript')
    <script>
        $('document').ready(function () {
            $('.toggleswitch').bootstrapToggle();


            
        
        });
    </script>
@stop
