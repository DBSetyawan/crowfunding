
@extends('voyager::master')

{{-- @section('page_title', __('voyager::generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular')) --}}

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-plus"></i>
        Informasi pembayaran donasi
    </h1>
@stop

@section('content')
    <div class="page-content container-fluid">
        <form class="form-edit-add" role="form"
            action="{{route('donaturs.confirm_donation')}}"
            method="POST">
            {{ csrf_field() }}
            <input type="hidden" value="" name="donation_id" id="confirmation-donation-id" />
            <div class="row">
                <div class="col-md-8">
                    <div class="panel panel-bordered">
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    {{-- @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach --}}
                                </ul>
                            </div>
                        @endif

                        <div class="panel-body">
                            <div class="contanier">
                            <?php if(Auth::user()->role->id == 1 || Auth::user()->role->id == 2){ 
                                        echo " Dengan melakukan ini anda akan merubah status menjadi <b>Settlements</b>";
                                    }else if(Auth::user()->role->id == 3){
                                        echo " Dengan melakukan ini anda akan merubah status menjadi <b>On Funding</b>";
                                }
                            ?>  
                            {{-- &nbsp;<button type="submit" class="btn btn-primary pull-right save">LUNASI</button>&nbsp; --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
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