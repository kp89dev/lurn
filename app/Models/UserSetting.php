<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    protected $guarded = ['id', 'user_id'];

    protected $casts = [
        'messages'        => 'object',
        'receive_updates' => 'integer',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Returns the state of a message notification.
     * Those alert messages that are dismissable by the user.
     *
     * @param string $key
     * @param null   $default
     * @return null
     */
    public function getMessageState(string $key, $default = null)
    {
        return isset($this->messages->$key) ? $this->messages->$key : $default;
    }

    /**
     * Sets a new state for the given message $key.
     *
     * @param string $key
     * @param        $value
     * @return $this
     */
    public function setMessageState(string $key, $value)
    {
        $messages = $this->messages ?: new \stdClass;
        $messages->$key = $value;
        $this->messages = $messages;

        return $this;
    }
}
