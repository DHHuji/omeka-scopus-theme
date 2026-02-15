<?php
namespace OmekaTheme\Helper;

use Laminas\View\Helper\AbstractHelper;

class AdvancedSearchUrl extends AbstractHelper
{
    /**
     * Returns advanced item search url using optional configured slug.
     *
     * @param array $query Query parameters to append to the url.
     * @return string
     */
    public function __invoke(array $query = [])
    {
        $view = $this->getView();
        $slugOverride = trim((string) $view->themeSetting('advanced_search_page_slug', ''));
        if ($slugOverride !== '') {
            $query = $this->normalizeAdvancedSearchQuery($query);
        }
        $options = $query ? ['query' => $query] : [];

        $fallbackUrl = $view->url(
            'site/resource',
            ['controller' => 'item', 'action' => 'search'],
            $options,
            true
        );

        if ($slugOverride !== '') {
            try {
                $baseUrl = null;
                if ($view->status()->isSiteRequest()) {
                    $site = $view->currentSite();
                    if ($site) {
                        $baseUrl = rtrim($site->siteUrl(), '/') . '/' . ltrim($slugOverride, '/');
                    }
                }
                if (!$baseUrl) {
                    $baseUrl = $view->url('search-page-' . $slugOverride, [], [], true);
                }
                return $this->appendQuery($baseUrl, $query);
            } catch (\Throwable $e) {
            }
        }

        return $fallbackUrl;
    }

    /**
     * Converts query keys expected by core search to Advanced Search keys.
     *
     * @param array $query
     * @return array
     */
    protected function normalizeAdvancedSearchQuery(array $query): array
    {
        if (isset($query['fulltext_search']) && !isset($query['q'])) {
            $query['q'] = $query['fulltext_search'];
        }
        unset($query['fulltext_search']);
        return $query;
    }

    /**
     * Appends query parameters to an URL.
     *
     * @param string $url
     * @param array $query
     * @return string
     */
    protected function appendQuery(string $url, array $query): string
    {
        if (!$query) {
            return $url;
        }
        $separator = strpos($url, '?') === false ? '?' : '&';
        return $url . $separator . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
    }
}
