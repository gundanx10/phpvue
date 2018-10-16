<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Api\ApiHelper;
use App\Models\User;
use Image;
use Storage;

class ImageController extends Controller
{
    public function show(Request $request)
    {
        $vData = $request->validate([
            'url' => 'required',
        ]);
        $path = '';
        $preg = "/uploadfile\/([\d]*)\/([\d]*)\/([\d]*)\.(?:png|jpg|PNG|JPG|jpeg|JEPG)/is";
        preg_match($preg, $vData['url'], $result);
        if (isset($result[0])) {
            $path = base_path() . '/' . $result[0];
        }
        if (!is_file($path)) {
            $img = Image::make($vData['url']);
        } else {
            $img = Image::make($path);
        }
        $height = $img->height();
        $width = $img->width();
        $newWidth = $request->input('width', 1080);
        $maxaspect = $request->input('maxaspect', 0.75);
        $scale = round($newWidth / $width, 2);
        $newHight = floor($height * $scale);
        $cropHitght = floor($newWidth / $maxaspect);
        $cropHitght = $cropHitght < $newHight ? $cropHitght : $newHight;
        $img->resize($newWidth, $newHight);
        $img->crop($newWidth, $cropHitght, 0, 0);

        return $img->response('jpg');
    }
}
