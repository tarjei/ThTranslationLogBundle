<?php

namespace TH\TranslationLogBundle;

use Symfony\Bundle\FrameworkBundle\Translation\Translator as BaseTranslator;


/**
 * User: tarjei
 * Date: 3/14/13 / 8:54 AM
 */
class Translator extends BaseTranslator
{

    public function setFallbackLocale($locales)
    {
        $this->container->get('logger')->info("setFallbackLocale: $locales"
         . $this->container->get('request')->getUri()
        );
        parent::setFallbackLocale(array("fallback"));
    }


    protected function loadCatalogue($locale)
    {
        $this->container->get('logger')->info("loadCatalogue: $locale" . $this->container->get('request')->getUri());
        if (!isset($this->catalogues['fallback']) && $locale != 'fallback') {
            $this->catalogues['fallback'] = new LoggingMessageCatalogue('fallback', $this->container->get('th_translation_log.logger'));
            $this->loadFallbackCatalogues('fallback');
        }
        parent::loadCatalogue($locale);
        if ($locale != 'fallback') {
                $this->catalogues[$locale]->addFallbackCatalogue($this->catalogues['fallback']);
        }
    }

    private function loadFallbackCatalogues($locale)
    {
        $current = $this->catalogues[$locale];

        foreach ($this->computeFallbackLocales($locale) as $fallback) {
            $current->addFallbackCatalogue($this->catalogues[$fallback]);
            $current = $this->catalogues[$fallback];
        }
    }
}
