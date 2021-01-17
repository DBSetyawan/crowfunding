<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,maximum-scale=1.0">
	<style type="text/css" media="screen"></style>
	<style type="text/css" media="print">
	/* @page { size: 2.5in 5.5in ; size: landscape; } */ @page { width: 242mm; // height: 93mm; // /* size: Potrait; */ } body { /* page-break-before: always; /* width:100%; height:100%; */ /* -webkit-transform: rotate(-90deg) scale(.68,.68); -moz-transform:rotate(-90deg) scale(.58,.58); */ /* zoom: 200%     */ /* width: 242mm; height: 93mm; */ /* margin-left:auto; margin-right:auto; */ } table{ overflow: hidden; display: block; page-break-after: always; border-collapse: separate; border-spacing: 9px; padding: 1px; border: 1px solid black; width:100%; // !important }
	</style>
	<title>Kotak Amal Indonesia</title>
</head>

<body> @foreach ($data as $d)
	<table style="position: relative; bottom: -10px;"> @php $date = \Carbon\Carbon::parse($d->updated_at, 'UTC'); $s = $date->isoFormat('MMMM YYYY'); @endphp
		<div> {{-- {{ $d-> }} --}} </div>
		<tr>
			<td>ID Donasi</td>
			<td>:&nbsp;{{$d->id}} &nbsp;{{ $ptgname[0]->name }} &nbsp;{{$carigroup__[0]->donatur_group_name}}</td>
		</tr>
		<tr>
			<td>NID</td>
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
			<td>{{$d->program->program_name}}</td>
			<td>:&nbsp;{{$d->rupiah}} ({{$d->terbilang}})</td>
		</tr>
		<tr>
			<td>bulan/tahun Hijriah</td>
			<td>:&nbsp;{{$s}} / {{$d->tanggal_hijiriah}}</td>
		</tr>
	</table> @endforeach
	<script>
	document.addEventListener("DOMContentLoaded", function(event) { window.print(); });
	</script>
</body>

</html>