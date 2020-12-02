<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use ReflectionClass;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Database\Schema\Table;
use TCG\Voyager\Database\Types\Type;
use TCG\Voyager\Events\BreadAdded;
use TCG\Voyager\Events\BreadDeleted;
use TCG\Voyager\Events\BreadUpdated;
use TCG\Voyager\Facades\Voyager;

class UserController extends VoyagerBaseController
{

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
        $dataTypeContent->donatur_group =  DonaturGroup::where('id',$dataTypeContent->donatur_group_id)->first();

        $dataTypeContent->added_by_user = User::where('id',$dataTypeContent->added_by_user_id)->first();

        // dd(Auth::user()->role);
        
        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'isSoftDeleted'));
    }
    public function index()
    {
        // $this->authorize('browse_bread');

        // $dataTypes = Voyager::model('DataType')->select('id', 'name', 'slug')->get()->keyBy('name')->toArray();

        // $tables = array_map(function ($table) use ($dataTypes) {
        //     $table = Str::replaceFirst(DB::getTablePrefix(), '', $table);

        //     $table = [
        //         'prefix'     => DB::getTablePrefix(),
        //         'name'       => $table,
        //         'slug'       => $dataTypes[$table]['slug'] ?? null,
        //         'dataTypeId' => $dataTypes[$table]['id'] ?? null,
        //     ];

        //     return (object) $table;
        // }, SchemaManager::listTableNames());

        // return Voyager::view('voyager::tools.bread.index')->with(compact('dataTypes', 'tables'));
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
   
               if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                   $query = $model->{$dataType->scope}();
               } else {
   
                   // dd(auth()->user());
                       if(auth()->user()->id = 1){
       
                           $query = $model::select('*');
   
                       } 
                           else {
                            
                               $query = $model::select('*')->
                           whereIn('user_id', [auth()->user()->id])
                           ->whereIn('id_cabang', [auth()->user()->additional_each_id])
                           ->orWhereIn('donatur_group_id', [auth()->user()->groups_id]);
                       }
   
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
               'showCheckboxColumn'
           ));
    }

    /**
     * Create BREAD.
     *
     * @param Request $request
     * @param string  $table   Table name.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request, $table)
    {
        $this->authorize('browse_bread');

        $dataType = Voyager::model('DataType')->whereName($table)->first();

        $data = $this->prepopulateBreadInfo($table);
        $data['fieldOptions'] = SchemaManager::describeTable((isset($dataType) && strlen($dataType->model_name) != 0)
            ? DB::getTablePrefix().app($dataType->model_name)->getTable()
            : DB::getTablePrefix().$table
        );

        return Voyager::view('voyager::tools.bread.edit-add', $data);
    }

    private function prepopulateBreadInfo($table)
    {
        $displayName = Str::singular(implode(' ', explode('_', Str::title($table))));
        $modelNamespace = config('voyager.models.namespace', app()->getNamespace());
        if (empty($modelNamespace)) {
            $modelNamespace = app()->getNamespace();
        }

        return [
            'isModelTranslatable'  => true,
            'table'                => $table,
            'slug'                 => Str::slug($table),
            'display_name'         => $displayName,
            'display_name_plural'  => Str::plural($displayName),
            'model_name'           => $modelNamespace.Str::studly(Str::singular($table)),
            'generate_permissions' => true,
            'server_side'          => false,
        ];
    }

    /**
     * Store BREAD.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        $this->authorize('browse_bread');

        try {
            $dataType = Voyager::model('DataType');
            $res = $dataType->updateDataType($request->all(), true);
            $data = $res
                ? $this->alertSuccess(__('voyager::bread.success_created_bread'))
                : $this->alertError(__('voyager::bread.error_creating_bread'));
            if ($res) {
                event(new BreadAdded($dataType, $data));
            }

            return redirect()->route('voyager.bread.index')->with($data);
        } catch (Exception $e) {
            return redirect()->route('voyager.bread.index')->with($this->alertException($e, 'Saving Failed'));
        }
    }

    /**
     * Edit BREAD.
     *
     * @param string $table
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

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
        // $donatur_groups = DonaturGroup::all();

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
    }

    /**
     * Update BREAD.
     *
     * @param \Illuminate\Http\Request $request
     * @param number                   $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(Request $request, $id)
    {
        $this->authorize('browse_bread');

        /* @var \TCG\Voyager\Models\DataType $dataType */
        try {
            $dataType = Voyager::model('DataType')->find($id);

            // Prepare Translations and Transform data
            $translations = is_bread_translatable($dataType)
                ? $dataType->prepareTranslations($request)
                : [];

            $res = $dataType->updateDataType($request->all(), true);
            $data = $res
                ? $this->alertSuccess(__('voyager::bread.success_update_bread', ['datatype' => $dataType->name]))
                : $this->alertError(__('voyager::bread.error_updating_bread'));
            if ($res) {
                event(new BreadUpdated($dataType, $data));
            }

            // Save translations if applied
            $dataType->saveTranslations($translations);

            return redirect()->route('voyager.bread.index')->with($data);
        } catch (Exception $e) {
            return back()->with($this->alertException($e, __('voyager::generic.update_failed')));
        }
    }

    /**
     * Delete BREAD.
     *
     * @param Number $id BREAD data_type id.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
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

    public function getModelScopes($model_name)
    {
        $reflection = new ReflectionClass($model_name);

        return collect($reflection->getMethods())->filter(function ($method) {
            return Str::startsWith($method->name, 'scope');
        })->whereNotIn('name', ['scopeWithTranslations', 'scopeWithTranslation', 'scopeWhereTranslation'])->transform(function ($method) {
            return lcfirst(Str::replaceFirst('scope', '', $method->name));
        });
    }

    // ************************************************************
    //  _____      _       _   _                 _     _
    // |  __ \    | |     | | (_)               | |   (_)
    // | |__) |___| | __ _| |_ _  ___  _ __  ___| |__  _ _ __  ___
    // |  _  // _ \ |/ _` | __| |/ _ \| '_ \/ __| '_ \| | '_ \/ __|
    // | | \ \  __/ | (_| | |_| | (_) | | | \__ \ | | | | |_) \__ \
    // |_|  \_\___|_|\__,_|\__|_|\___/|_| |_|___/_| |_|_| .__/|___/
    //                                                  | |
    //                                                  |_|
    // ************************************************************

    /**
     * Add Relationship.
     *
     * @param Request $request
     */
    public function addRelationship(Request $request)
    {
        $relationshipField = $this->getRelationshipField($request);

        if (!class_exists($request->relationship_model)) {
            return back()->with([
                'message'    => 'Model Class '.$request->relationship_model.' does not exist. Please create Model before creating relationship.',
                'alert-type' => 'error',
            ]);
        }

        try {
            DB::beginTransaction();

            $relationship_column = $request->relationship_column_belongs_to;
            if ($request->relationship_type == 'hasOne' || $request->relationship_type == 'hasMany') {
                $relationship_column = $request->relationship_column;
            }

            // Build the relationship details
            $relationshipDetails = [
                'model'       => $request->relationship_model,
                'table'       => $request->relationship_table,
                'type'        => $request->relationship_type,
                'column'      => $relationship_column,
                'key'         => $request->relationship_key,
                'label'       => $request->relationship_label,
                'pivot_table' => $request->relationship_pivot,
                'pivot'       => ($request->relationship_type == 'belongsToMany') ? '1' : '0',
                'taggable'    => $request->relationship_taggable,
            ];

            $className = Voyager::modelClass('DataRow');
            $newRow = new $className();

            $newRow->data_type_id = $request->data_type_id;
            $newRow->field = $relationshipField;
            $newRow->type = 'relationship';
            $newRow->display_name = $request->relationship_table;
            $newRow->required = 0;

            foreach (['browse', 'read', 'edit', 'add', 'delete'] as $check) {
                $newRow->{$check} = 1;
            }

            $newRow->details = $relationshipDetails;
            $newRow->order = intval(Voyager::model('DataType')->find($request->data_type_id)->lastRow()->order) + 1;

            if (!$newRow->save()) {
                return back()->with([
                    'message'    => 'Error saving new relationship row for '.$request->relationship_table,
                    'alert-type' => 'error',
                ]);
            }

            DB::commit();

            return back()->with([
                'message'    => 'Successfully created new relationship for '.$request->relationship_table,
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with([
                'message'    => 'Error creating new relationship: '.$e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     * Get Relationship Field.
     *
     * @param Request $request
     *
     * @return string
     */
    private function getRelationshipField($request)
    {
        // We need to make sure that we aren't creating an already existing field

        $dataType = Voyager::model('DataType')->find($request->data_type_id);

        $field = Str::singular($dataType->name).'_'.$request->relationship_type.'_'.Str::singular($request->relationship_table).'_relationship';

        $relationshipFieldOriginal = $relationshipField = strtolower($field);

        $existingRow = Voyager::model('DataRow')->where('field', '=', $relationshipField)->first();
        $index = 1;

        while (isset($existingRow->id)) {
            $relationshipField = $relationshipFieldOriginal.'_'.$index;
            $existingRow = Voyager::model('DataRow')->where('field', '=', $relationshipField)->first();
            $index += 1;
        }

        return $relationshipField;
    }

    /**
     * Delete Relationship.
     *
     * @param Number $id Record id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteRelationship($id)
    {
        Voyager::model('DataRow')->destroy($id);

        return back()->with([
            'message'    => 'Successfully deleted relationship.',
            'alert-type' => 'success',
        ]);
    }
}
