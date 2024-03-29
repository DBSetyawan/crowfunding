<?php

namespace App\Http\Controllers\Voyager;

use App\User;
use DataTables;
use App\Donatur;
use App\Midtran;
use App\Jobs\Exs;
use Carbon\Carbon;
use App\DonaturGroup;
use App\Jobs\ImidtransJobs;
use App\Jobs\ImportMIdtrns;
use App\Jobs\ImportsHistory;
use App\Jobs\JimportMidtran;
use Illuminate\Http\Request;
use App\Jobs\ExImportHistory;
use App\Jobs\UserImportsCase;
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
use App\Http\Controllers\Voyager\ProgramGroupsController;
use TCG\Voyager\Http\Controllers\Traits\BreadRelationshipParser;
use TCG\Voyager\Http\Controllers\VoyagerUserController as BaseVoyagerUserController;

class VoyagerUserController extends BaseVoyagerUserController
{

    use BreadRelationshipParser, Importable;

    public function store(Request $request)
    {
        // dd($request->all());
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();
        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());

        event(new BreadDataAdded($dataType, $data));

        if (!$request->has('_tagging')) {
            if (auth()->user()->can('browse', $data)) {
                $redirect = redirect()->route("voyager.{$dataType->slug}.index");
            } else {
                $redirect = redirect()->back();
            }

            return $redirect->with([
                'message'    => __('voyager::generic.successfully_added_new')." {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
        } else {
            return response()->json(['success' => true, 'data' => $data]);
        }
    }

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

            UserImportsCase::dispatch($filename);

            return redirect()->back();
            // (new UserAutomaticallyInsert)->queue(storage_path('app/public/temp/'.$filename));
            //  Excel::import($import, $request->file('file')->store('temp'));

            // (new ImidtransJobs::dispatch(file))->queue(storage_path('app/public/temp/'.$filename));
            // (new Exs($file))->queue($request->file('file'));

            // return back();
            

        }  
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('browse_bread');

        /* @var \TCG\Voyager\Models\DataType $dataType */
        $dataType = Voyager::model('DataType')->find($id);

        // Delete Translations, if present
        if (is_bread_translatable($dataType)) {
            $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
        }

        $res = Voyager::model('DataType')->destroy($id);
        $data = $res
            ? $this->alertSuccess(__('voyager::bread.success_remove_bread', ['datatype' => $dataType->name]))
            : $this->alertError(__('voyager::bread.error_updating_bread'));
        if ($res) {
            event(new BreadDeleted($dataType, $data));
        }

        if (!is_null($dataType)) {
            Voyager::model('Permission')->removeFrom($dataType->name);
        }

        return redirect()->route('voyager.bread.index')->with($data);
    }

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

    public function index(Request $request, $status = NULL)
    {

        $tahun = Carbon::parse($request->tahun);
        $tahun->format('m');
        $bulan = Carbon::parse($request->tahun);
        $bulan->format('Y');

        if(Auth::user()->role->id == 3){
    
            return redirect()->route('voyager.donatur-groups.index.detail', ['id'=> Auth::user()->id ]); 
            
        }

        // if(Auth::user()->role->id == 2){
    
        //     // return redirect()->route('donaturs.sub.amil.history', ['group_id'=> Auth::user()->id ]); 
        //     return redirect()->route('voyager.donatur-groups.index.detail', ['id'=> Auth::user()->id ]); 

            
        // }

        $slug = $this->getSlug($request);

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('browse', app($dataType->model_name));

        $getter = $dataType->server_side ? 'paginate' : 'get';

        $search = (object) ['value' => $request->get('s'), 'key' => $request->get('key'), 'filter' => $request->get('filter')];

        $searchNames = [];
        if ($dataType->server_side) {
            $searchable = SchemaManager::describeTable(app($dataType->model_name)->getTable())->pluck('name')->toArray();
            $dataRow = Voyager::model('DataRow')->whereDataTypeId($dataType->id)->get();
            foreach ($searchable as $key => $value) {
                $field = $dataRow->where('field', $value)->first();
                $displayName = ucwords(str_replace('_', ' ', $value));
                if ($field !== null) {
                    $displayName = $field->getTranslatedAttribute('display_name');
                }
                $searchNames[$value] = $displayName;
            }
        }

        $orderBy = $request->get('order_by', $dataType->order_column);
        $sortOrder = $request->get('sort_order', $dataType->order_direction);
        $usesSoftDeletes = false;
        $showSoftDeleted = false;

        // Next Get or Paginate the actual content from the MODEL that corresponds to the slug DataType
        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);
            // dd(Auth::user()->role->id);
            if(Auth::user()->role->id == 1){
            $query = $model->whereIn('role_id', [2]);
                // dd($query);
                foreach ($query->get() as $key => $value) {
                    # code...
                    $namacabang[] = $value->name;
                }
            

            } else {

                if(Auth::user()->role->id == 2){

                    return $this->show($request, Auth::user()->id);
                    
                }

                if(Auth::user()->role->id == 3){

                    return $this->show($request, Auth::user()->id);
                    
                }
    
            }

            // dd($namacabang);

            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $query = $model->{$dataType->scope}();
            } 
            
            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model)) && Auth::user()->can('delete', app($dataType->model_name))) {
                $usesSoftDeletes = true;

                if ($request->get('showSoftDeleted')) {
                    $showSoftDeleted = true;
                    $query = $query->withTrashed();
                }
            }

            // If a column has a relationship associated with it, we do not want to show that field
            $this->removeRelationshipField($dataType, 'browse');

            if ($search->value != '' && $search->key && $search->filter) {
                $search_filter = ($search->filter == 'equals') ? '=' : 'LIKE';
                $search_value = ($search->filter == 'equals') ? $search->value : '%'.$search->value.'%';
                $query->where($search->key, $search_filter, $search_value);
            }

            if ($orderBy && in_array($orderBy, $dataType->fields())) {
                $querySortOrder = (!empty($sortOrder)) ? $sortOrder : 'desc';
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
        $isModelTranslatable = is_bread_translatable($model);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'browse', $isModelTranslatable);

        // Check if server side pagination is enabled
        $isServerSide = isset($dataType->server_side) && $dataType->server_side;

        // Check if a default search key is set
        $defaultSearchKey = $dataType->default_search_key ?? null;

        // Actions
        $actions = [];
        if (!empty($dataTypeContent->first())) {
            foreach (Voyager::actions() as $action) {
                $action = new $action($dataType, $dataTypeContent->first());

                if ($action->shouldActionDisplayOnDataType()) {
                    $actions[] = $action;
                }
            }
        }

        // Define showCheckboxColumn
        $showCheckboxColumn = false;
        if (Auth::user()->can('delete', app($dataType->model_name))) {
            $showCheckboxColumn = true;
        } else {
            foreach ($actions as $action) {
                if (method_exists($action, 'massAction')) {
                    $showCheckboxColumn = true;
                }
            }
        }

        // Define orderColumn
        $orderColumn = [];
        if ($orderBy) {
            $index = $dataType->browseRows->where('field', $orderBy)->keys()->first() + ($showCheckboxColumn ? 1 : 0);
            $orderColumn = [[$index, $sortOrder ?? 'desc']];
        }

        $view = 'voyager::bread.browse';

        if (view()->exists("voyager::$slug.browse")) {
            $view = "voyager::$slug.browse";
        }
        return Voyager::view($view, compact(
            'actions',
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'search',
            'orderBy',
            'orderColumn',
            'sortOrder',
            'searchNames',
            'isServerSide',
            'defaultSearchKey',
            'usesSoftDeletes',
            'showSoftDeleted',
            'showCheckboxColumn',
            'namacabang',
            'status'
        ));
    }

    public function filter_bln_thn_cbg(Request $request)
    {
        $tahun = Carbon::parse($request->tahun)->format('Y');
        $bulan = Carbon::parse($request->bulan)->format('m');

        if(Auth::user()->role->id == 3){
    
            return redirect()->route('voyager.donatur-groups.index.detail', ['id'=> Auth::user()->id ]); 
            
        }

        $slug = $this->getSlug($request);

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('browse', app($dataType->model_name));

        $getter = $dataType->server_side ? 'paginate' : 'get';

        $search = (object) ['value' => $request->get('s'), 'key' => $request->get('key'), 'filter' => $request->get('filter')];

        $searchNames = [];
        if ($dataType->server_side) {
            $searchable = SchemaManager::describeTable(app($dataType->model_name)->getTable())->pluck('name')->toArray();
            $dataRow = Voyager::model('DataRow')->whereDataTypeId($dataType->id)->get();
            foreach ($searchable as $key => $value) {
                $field = $dataRow->where('field', $value)->first();
                $displayName = ucwords(str_replace('_', ' ', $value));
                if ($field !== null) {
                    $displayName = $field->getTranslatedAttribute('display_name');
                }
                $searchNames[$value] = $displayName;
            }
        }

        $orderBy = $request->get('order_by', $dataType->order_column);
        $sortOrder = $request->get('sort_order', $dataType->order_direction);
        $usesSoftDeletes = false;
        $showSoftDeleted = false;

        // Next Get or Paginate the actual content from the MODEL that corresponds to the slug DataType
        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);
            // dd(Auth::user()->role->permissions);
            if(Auth::user()->role->id == 1){
            $query = $model->whereIn('role_id', [2]);
                // dd($query);
                foreach ($query->get() as $key => $value) {
                    # code...
                    $namacabang[] = $value->name;
                }
            

            }

            if(Auth::user()->role->id == 2){

                return $this->show($request, Auth::user()->id);
                
            }

            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $query = $model->{$dataType->scope}();
            } 
            
            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model)) && Auth::user()->can('delete', app($dataType->model_name))) {
                $usesSoftDeletes = true;

                if ($request->get('showSoftDeleted')) {
                    $showSoftDeleted = true;
                    $query = $query->withTrashed();
                }
            }

            // If a column has a relationship associated with it, we do not want to show that field
            $this->removeRelationshipField($dataType, 'browse');

            if ($search->value != '' && $search->key && $search->filter) {
                $search_filter = ($search->filter == 'equals') ? '=' : 'LIKE';
                $search_value = ($search->filter == 'equals') ? $search->value : '%'.$search->value.'%';
                $query->where($search->key, $search_filter, $search_value);
            }

            if ($orderBy && in_array($orderBy, $dataType->fields())) {
                $querySortOrder = (!empty($sortOrder)) ? $sortOrder : 'desc';
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
        $isModelTranslatable = is_bread_translatable($model);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'browse', $isModelTranslatable);

        // Check if server side pagination is enabled
        $isServerSide = isset($dataType->server_side) && $dataType->server_side;

        // Check if a default search key is set
        $defaultSearchKey = $dataType->default_search_key ?? null;

        // Actions
        $actions = [];
        if (!empty($dataTypeContent->first())) {
            foreach (Voyager::actions() as $action) {
                $action = new $action($dataType, $dataTypeContent->first());

                if ($action->shouldActionDisplayOnDataType()) {
                    $actions[] = $action;
                }
            }
        }

        // Define showCheckboxColumn
        $showCheckboxColumn = false;
        if (Auth::user()->can('delete', app($dataType->model_name))) {
            $showCheckboxColumn = true;
        } else {
            foreach ($actions as $action) {
                if (method_exists($action, 'massAction')) {
                    $showCheckboxColumn = true;
                }
            }
        }

        // Define orderColumn
        $orderColumn = [];
        if ($orderBy) {
            $index = $dataType->browseRows->where('field', $orderBy)->keys()->first() + ($showCheckboxColumn ? 1 : 0);
            $orderColumn = [[$index, $sortOrder ?? 'desc']];
        }

        $view = 'voyager::bread.browse';

        if (view()->exists("voyager::$slug.browse")) {
            $view = "voyager::$slug.browse";
        }
        return Voyager::view($view, compact(
            'actions',
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'search',
            'orderBy',
            'orderColumn',
            'sortOrder',
            'searchNames',
            'isServerSide',
            'defaultSearchKey',
            'usesSoftDeletes',
            'showSoftDeleted',
            'showCheckboxColumn',
            'namacabang',
            'bulan',
            'tahun'
        ));
    }

    public function relation(Request $request)
    {
        $slug = $this->getSlug($request);
        $page = $request->input('page');
        $on_page = 50;
        $search = $request->input('search', false);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $method = $request->input('method', 'add');

        $model = app($dataType->model_name);
        if ($method != 'add') {
            $model = $model->find($request->input('id'));
        }

        $this->authorize($method, $model);

        $rows = $dataType->{$method.'Rows'};
        foreach ($rows as $key => $row) {
            if ($row->field === $request->input('type')) {
                $options = $row->details;
                $model = app($options->model);
                $skip = $on_page * ($page - 1);

                // Apply local scope if it is defined in the relationship-options
                if (isset($options->scope) && $options->scope != '' && method_exists($model, 'scope'.ucfirst($options->scope))) {
                    $model = $model->{$options->scope}();
                }

                // If search query, use LIKE to filter results depending on field label
                if ($search) {
                    // If we are using additional_attribute as label
                    if (in_array($options->label, $model->additional_attributes ?? [])) {
                        $relationshipOptions = $model->all();
                        $relationshipOptions = $relationshipOptions->filter(function ($model) use ($search, $options) {
                            return stripos($model->{$options->label}, $search) !== false;
                        });
                        $total_count = $relationshipOptions->count();
                        $relationshipOptions = $relationshipOptions->forPage($page, $on_page);
                    } else {
                        $total_count = $model->where($options->label, 'LIKE', '%'.$search.'%')->count();
                        $relationshipOptions = $model->take($on_page)->skip($skip)
                            ->where($options->label, 'LIKE', '%'.$search.'%')
                            ->get();
                    }
                } else {
                    $total_count = $model->count();
                    $relationshipOptions = $model->take($on_page)->skip($skip)->get();
                }

                $results = [];

                if (!$row->required && !$search && $page == 1) {
                    $results[] = [
                        'id'   => '',
                        'text' => __('voyager::generic.none'),
                    ];
                }

                // Sort results
                if (!empty($options->sort->field)) {
                    if (!empty($options->sort->direction) && strtolower($options->sort->direction) == 'desc') {
                        $relationshipOptions = $relationshipOptions->sortByDesc($options->sort->field);
                    } else {
                        $relationshipOptions = $relationshipOptions->sortBy($options->sort->field);
                    }
                }

                foreach ($relationshipOptions as $relationshipOption) {
                    $results[] = [
                        'id'   => $relationshipOption->{$options->key},
                        'text' => $relationshipOption->{$options->label},
                    ];
                }

                return response()->json([
                    'results'    => $results,
                    'pagination' => [
                        'more' => ($total_count > ($skip + $on_page)),
                    ],
                ]);
            }
        }

        // No result found, return empty array
        return response()->json([], 404);
    }

    public function create(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        $dataTypeContent = (strlen($dataType->model_name) != 0)
                            ? new $dataType->model_name()
                            : false;

        foreach ($dataType->addRows as $key => $row) {
            $dataType->addRows[$key]['col_width'] = $row->details->width ?? 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'add');

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'add', $isModelTranslatable);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
    }

    public function show(Request $request, $id){
        
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $isSoftDeleted = false;

        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
                $model = $model->withTrashed();
            }
            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $model = $model->{$dataType->scope}();
            }
            $dataTypeContent = call_user_func([$model, 'findOrFail'], $id);
            if ($dataTypeContent->deleted_at) {
                $isSoftDeleted = true;
            }
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
        }

        // Replace relationships' keys for labels and create READ links if a slug is provided.
        $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType, true);

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'read');

        // Check permission
        $this->authorize('read', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'read', $isModelTranslatable);

        $view = 'voyager::bread.read';

        if (view()->exists("voyager::$slug.read")) {
            $view = "voyager::$slug.read";
        }

        // dd($dataTypeContent);

        // $kelurahan = Kelurahan::where('id',$dataTypeContent->kelurahan_id)->first();
        // $dataTypeContent->domisili="";
        // if($kelurahan){
        //     $dataTypeContent->domisili = $kelurahan->kelurahan.', '.$kelurahan->kecamatan->kecamatan.', '.$kelurahan->kecamatan->kabkot->kabupaten_kota.', '.$kelurahan->kecamatan->kabkot->provinsi->provinsi.', '.$kelurahan->kd_pos;
        // }
        $dataTypeContent->donatur_group =  DonaturGroup::where('id', $dataTypeContent->groups_id)->first();

        $dataTypeContent->added_by_user = User::where('add_by_user_id', $dataTypeContent->id)->get();

        // dd(Auth::user()->role);
        
        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'isSoftDeleted'));
    }

    // public function edit(Request $request, $id)
    // {
    //     $slug = $this->getSlug($request);

    //     $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

    //     if (strlen($dataType->model_name) != 0) {
    //         $model = app($dataType->model_name);

    //         // Use withTrashed() if model uses SoftDeletes and if toggle is selected
    //         if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
    //             $model = $model->withTrashed();
    //         }
    //         if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
    //             $model = $model->{$dataType->scope}();
    //         }
    //         $dataTypeContent = call_user_func([$model, 'findOrFail'], $id);
    //     } else {
    //         // If Model doest exist, get data from table name
    //         $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
    //     }

    //     foreach ($dataType->editRows as $key => $row) {
    //         $dataType->editRows[$key]['col_width'] = isset($row->details->width) ? $row->details->width : 100;
    //     }

    //     // If a column has a relationship associated with it, we do not want to show that field
    //     $this->removeRelationshipField($dataType, 'edit');

    //     // Check permission
    //     $this->authorize('edit', $dataTypeContent);

    //     // Check if BREAD is Translatable
    //     $isModelTranslatable = is_bread_translatable($dataTypeContent);

    //     // Eagerload Relations
    //     $this->eagerLoadRelations($dataTypeContent, $dataType, 'edit', $isModelTranslatable);

    //     $view = 'voyager::bread.edit-add';

    //     if (view()->exists("voyager::$slug.edit-add")) {
    //         $view = "voyager::$slug.edit-add";
    //     }
    //     // $kelurahan = Kelurahan::where('id',$dataTypeContent->kelurahan_id)->first();
    //     // $selected_domisili = (object)array('value'=>'','text'=>'');
    //     // if($kelurahan){
    //     //     $selected_domisili->value = $kelurahan->id;
    //     //     $selected_domisili->text = $kelurahan->kelurahan.', '.$kelurahan->kecamatan->kecamatan.', '.$kelurahan->kecamatan->kabkot->kabupaten_kota.', '.$kelurahan->kecamatan->kabkot->provinsi->provinsi.', '.$kelurahan->kd_pos;
    //     // }
    //     // $donatur_groups = DonaturGroup::all();

    //     return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
    // }
    public function edit(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
                $model = $model->withTrashed();
            }
            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $model = $model->{$dataType->scope}();
            }
            $dataTypeContent = call_user_func([$model, 'findOrFail'], $id);
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
        }

        foreach ($dataType->editRows as $key => $row) {
            $dataType->editRows[$key]['col_width'] = isset($row->details->width) ? $row->details->width : 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'edit');

        // Check permission
        $this->authorize('edit', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'edit', $isModelTranslatable);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
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

    
    public function donaturDetailTransaction(Request $request, $group_id)
    {
        // $data = User::with('role','AmilDonaturGroup')->whereIn('parent_id', [$parent_id])->get();
        return redirect()->route("voyager.donatur-groups.index.detail", ['id' => $group_id]);

        // if ($request->ajax()) {
        //     $data = Donatur::whereIn('donatur_group_id', [$group_id])->get();
    
        //         return Datatables::of($data)
        //                 ->addIndexColumn()
        //                 ->addColumn('action', function($row){
    
        //                     // dd($row);
        //                     // if($row->payment_gateway !== "offline"){
        //                     //     return "";
        //                     // }else{
        //                     //     $disable="";
        //                     //     if($row->payment_status == "settlement"){
        //                     //         return "";
        //                     //     }
        //                     //     $btn = '<button type="button" class="btn btn-primary btn-lg button-confirmation" data-toggle="modal" data-target="#myModal" data-id="'.$row->id.'" '.$disable.'>Konfirmasi</button>';
        //                     //     return $btn;
        //                     // }
        //                     $btn = '<a href="/'.$row->id.'" class="btn btn-primary btn-lg button-confirmation">Detail group</a>';
        //                         return $btn;
        //                 })
        //                 // ->addColumn('action_petugas', function($row){
        //                 //     if($row->payment_gateway !== "offline"){
        //                 //         return "";
        //                 //     }else{
        //                 //         $disable="";
        //                 //         if($row->payment_status == "kwitansi" && Auth::user()->id == $row->added_by_user_id){
        //                 //             $btn = '<button type="button" class="btn btn-primary btn-lg button-confirmation" data-toggle="modal" data-target="#myModal" data-id="'.$row->id.'" '.$disable.'>Konfirmasi</button>';
        //                 //             return $btn;
        //                 //         }
        //                 //         return "";
        //                 //     }
                               
        //                 // })
        //                 ->rawColumns(['action'])
        //                 // ->rawColumns(['action','action_petugas'])
        //                 ->make(true);
            // }
    }
    public function detailBranchUser(Request $request, $parent_id)
    {
        $data = User::with('role','AmilDonaturGroup')->whereIn('parent_id', [$parent_id])->whereIn('role_id', [3])->get();

        // // dd($data);
            foreach ($data as $key => $value) {
                # code...
                $namapetugas[] = $value->normalizer_get_raw_decomposition;
            }
            $datas = DonaturGroup::whereIn('id_parent', [$namapetugas])->get();
            
            // dd($datas);
            if ($request->ajax()) {
                $data = User::with('role','usersDonatur','AmilDonaturGroup')->whereIn('parent_id', [$parent_id])->whereIn('role_id', [3])->get();
                // dd($parent_id);
            
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row) use($parent_id){

                        // dd($row);
                        // if($row->payment_gateway !== "offline"){
                        //     return "";
                        // }else{
                        //     $disable="";
                        //     if($row->payment_status == "settlement"){
                        //         return "";
                        //     }
                        //     $btn = '<button type="button" class="btn btn-primary btn-lg button-confirmation" data-toggle="modal" data-target="#myModal" data-id="'.$row->id.'" '.$disable.'>Konfirmasi</button>';
                        //     return $btn;
                        // }
                        $btn = '<div class="col-md-12"><span class="no-sort no-click bread-actions"><a class="btn btn-sm btn-primary pull-right edit" href="' .'/admin/users/'.$row->id .'/edit"><span class="voyager-edit"></span> Edit </a>';
                        // $btn = $btn.'&nbsp;<a class="btn btn-sm btn-danger pull-right delete" href="' .'/admin/users/'.$row->id.'"><span class="voyager-trash"></span> Delete </a>';
                        // $btn = $btn.'<a class="btn btn-danger btn-sm  pull-right" href="' . route('donaturs.sub.amil.history', ['group_id'=> $row->id]) .'"> Delete </a>';
                        $btn = $btn.'<a class="btn btn-success btn-sm pull-right icofont-box view" href="' . route('donaturs.sub.amil.history', ['group_id'=> $row->users_id]) .'"><span class="voyager-window-list"></span> VIEW</a>';
                        $btn = $btn.'&nbsp;<a class="btn btn-dark btn-sm pull-right icofont-box view" target="_blank" href="' . route('donaturs.print.prpetugas', ['petugas'=> $row->name]) .'"><span class="voyager-window-list"></span> kwitansi</a></span></div>';
                        // $btn = '<a class="btn btn-primary btn-lg button-confirmation" href="' . route('donaturs.sub.amil.history', ['group_id'=> $row->users_id]) .'">'.$row->users_id.'</a>';
                        // $btn = '<a href="{{ route("donaturs.sub.amil.history",  ["group_id"=> $row->id]) }}" class="btn btn-primary btn-lg button-confirmation">Detail group</a>';
                            return $btn;
                    }) 
                       ->addColumn('group_total', function ($datas) use($parent_id){

                        // foreach ($rows as $key => $value) {
                        //        # code...
                        //        $d[] = $value;
                        // }

                        // $datas = User::with('role','usersDonatur','AmilDonaturGroup')->whereIn('parent_id', [$parent_id])->whereIn('role_id', [3])->get();

                        // }

                        // dd(session()->get('id_donatur_groups'));

                        // foreach($datas as $kl){
                        //     // $ds[] = $kl->name;
                        //     $counts[] = DonaturGroup::whereIn('id_parent', [$kl->name])->count();
                        // }
                // $datas = User::whereIn('add_by_user_id', [$parent_id])->get();
                        //  foreach($datas as $kl){
                        //     $ds[] = $kl->name;
                        //     $counts = DonaturGroup::whereIn('id_parent', [$ds])->get();
                        // }
// dd($ds);
            $datas = DonaturGroup::whereIn('add_by_user_id', [$datas->id])->count();
            return $datas;

                        // $name = $query->name;
                        // $da = Donatur::whereIn('added_by_user_id', [$name])->get();

                        // foreach ($da as $key => $value) {
                        //     # code...
                        //     $namadonatur[] = $value->nama;
                        // }
                        
                        // $daonturgroups = DonaturGroup::whereIn('donatur_group_name', $namadonatur)->get();
                        
                        // foreach ($daonturgroups as $key => $value) {
                        //     # code...
                        //     $sdsad[] = $value->donatur_group_name;
                        // }
                        // $numbers = Midtran::whereIn('added_by_user_id', $sdsad)->sum('amount');return"Rp " . number_format($numbers,2,',','.');
                        // dd($sdsad);
                        // return $datas;
                    })
                    // ->addColumn('asd', function ($query) use($parent_id){
                    //     $name = $query->name;
                    //     $da = Donatur::whereIn('added_by_user_id', [$name])->get();

                    //     foreach ($da as $key => $value) {
                    //         # code...
                    //         $namadonatur[] = $value->nama;
                    //     }
                        
                    //     $daonturgroups = DonaturGroup::whereIn('donatur_group_name', $namadonatur)->get();
                        
                    //     foreach ($daonturgroups as $key => $value) {
                    //         # code...
                    //         $sdsad[] = $value->donatur_group_name;
                    //     }
                    //     $numbers = Midtran::whereIn('added_by_user_id', $sdsad)->sum('amount');return"Rp " . number_format($numbers,2,',','.');
                    //     // dd($sdsad);
                    // })
                    // ->addColumn('action_edit', function($row){
                    //     // if($row->payment_gateway !== "offline"){
                    //     //     return "";
                    //     // }else{
                    //     //     $disable="";
                    //     //     if($row->payment_status == "kwitansi" && Auth::user()->id == $row->added_by_user_id){
                    //             $btn = '<button type="button" class="btn btn-primary btn-lg button-confirmation">Edit</button>';
                    //             return $btn;
                    //         // }
                    //         // return "";
                    //     // }
                           
                    // })
                    ->rawColumns(['action','group_total'])
                    // ->rawColumns(['action','action_petugas'])
                    ->make(true);
        }
      
    }

    // POST BR(E)AD
    // public function update(Request $request, $id)
    // {
    //     if (Auth::user()->getKey() == $id) {
    //         $request->merge([
    //             'role_id'                              => Auth::user()->role_id,
    //             'user_belongstomany_role_relationship' => Auth::user()->roles->pluck('id')->toArray(),
    //         ]);
    //     }

    //     return parent::update($request, $id);
    // }

      // POST BR(E)AD
      public function update(Request $request, $id)
      {
          $slug = $this->getSlug($request);
  
          $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
  
          // Compatibility with Model binding.
          $id = $id instanceof \Illuminate\Database\Eloquent\Model ? $id->{$id->getKeyName()} : $id;
  
          $model = app($dataType->model_name);
          if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
              $model = $model->{$dataType->scope}();
          }
          if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
              $data = $model->withTrashed()->findOrFail($id);
          } else {
              $data = $model->findOrFail($id);
          }
  
          // Check permission
          $this->authorize('edit', $data);
  
          // Validate fields with ajax
          $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id)->validate();
          $this->insertUpdateData($request, $slug, $dataType->editRows, $data);
  
          event(new BreadDataUpdated($dataType, $data));
  
          if (auth()->user()->can('browse', app($dataType->model_name))) {
              $redirect = redirect()->route("voyager.{$dataType->slug}.index");
          } else {
              $redirect = redirect()->back();
          }
  
          return $redirect->with([
              'message'    => __('voyager::generic.successfully_updated')." {$dataType->getTranslatedAttribute('display_name_singular')}",
              'alert-type' => 'success',
          ]);
      }
    
}
