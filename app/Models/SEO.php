<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SeoDefault;

class SEO extends Model
{
    
    public function getStoredDefaults()
    {
        $seoDefault = SeoDefault::latest()->first();
        if ($seoDefault) {
            foreach ($seoDefault as $k => $v) {
                if (str_contains($k, 'attributes')) {
                    return $v;
                }
            }
        }
        return (array) $seoDefault;
    }

    public function getConfigDefaults()
    {
        $metaArray = (array) seo_helper()->meta();
        $configDefaults = array();
        foreach ($metaArray as $k => $v) {
            if (str_contains($k, 'configs')) {
                $configDefaults = $v;
            }
        }
        if (count($configDefaults) > 0) {
            $configDefaults['keywords']['default']['string'] = '';
            $keywordCount = count($configDefaults['keywords']['default']);
            $k = 1;
            if ($keywordCount > 0) {
                foreach ($configDefaults['keywords']['default'] as $keyword) {
                    $configDefaults['keywords']['default']['string'] .= $keyword;
                    if ($k !== $keywordCount) {
                        $configDefaults['keywords']['default']['string'] .= ',';
                    }
                    $k++;
                }
            }
            $configDefaults['open-graph']['properties']['string'] = ($this->metaToString($configDefaults['open-graph']['properties']) ?: '');
            $configDefaults['twitter']['metas']['string'] = ($this->metaToString($configDefaults['twitter']['metas']) ?:'');

            $mappedConfigDefaults = [
                'title' => $configDefaults['title']['default'],
                'site_name' => $configDefaults['title']['site-name'],
                'separator' => $configDefaults['title']['separator'],
                'description' => $configDefaults['description']['default'],
                'keywords' => $configDefaults['keywords']['default']['string'],
                'robots' => $configDefaults['misc']['robots'],
                'author' => $configDefaults['misc']['default']['author'],
                'publisher' => $configDefaults['misc']['default']['publisher'],
                'webmasters_google' => $configDefaults['webmasters']['google'],
                'webmasters_bing' => $configDefaults['webmasters']['bing'],
                'webmasters_alexa' => $configDefaults['webmasters']['alexa'],
                'webmasters_pinterest' => $configDefaults['webmasters']['pinterest'],
                'webmasters_yandex' => $configDefaults['webmasters']['yandex'],
                'og_enabled' => $configDefaults['open-graph']['enabled'],
                'og_prefix' => $configDefaults['open-graph']['prefix'],
                'og_type' => $configDefaults['open-graph']['type'],
                'og_title' => $configDefaults['open-graph']['title'],
                'og_site_name' => $configDefaults['open-graph']['site-name'],
                'og_description' => $configDefaults['open-graph']['description'],
                'og_properties' => $configDefaults['open-graph']['properties']['string'],
                'twitter_enabled' => $configDefaults['twitter']['enabled'],
                'twitter_card' => $configDefaults['twitter']['card'],
                'twitter_site' => $configDefaults['twitter']['site'],
                'twitter_title' => $configDefaults['twitter']['title'],
                'twitter_meta' => $configDefaults['twitter']['metas']['string'],
                'analytics_google' => $configDefaults['analytics']['google'],
            ];
            return $mappedConfigDefaults;
        }
        return (array) $configDefaults;
    }

    public function metaToString($metaArray)
    {
        $current = 1;
        $metaString = '';
        foreach ($metaArray as $meta => $content) {
            if (strlen($content) > 0) {
                $metaString .= $meta . '=' . $content;
                if ($current !== count($metaArray)) {
                    $metaString .= ',';
                }
                $current++;
            }
        }
        return $metaString;
    }

    public function getSeoDefaults()
    {
        $storedDefaults = $this->getStoredDefaults();
        $configDefaults = $this->getConfigDefaults();
        $seoDefaults = array();
        if (count($storedDefaults) > 0) {
            foreach ($configDefaults as $key => $value){
                $seoDefaults[$key] = ($storedDefaults[$key] ?: $value);
            }
            return $seoDefaults;
        }else{
            return $configDefaults;
        }
    }
}
