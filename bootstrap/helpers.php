<?php

use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Debug\Dumper;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

define('TEST_PASSING_MARK', 74.99);

if (! function_exists('user')) {
    /**
     * @return User
     */
    function user()
    {
        return Auth::user();
    }
}

if (! function_exists('user_array')) {
    /**
     * @return array|null
     */
    function user_array()
    {
        return user() ? user()->toArray() : null;
    }
}

if (! function_exists('now')) {
    /**
     * @return Carbon
     */
    function now()
    {
        return Carbon::now();
    }
}

if (! function_exists('crud')) {
    /**
     * @return Ionut\Crud\Crud
     */
    function crud()
    {
        return app('crud');
    }
}

if (! function_exists('raw')) {
    /**
     * @param $input
     * @return \Illuminate\Database\Query\Expression
     */
    function raw($input)
    {
        return \DB::raw($input);
    }
}

if (! function_exists('prepare_insert_bindings')) {
    /**
     * @param  array $values
     * @return string
     */
    function prepare_insert_bindings(array $values)
    {
        // We'll treat every insert like a batch insert so we can easily insert each
        // of the records into the database consistently. This will make it much
        // easier on the grammars to just handle one type of record insertion.
        $bindings = [];

        foreach ($values as $record) {
            foreach ($record as $value) {
                $bindings[] = $value;
            }
        }

        return $bindings;
    }
}

if (! function_exists('prepare_insert_values')) {
    function prepare_insert_values($values)
    {
        // Since every insert gets treated like a batch insert, we will make sure the
        // bindings are structured in a way that is convenient for building these
        // inserts statements by verifying the elements are actually an array.
        if (! is_array(reset($values))) {
            $values = [$values];
        }

        // Since every insert gets treated like a batch insert, we will make sure the
        // bindings are structured in a way that is convenient for building these
        // inserts statements by verifying the elements are actually an array.
        else {
            foreach ($values as $key => $value) {
                ksort($value);
                $values[$key] = $value;
            }
        }

        return $values;
    }
}


if (! function_exists('input')) {
    /**
     * @return string|Request
     */
    function input()
    {
        $parameters = func_get_args();

        if (! count($parameters)) {
            return app('request');
        }

        if (count($parameters) == 1) {
            return Input::get(array_shift($parameters));
        }

        return call_user_func_array('Input::only', $parameters);
    }
}


if (! function_exists('angular')) {
    /**
     * @return Illuminate\Contracts\View\
     */
    function angular($initialState = null)
    {
        return view('angular', compact('initialState'));
    }
}

if (! function_exists('relieve_revisions')) {
    /**
     * @param  array $collection
     * @return array
     */
    function relieve_revisions($collection)
    {
        foreach ($collection as $k => $model) {
            if ($model->revision) {
                $model->bringRevisioned();
            }
        }

        return $collection;
    }
}

if (! function_exists('hex2rgb')) {
    /**
     * @param  string $hex
     * @return array
     */
    function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        }
        else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = [$r, $g, $b];

        //return implode(",", $rgb); // returns the rgb values separated by commas
        return $rgb; // returns an array with the rgb values
    }
}

if (! function_exists('dump_builder')) {

    /**
     * @param Builder $builder
     */
    function dump_builder($builder, array $outputAlso = [])
    {
        $sql_string = $builder->toSql();
        $params = $builder->getBindings();

        if (! empty($params)) {
            $indexed = $params == array_values($params);
            foreach ($params as $k => $v) {
                if (is_object($v)) {
                    if ($v instanceof \DateTime) {
                        $v = $v->format('Y-m-d H:i:s');
                    }
                    else {
                        continue;
                    }
                }
                elseif (is_string($v)) {
                    $v = "'$v'";
                }
                elseif ($v === null) {
                    $v = 'NULL';
                }
                elseif (is_array($v)) {
                    $v = implode(',', $v);
                }

                if ($indexed) {
                    $sql_string = preg_replace('/\?/', $v, $sql_string, 1);
                }
                else {
                    if ($k[0] != ':') {
                        $k = ':' . $k;
                    } //add leading colon if it was left out
                    $sql_string = str_replace($k, $v, $sql_string);
                }
            }
        }

        die($sql_string);
        array_unshift($outputAlso, 'SQL: ' . $sql_string);
        call_user_func_array('dd', $outputAlso);
    }
}


if (! function_exists('dd')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function dd()
    {
        // Set the charset header in order to fix the chrome developer tools "No Content Available"
        header('Content-Type: text/html; charset=utf-8');

        array_map(function ($x) {
            (new Dumper)->dump($x);
        }, func_get_args());

        die;
    }
}

if (! function_exists('isset_or')) {
    /**
     * @param $object
     * @param $key
     * @param $default
     * @return bool
     */
    function isset_or($object, $key, $default)
    {
        return $object[$key] ?? $default;
    }
}

if (! function_exists('nice_camel')) {
    /**
     * Return ucwords from camel or snake cases.
     *
     * @param $input
     * @return string
     */
    function nice_camel($input)
    {
        $output = snake_case($input);
        $output = str_replace('_', ' ', $output);
        $output = ucwords($output);

        // Corect some known words,
        $output = str_replace('Id', 'ID', $output);

        return $output;
    }
}

if (! function_exists('subdomain_abort')) {
    /**
     * Returns a default nginx 404 page.
     *
     * @param null   $title
     * @param string $reason
     * @return string
     */
    function subdomain_abort($title = null, $reason = '')
    {
        if (is_null($title) || ! strlen($reason)) {
            return response()->view('errors.nginx-404', [], 404);
        }
        else {
            return response()->view('errors.nginx-custom', compact('title', 'reason'), 410);
        }
    }
}

if (! function_exists('subdomain_not_available')) {
    /**
     * Returns a default nginx 404 page.
     *
     * @param bool $invalidSLKeys
     * @return string
     */
    function subdomain_not_available($invalidSLKeys = false)
    {
        if ($invalidSLKeys) {
            return response()->view('errors.invalid-sendlane-keys-503', [], 503);
        }

        return response()->view('errors.basic-503', [], 503);
    }
}

if (! function_exists('subdomain_under_construction')) {
    /**
     * Returns a default nginx 404 page.
     *
     * @return string
     */
    function subdomain_under_construction()
    {
        return response()->view('errors.under-construction', [], 503);
    }
}

if (! function_exists('replace_link_tags')) {
    /**
     * Returns a default nginx 404 page.
     *
     * @param $content
     * @param $link
     * @param $adTexts
     * @return string
     */
    function replace_link_tags($content, $link, $adTexts)
    {
        $index = 0;

        if (count($adTexts) == 0) {
            $adTexts = [$link];
        }

        $callback = function ($matches) use (&$index, $link, $adTexts) {
            return sprintf('<a href="%s">%s</a>', $link, $adTexts[$index++ % count($adTexts)]);
        };

        // Replace [link] (all but not [link]) [/link]
        $content = preg_replace_callback('#(\[link\])(?:(?!\1).)*\[/link\]#si', $callback, $content);

        // Replace [link]
        $content = preg_replace_callback('#(\[link\])#si', $callback, $content);

        return $content;
    }
}

if (! function_exists('format_css_value')) {
    /**
     * Returns a css property from color picker saved data.
     *
     * @param $value
     *
     * @return string
     */
    function format_css_value($value)
    {
        if (is_array($value) && isset($value['_a'])) {
            list($r, $g, $b) = array_map('intval', [$value['_r'], $value['_g'], $value['_b']]);
            $a = $value['_a'];

            return "rgba($r, $g, $b, $a)";
        }

        return $value;
    }
}

if (! function_exists('get_git_hash')) {
    /**
     * Returns current git hash.
     *
     * @return string
     */
    function get_git_hash()
    {
        $value = Cache::rememberForever('git_hash', function () {
            exec('git rev-parse --verify HEAD 2> /dev/null', $output);

            return $output[0];
        });

        return $value;
    }
}

if (! function_exists('handle_heading_elements')) {
    /**
     * Replaces <h[0-9] with <div class="h[0-9] ...
     *
     * @param $aboutMe
     * @return string
     */
    function handle_heading_elements($aboutMe)
    {
        return preg_replace_callback('#<(h[1-6])(.*?>.*?</)\1>#si', function ($matches) {
            return sprintf(
                '<div class="%s"%sdiv>',
                $matches[1],
                preg_replace('# class=(["\'])[^\1]*?\1#s', '', $matches[2])
            );
        }, $aboutMe);
    }
}

if (! function_exists('get_step_name')) {
    /**
     * Returns the step name from the number.
     *
     * @param $stepNo
     * @return string
     */
    function get_step_name($stepNo)
    {
        $stepNo = $stepNo instanceof FunnelProgressState ? $stepNo : new FunnelProgressState($stepNo);

        return app(FunnelProgressReader::class)->translate($stepNo);
    }
}

if (! function_exists('convert_smart_quotes')) {
    /**
     * Converts curly quotes into normal ones. UTF-8 reasons.
     *
     * @param $string
     * @return string
     */
    function convert_smart_quotes($string)
    {
        $quotes = [
            "\xC2\xAB"     => '"', // « (U+00AB) in UTF-8
            "\xC2\xBB"     => '"', // » (U+00BB) in UTF-8
            "\xE2\x80\x98" => "'", // ‘ (U+2018) in UTF-8
            "\xE2\x80\x99" => "'", // ’ (U+2019) in UTF-8
            "\xE2\x80\x9A" => "'", // ‚ (U+201A) in UTF-8
            "\xE2\x80\x9B" => "'", // ‛ (U+201B) in UTF-8
            "\xE2\x80\x9C" => '"', // “ (U+201C) in UTF-8
            "\xE2\x80\x9D" => '"', // ” (U+201D) in UTF-8
            "\xE2\x80\x9E" => '"', // „ (U+201E) in UTF-8
            "\xE2\x80\x9F" => '"', // ‟ (U+201F) in UTF-8
            "\xE2\x80\xB9" => "'", // ‹ (U+2039) in UTF-8
            "\xE2\x80\xBA" => "'", // › (U+203A) in UTF-8
        ];
        $string = strtr($string, $quotes);

        // Version 2
        $search = [
            chr(145),
            chr(146),
            chr(147),
            chr(148),
            chr(151),
        ];
        $replace = ["'", "'", '"', '"', ' - '];
        $string = str_replace($search, $replace, $string);

        // Version 3
        $string = str_replace(
            ['&#8216;', '&#8217;', '&#8220;', '&#8221;'],
            ["'", "'", '"', '"'],
            $string
        );

        // Version 4
        $search = [
            '&lsquo;',
            '&rsquo;',
            '&ldquo;',
            '&rdquo;',
            '&mdash;',
            '&ndash;',
        ];
        $replace = ["'", "'", '"', '"', ' - ', '-'];
        $string = str_replace($search, $replace, $string);

        return $string;
    }
}

if (! function_exists('get_bool')) {
    /**
     * Return a boolean even from strings "false" and "true".
     *
     * @param $value
     * @return bool
     */
    function get_bool($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}

if (! function_exists('get_domain')) {
    /**
     * Parses the given URL and returns the main domain.
     *
     * @param $domain
     * @return string
     */
    function get_domain($domain)
    {
        $domain = preg_replace('#^[^/]*?//#', '', $domain);
        $domain = preg_replace('#^([^/\?]+).*#', '$1', $domain);
        $domain = preg_replace('#.*?([a-z0-9][a-z0-9-]*?(?:\.(?:com?))?\.\w\w+)$#i', '$1', $domain);

        return strtolower($domain);
    }
}

if (! function_exists('minify_html')) {
    /**
     * Returns the step name from the number.
     *
     * @param $html
     * @return string
     */
    function minify_html($html)
    {
        $search = [
            '/<!--.*?-->/s',
            '/\>[^\S ]+/s',
            '/[^\S ]+\</s',
            '/(\s)+/s',
        ];
        $replace = [
            '',
            '>',
            '<',
            '\\1',
        ];

        return trim(preg_replace($search, $replace, $html));
    }
}

if (! function_exists('get_quiz_theme_name')) {
    /**
     * Returns the theme name from the quiz HTML.
     *
     * @param $html
     * @return string
     */
    function get_quiz_theme_name($html)
    {
        if (preg_match('#id="quiz" class=".*?((?:\w+) theme|theme (?:\w+)).*?"#', $html, $theme)) {
            return trim(str_replace('theme', '', $theme[1]));
        }

        return '';
    }
}

if (! function_exists('for_except')) {
    /**
     * Iterates through the $list, ignoring $exceptions, calling $callback on each iteration.
     *
     * @param         $list
     * @param         $exceptions
     * @param Closure $callback
     */
    function for_except($list, $exceptions, Closure $callback)
    {
        foreach ($list as $index => $item) {
            if (! in_array($item, $exceptions)) {
                $callback($item, $index);
            }
        }
    }
}

if (! function_exists('get_date_range')) {
    /**
     * Returns the theme name from the quiz HTML.
     *
     * @param $startDate
     * @param $endDate
     * @return array
     */
    function get_date_range($startDate, $endDate = null)
    {
        $endDate = is_null($endDate) ? $startDate : $endDate;
        $startTs = strtotime($startDate);
        $endTs = min(strtotime($endDate), time());
        $dates = [$startDate];

        while ($startTs < $endTs) {
            $dates[] = date('Y-m-d', $startTs = strtotime('+1 day', $startTs));
        }

        return $dates;
    }
}

if (! function_exists('get_shorter_date')) {
    /**
     * @param $date
     * @return mixed
     */
    function get_shorter_date($date)
    {
        return str_replace('-', '', substr($date, -8));
    }
}

if (! function_exists('shorter_number')) {
    /**
     * @param     $number
     * @param int $digits
     * @return mixed
     */
    function shorter_number($number, $digits = 0)
    {
        if ($number >= 1e6) {
            return rtrim(number_format($number / 1e6, $digits), '0.') . 'M';
        } elseif ($number >= 1e3) {
            return rtrim(number_format($number / 1e3, $digits), '0.') . 'K';
        }

        return $number;
    }
}

if (! function_exists('str_begin')) {
    /**
     * Returns the first part of a string by the given delimiter.
     *
     * @param        $string
     * @param string $delimiter
     * @return bool|string
     */
    function str_begin($string, $delimiter = '/')
    {
        return ($pos = strrpos($string, $delimiter)) === false ? $string : substr($string, 0, $pos);
    }
}

if (! function_exists('str_end')) {
    /**
     * Returns the last part of a string by the given delimiter.
     * Faster than basename()
     *
     * @param        $string
     * @param string $delimiter
     * @return bool|string
     */
    function str_end($string, $delimiter = '/')
    {
        return substr($string, ($pos = strrpos($string, $delimiter)) === false ? 0 : $pos + 1);
    }
}

if (! function_exists('course_status')) {
    /**
     * Returns a course's status id.
     *
     * @param $statusName
     * @return bool|int
     */
    function course_status($statusName)
    {
        return array_search($statusName, Course::$userStatuses);
    }
}

if (! function_exists('course_status_name')) {
    /**
     * Returns the status name from the given ID.
     *
     * @param $statusId
     * @return bool
     */
    function course_status_name($statusId)
    {
        return Course::$userStatuses[$statusId] ?? '';
    }
}

if (! function_exists('user_enrolled')) {
    /**
     * Checks if the given or current user is enrolled to the given course.
     *
     * @param           $course
     * @param null      $user
     * @param bool      $returnSubscription
     * @return bool
     */
    function user_enrolled($course, $user = null, $returnSubscription = false)
    {
        $user or $user = user();
        $check = $user && $subscription = $user->enrolled($course);

        if ($returnSubscription) {
            return $subscription;
        }

        return $check;
    }
}

if (! function_exists('user_isnt_enrolled')) {
    /**
     * Checks if the given or current user is not enrolled to the given course.
     *
     * @param           $course
     * @param null      $user
     * @param bool      $returnSubscription
     * @return bool
     */
    function user_isnt_enrolled($course, $user = null, $returnSubscription = false)
    {
        return ! user_enrolled($course, $user, $returnSubscription);
    }
}

if (! function_exists('user_completed')) {
    /**
     * Checks if the given user completed the given lesson.
     *
     * @param      $lesson
     * @param null $user
     * @return bool
     */
    function user_completed($lesson, $user = null)
    {
        $user or $user = user();

        return $user && $user->completed($lesson);
    }
}

if (! function_exists('user_hasnt_completed')) {
    /**
     * Checks if the given user hasn't completed the given lesson.
     *
     * @param      $lesson
     * @param null $user
     * @return bool
     */
    function user_hasnt_completed($lesson, $user = null)
    {
        return ! user_completed($lesson, $user);
    }
}

if (! function_exists('get_payment_icon_name')) {
    /**
     * @param string $type
     * @return string
     */
    function get_payment_icon_name(string $type): string
    {
        $types = [
            'visa'       => 'visa',
            'mastercard' => 'mastercard',
            'amex'       => 'american express',
            'discover'   => 'discover',
        ];

        return $types[strtolower($type)] ?? 'credit card alternative';
    }
}

if (! function_exists('user_is_admin')) {
    /**
     * Check if the user is an admin
     * 
     * @param null $user
     * @return boolean
     */
    function user_is_admin($user = null)
    {
        $user or $user = user();
        
        return $user && $user->isAdmin;
    }
}

if (! function_exists('test_passed')) {
    /**
     * Check if the test is passed based on the given mark.
     *
     * @param $mark
     * @return boolean
     */
    function test_passed($mark)
    {
        return (float) $mark > TEST_PASSING_MARK;
    }
}

if (! function_exists('catch_and_return')) {
    /**
     * @param $message
     * @param Exception $exception
     * @return string
     */
    function catch_and_return($message, Exception $exception)
    {
        $time = Carbon::now()->toDateTimeString();
        $message = "{$time}: {$message}";
        Log::critical($message . PHP_EOL .
            $exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
        return $message;
    }
}

if (! function_exists('convert_to_collection')) {
    function convert_to_collection(array $data)
    {
        $converted = new Collection();

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $converted->put($key, convert_to_collection($value));
            } else {
                $converted->put($key, $value);
            }
        }

        return $converted;
    }
}

if (! function_exists('gcache')) {
    /**
     * Returs a globally cached value if it's been set,
     * otherwise, it will set it and then return it.
     *
     * @param string   $key
     * @param callable $value
     * @return mixed
     */
    function gcache (string $key, callable $value = null)
    {
        if (isset($GLOBALS[$key])) {
            return $GLOBALS[$key];
        }

        return $GLOBALS[$key] = $value();
    }
}

if (! function_exists('cdnurl')) {
    /**
     * Returns the CDN URL with the provided $path appended to it.
     *
     * @param string $path
     * @return mixed
     */
    function cdnurl (string $path = '')
    {
        return str_finish(config('app.cdn_assets', '/'), '/') . $path;
    }
}
