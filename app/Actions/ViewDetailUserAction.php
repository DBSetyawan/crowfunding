<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;
use TCG\Voyager\Actions\ViewAction as VoyagerViewAction;
class ViewDetailUserAction extends VoyagerViewAction
{
    public function getTitle()
    {
        return 'VIEW';
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'users';
    }

    public function getAttributes()
    {
        // Action button class
        return [
            'class' => 'btn btn-sm btn-success pull-right',
        ];
    }

}