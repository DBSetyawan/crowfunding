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
<title>Kotak Amal Indonesia</title>
</head>
<body>

@foreach ($data as $d)
<table style="position: relative;
bottom: -240px;">
@php
      $date = \Carbon\Carbon::parse($d->updated_at, 'UTC');
    $s = $date->isoFormat('MMMM YYYY');    
@endphp
<div>
    {{-- {{ $d-> }} --}}
</div>
    <tr>
        <td>ID Donasi</td>
        <td>:&nbsp;{{$d->id}}</td>
    </tr>
    <tr>
        <td>Bulan-Tahun</td>
        <td>:&nbsp;{{$s}}</td>
    </tr>
    <tr>
        <td>ID donatur</td>
        <td>:&nbsp;{{$d->donatursFK->id}}</td>
    </tr>
    <tr>
        <td>Nama</td>
        <td>:&nbsp;{{$d->donatur->nama}}</td>
    </tr>
    <tr>
        <td>Alamat</td>
        <td>:&nbsp;{{$d->donatursFK->alamat}}</td>
    </tr>
    <tr>
        <td>Program</td>
        <td>:&nbsp;{{$d->program->program_name}}</td>
    </tr>
    <tr>
        <td>Nominal</td>
        <td>:&nbsp;{{$d->rupiah}} {{$d->terbilang}}</td>
    </tr>
    <tr>
        <td>bulan/tahun Hijriah</td>
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
