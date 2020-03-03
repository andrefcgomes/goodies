<?php

namespace App\Http\Services;

use Session;

class FlashService
{

    public function flashMessage($type, $title, $message = null)
    {
        $cur_flash = Session::get('flash_message', []);
        array_push($cur_flash, collect(['type' => $type, 'title' => $title, 'message' => $message]));
//    Session::flash('flash_message', collect(['type' => $type,'title'=> $title,'message' => $message]));
        Session::flash('flash_message', $cur_flash);
    }

    public function flashSuccess($object, $action)
    {
        $message = trans('objects.' . $object) . " " . trans('actions.success.' . $action);
        return $this->flashMessage('success', $message);
    }

    public function flashError($object, $action)
    {
        $message = trans('actions.error.' . $action) . " " . trans('objects.' . $object);
        return $this->flashMessage('error', $message);
    }
}