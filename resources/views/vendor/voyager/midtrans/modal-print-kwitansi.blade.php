<script src="https://cdnjs.cloudflare.com/ajax/libs/validator/13.1.0/validator.js"></script>
<!-- Modal Print Kwitansi -->
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
    async function GenerateKwintansi(bulan, tahun) {
                      
                      let dataGenerateDonatur = {
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

    $('#submit-generate-print').click(function(){
        var bulan = $('#start_date').val();
        var tahun = $('#end_date').val();
        GenerateKwintansi(bulan,tahun).then(function(results){

                console.log(results)
                
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
        <h4 class="modal-title" id="myModalLabel">Print Kwitansi Bulanan Berdasarkan Bulan lalu</h4>
        </div>
        <div class="form-row" >
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="validationTooltip03">Bulan</label>
                    <input type="text" class="form-control" id="start_date" required="true">
                    <div class="invalid-tooltip">
                        {{-- Please provide a valid city. --}}
                    </div>
                </div>
            </div>
            <div class="col-md-6  mb-3">
                <div class="form-group">
                    <label for="validationTooltip04">Tahun</label>
                    <input type="text" class="form-control" id="end_date"  required="true">
                    <div class="invalid-tooltip">
                      {{-- Please provide a valid state. --}}
                    </div>
                </div>
            </div>
            <div class="col-md-12  mb-3">
                <div class="form-group" style="margin-bottom:20px;">
                    <label for="kelurahan_id">Grup Donatur</label>
                    <select class="form-control select2" id="donatur_group_id" name="donatur_group_id">
                        @foreach ($donatur_groups as $donatur_group)
                            <option value="{{$donatur_group->id}}" >{{$donatur_group->donatur_group_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            
          </div>
          
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