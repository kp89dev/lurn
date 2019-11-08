<?php

namespace Gamification\Transformers;

use App\Models\UserPointActivity;
use Gamification\Exceptions\InvalidSchemaException;
use Carbon\Carbon;
use ReflectionClass;
use Validator;

class Activity
{
    /**
     * API endpoint.
     * 
     * @var string
     */
    protected $endpoint = 'log-activity';

    /**
     * Request schema.
     *
     * @var array
     */
    protected $schema = [];

    /**
     * The function index of this activity.
     * 
     * @var string
     */
    protected $function = null;

    /**
     * A description of the transaction.
     *
     * @var string
     */
    protected $description = null;

    /**
     * Metadata of the activity.
     *
     * @var string
     */
    protected $metadata = null;

    /**
     * Should this transaction be marked pending?
     * 
     * @var boolean
     */
    protected $pending = true;

    /**
     * The user we're awarding points to.
     *
     * @var \App\Models\User
     */
    protected $user = null;

    /**
     * Point amount for this activity.
     *
     * @var integer
     */
    protected $points = 0;

    /**
     * Transform activity date into a request we can make.
     *
     * @param  array  $data
     * @return \Gamification\Transformers\TransformedDataObject
     * 
     * @throws \Gamification\Exceptions\InvalidSchemaException
     */
    protected function transform(array $data)
    {
        $data['activityDetailsJSON'] = json_encode([]);

        if (!empty($this->schema)) {
            $validator = Validator::make($data, $this->schema);

            if ($validator->fails()) {
                throw new InvalidSchemaException($validator);
            }
        }

        if (isset($data['description'])) {
            $this->description = $data['description'];
        }

        if (isset($data['metadata'])) {
            $this->metadata = $data['metadata'];
        }

        if (isset($data['pending'])) {
            $this->pending = $data['pending'];
        }

        if (isset($data['points'])) {
            $this->points = intval($data['points']);
        }

        if (isset($data['user'])) {
            $this->user = $data['user'];
        } else {
            $this->user = user();
        }

        if (!isset($data['ts'])) {
            $data['ts'] = Carbon::now()->toDateTimeString();
        }

        if (isset($data['details'])) {
            $data['activityDetailsJSON'] = json_encode($data['details']);
        }

        $data['function'] = lcfirst(
            $this->function ?: (new ReflectionClass($this))->getShortName()
        );

        $data['digest'] = md5(strtolower($data['email']) . env('GAMIFICATION_SECRET') . $data['ts']);

        unset(
            $data['description'],
            $data['metadata'],
            $data['pending'],
            $data['points'],
            $data['details'],
            $data['user']
        );

        $transformedData = [
            'endpoint' => $this->endpoint,
            'params' => $data,
        ];

        $this->save();

        return new TransformedDataObject($transformedData);
    }

    /**
     * Save a point transaction to the database.
     * 
     * @return void
     */
    public function save()
    {
        UserPointActivity::create([
            'user_id'     => $this->user->id,
            'transaction' => $this->description,
            'points'      => $this->points,
            'pending'     => $this->pending,
        ]);
    }
}