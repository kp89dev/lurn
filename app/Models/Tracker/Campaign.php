<?php
namespace App\Models\Tracker;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $table = 'tr_campaigns';
    protected $guarded = ['id'];

    public function getCampaign($name, $source, $medium, $content)
    {
        $string = trim($name) . trim($source) . trim($medium) . trim($content);
        $hash    = md5($string);

        $row = $this->where('hash', $hash)->first();
        if ($row instanceof self) {
            return $row;
        }

        return self::create(compact('name', 'source', 'medium', 'content', 'hash'));
    }
}
