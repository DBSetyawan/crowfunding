<script src="https://cdnjs.cloudflare.com/ajax/libs/validator/13.1.0/validator.js"></script>
<!-- Modal Print Kwitansi -->
@yield('css')
<style>
 @import url(https://fonts.googleapis.com/css?family=Open+Sans);
.load {
  position: relative;
  margin: -35px auto;
  width: 100px;
  height: 80px;
}

.gear {
  position: absolute;
  z-index: -10;
  width: 40px;
  height: 40px;
  -webkit-animation: spin 5s infinite;
          animation: spin 5s infinite;
}

.two {
  left: 40px;
  width: 80px;
  height: 80px;
  -webkit-animation: spin-reverse 5s infinite;
          animation: spin-reverse 5s infinite;
}

.three {
  top: 45px;
  left: -10px;
  width: 60px;
  height: 60px;
}

@-webkit-keyframes spin {
  50% {
    -webkit-transform: rotate(360deg);
            transform: rotate(360deg);
  }
}

@keyframes spin {
  50% {
    -webkit-transform: rotate(360deg);
            transform: rotate(360deg);
  }
}
@-webkit-keyframes spin-reverse {
  50% {
    -webkit-transform: rotate(-360deg);
            transform: rotate(-360deg);
  }
}
@keyframes spin-reverse {
  50% {
    -webkit-transform: rotate(-360deg);
            transform: rotate(-360deg);
  }
}
.lil-circle {
  position: absolute;
  border-radius: 50%;
  box-shadow: inset 0 0 12px 3px gray, 0 0 15px;
  width: 100px;
  height: 100px;
  opacity: .65;
}

.blur-circle {
  position: absolute;
  top: -19px;
  left: -19px;
}

.text {
  color: lightgray;
  font-size: 18px;
  font-family: 'Open Sans', sans-serif;
  text-align: center;
}
    </style>
@section('javascript')
<script>
    $('#submit-print').click(function(){
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        if(!validator.isDate(start_date)){
            alert('Tanggal Mulai Harus Diisi!');
            return 0;
        }
        if(!validator.isDate(end_date)){
            alert('Tanggal Hingga Harus Diisi!');
            return 0;
        }

        if(!validator.isAfter(end_date,start_date)){
            alert('Tanggal Hingga tidak boleh sebelum tanggal Mulai!');
            return 0;
        }

        $("<iframe>")                             // create a new iframe element
        .hide()                               // make it invisible
        .attr("src", "{{route('donaturs.print')}}?start_date="+start_date+'&end_date='+end_date) // point the iframe to the page you want to print
        .appendTo("body");                    // add iframe to the DOM to cause it to load the page

        // if()
          
    });
    async function GenerateKwintansi(hari, bulan, tahun) {
                      
                      let dataGenerateDonatur = {
                              hari:hari,
                              bulan:bulan,
                              tahun: tahun
                          }

                  const AsyncGenerateKwintansiDonatur = "{{ route('donaturs.generate_and_print_last_month') }}";
                          
                      const settings = {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                    'Content-Type': 'application/json;charset=utf-8',
                                    'Accept': 'application/json'
                                    },
                                body: JSON.stringify(dataGenerateDonatur)
                          }

                  try {
                        
                        const fetchResponse = await fetch(`${AsyncGenerateKwintansiDonatur}`, settings);
                        const data = await fetchResponse.json();
                        return data;
                    } catch (e) {
                        return JSON.stringify(e);
                    }    

              }
        $(document).ready(function(){
            $(".load").hide();
            $(".text").hide();
        });

        function HandleRefresh() {
            let detailDonaturs = "{{ $donaturdetailid }}";
                                                    let link = '{!! route("voyager.donaturs.index", ":detailDonaturs")  !!}';
                                                    let redirect = link.replace(":detailDonaturs",detailDonaturs)

                                    setTimeout(function(){ 

                                        window.location.href = redirect;

                        }, 5000);
        }

    $('#submit-generate-print').click(function(){
        var hari = $('#hari').val();
        var bulan = $('#start_date').val();
        var tahun = $('#end_date').val();
        GenerateKwintansi(hari,bulan,tahun).then(function(results){
            setTimeout(() => {
                        $(".load").fadeIn( "slow" );
                        $(".text").fadeIn( "slow" );
                    }, 1000);
                if(results.status == true){
                    setTimeout(() => {
                            $("#alert-bulk-kwitansi").fadeIn( "slow" );
                            $(".load").fadeOut( "slow" );
                            $(".text").fadeOut( "slow" );
                        let R = [results.success];
                        $.each(R, function(s, f){
                            $("#alert-bulk-kwitansi").html(f);
                            $("#alert-bulk-kwitansi").fadeOut( "slow" );
                        })
                    }, 3000);HandleRefresh();
                }
                if(results.status == false){
                        setTimeout(() => {
                            $("#alert-bulk-kwitansi").fadeIn( "slow" );
                            $(".load").fadeOut( "slow" );
                            $(".text").fadeOut( "slow" );
                        let R = [results.failed];
                            $.each(R, function(s, f){
                                $("#alert-bulk-kwitansi").html(f);
                                $("#alert-bulk-kwitansi").fadeOut( "slow" );
                            })
                        }, 3000);
                       
                    }
                });
       
        // alert("asdas")
        // var group_id = $('#donatur_group_id').val();
        // $("<iframe>")                             // create a new iframe element
        // .hide()                               // make it invisible
        // .attr("src", "{{route('donaturs.generate_and_print_last_month')}}?group_id="+group_id) // point the iframe to the page you want to print
        // .appendTo("body");                    // add iframe to the DOM to cause it to load the page
        // if()
          
    });


    
    
</script>
@stop
<div class="modal fade modal-confirmation" id="modal-print" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Print Kwitansi Sesuai Jangka Waktu</h4>
        </div>
        <div class="modal-body" >
            <div class="form-row" style="margin-bottom:20px;">
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="validationTooltip03">Mulai</label>
                        <input type="date" class="form-control" name="start_date" placeholder="City" required>
                        <div class="invalid-tooltip">
                            {{-- Please provide a valid city. --}}
                        </div>
                    </div>
                </div>
                <div class="col-md-6  mb-3">
                    <div class="form-group">
                        <label for="validationTooltip04">Hingga</label>
                        <input type="date" class="form-control" name="end_date" placeholder="State" required>
                        <div class="invalid-tooltip">
                          {{-- Please provide a valid state. --}}
                        </div>
                    </div>
                </div>
              </div>
        </div>
        
        <div class="modal-footer">
            {{-- <form method="POST" action="{{route('donaturs.confirm_donation')}}"> --}}
                {{-- {{ csrf_field() }} --}}
            <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
        
            {{-- <input type="hidden" value="" name="donation_id" id="confirmation-donation-id" /> --}}
            <button type="button" id="submit-print" class="btn btn-primary">Print</button>
        {{-- </form> --}}
        
        </div>
    </div>
    </div>
</div>



<div class="modal fade modal-confirmation" id="modal-print-last-month" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Print Kwitansi </h4>
        </div>
    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-11">
        <div id="alert-bulk-kwitansi">
        </div>
    </div>
</div>
        </div>
        <div class="form-row" >
            <div class="col-md-4 mb-3">
                <div class="form-group">
                    <label for="validationTooltip03">Hari</label>
                    <input type="text" class="form-control" id="hari" required="true">
                    <div class="invalid-tooltip">
                        {{-- Please provide a valid city. --}}
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="form-group">
                    <label for="validationTooltip03">Bulan</label>
                    <input type="text" class="form-control" id="start_date" required="true">
                    <div class="invalid-tooltip">
                        {{-- Please provide a valid city. --}}
                    </div>
                </div>
            </div>
            <div class="col-md-4  mb-3">
                <div class="form-group">
                    <label for="validationTooltip04">Tahun</label>
                    <input type="text" class="form-control" id="end_date"  required="true">
                    <div class="invalid-tooltip">
                      {{-- Please provide a valid state. --}}
                    </div>
                </div>
            </div>
            {{-- <div class="col-md-12  mb-3">
                <div class="form-group" style="margin-bottom:20px;">
                    <label for="kelurahan_id">Grup Donatur</label>
                    <select class="form-control select2" id="donatur_group_id" name="donatur_group_id">
                        @foreach ($donatur_groups as $donatur_group)
                            <option value="{{$donatur_group->id}}" >{{$donatur_group->donatur_group_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div> --}}

            
          </div>
          <div class="load">
            <div class="gear one">
              <svg id="blue" viewbox="0 0 100 100" fill="#94DDFF">
                <path d="M97.6,55.7V44.3l-13.6-2.9c-0.8-3.3-2.1-6.4-3.9-9.3l7.6-11.7l-8-8L67.9,20c-2.9-1.7-6-3.1-9.3-3.9L55.7,2.4H44.3l-2.9,13.6      c-3.3,0.8-6.4,2.1-9.3,3.9l-11.7-7.6l-8,8L20,32.1c-1.7,2.9-3.1,6-3.9,9.3L2.4,44.3v11.4l13.6,2.9c0.8,3.3,2.1,6.4,3.9,9.3      l-7.6,11.7l8,8L32.1,80c2.9,1.7,6,3.1,9.3,3.9l2.9,13.6h11.4l2.9-13.6c3.3-0.8,6.4-2.1,9.3-3.9l11.7,7.6l8-8L80,67.9      c1.7-2.9,3.1-6,3.9-9.3L97.6,55.7z M50,65.6c-8.7,0-15.6-7-15.6-15.6s7-15.6,15.6-15.6s15.6,7,15.6,15.6S58.7,65.6,50,65.6z"></path>
              </svg>
            </div>
            <div class="gear two">
              <svg id="pink" viewbox="0 0 100 100" fill="#FB8BB9">
                <path d="M97.6,55.7V44.3l-13.6-2.9c-0.8-3.3-2.1-6.4-3.9-9.3l7.6-11.7l-8-8L67.9,20c-2.9-1.7-6-3.1-9.3-3.9L55.7,2.4H44.3l-2.9,13.6      c-3.3,0.8-6.4,2.1-9.3,3.9l-11.7-7.6l-8,8L20,32.1c-1.7,2.9-3.1,6-3.9,9.3L2.4,44.3v11.4l13.6,2.9c0.8,3.3,2.1,6.4,3.9,9.3      l-7.6,11.7l8,8L32.1,80c2.9,1.7,6,3.1,9.3,3.9l2.9,13.6h11.4l2.9-13.6c3.3-0.8,6.4-2.1,9.3-3.9l11.7,7.6l8-8L80,67.9      c1.7-2.9,3.1-6,3.9-9.3L97.6,55.7z M50,65.6c-8.7,0-15.6-7-15.6-15.6s7-15.6,15.6-15.6s15.6,7,15.6,15.6S58.7,65.6,50,65.6z"></path>
              </svg>
            </div>
            <div class="gear three">
              <svg id="yellow" viewbox="0 0 100 100" fill="#FFCD5C">
                <path d="M97.6,55.7V44.3l-13.6-2.9c-0.8-3.3-2.1-6.4-3.9-9.3l7.6-11.7l-8-8L67.9,20c-2.9-1.7-6-3.1-9.3-3.9L55.7,2.4H44.3l-2.9,13.6      c-3.3,0.8-6.4,2.1-9.3,3.9l-11.7-7.6l-8,8L20,32.1c-1.7,2.9-3.1,6-3.9,9.3L2.4,44.3v11.4l13.6,2.9c0.8,3.3,2.1,6.4,3.9,9.3      l-7.6,11.7l8,8L32.1,80c2.9,1.7,6,3.1,9.3,3.9l2.9,13.6h11.4l2.9-13.6c3.3-0.8,6.4-2.1,9.3-3.9l11.7,7.6l8-8L80,67.9      c1.7-2.9,3.1-6,3.9-9.3L97.6,55.7z M50,65.6c-8.7,0-15.6-7-15.6-15.6s7-15.6,15.6-15.6s15.6,7,15.6,15.6S58.7,65.6,50,65.6z"></path>
              </svg>
            </div>
            <div class="lil-circle"></div>
            <svg class="blur-circle">
              <filter id="blur">
                <fegaussianblur in="SourceGraphic" stddeviation="13"></fegaussianblur>
              </filter>
              <circle cx="70" cy="70" r="66" fill="transparent" stroke="white" stroke-width="40" filter="url(#blur)"></circle>
            </svg>
          </div>
          <div class="text">loading</div>
        <div class="modal-footer">
            {{-- <form method="POST" action="{{route('donaturs.confirm_donation')}}"> --}}
                {{-- {{ csrf_field() }} --}}
            <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            {{-- <input type="hidden" value="" name="donation_id" id="confirmation-donation-id" /> --}}
            <button type="button" id="submit-generate-print" class="btn btn-primary">Generate dan Print Kwitansi Bulanan</button>
        {{-- </form> --}}
        
        </div>
    </div>
    </div>
</div>