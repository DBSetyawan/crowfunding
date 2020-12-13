<?php

namespace App\Actions;

// use TCG\Voyager\Actions\AbstractAction;
use TCG\Voyager\Actions\ViewAction as VoyagerViewAction;
class ViewDonatorAction extends VoyagerViewAction
{
    public function getTitle()
    {
        return 'View / Donation History';
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'donaturs';
    }

    public function getAttributes()
    {
        // Action button class
        return [
            'class' => 'btn btn-sm btn-warning pull-right sm',
        ];
    }
    

}