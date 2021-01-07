<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class AddDeleteDonaturgroupsAction extends AbstractAction
{
    public function getTitle()
    {
        // Action title which display in button based on current status
        // return $this->data->{'status'}=="PUBLISHED"?'Pending':'Publish';
        return 'delete';
    }

    public function getIcon()
    {
        // Action icon which display in left of button based on current status
        // return $this->data->{'status'}=="PUBLISHED"?'voyager-x':'voyager-external';
        return 'voyager-trash';
    }

    public function getAttributes()
    {
        // Action button class
        return [
            'class' => 'hidden btn btn-sm btn-danger pull-right',
        ];
    }

    public function shouldActionDisplayOnDataType()
    {
        // show or hide the action button, in this case will show for posts model
        return $this->dataType->slug == 'donatur-groups';
    }

    public function getDefaultRoute()
    {
        // URL for action button when click
        return route('voyager.donatur-groups.destroy', array("id"=>$this->data->{$this->data->getKeyName()}));
    }
}