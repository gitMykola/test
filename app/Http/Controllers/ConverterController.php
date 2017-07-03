<?php

namespace F16\Http\Controllers;

use Illuminate\Http\Request;
use F16\Converter;

//require_once('app/Converter.php');
class ConverterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('converter');
    }
    public function convert(Request $request)
    {
       if($request->hasFile('source'))
        {
            $file = $request->file('source');
            $source = Converter::sourceToarray($file->path(),$file->getClientOriginalExtension());
            if(!is_array($source))return response()->json(array('result'=>'!','errorStatus'=>true,'error'=>'File incorrect.'),    200);
            $cExt = strtolower($request->get('extention'));
            $fileName = explode('.',$file->getClientOriginalName());
            $data = Converter::arrayToFile($source, $cExt, $fileName[0]);
            if(is_string($data) && preg_match('/.*(json|xml|csv|yml)\z/', $data))return response()->json(array('result'=>$data,'errorStatus'=>false),    200);
            else return response()->json(array('result'=>'!','errorStatus'=>true,'error'=>'File d\'not converted.'),    200);
        }
        else return response()->json(array('result'=>'!','errorStatus'=>true,'error'=>'File d\'not recieved.'),    200);

    }
}


