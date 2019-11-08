<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{

    protected $guarded = ['id'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function getSrc($i = '')
    {
        switch ($i) {
            case 'logo':
                return str_finish(config('app.cdn_static'), '/') . $this->logo;
            case 'border':
                return str_finish(config('app.cdn_static'), '/') . $this->border;
            case 'background':
                return str_finish(config('app.cdn_static'), '/') . $this->background;
            case 'sign':
                return str_finish(config('app.cdn_static'), '/') . $this->sign;
            case 'badge':
                return str_finish(config('app.cdn_static'), '/') . $this->badge;
            case 'date_bg':
                return str_finish(config('app.cdn_static'), '/') . $this->date_bg;
        }
    }
}
