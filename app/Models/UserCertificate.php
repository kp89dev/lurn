<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCertificate extends Model
{
    protected $fillable = ['user_id', 'certificate_id'];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function issueCert($test_id, $user_id, $test_cert)
    {
        $test_cert = Certificate::find($test_cert);
        $cert = new UserCertificate();
        $cert->user_id = $user_id;
        $cert->test_id = $test_id;
        $cert->certificate_title = $test_cert->title;
        $cert->certificate_logo = $test_cert->logo;
        $cert->certificate_logo_style = $test_cert->logo_style;
        $cert->certificate_border = $test_cert->border;
        $cert->certificate_border_style = $test_cert->border_style;
        $cert->certificate_background = $test_cert->background;
        $cert->certificate_date_bg = $test_cert->date_bg;
        $cert->certificate_date_style = $test_cert->date_style;
        $cert->certificate_sign = $test_cert->sign;
        $cert->certificate_sign_style = $test_cert->sign_style;
        $cert->certificate_badge = $test_cert->badge;
        $cert->certificate_badge_style = $test_cert->badge_style;
        $cert->certificate_style = $test_cert->style;
        $cert->certificate_body = $test_cert->body;
        $cert->issued = date('Y-m-d');
        $cert->save();

        return $cert;
    }

    public function getSrc($i = '')
    {
        switch ($i) {
            case 'logo':
                return str_finish(config('app.cdn_static'), '/') . $this->certificate_logo;
            case 'border':
                return str_finish(config('app.cdn_static'), '/') . $this->certificate_border;
            case 'background':
                return str_finish(config('app.cdn_static'), '/') . $this->certificate_background;
            case 'sign':
                return str_finish(config('app.cdn_static'), '/') . $this->certificate_sign;
            case 'badge':
                return str_finish(config('app.cdn_static'), '/') . $this->certificate_badge;
            case 'date_bg':
                return str_finish(config('app.cdn_static'), '/') . $this->certificate_date_bg;
        }
    }
}
