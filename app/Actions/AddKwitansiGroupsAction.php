<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class AddKwitansiGroupsAction extends AbstractAction
{
    public function getTitle()
    {
        // Action title which display in button based on current status
        // return $this->data->{'status'}=="PUBLISHED"?'Pending':'Publish';
        return 'kwitansi';
    }

    public function getIcon()
    {
        // Action icon which display in left of button based on current status
        // return $this->data->{'status'}=="PUBLISHED"?'voyager-x':'voyager-external';
        return 'voyager-list';
    }

    public function getAttributes()
    {
        // Action button class
        return [
            'class' => 'btn btn-sm btn-success pull-right',
            'target' => '_blank'
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
        return route('donaturs.print.donatur.groups', array("group_name"=>$this->data->id));
    }
}