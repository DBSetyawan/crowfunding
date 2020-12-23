<?php

namespace App\Widgets;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Widgets\BaseDimmer;
use App\Donatur;
use DB;
class DonaturDimmer extends BaseDimmer
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $count = Donatur::count();
        $string = 'Donatur';

        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-people',
            'title'  => "{$count} {$string}",
            'text'   => __('voyager::dimmer.post_text', ['count' => $count, 'string' => Str::lower($string)]),
            'button' => [
                'text' => 'View All Donaturs',
                'link' => route('voyager.donaturs.index'),
            ],
            'image' => voyager_asset('images/widget-backgrounds/02.jpg'),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        $count = DB::table('users')->join('user_roles','users.id','user_roles.user_id')
        ->join('permission_role','permission_role.role_id','user_roles.role_id')
        ->join('permissions','permissions.id','permission_role.permission_id')
        ->where('users.id',Auth::user()->id)
        ->where('permissions.key','browse_donaturs')
        ->count();
        // dd(Auth::user()->role);
        if(Auth::user()->role->id == 1){
            return true;
        }else{
            return false;
        }
        // return Auth::user()->can('browse');
    }
}
