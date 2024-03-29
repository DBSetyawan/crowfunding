<?php

namespace App\Http\Controllers\Voyager;

use App\User;
use Exception;
use Validator;
use DataTables;
use App\Donatur;
use App\Midtran;
use App\Program;
use \go2hi\go2hi;
use App\Kelurahan;
use Carbon\Carbon;
use App\DonaturGroup;
use Illuminate\Http\Request;
use App\Imports\donaturgImports;
use TCG\Voyager\Facades\Voyager;
use App\Jobs\ImportDonaturNewJobs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataDeleted;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Events\BreadDataRestored;
use TCG\Voyager\Events\BreadImagesDeleted;
use Illuminate\Database\Eloquent\SoftDeletes;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Http\Controllers\Traits\BreadRelationshipParser;
// use Auth;
class DonaturController extends VoyagerBaseController
{

    public function add_donation(Request $request){
        $programs = Program::all();
        $donatur = Donatur::where('id',$request->id)->first();
        return view('vendor.voyager.donaturs.add_donation',compact('donatur','programs'));
    }

    public function testResetIncrement($table_name){
        // DB::statement("SET @count = 0;");
        // DB::statement("UPDATE `$table_name` SET `$table_name`.`id` = @count:= @count + 1;");
        DB::statement("ALTER TABLE `$table_name` AUTO_INCREMENT = 1;");
        return "ok";
    }

    public function fileImport(Request $request) 
    {
        // Excel::import(new donaturGroups, $request->file('file')->store('temp'));
        // $import = new donaturgImports();
        // $import->onlySheets('DATA BATCH USERS');

        // Excel::import($import, $request->file('file')->store('temp'));
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

            ImportDonaturNewJobs::dispatch($filename);

            return redirect()->back();
            // (new UserAutomaticallyInsert)->queue(storage_path('app/public/temp/'.$filename));
            //  Excel::import($import, $request->file('file')->store('temp'));

            // (new ImidtransJobs::dispatch(file))->queue(storage_path('app/public/temp/'.$filename));
            // (new Exs($file))->queue($request->file('file'));

            // return back();
            

        }  
     
        // $array = (new donaturGroups)->toArray($request->file('file')->store('temp'));
        // Excel::queueImport(new donaturGroups,  $request->file('file')->store('temp'));
        return back();
    }

    public function store_donation(Request $request){
        Midtran::create([
            'amount'=>$request->amount,
            'paid_date'=>date("Y-m-d H:i:s"),
            'payment_gateway'=>'offline',
            'payment_status'=>'on_funding',
            'donatur_id'=>$request->donatur_id,
            'transaction_time'=>date("Y-m-d H:i:s"),
            'program_id'=>$request->program_id,
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s"),
            'added_by_user_id'=>Auth::user()->id,
        ]);

        $redirect = redirect()->route("voyager.donaturs.index");
        return $redirect->with([
            'message'    => "Donation Added Successfully",
            'alert-type' => 'success',
        ]);

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

    public function index(Request $request, $group_id = null)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
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
            if(Auth::user()->role->id == 1){
                $query_donatur_group = DonaturGroup::whereIn('id', [(Int) $group_id])->get();
                    foreach ($query_donatur_group as $key => $value) {
                        # code...
                        $queryIngroupName[$key] = $value->donatur_group_name;
                    }
                    $query = isset($queryIngroupName) 
                    ? $model->whereIn('added_by_user_id', [$queryIngroupName]) 
                    : $model->select('*');

            }

            if(Auth::user()->role->id == 2){
                $query_donatur_group = DonaturGroup::whereIn('id', [(Int) $group_id])->get();
                    foreach ($query_donatur_group as $key => $value) {
                        # code...
                        $queryIngroupName[$key] = $value->donatur_group_name;
                    }
                    $query = isset($queryIngroupName) 
                    ? $model->whereIn('added_by_user_id', [$queryIngroupName]) 
                    : $model->select('*');
            }

            if(Auth::user()->role->id == 3){
                $query_donatur_group = DonaturGroup::whereIn('id', [(Int) $group_id])->get();

                    foreach ($query_donatur_group as $key => $value) {
                        # code...
                        $queryIngroupName[$key] = $value->donatur_group_name;
                    }

                    $query = isset($queryIngroupName) 
                    ? $model->whereIn('added_by_user_id', [$queryIngroupName]) 
                    : $model->select('*');

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
        $donatur_groups = DonaturGroup::all();
        $donaturdetailid = $group_id;

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
            'donatur_groups',
            'donaturdetailid'
        ));
    }

    //***************************************
    //                _____
    //               |  __ \
    //               | |__) |
    //               |  _  /
    //               | | \ \
    //               |_|  \_\
    //
    //  Read an item of our Data Type B(R)EAD
    //
    //****************************************

    public function show(Request $request, $id)
    {
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
        $dataTypeContent->donatur_group =  DonaturGroup::where('id',$dataTypeContent->donatur_group_id)->first();

        $dataTypeContent->added_by_user = User::where('id',$dataTypeContent->added_by_user_id)->first();

        // dd(Auth::user()->role);
        
        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'isSoftDeleted'));
    }

    //***************************************
    //                ______
    //               |  ____|
    //               | |__
    //               |  __|
    //               | |____
    //               |______|
    //
    //  Edit an item of our Data Type BR(E)AD
    //
    //****************************************

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
        // $kelurahan = Kelurahan::where('id',$dataTypeContent->kelurahan_id)->first();
        // $selected_domisili = (object)array('value'=>'','text'=>'');
        // if($kelurahan){
        //     $selected_domisili->value = $kelurahan->id;
        //     $selected_domisili->text = $kelurahan->kelurahan.', '.$kelurahan->kecamatan->kecamatan.', '.$kelurahan->kecamatan->kabkot->kabupaten_kota.', '.$kelurahan->kecamatan->kabkot->provinsi->provinsi.', '.$kelurahan->kd_pos;
        // }
        $donatur_groups = DonaturGroup::all();

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable','donatur_groups'));
    }

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

    //***************************************
    //
    //                   /\
    //                  /  \
    //                 / /\ \
    //                / ____ \
    //               /_/    \_\
    //
    //
    // Add a new item of our Data Type BRE(A)D
    //
    //****************************************

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

        $donatur_groups = DonaturGroup::all();

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable','donatur_groups'));
    }

    public function form_confirmation_referer(Request $request)
    {
        $programs = Program::all();
        $donatur = Donatur::where('id',$request->id)->first();
        return view('vendor.voyager.donaturs.form_confirmation_donasi',compact('donatur','programs'));
    }

    /**
     * POST BRE(A)D - Store data.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        $slug = $this->getSlug($request);

        $user = User::create(['alamat' => $request->kelurahan_id, 'role_id'=> 4,'password'=> Hash::make($request->password),'name'=> $request->name,'users_id'=> auth()->user()->id,'parent_id'=> auth()->user()->name,'cabang_id' => auth()->user()->cabang_id, 'group_id' => 0, 'email' => $request->email, 'added_by_user_id' => $request->added_by_user_id]);
        Donatur::create(['id'=> $user->id,'alamat'=>$request->kelurahan_id,'added_by_user_id' => $request->group_id, 'user_id' => auth()->user()->id,'nama'=> $user->name]);

        // dd($request->all());

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        // $this->authorize('add', app($dataType->model_name));

        // Validate fields with ajax
        // $val = $this->validateBread($request->all(), $dataType->addRows)->validate();
        // $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());

        // event(new BreadDataAdded($dataType, $data));

        $redirect = redirect()->route("voyager.donaturs.index.groups", ['group_id' => auth()->user()->id]);

        return $redirect->with([
            'message'    => __('voyager::generic.successfully_added_new')." {$dataType->getTranslatedAttribute('display_name_singular')}",
            'alert-type' => 'success',
        ]);
    }

    //***************************************
    //                _____
    //               |  __ \
    //               | |  | |
    //               | |  | |
    //               | |__| |
    //               |_____/
    //
    //         Delete an item BREA(D)
    //
    //****************************************

    public function destroy(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Init array of IDs
        $ids = [];
        if (empty($id)) {
            // Bulk delete, get IDs from POST
            $ids = explode(',', $request->ids);
        } else {
            // Single item delete, get ID from URL
            $ids[] = $id;
        }
        foreach ($ids as $id) {
            $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);

            // Check permission
            $this->authorize('delete', $data);

            $model = app($dataType->model_name);
            if (!($model && in_array(SoftDeletes::class, class_uses_recursive($model)))) {
                $this->cleanup($dataType, $data);
            }
        }

        $displayName = count($ids) > 1 ? $dataType->getTranslatedAttribute('display_name_plural') : $dataType->getTranslatedAttribute('display_name_singular');

        $res = $data->destroy($ids);
        $data = $res
            ? [
                'message'    => __('voyager::generic.successfully_deleted')." {$displayName}",
                'alert-type' => 'success',
            ]
            : [
                'message'    => __('voyager::generic.error_deleting')." {$displayName}",
                'alert-type' => 'error',
            ];

        if ($res) {
            event(new BreadDataDeleted($dataType, $data));
        }

        return redirect()->route("voyager.{$dataType->slug}.index")->with($data);
    }

    public function restore(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('delete', app($dataType->model_name));

        // Get record
        $model = call_user_func([$dataType->model_name, 'withTrashed']);
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
            $model = $model->{$dataType->scope}();
        }
        $data = $model->findOrFail($id);

        $displayName = $dataType->getTranslatedAttribute('display_name_singular');

        $res = $data->restore($id);
        $data = $res
            ? [
                'message'    => __('voyager::generic.successfully_restored')." {$displayName}",
                'alert-type' => 'success',
            ]
            : [
                'message'    => __('voyager::generic.error_restoring')." {$displayName}",
                'alert-type' => 'error',
            ];

        if ($res) {
            event(new BreadDataRestored($dataType, $data));
        }

        return redirect()->route("voyager.{$dataType->slug}.index")->with($data);
    }

    //***************************************
    //
    //  Delete uploaded file
    //
    //****************************************

    public function remove_media(Request $request)
    {
        try {
            // GET THE SLUG, ex. 'posts', 'pages', etc.
            $slug = $request->get('slug');

            // GET file name
            $filename = $request->get('filename');

            // GET record id
            $id = $request->get('id');

            // GET field name
            $field = $request->get('field');

            // GET multi value
            $multi = $request->get('multi');

            $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

            // Load model and find record
            $model = app($dataType->model_name);
            $data = $model::find([$id])->first();

            // Check if field exists
            if (!isset($data->{$field})) {
                throw new Exception(__('voyager::generic.field_does_not_exist'), 400);
            }

            // Check permission
            $this->authorize('edit', $data);

            if (@json_decode($multi)) {
                // Check if valid json
                if (is_null(@json_decode($data->{$field}))) {
                    throw new Exception(__('voyager::json.invalid'), 500);
                }

                // Decode field value
                $fieldData = @json_decode($data->{$field}, true);
                $key = null;

                // Check if we're dealing with a nested array for the case of multiple files
                if (is_array($fieldData[0])) {
                    foreach ($fieldData as $index=>$file) {
                        // file type has a different structure than images
                        if (!empty($file['original_name'])) {
                            if ($file['original_name'] == $filename) {
                                $key = $index;
                                break;
                            }
                        } else {
                            $file = array_flip($file);
                            if (array_key_exists($filename, $file)) {
                                $key = $index;
                                break;
                            }
                        }
                    }
                } else {
                    $key = array_search($filename, $fieldData);
                }

                // Check if file was found in array
                if (is_null($key) || $key === false) {
                    throw new Exception(__('voyager::media.file_does_not_exist'), 400);
                }

                $fileToRemove = $fieldData[$key]['download_link'] ?? $fieldData[$key];

                // Remove file from array
                unset($fieldData[$key]);

                // Generate json and update field
                $data->{$field} = empty($fieldData) ? null : json_encode(array_values($fieldData));
            } else {
                if ($filename == $data->{$field}) {
                    $fileToRemove = $data->{$field};

                    $data->{$field} = null;
                } else {
                    throw new Exception(__('voyager::media.file_does_not_exist'), 400);
                }
            }

            $row = $dataType->rows->where('field', $field)->first();

            // Remove file from filesystem
            if (in_array($row->type, ['image', 'multiple_images'])) {
                $this->deleteBreadImages($data, [$row], $fileToRemove);
            } else {
                $this->deleteFileIfExists($fileToRemove);
            }

            $data->save();

            return response()->json([
                'data' => [
                    'status'  => 200,
                    'message' => __('voyager::media.file_removed'),
                ],
            ]);
        } catch (Exception $e) {
            $code = 500;
            $message = __('voyager::generic.internal_error');

            if ($e->getCode()) {
                $code = $e->getCode();
            }

            if ($e->getMessage()) {
                $message = $e->getMessage();
            }

            return response()->json([
                'data' => [
                    'status'  => $code,
                    'message' => $message,
                ],
            ], $code);
        }
    }

    /**
     * Remove translations, images and files related to a BREAD item.
     *
     * @param \Illuminate\Database\Eloquent\Model $dataType
     * @param \Illuminate\Database\Eloquent\Model $data
     *
     * @return void
     */
    protected function cleanup($dataType, $data)
    {
        // Delete Translations, if present
        if (is_bread_translatable($data)) {
            $data->deleteAttributeTranslations($data->getTranslatableAttributes());
        }

        // Delete Images
        $this->deleteBreadImages($data, $dataType->deleteRows->whereIn('type', ['image', 'multiple_images']));

        // Delete Files
        foreach ($dataType->deleteRows->where('type', 'file') as $row) {
            if (isset($data->{$row->field})) {
                foreach (json_decode($data->{$row->field}) as $file) {
                    $this->deleteFileIfExists($file->download_link);
                }
            }
        }

        // Delete media-picker files
        $dataType->rows->where('type', 'media_picker')->where('details.delete_files', true)->each(function ($row) use ($data) {
            $content = $data->{$row->field};
            if (isset($content)) {
                if (!is_array($content)) {
                    $content = json_decode($content);
                }
                if (is_array($content)) {
                    foreach ($content as $file) {
                        $this->deleteFileIfExists($file);
                    }
                } else {
                    $this->deleteFileIfExists($content);
                }
            }
        });
    }

    /**
     * Delete all images related to a BREAD item.
     *
     * @param \Illuminate\Database\Eloquent\Model $data
     * @param \Illuminate\Database\Eloquent\Model $rows
     *
     * @return void
     */
    public function deleteBreadImages($data, $rows, $single_image = null)
    {
        $imagesDeleted = false;

        foreach ($rows as $row) {
            if ($row->type == 'multiple_images') {
                $images_to_remove = json_decode($data->getOriginal($row->field), true) ?? [];
            } else {
                $images_to_remove = [$data->getOriginal($row->field)];
            }

            foreach ($images_to_remove as $image) {
                // Remove only $single_image if we are removing from bread edit
                if ($image != config('voyager.user.default_avatar') && (is_null($single_image) || $single_image == $image)) {
                    $this->deleteFileIfExists($image);
                    $imagesDeleted = true;

                    if (isset($row->details->thumbnails)) {
                        foreach ($row->details->thumbnails as $thumbnail) {
                            $ext = explode('.', $image);
                            $extension = '.'.$ext[count($ext) - 1];

                            $path = str_replace($extension, '', $image);

                            $thumb_name = $thumbnail->name;

                            $this->deleteFileIfExists($path.'-'.$thumb_name.$extension);
                        }
                    }
                }
            }
        }

        if ($imagesDeleted) {
            event(new BreadImagesDeleted($data, $rows));
        }
    }

    /**
     * Order BREAD items.
     *
     * @param string $table
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function order(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('edit', app($dataType->model_name));

        if (!isset($dataType->order_column) || !isset($dataType->order_display_column)) {
            return redirect()
            ->route("voyager.{$dataType->slug}.index")
            ->with([
                'message'    => __('voyager::bread.ordering_not_set'),
                'alert-type' => 'error',
            ]);
        }

        $model = app($dataType->model_name);
        if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
            $model = $model->withTrashed();
        }
        $results = $model->orderBy($dataType->order_column, $dataType->order_direction)->get();

        $display_column = $dataType->order_display_column;

        $dataRow = Voyager::model('DataRow')->whereDataTypeId($dataType->id)->whereField($display_column)->first();

        $view = 'voyager::bread.order';

        if (view()->exists("voyager::$slug.order")) {
            $view = "voyager::$slug.order";
        }

        return Voyager::view($view, compact(
            'dataType',
            'display_column',
            'dataRow',
            'results'
        ));
    }

    public function update_order(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('edit', app($dataType->model_name));

        $model = app($dataType->model_name);

        $order = json_decode($request->input('order'));
        $column = $dataType->order_column;
        foreach ($order as $key => $item) {
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
                $i = $model->withTrashed()->findOrFail($item->id);
            } else {
                $i = $model->findOrFail($item->id);
            }
            $i->$column = ($key + 1);
            $i->save();
        }
    }

    public function action(Request $request)
    {
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $action = new $request->action($dataType, null);

        return $action->massAction(explode(',', $request->ids), $request->headers->get('referer'));
    }

    /**
     * Get BREAD relations data.
     *
     * @param Request $request
     *
     * @return mixed
     */
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

    public function listHistoryGroupDonatur(Request $request, $donatur_id)
    {
        dd($donatur_id);
        $data = Midtran::select('midtrans.*','programs.program_name')->leftjoin('programs','midtrans.program_id','programs.id')->where('donatur_id',$donatur_id)->latest()->get();


    }


    public function donation_history_index(Request $request,$donatur_id)
    {
        if ($request->ajax()) {
            $data = Midtran::select('midtrans.*','programs.program_name')->leftjoin('programs','midtrans.program_id','programs.id')->where('donatur_id',$donatur_id)->latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('tr_date', function($row){
                        return $row->created_at;
                    })
                    ->addColumn('action_petugas', function($row){

                        if(Auth::user()->role->id == 1 || Auth::user()->role->id == 2){
                            $disable="hidden";
                            if($row->payment_status == "kwitansi"){
                                $btn = '<button type="button" class="btn btn-primary btn-lg '.$disable.' button-confirmation" data-toggle="modal" data-target="#myModal" data-id="'.$row->id.'">Konfirmasi</button>';
                            }
                                else{
                                    if($row->payment_status == "on_funding"){
                                         $btn = '<button type="button" class="btn btn-primary btn-lg button-confirmation" data-toggle="modal" data-target="#myModal" data-id="'.$row->id.'">Konfirmasi</button>';
                                    } else {
                                        if($row->payment_status == "settlement"){
                                            $btn = '<button type="button" class="btn btn-primary btn-lg '.$disable.' button-confirmation" data-toggle="modal" data-target="#myModal" data-id="'.$row->id.'">Konfirmasi</button>';
                                       }
                                    }
                                }
                        }
                        if(Auth::user()->role->id == 3 || Auth::user()->role->id == 1){
                            $disable="hidden";
                            if($row->payment_status == "kwitansi"){
                                $btn = '<button type="button" class="btn btn-primary btn-lg button-confirmation" data-toggle="modal" data-target="#myModal" data-id="'.$row->id.'">Konfirmasi</button>';
                            }
                                else    
                                    {
                                        if($row->payment_status == "settlement"){
                                            $btn = '<button type="button" class="btn btn-primary btn-lg '.$disable.' button-confirmation" data-toggle="modal" data-target="#myModal" data-id="'.$row->id.'">Konfirmasi</button>';
                                        } 
                                            else {
                                                if($row->payment_status == "on_funding"){
                                                    $btn = '<button type="button" class="btn btn-primary btn-lg '.$disable.' button-confirmation" data-toggle="modal" data-target="#myModal" data-id="'.$row->id.'">Konfirmasi</button>';
                                                }
                                        }
                                }
                        }

                      
                        return $btn;
                           
                    })
                    // ->addColumn('action_petugas', function($row){
                    //     if($row->payment_gateway !== "offline"){
                    //         return "";
                    //     }else{
                    //         $disable="";
                    //         if($row->payment_status == "kwitansi" && Auth::user()->id == $row->added_by_user_id){
                    //             $btn = '<button type="button" class="btn btn-primary btn-lg button-confirmation" data-toggle="modal" data-target="#myModal" data-id="'.$row->id.'" '.$disable.'>Konfirmasi</button>';
                    //             return $btn;
                    //         }
                    //         return "";
                    //     }
                           
                    // })
                    ->rawColumns(['action_petugas','tr_date'])
                    ->make(true);
        }
      
    }

    public function confirm_donation(Request $request){
        $donation_id = $request->donation_id;
        $status = null;
        $midtran = Midtran::where('id',$donation_id)->first();
        // dd($donation_id);die;
        // return response()->json(
        //     ['data'=> $midtran->payment_status]
        // );die;
        if(!$midtran){
            return redirect()->back()->with([
                'message'    => "donasi tidak ditemukan",
                'alert-type' => 'error',
            ]);
            // return response()->json(['response'=> false]);

        }
        if(Auth::user()->role->id == 3){
            $status="on_funding";
            $cash="pending";

        }else if(Auth::user()->role->id == 2 || Auth::user()->role->id == 1){
            $status="settlement";
            $cash="cash";

        
        }

    Midtran::where('id',$donation_id)->update([
        'payment_status'=>$status,
        'payment_gateway'=> $cash
    ]);

    return redirect()->back()->with([
        'message'    => "Berhasil mengupdate transaksi status menjadi {$status}.",
        'alert-type' => 'success',
    ]);
    // return response()->json(
    //         ['data'=> true,'response'=>'Dengan anda melakukan fitur ini. donasi berhasil dikonfirmasi, status menjadi settlement.']
    //     );

        // if($midtran->payment_status == "settlement"){
          
        //     return response()->json(
        //             ['data'=> true,'response'=>'tidak ada aksi apapun untuk status ini.']
        //         );

        //     // return redirect()->back()->with([
        //     //     'message'    => "Donation telah di konfirmasi",
        //     //     'alert-type' => 'success',
        //     // ]);
        // }
        
        // if($midtran->payment_status == "on_funding"){
        //     Midtran::where('id',$donation_id)->update([
        //         'payment_status'=>"settlement",
        //         'payment_gateway'=> "cash"
        //     ]);
        //     return response()->json(
        //             ['data'=> true,'response'=>'Dengan anda melakukan fitur ini. donasi berhasil dikonfirmasi, status menjadi settlement.']
        //         );

        //     // return redirect()->back()->with([
        //     //     'message'    => "Donation telah di konfirmasi",
        //     //     'alert-type' => 'success',
        //     // ]);
        // }
        
        // if($midtran->payment_status == "kwitansi"){
        //     Midtran::where('id',$donation_id)->update([
        //         'payment_status'=>"on_funding",
        //         'payment_gateway'=> "pending"
        //     ]);
        //     return response()->json(
        //             ['data'=> true,'response'=>'Dengan anda melakukan fitur ini. donasi telah diproses, status menjadi on_funding.']
        //         );

        //     // return redirect()->back()->with([
        //     //     'message'    => "Donation telah di konfirmasi",
        //     //     'alert-type' => 'success',
        //     // ]);
        // }
        //     else{

        //         return response()->json(
        //         ['data'=> false,'response'=>'status tidak diketahui.']
        //     );
        //     // return redirect()->back()->with([
        //     //     'message'    => "hak akses dibatasi untuk user ini.",
        //     //     'alert-type' => 'error',
        //     // ]);
        // }
        

    }
    
    public function printgroupnames(Request $request, String $group_name = null){
        
        // $validator = Validator::make($request->all(), [
        //     'start_date'=>'required|date|before:end_date',
        //     'end_date'=>'required|date|after:start_date',
        // ]);

        // if ($validator->fails()) {    
        //     return response()->json($validator->messages(), 400);
        // }
        $donatur_name = DonaturGroup::findOrFail($group_name)->donatur_group_name;

        $data = Midtran::with('donatursFK')->whereIn('added_by_user_id',[$donatur_name])
                ->where('payment_status', 'kwitansi')->get();
                $caripetugas__ = DonaturGroup::where(function($query) use($group_name){
                        return $query->whereIn('id', [$group_name]);
                })->select('id_petugas')->get();
                $ptgname = User::whereIn('id',[$caripetugas__[0]->id_petugas])->get();
                // dd($ptgname);
                $carigroup__ = DonaturGroup::whereIn('id',[$group_name])->get();
                // dd($carigroup__);
        // $data = Midtran::whereBetween('updated_at',[$request->start_date,$request->end_date])->limit(100)->get();
        // $data = Midtran::where('payment_gateway','offline')->whereBetween('updated_at',[$request->start_date,$request->end_date])->get();
        // dd($data);
        foreach ($data as $key => $d) {
            $data[$key]->donatur = Donatur::where('id',$d->donatur_id)->first();
            $data[$key]->program = Program::where('id',$d->program_id)->first();

            // $kelurahan = Kelurahan::where('id',$data[$key]->donatur->kelurahan_id)->first();
            // $data[$key]->donatur->domisili = "";
            // if($kelurahan){
            //     $data[$key]->donatur->domisili = $kelurahan->kelurahan.', '.$kelurahan->kecamatan->kecamatan.', '.$kelurahan->kecamatan->kabkot->kabupaten_kota.', '.$kelurahan->kecamatan->kabkot->provinsi->provinsi.', '.$kelurahan->kd_pos;
            // }

            if($d->updated_at){
                $data[$key]->tanggal_masehi = date('Y-m-d', strtotime($d->updated_at));
                // dd(date('Y-m-d',strtotime($d->updated_at)));
                $strdate = date('Y-m-d',strtotime($d->updated_at));
                $arr_date = explode('-',$strdate);
                $hij = $this->GregorianToHijriah($arr_date[0],$arr_date[1],$arr_date[2]);
                $data[$key]->tanggal_hijiriah = $hij['day'].' '.$this->month_hij($hij['month']).' '.$hij['year']; 
                $data[$key]->terbilang = ucwords($this->terbilang($d->amount))." Rupiah";
                $data[$key]->rupiah = $this->rupiah($data[$key]->amount);
                
            } 
        }

        return view('kwitansi',compact('data','carigroup__','ptgname'));
        
        
    }
    
    public function printperpetugas(Request $request, String $petugas = null){
        
        // $validator = Validator::make($request->all(), [
        //     'start_date'=>'required|date|before:end_date',
        //     'end_date'=>'required|date|after:start_date',
        // ]);

        // if ($validator->fails()) {    
        //     return response()->json($validator->messages(), 400);
        // }

        $data = Midtran::whereIn('added_by_user_id',[$petugas])
                ->where('payment_status', 'kwitansi')->get();
                // dd($data);
        // $data = Midtran::whereBetween('updated_at',[$request->start_date,$request->end_date])->limit(100)->get();
        // $data = Midtran::where('payment_gateway','offline')->whereBetween('updated_at',[$request->start_date,$request->end_date])->get();
        // dd($data);
        foreach ($data as $key => $d) {
            $data[$key]->donatur = Donatur::where('id',$d->donatur_id)->first();
            $data[$key]->program = Program::where('id',$d->program_id)->first();

            // $kelurahan = Kelurahan::where('id',$data[$key]->donatur->kelurahan_id)->first();
            // $data[$key]->donatur->domisili = "";
            // if($kelurahan){
            //     $data[$key]->donatur->domisili = $kelurahan->kelurahan.', '.$kelurahan->kecamatan->kecamatan.', '.$kelurahan->kecamatan->kabkot->kabupaten_kota.', '.$kelurahan->kecamatan->kabkot->provinsi->provinsi.', '.$kelurahan->kd_pos;
            // }

            if($d->updated_at){
                $data[$key]->tanggal_masehi = date('Y-m-d', strtotime($d->updated_at));
                // dd(date('Y-m-d',strtotime($d->updated_at)));
                $strdate = date('Y-m-d',strtotime($d->updated_at));
                $arr_date = explode('-',$strdate);
                $hij = $this->GregorianToHijriah($arr_date[0],$arr_date[1],$arr_date[2]);
                $data[$key]->tanggal_hijiriah = $hij['day'].' '.$this->month_hij($hij['month']).' '.$hij['year']; 
                $data[$key]->terbilang = ucwords($this->terbilang($d->amount))." Rupiah";
                $data[$key]->rupiah = $this->rupiah($data[$key]->amount);
                
            } 
        }

        return view('kwitansi',compact('data'));
        
        
    }

    public function print(Request $request, String $cabang = null){
        
        // dd($id);
        // $validator = Validator::make($request->all(), [
        //     'start_date'=>'required|date|before:end_date',
        //     'end_date'=>'required|date|after:start_date',
        // ]);

        // if ($validator->fails()) {    
        //     return response()->json($validator->messages(), 400);
        // }

        $data = Midtran::whereIn('added_by_user_id',[$cabang])
                ->where('payment_status', 'kwitansi')->get();
                // dd($data);
        // $data = Midtran::whereBetween('updated_at',[$request->start_date,$request->end_date])->limit(100)->get();
        // $data = Midtran::where('payment_gateway','offline')->whereBetween('updated_at',[$request->start_date,$request->end_date])->get();
        // dd($data);
        foreach ($data as $key => $d) {
            $data[$key]->donatur = Donatur::where('id',$d->donatur_id)->first();
            $data[$key]->program = Program::where('id',$d->program_id)->first();

            // $kelurahan = Kelurahan::where('id',$data[$key]->donatur->kelurahan_id)->first();
            // $data[$key]->donatur->domisili = "";
            // if($kelurahan){
            //     $data[$key]->donatur->domisili = $kelurahan->kelurahan.', '.$kelurahan->kecamatan->kecamatan.', '.$kelurahan->kecamatan->kabkot->kabupaten_kota.', '.$kelurahan->kecamatan->kabkot->provinsi->provinsi.', '.$kelurahan->kd_pos;
            // }

            if($d->updated_at){
                $data[$key]->tanggal_masehi = date('Y-m-d', strtotime($d->updated_at));
                // dd(date('Y-m-d',strtotime($d->updated_at)));
                $strdate = date('Y-m-d',strtotime($d->updated_at));
                $arr_date = explode('-',$strdate);
                $hij = $this->GregorianToHijriah($arr_date[0],$arr_date[1],$arr_date[2]);
                $data[$key]->tanggal_hijiriah = $hij['day'].' '.$this->month_hij($hij['month']).' '.$hij['year']; 
                $data[$key]->terbilang = ucwords($this->terbilang($d->amount))." Rupiah";
                $data[$key]->rupiah = $this->rupiah($data[$key]->amount);
                
            } 
        }

        return view('kwitansi',compact('data'));
        
        
    }

    public function prints(Request $request){
        
        // dd($request->start_date);
        $validator = Validator::make($request->all(), [
            'start_date'=>'required|date|before:end_date',
            'end_date'=>'required|date|after:start_date',
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 400);
        }

        // $data = Midtran::whereIn('added_by_user_id',[$cabang])
        //         ->where('payment_status', 'kwitansi')->get();
        //         dd($data);
        $data = Midtran::whereBetween('updated_at',[$request->start_date,$request->end_date])->limit(1000)->get(); //original 5k
        // $data = Midtran::where('payment_gateway','offline')->whereBetween('updated_at',[$request->start_date,$request->end_date])->get();
        // dd($data);
        foreach ($data as $key => $d) {
            $data[$key]->donatur = Donatur::where('id',$d->donatur_id)->first();
            $data[$key]->program = Program::where('id',$d->program_id)->first();

            // $kelurahan = Kelurahan::where('id',$data[$key]->donatur->kelurahan_id)->first();
            // $data[$key]->donatur->domisili = "";
            // if($kelurahan){
            //     $data[$key]->donatur->domisili = $kelurahan->kelurahan.', '.$kelurahan->kecamatan->kecamatan.', '.$kelurahan->kecamatan->kabkot->kabupaten_kota.', '.$kelurahan->kecamatan->kabkot->provinsi->provinsi.', '.$kelurahan->kd_pos;
            // }

            if($d->updated_at){
                $data[$key]->tanggal_masehi = date('Y-m-d', strtotime($d->updated_at));
                // dd(date('Y-m-d',strtotime($d->updated_at)));
                $strdate = date('Y-m-d',strtotime($d->updated_at));
                $arr_date = explode('-',$strdate);
                $hij = $this->GregorianToHijriah($arr_date[0],$arr_date[1],$arr_date[2]);
                $data[$key]->tanggal_hijiriah = $hij['day'].' '.$this->month_hij($hij['month']).' '.$hij['year']; 
                $data[$key]->terbilang = ucwords($this->terbilang($d->amount))." Rupiah";
                $data[$key]->rupiah = $this->rupiah($data[$key]->amount);
                
            } 
        }

        return view('kwitansi',compact('data'));
        
        
    }

    //Deploy searching kwitansi donaturs
    public function generate_and_print_last_month(Request $request){
        // dd($request->all());
        // $dt = Carbon::now();
        // $bulan = $dt->month($request->bulan)->toDateTimeString();
        // $dts = Carbon::parse($bulan);
        // $bulan = $dt->month($request->bulan)->toDateTimeString();
        // $tahun = $dt->year($request->tahun)->toDateTimeString();
        $dt = Carbon::create($request->tahun, $request->bulan, $request->hari, 0);
        $nextMonthTransaction = $dt->addMonth()->toDateTimeString();
        // dd($addmonth);die;
        $kloningDonaturs = Midtran::whereYear('created_at', '=', $request->tahun)
              ->whereDay('created_at', '=', $request->hari)
              ->whereMonth('created_at', '=', $request->bulan)
              ->get();

            //   foreach ($kloningDonaturs as $key => $valueDonaturGenerate) {
            //       # code...
            //       $DataNameGenerateKwitansi[] = $valueDonaturGenerate;
            //   }
            try{

                if($kloningDonaturs->isEmpty() == true) {
                    $failed = "<div class='alert alert-danger'>gagal menyimpan data.</div>";
                    return response()->json(['failed' => $failed, 'status' => false]);
                } else {
                    // $success = "<div class='alert alert-success'>Berhasil menyimpan data.</div>";
                    // return response()->json(['success'=> $success, 'status' => true]);
                    $bulkAction = DB::transaction(function() use ($kloningDonaturs, $nextMonthTransaction) {
                        foreach (array_chunk($kloningDonaturs->toArray(), 1000) as $responseChunk)
                        {
                            $insertableArray = [];
                            foreach($responseChunk as $BulkHistory) {
                                $insertableArray[] = [
                                    'created_at' => $nextMonthTransaction,
                                    'amount' => $BulkHistory['amount'],
                                    'donatur_id' => $BulkHistory['donatur_id'],
                                    'id_cabang' => $BulkHistory['id_cabang'],
                                    'group_id' => $BulkHistory['group_id'],
                                    'transaction_id' => $BulkHistory['transaction_id'],
                                    'transaction_time' => $BulkHistory['transaction_time'],
                                    'payment_gateway' => $BulkHistory['payment_gateway'],
                                    'payment_status' => "kwitansi",
                                    'program_id' => $BulkHistory['program_id'],
                                    'updated_at' => $nextMonthTransaction,
                                    'added_by_user_id' => $BulkHistory['added_by_user_id']
                                ];
                            }
                            $response = DB::table('midtrans')->insert($insertableArray);
                        }

                        return $response;

                    });
                    // $bulkAction = true;

                    if($bulkAction == true){
                        $success = "<div class='alert alert-success'>Berhasil menyimpan data.</div>";
                        // $success = "<div class='alert alert-success'>Data pernah disimpan sebelumnya !</div>";
                        return response()->json(['success'=> $success, 'status' => true]);
                    } else {
                        $failed = "<div class='alert alert-danger'>gagal menyimpan data.</div>";
                        return response()->json(['failed' => $failed, 'status' => false]);
                    }
                }

            }catch(\Throwable $e){
                    echo json_encode(
                        array('status'=> $e->getMessage()),
                        JSON_PRETTY_PRINT
                    );
            };
die;
        // dd($);
        $validator = Validator::make($request->all(), [
            'group_id'=>'required',
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 400);
        }

        $group_id = $request->group_id;
        $start_date = date('Y-m-d', strtotime('first day of last month'));
        $end_date = date('Y-m-d', strtotime('last day of last month'));

        $data = Midtran::select('midtrans.*')
        ->join('donaturs','donaturs.id','midtrans.donatur_id')
        ->where('donaturs.donatur_group_id',$group_id)
        ->where('midtrans.payment_gateway','offline')
        ->where('midtrans.payment_status','settlement')
        ->whereBetween('midtrans.updated_at',[$start_date,$end_date])
        ->get();
        foreach ($data as $key => $d) {

            $new_created = Midtran::create([
                'amount'=>$d->amount,
                'paid_date'=>date("Y-m-d H:i:s"),
                'payment_gateway'=>'offline',
                'payment_status'=>'kwitansi',
                'donatur_id'=>$d->donatur_id,
                'transaction_time'=>date("Y-m-d H:i:s"),
                'program_id'=>$d->program_id,
                'created_at'=>date("Y-m-d H:i:s"),
                'updated_at'=>date("Y-m-d H:i:s"),
                'added_by_user_id'=>$d->added_by_user_id,
            ]);

            $data[$key]->id = $new_created->id;
            $data[$key]->donatur = Donatur::where('id',$d->donatur_id)->first();
            $data[$key]->program = Program::where('id',$d->program_id)->first();

            $kelurahan = Kelurahan::where('id',$data[$key]->donatur->kelurahan_id)->first();
            $data[$key]->donatur->domisili = "";
            if($kelurahan){
                $data[$key]->donatur->domisili = $kelurahan->kelurahan.', '.$kelurahan->kecamatan->kecamatan.', '.$kelurahan->kecamatan->kabkot->kabupaten_kota.', '.$kelurahan->kecamatan->kabkot->provinsi->provinsi.', '.$kelurahan->kd_pos;
            }

            if($new_created->updated_at){
                $data[$key]->tanggal_masehi = $this->tgl_indo(date('d-m-Y', strtotime($new_created->updated_at)));
                $strdate = date('Y-m-d',strtotime($new_created->updated_at));
                $arr_date = explode('-',$strdate);
                $hij = $this->GregorianToHijriah($arr_date[0],$arr_date[1],$arr_date[2]);
                $data[$key]->tanggal_hijiriah = $hij['day'].' '.$this->month_hij($hij['month']).' '.$hij['year']; 
                $data[$key]->terbilang = ucwords($this->terbilang($d->amount))." Rupiah";
                $data[$key]->rupiah = $this->rupiah($data[$key]->amount);
            } 
        }

        return view('kwitansi',compact('data'));
        
        
    }

    private function GregorianToHijriah($GYear, $GMonth, $GDay) {
        $y = $GYear;
        $m = $GMonth;
        $d = $GDay;
        $jd = GregoriantoJD($m, $d, $y);
        $l = $jd - 1948440 + 10632;
        $n = (int) (( $l - 1 ) / 10631);
        $l = $l - 10631 * $n + 354;
        $j = ( (int) (( 10985 - $l ) / 5316)) * ( (int) (( 50 * $l) / 17719)) + (
        (int) ( $l / 5670 )) * ( (int) (( 43 * $l ) / 15238 ));
        $l = $l - ( (int) (( 30 - $j ) / 15 )) * ( (int) (( 17719 * $j ) / 50)) - (
        (int) ( $j / 16 )) * ( (int) (( 15238 * $j ) / 43 )) + 29;
        $m = (int) (( 24 * $l ) / 709 );
        $d = $l - (int) (( 709 * $m ) / 24);
        $y = 30 * $n + $j - 30;
         
        $Hijriah['year'] = $y;
        $Hijriah['month'] = $m;
        $Hijriah['day'] = $d;
         
        return $Hijriah;
    }

    private function month_hij($index){
        $bulanHijriah = array(1 => "Muharram", "Shofar", "Robi'ul Awwal", "Robi'uts Tsani",
        "Jumadil Ula", "Jumadil Akhiroh", "Rojab", "Sya'ban",
        "Romadhon", "Syawwal", "Dzulqo'dah", "Dzulhijjah");
        return $bulanHijriah[$index];
    }

    private function tgl_indo($tanggal){
        $bulan = array (
            1 =>   'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );
        $pecahkan = explode('-', $tanggal);
        
        // variabel pecahkan 0 = tanggal
        // variabel pecahkan 1 = bulan
        // variabel pecahkan 2 = tahun
     
        return  $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[2];
    }

    
    private function penyebut($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " ". $huruf[$nilai];
        } else if ($nilai <20) {
            $temp = $this->penyebut($nilai - 10). " belas";
        } else if ($nilai < 100) {
            $temp = $this->penyebut($nilai/10)." puluh". $this->penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . $this->penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->penyebut($nilai/100) . " ratus" . $this->penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . $this->penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->penyebut($nilai/1000) . " ribu" . $this->penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->penyebut($nilai/1000000) . " juta" . $this->penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $this->penyebut($nilai/1000000000) . " milyar" . $this->penyebut(fmod($nilai,1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = $this->penyebut($nilai/1000000000000) . " trilyun" . $this->penyebut(fmod($nilai,1000000000000));
        }     
        return $temp;
    }
    
    private function terbilang($nilai) {
        if($nilai<0) {
            $hasil = "minus ". trim($this->penyebut($nilai));
        } else {
            $hasil = trim($this->penyebut($nilai));
        }     		
        return $hasil;
    }

    function rupiah($angka){
	
        $hasil_rupiah = "Rp " . number_format($angka,2,',','.');
        return $hasil_rupiah;
     
    }
}
