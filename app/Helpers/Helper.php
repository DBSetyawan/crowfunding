<?php
 
if (!function_exists('rupiah')) {

    function rupiah($angka){
	
        $hasil_rupiah = "Rp " . number_format($angka,2,',','.');
        return $hasil_rupiah;
     
    }
     
}



if (!function_exists('get_days_between_dates')) {
   
    function get_days_between_dates($d1,$d2){
        if(strtotime($d1) >= strtotime($d2)){
            return 0;
        }else{
            $date1 = new DateTime($d1);
            $date2 = new DateTime($d2);
            
            return $date2->diff($date1)->format('%a');
        }
        
        
    }
     
}