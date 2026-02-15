<?php
namespace OmekaTheme\Helper;

use Laminas\View\Helper\AbstractHelper;

class AdvancedSearchUrl extends AbstractHelper
{
    /**
     * Returns advanced item search url, optionally via AdvancedSearch module.
     *
     * @param array $query Query parameters to append to the url.
     * @return string
     */
    public function __invoke(array $query = [])
    {
        $view = $this->getView();
        $useAdvancedSearchModule = (bool) $view->themeSetting('use_advanced_search_module_default');
        if ($useAdvancedSearchModule) {
            $query = $this->normalizeAdvancedSearchQuery($query);
        }
        $options = $query ? ['query' => $query] : [];

        $fallbackUrl = $view->url(
            'site/resource',
            ['controller' => 'item', 'action' => 'search'],
            $options,
            true
        );

        if (!$useAdvancedSearchModule) {
            return $fallbackUrl;
        }

        // Prefer the module helper when available to keep behavior aligned.
        try {
            $pluginManager = $view->getHelperPluginManager();
            if ($pluginManager->has('searchingUrl')) {
                return $view->plugin('searchingUrl')->__invoke(true, $options);
            }
        } catch (\Throwable $e) {
        }

        // Fallback if module helper is not available in this context.
        try {
            $searchMainConfig = $view->status()->isSiteRequest()
                ? $view->siteSetting('advancedsearch_main_config')
                : $view->setting('advancedsearch_main_config');

            if (!$searchMainConfig) {
                return $fallbackUrl;
            }

            $searchConfig = $view->api()->read(
                'search_configs',
                [is_numeric($searchMainConfig) ? 'id' : 'slug' => $searchMainConfig]
            )->getContent();

            return $view->url('search-page-' . $searchConfig->slug(), [], $options, true);
        } catch (\Throwable $e) {
            return $fallbackUrl;
        }
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
}
