<?php

namespace Nexusvc\Pwned;

use Illuminate\Validation\Validator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Lang;

class Pwned extends Validator
{
    protected $attribute;
    protected $minimum = 1;
    protected $ttl = 10080;
    protected $value;

    public function __construct($minimum = 1)
    {
        $this->minimum = $minimum;
    }

    public function validate($attribute, $rule)
    {
        $this->attribute = $attribute;
        $this->value     = $rule;

        return $this->passes();
    }

    public function passes()
    {
        $attribute = $this->attribute;
        $value     = $this->value;

        list($prefix, $suffix) = $this->hashAndSplit($value);
        $results = $this->query($prefix);
        $count = $results[$suffix] ?? 0;

        return $count < $this->minimum;
    }

    public function message()
    {
        return Lang::get('validation.pwned');
    }

    private function hashAndSplit($value)
    {
        $hash = strtoupper(sha1($value));
        $prefix = substr($hash, 0, 5);
        $suffix = substr($hash, 5);

        return [$prefix, $suffix];
    }

    protected function query($prefix)
    {
        // @TODO: Use Guzzle instead of Curl
        // Cache results for a week, to avoid constant API calls for identical prefixes
        return Cache::remember('pwned:'.$prefix, $this->ttl, function () use ($prefix) {
            
            $curl = curl_init('https://api.pwnedpasswords.com/range/'.$prefix);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $results = curl_exec($curl);
            curl_close($curl);

            return (new Collection(explode("\n", $results)))
                ->mapWithKeys(function ($value) {
                    list($suffix, $count) = explode(':', trim($value));
                    return [$suffix => $count];
                });
        });
    }
}
