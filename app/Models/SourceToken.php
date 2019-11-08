<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SourceToken extends Model
{
    protected $casts = [
        'used' => 'integer',
    ];

    protected $guarded = ['id'];

    public static function createNew(Source $source, User $user): self
    {
        self::unguard(true);

        return self::create([
            'user_id'   => $user->id,
            'source_id' => $source->id,
            'token'     => self::getToken(50)
        ]);
    }

    /**
     * @param int $length
     *
     * @return string
     */
    private static function getToken(int $length): string
    {
        return bin2hex(random_bytes($length));
    }

    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
