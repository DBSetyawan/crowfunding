<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,maximum-scale=1.0">

<style type="text/css" media="screen"></style>

<style type="text/css" media="print">
 
@page {
    size: 2.5in 5.5in ;
    size: landscape;
    

}    
body {
    /* page-break-before: avoid; */
    /* width:100%;
    height:100%; */
    /* -webkit-transform: rotate(-90deg) scale(.68,.68); 
    -moz-transform:rotate(-90deg) scale(.58,.58); */
    /* zoom: 200%     */

    }
table{
    margin-left:auto; margin-right:auto;
    page-break-after: always;
    border-collapse: separate;
    border-spacing: 10px;
    padding: 5px;
    border: 1px solid black;

}

</style>
<title>Landscape To Fit Paper Page</title>
</head>
<body>

@foreach ($data as $d)
<table style="position: relative;
bottom: -240px;">
    <tr>
        <td>NID</td>
        <td>:&nbsp;{{$d->id}}</td>
    </tr>
    <tr>
        <td>Nama</td>
        <td>:&nbsp;{{$d->donatur->nama}}</td>
    </tr>
    <tr>
        {{-- <td>Alamat</td> --}}
        {{-- <td>:&nbsp;{{$d->donatur->alamat}}, {{$d->donatur->domisili}}</td> --}}
    </tr>
    <tr>
        {{-- <td>Program</td> --}}
        {{-- <td>:&nbsp;{{$d->program->program_name}}</td> --}}
    </tr>
    <tr>
        <td>Jumlah</td>
        <td>:&nbsp;{{$d->rupiah}} {{$d->terbilang}}</td>
    </tr>
    <tr>
        <td>Bulan</td>
        <td>:&nbsp;{{$d->tanggal_masehi}} / {{$d->tanggal_hijiriah}}</td>
    </tr>
</table>

@endforeach
<script>
    document.addEventListener("DOMContentLoaded", function(event) { 
        window.print();
    });
</script>
</body>
</html>
