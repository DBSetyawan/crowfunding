<?php

namespace App\Actions;

// use TCG\Voyager\Actions\AbstractAction;
use TCG\Voyager\Actions\ViewAction as VoyagerViewAction;
class ViewDonatorAction extends VoyagerViewAction
{
    public function getTitle()
    {
        return 'VIEW';
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'donaturs';
    }

    public function getIcon()
    {
        // Action icon which display in left of button based on current status
        // return $this->data->{'status'}=="PUBLISHED"?'voyager-x':'voyager-external';
        return 'voyager-window-list';
    }

    public function getAttributes()
    {
        // Action button class
        return [
            'class' => 'btn btn-sm btn-success pull-right',
        ];
    }
    

}