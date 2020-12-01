<?php

namespace App\Http\Controllers\Voyager;

use App\Jobs\Exs;
use App\Jobs\ImidtransJobs;
use App\Jobs\ImportMIdtrns;
use App\Jobs\ImportsHistory;
use App\Jobs\JimportMidtran;
use Illuminate\Http\Request;
use App\Jobs\ExImportHistory;
use App\Imports\PetugasSheets;
use App\Jobs\ExIMportMidtrans;
use App\Jobs\ImprtJobsMidtrans;
use App\Imports\donaturgImports;
use App\Jobs\ImportDonaturGroup;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\DB;
use App\Jobs\ImportExecuteMidtrans;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use TCG\Voyager\Events\BreadDataAdded;
use Illuminate\Database\Eloquent\Model;
use App\Imports\UserAutomaticallyInsert;
use TCG\Voyager\Events\BreadDataDeleted;
use TCG\Voyager\Events\BreadDataUpdated;
use Maatwebsite\Excel\Concerns\Importable;
use TCG\Voyager\Events\BreadImagesDeleted;
use TCG\Voyager\Database\Schema\SchemaManager;
use App\Jobs\UserAutomaticallyInsertImportJobs;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use TCG\Voyager\Http\Controllers\Traits\BreadRelationshipParser;
use TCG\Voyager\Http\Controllers\VoyagerUserController as BaseVoyagerUserController;

class VoyagerUserController extends BaseVoyagerUserController
{

    use Importable;

    public function import(Request $request) 
    {
        // Excel::import(new donaturGroups, $request->file('file')->store('temp'));
        // $import = new UserAutomaticallyInsert();
        // $import->onlySheets('HISTORY BULAN OKT 2020');
        // $import->onlySheets('HISTORY batch 1');
        // Excel::queueImport(new UserAutomaticallyInsert, $request->file('file')->store('temp'));
        // Excel::queueImport($import, $request->file('file')->store('temp'));
        // $array = (new donaturGroups)->toArray($request->file('file')->store('temp'));
        // Excel::queueImport(new donaturGroups,  $request->file('file')->store('temp'));
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            

            // $file->storeAs(
            //     'public/temp', $file
            // );
        
            // // ImportMIdtrns::dispatch($filename);
            // $import = new donaturgImports();
            // $import->onlySheets('HISTORY BULAN OKT 2020');

            // $import = new FertilizerImport();
            // $file = $request->file('file')->store('temp');
            // dispatch(new ($import));
            // return $file;die;
            $file->storeAs(
                'public/temp', $filename
            );

            ImportExecuteMidtrans::dispatch($filename);

            return redirect()->back();
            // (new UserAutomaticallyInsert)->queue(storage_path('app/public/temp/'.$filename));
            //  Excel::import($import, $request->file('file')->store('temp'));

            // (new ImidtransJobs::dispatch(file))->queue(storage_path('app/public/temp/'.$filename));
            // (new Exs($file))->queue($request->file('file'));

            // return back();
            

        }  
    }

    use BreadRelationshipParser;
    //***************************************
    //               ____
    //              |  _ \
    //              | |_) |
    //              |  _ <
    //              | |_) |
    //              |____/
    //
    //      Browse our Data Type (B)READ
    //
    //****************************************

    public function index(Request $request)
    {
        $slug = $this->getSlug($request);
        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        
        // $additionalSlug = 'users'; // for example
        // $additionalDataType = Voyager::model('DataType')->where('slug', '=', $additionalSlug)->first();
        // Check permission
        $this->authorize('browse', app($dataType->model_name));

        $getter = $dataType->server_side ? 'paginate' : 'get';

        $search = (object) ['value' => $request->get('s'), 'key' => $request->get('key'), 'filter' => $request->get('filter')];
        $searchable = $dataType->server_side ? array_keys(SchemaManager::describeTable(app($dataType->model_name)->getTable())->toArray()) : '';
        $orderBy = $request->get('order_by');
        $sortOrder = $request->get('sort_order', null);

        // Next Get or Paginate the actual content from the MODEL that corresponds to the slug DataType
        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);
            $query = $model::select('*');
            // $query = $model::select('*')->whereIn('parent_id', [auth()->user()->role->name]);

            // If a column has a relationship associated with it, we do not want to show that field
            $this->removeRelationshipField($dataType, 'browse');

            if ($search->value && $search->key && $search->filter) {
                $search_filter = ($search->filter == 'equals') ? '=' : 'LIKE';
                $search_value = ($search->filter == 'equals') ? $search->value : '%'.$search->value.'%';
                $query->where($search->key, $search_filter, $search_value);
            }

            if ($orderBy && in_array($orderBy, $dataType->fields())) {
                $querySortOrder = (!empty($sortOrder)) ? $sortOrder : 'DESC';
                $dataTypeContent = call_user_func([
                    $query->orderBy($orderBy, $querySortOrder),
                    $getter,
                ]);
            } elseif ($model->timestamps) {
                $dataTypeContent = call_user_func([$query->latest($model::CREATED_AT), $getter]);
            } else {
                $dataTypeContent = call_user_func([$query->orderBy($model->getKeyName(), 'DESC'), $getter]);
            }

            // Replace relationships' keys for labels and create READ links if a slug is provided.
            $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType);
        } else {
            // If Model doesn't exist, get data from table name
            $dataTypeContent = call_user_func([DB::table($dataType->name), $getter]);
            $model = false;
        }

        // Check if BREAD is Translatable
        if (($isModelTranslatable = is_bread_translatable($model))) {
            $dataTypeContent->load('translations');
        }

        // Check if server side pagination is enabled
        $isServerSide = isset($dataType->server_side) && $dataType->server_side;

        $view = 'voyager::bread.browse';

        if (view()->exists("voyager::$slug.browse")) {
            $view = "voyager::$slug.browse";
        }

        // $dataTypeBrowseRows = $dataType->browseRows->merge($additionalDataType->browseRows);

        return Voyager::view($view, compact(
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'search',
            'orderBy',
            'sortOrder',
            'searchable',
            'isServerSide'
        ));
    }

    public function profile(Request $request)
    {
        $route = '';
        $dataType = Voyager::model('DataType')->where('model_name', Auth::guard(app('VoyagerGuard'))->getProvider()->getModel())->first();
        if (!$dataType && app('VoyagerGuard') == 'web') {
            $route = route('voyager.users.edit', Auth::user()->getKey());
        } elseif ($dataType) {
            $route = route('voyager.'.$dataType->slug.'.edit', Auth::user()->getKey());
        }

        return Voyager::view('voyager::profile', compact('route'));
    }

    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        if (Auth::user()->getKey() == $id) {
            $request->merge([
                'role_id'                              => Auth::user()->role_id,
                'user_belongstomany_role_relationship' => Auth::user()->roles->pluck('id')->toArray(),
            ]);
        }

        return parent::update($request, $id);
    }
    
}
