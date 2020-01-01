<?php
/**
 * ResourceController v0.0.1
 * 
 * Base class for usage in resource routes provided by Laravel. 
 * All controllers setup as resource routes in laravel should extend this class and implement the abstract methods.
 * 
 * @author André Gomes
 * @link https://github.com/andrefcgomes/goodies
 * 
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;

//use Storage;
//use File;
//use Session;
use Response;
use FileService;
use FlashService;

abstract class ResourceController extends Controller
{


    public abstract function getModel(); // Model instance. eg: return new \\App\User;
    // public abstract function getObjectName(); // WIP Nome utilizado para fins de translate (ver FlashService)
    public abstract function getCreateView(); //nome da view para create
    public abstract function getIndexView(); //nome da view para listagem
    public abstract function getEditView(); //nome da view para edicao
    public abstract function getStoreValidator(); //custom request para validar o create -> usar EmptyRequest se nao for necessaria validacao
    public abstract function getUpdateValidator(); //custom request para validar o create -> usar EmptyRequest se nao for necessaria validacao
    public abstract function getHomeRoute(); //rota base (normalmente index) para redirecionar apos formularios


//    public function getEntityName() { return class_basename($this->getModel()); } //reflexao para tirar o nome da class
    public function getFiles() { return []; } //nomes de atributos que sao ficheiros para processar no store/update
    public function getBase64Images() { return []; } //nomes de atributos que sao imagens base64 para processar no store/update
    public function getDependencies() { return collect([]); } //modelos extra para popular dropdowns (ex: lista de departamentos para criar user)
    public function afterStore($item) { return null; }
    public function afterUpdate($item) { return null; }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = $this->getModel()->all();
        return view($this->getIndexView())->with(compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $dependencies = $this->getDependencies();
        return view($this->getCreateView())->with(compact('dependencies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        $this->validate($request, $this->getStoreValidator()->rules());
        $data = $request->intersect($this->getModel()->getFillable()); //tipo only mas nao devolve campos que nao existem
        $data = $this->processFiles($data, $request);
        $item = $this->getModel()->create($data);
        $this->afterStore($item);
        FlashService::flashSuccess($this->getObjectName(), "create");
        return redirect($this->getHomeRoute());
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = $this->getModel()->findOrFail($id);
        $dependencies = $this->getDependencies();
        return view($this->getEditView())->with(compact('item', 'dependencies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */


    public function update(Request $request, $id)
    {
        $this->validate($request, $this->getUpdateValidator()->rules());
        $data = $request->intersect($this->getModel()->getFillable()); //tipo only mas nao devolve campos que nao existem
        $data = $this->processFiles($data, $request);
        $item = $this->getModel()->findorFail($id)->update($data);
        $this->afterUpdate($item);
        FlashService::flashSuccess($this->getObjectName(), "edit");
        return redirect($this->getHomeRoute());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->getModel()->destroy($id)) {
            FlashService::flashSuccess($this->getObjectName(), "destroy");
            return Response::json(['return_to' => $this->getHomeRoute()], 200);
        }
        FlashService::flashError($this->getObjectName(), "destroy");
    }

    /**
     * Process request files
     *
     * @param array $dto
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    private function processFiles($dto, $request)
    {
        foreach ($this->getFiles() as $filename) {
            if ($request->hasFile($filename)) {
                $path = FileService::uploadFile($request, $filename, $this->getObjectName());
                $dto[$filename] = $path;
            }
        }
        return $dto;
    }


//  public function flashMessage($type,$title,$message=null){
//    Session::flash('flash_message', collect(['type' => $type,'title'=> $title,'message' => $message]));
//  }
//
//
//
//  public function flashSuccess($message){
//    return $this->flashMessage('success',$message);
//  }
//
//  public function flashError($message){
//    return $this->flashMessage('error',$message);
//  }
}
