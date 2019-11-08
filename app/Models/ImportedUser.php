<?php

namespace App\Models;
use App\Models\Traits\MergeableUser;
use App\Models\Traits\SearchableUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as PasswordResetableInterface;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Notifications\Account\ResetPassword;

/**
 * @method static getByIdAndTable(\int $id, \string $table)
 */
class ImportedUser extends Model implements PasswordResetableInterface
{
    use CanResetPassword, Notifiable, SearchableUser, MergeableUser;
    
    protected $table = 'users_import_all';

    protected $primaryKey = 'an_id';

    protected $visible = ['an_id', 'user_id', 'name', 'email', 'description'];

    protected $casts = [
        'user_id' => 'integer',
    ];

    /**
     * @param Builder $query
     * @param int     $id
     * @param string  $table
     *
     * @return $this
     */
    public function scopeGetByIdAndTable(Builder $query, int $id, string $table)
    {
        return $query->where('user_id', '=', $id)
                     ->where('from_table', '=', $table);
    }


    public function mergedInto()
    {
        return $this->belongsToMany(User::class, 'user_merges', 'merged_user_id', 'into_user_id')
                    ->wherePivot('from_table', '=', 'users_import_all');
    }

    /**
     * @param string $connection
     *
     * @return null
     */
    public function getImportedUserCoursesIds()
    {
        return DB::table('course_subscriptions')
            ->select('course_id')
            ->where('user_id', '=', $this->user_id)
            ->where('from_table', '=', $this->getAttribute('connection'))
            ->get();
    }
    
    public function sendPasswordResetNotification($token) {
        $this->notify(new ResetPassword($token));
    }
}
