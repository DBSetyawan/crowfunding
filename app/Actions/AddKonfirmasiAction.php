<?php

namespace App\Actions;

use App\Midtran;
use TCG\Voyager\Actions\AbstractAction;
use Illuminate\Support\Facades\Auth;

class AddKonfirmasiAction extends AbstractAction
{
    public function getTitle()
    {
        return 'Konfirmasi';
    }

    public function getIcon()
    {
        // Action icon which display in left of button based on current status
        // return $this->data->{'status'}=="PUBLISHED"?'voyager-x':'voyager-external';
        return 'voyager-window-list';
    }

    public function getAttributes()
    {
        $checks = Midtran::whereNotIn('payment_status', ['settlement'])->first();
        // return [
        //     'class' => "btn btn-sm btn-success pull-right stats_pembayaran",
        //     'data-id' => $this->data->{$this->data->getKeyName()}
        // ];

        // // dd(Auth::user()->role->id);
        if(Auth::user()->role->id == 2 || Auth::user()->role->id == 3){
            if($checks->payment_status == "kwitansi" || "on_funding"){

                return [
                    'class' => "btn btn-sm btn-warning pull-right",
                    'data-id' => $this->data->{$this->data->getKeyName()}
                ];

            }else{}
        } else {
            return [
                'class' => "btn btn-sm hidden",
                'data-id' => $this->data->{$this->data->getKeyName()}
            ];
        }
       
    }

    public function shouldActionDisplayOnDataType()
    {
        // show or hide the action button, in this case will show for posts model
        return $this->dataType->slug == 'donaturs';
    }
    
    public function getDefaultRoute()
    {
        // exit;
        // URL for action button when click
        return route('donaturs.groups.form.confirmation', array("id"=>$this->data->{$this->data->getKeyName()}));
    }
    

}