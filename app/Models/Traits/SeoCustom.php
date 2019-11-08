<?php
namespace App\Models\Traits;

use Arcanedev\SeoHelper\Traits\Seoable;
use App\Models\SEO;

trait SeoCustom
{

    use Seoable;

    public function seoSetup($request)
    {

        if ($request->course) {
            if (ends_with($request->path(), 'certificate')) {
                return $this->seo();
            }
            $this->pushSeo($request->course->getCourseSEO());
            $courseTitle = ($request->course->getCourseSEO()['title'] ?: $request->course->title);
            if ($request->module) {
                if ($request->lesson) {
                    $this->seo()->setTitle(sprintf('%s - %s - %s', $request->lesson->title, $request->module->title, $courseTitle));
                } elseif ($request->test) {
                    $this->seo()->setTitle(sprintf('Test: %s - %s - %s', $request->test->title, $request->module->title, $courseTitle));
                } else {
                    $this->seo()->setTitle(sprintf('%s - %s', $request->module->title, $courseTitle));
                }
            }else{
                $section = ($request->segment(3) == 'thank-you' ? 'Thank You' : ucfirst($request->segment(3)));
                $this->seo()->setTitle(sprintf("%s $section", $courseTitle));
            }
        } else {
            $this->pushSeo($this->getDefaults());
            $section = ($request->segment(1) == 'faq' ? 'FAQ' : ucfirst($request->segment(1)));
            if ($section == 'Tools' || $section == 'News' || $section == 'account') {
                $spec = ($request->segment(2) ? ': ' . $request->segment(2) : '');
                $section = $section . ' ' . $spec;
            }
            $this->seo()->setTitle("Lurn Nation $section");
        }
        return $this->seo();
    }

    public function getDefaults()
    {
        $seoDefaults = new SEO;
        return $seoDefaults->getSeoDefaults();
    }

    public function pushSeo($seoArray)
    {
        $webmasters = array('google', 'bing', 'alexa', 'pinterest', 'yandex');

        $this->seoMeta()->setTitle($seoArray['title'], $seoArray['site_name'], $seoArray['separator'])
            ->setDescription($seoArray['description'])
            ->setKeywords($seoArray['keywords']);

        if ($seoArray['robots'] == 0) {
            $this->seoMeta()->removeMeta('robots');
        }

        if ($seoArray['author']) {
            $this->seoMeta()->addMeta('author', $seoArray['author']);
        }

        if ($seoArray['publisher']) {
            $this->seoMeta()->addMeta('publisher', $seoArray['publisher']);
        }

        foreach ($webmasters as $webmaster) {
            if ($seoArray['webmasters_' . $webmaster]) {
                $this->seoMeta()->addWebmaster($webmaster, $seoArray['webmasters_' . $webmaster]);
            }
        }

        if ($seoArray['analytics_google']) {
            $this->seoMeta()->setGoogleAnalytics($seoArray['analytics_google']);
        }

        $this->pushOgSeo($seoArray);
        $this->pushTwitterSeo($seoArray);

        return $this->seo();
    }

    public function pushOgSeo($seoArray)
    {
        if ($seoArray['og_enabled'] == 1) {
            $this->seoGraph()->setPrefix($seoArray['og_prefix'])
                ->setType($seoArray['og_type'])
                ->setTitle($seoArray['og_title'])
                ->setDescription($seoArray['og_description'])
                ->setSiteName($seoArray['og_site_name']);

            $ogProperties = explode(',', $seoArray['og_properties']);
            if (strlen($ogProperties[0]) > 0) {
                foreach ($ogProperties as $ogProperty) {
                    $property = explode('=', $ogProperty);
                    $this->seoGraph()->addProperty($property[0], $property[1]);
                }
            }
        } else {
            $this->seoGraph()->disable();
        }
    }

    public function pushTwitterSeo($seoArray)
    {
        if ($seoArray['twitter_enabled'] == 1) {
            $this->seoCard()->setType($seoArray['twitter_card'])
                ->setSite($seoArray['twitter_site'])
                ->setTitle($seoArray['twitter_title']);

            $twitterMetas = explode(',', $seoArray['twitter_meta']);
            if (strlen($twitterMetas[0]) > 0) {
                foreach ($twitterMetas as $twitterMeta) {
                    $meta = explode('=', $twitterMeta);
                    $this->seoCard()->addMeta($meta[0], $meta[1]);
                }
            }
        } else {
            $this->seoCard()->disable();
        }
    }
}
