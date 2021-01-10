<?php

namespace App\Actions;

use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Actions\AbstractAction;

class AddCOnfirmationAction extends AbstractAction
{
    public function getTitle()
    {
        return 'Konfirmasi';
    }

    public function getIcon()
    {
        return 'voyager-plus';
    }

    public function getAttributes($req = null)
    {
        return $req;
        if(Auth::user()->role->id == 3 || Auth::user()->role->id == 1 || Auth::user()->role->id == 2) {
            return [
                'class' => 'btn btn-sm btn-primary pull-right',
            ];
        }
            else {

                return [
                    'class' => 'btn hidden btn-sm btn-primary pull-right',
                ];

            }
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'donaturs';
    }

    public function getDefaultRoute()
    {
        if(Auth::user()->role->id == 2 || Auth::user()->role->id == 3) {
            return route('donaturs.update.transaction', 
                array(
                    "id"=>$this->data->{$this->data->getKeyName()},
                    "donatur_id"=> $this->data->{$this->data->getKeyName()}
                ));
        }   
            else {
                return;
        }
    }
}