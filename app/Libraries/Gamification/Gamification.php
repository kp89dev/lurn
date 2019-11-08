<?php

namespace Gamification;

use Gamification\Transformers\TransformedDataObject;
use Gamification\Transformers\Activity;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

class Gamification
{
    /**
     * Guzzle client object.
     * 
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Activities registered with the class.
     * 
     * @var \Illuminate\Support\Collection
     */
    protected $activities;

    /**
     * Class path for activities.
     *
     * @var string
     */
    protected $activityPath = '\\Gamification\\Activities';

    /**
     * URL for the API calls.
     * 
     * @var string
     */
    protected $apiResource;

    /**
     * Create a new instance of the class.
     * 
     */
    public function __construct()
    {
        $this->activities = collect([]);
    
        $this->apiResource = env('GAMIFICATION_API_URL');

        $this->client = new Client(['base_uri' => $this->apiResource]);
    }

    /**
     * Register an activity class.
     *
     * @param string $activity
     * @return void
     *
     * @throws UnexpectedValueException
     */
    public function registerActivityHandler($activity)
    {
        $className = ucfirst($activity);

        $class = "{$this->activityPath}\\{$className}";

        $object = new $class();

        if (! $object instanceof Activity) {
            throw new \UnexpectedValueException();
        }

        $this->activities->put($activity, $object);
    }

    /**
     * Send a request to the API.
     *
     * @param  string  $method
     * @param  \Gamification\Transformers\TransformedDataObject  $data
     * @return array
     */
    public function send($method, TransformedDataObject $data)
    {
        $uri = env('GAMIFICATION_API_STAGE') . '/' . $data->getEndpoint();

        $json = $data->getParams();

        $headers = [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'application/json',
        ];

        try {
            $response = $this->client->request($method, $uri, compact('headers', 'json'));
            $reply = json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            $reply = $e->getResponse()->getBody(true);
        } catch (ServerException $e) {
            $reply = $e->getResponse()->getBody(true);
        } catch (RequestException $e) {
            $reply = $e->hasResponse() ? $e->getResponse()->getBody(true) : Psr7\str($e->getRequest());
        }

        return $reply;
    }

    /**
     * Handle method calls.
     *
     * @param string $name
     * @param array  $parameters
     * @return mixed
     *
     * @throws BadMethodCallException
     * @throws UnexpectedValueException
     */
    public function __call($name, $parameters)
    {
        $className = ucfirst($name);

        if (!$this->activities->has($name) && class_exists("{$this->activityPath}\\{$className}")) {
            $this->registerActivityHandler($name);
        }

        if ($this->activities->has($name)) {
            $data = $this->activities->get($name)->handle(...$parameters);

            return $this->send('POST', $data);
        }

        if (method_exists($this, $name)) {
            return $this->$name(...$parameters);
        }

        throw new \BadMethodCallException;
    }
}