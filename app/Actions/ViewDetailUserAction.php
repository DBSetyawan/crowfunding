<?php

namespace App\Actions;

// use TCG\Voyager\Actions\AbstractAction;
use TCG\Voyager\Actions\ViewAction as VoyagerViewAction;
class ViewDetailUserAction extends VoyagerViewAction
{
    public function getTitle()
    {
        return 'View / Detail users';
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'users';
    }

}