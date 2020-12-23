<?php

namespace App\Actions;

use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Actions\AbstractAction;

class AddDonationAction extends AbstractAction
{
    public function getTitle()
    {
        return 'Add Donation';
    }

    public function getIcon()
    {
        return 'voyager-plus';
    }

    public function getAttributes()
    {
        if(Auth::user()->role->id == 3) {
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
        if(Auth::user()->role->id == 3) {
            return route('donaturs.add_donation', array("id"=>$this->data->{$this->data->getKeyName()}));
        }   
            else {
                return;
        }
    }
}