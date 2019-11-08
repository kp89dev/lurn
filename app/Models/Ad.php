<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Ad extends Model
{
    protected $fillable = ['admin_title', 'link', 'location', 'position', 'status'];

    public function getPrintableImageUrl($show = 'primary')
    {
        if ($show == 'primary'){
            $file = $this->image;
        }else {
            $file = $this->hover_image;
        }
        $disk = Storage::disk('static');
        $cdnUrl = sprintf('%s/%s', config('app.cdn_static'), $file);
        $inAdminArea = strpos(request()->path(), 'admin/') === 0;

        // Check the file availability only locally or in the admin panel.
        if (app()->environment('local') || $inAdminArea) {
            return $disk->exists($file) ? $cdnUrl : url('images/default-ad-thumbnail.png');
        }

        return $cdnUrl;
    }

    public function getByLocationAndPosition($location, $position)
    {
        return self::where('location', $location)->where('position', $position)->where('status', 1)->orderBy('updated_at')->take(1)->get();
    }
}
