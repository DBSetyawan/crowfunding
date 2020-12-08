<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class ViewDonaturGroupsAction extends AbstractAction
{
    public function getTitle()
    {
        // Action title which display in button based on current status
        // return $this->data->{'status'}=="PUBLISHED"?'Pending':'Publish';
        return 'View | Detail Donatur';
    }

    public function getIcon()
    {
        // Action icon which display in left of button based on current status
        // return $this->data->{'status'}=="PUBLISHED"?'voyager-x':'voyager-external';
        return 'voyager-plus';
    }

    public function getAttributes()
    {
        // Action button class
        return [
            'class' => 'btn btn-sm btn-primary pull-sm-right',
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
        return route('voyager.donaturs.index.groups', array("group_id"=>$this->data->{$this->data->getKeyName()}));
    }
}