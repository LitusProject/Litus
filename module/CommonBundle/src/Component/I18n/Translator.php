<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace CommonBundle\Component\I18n;

use Locale;
use Traversable;
use Zend\Cache;
use Zend\Cache\Storage\StorageInterface as CacheStorage;
use Zend\I18n\Exception;
use Zend\I18n\Translator\TextDomain;
use Zend\Stdlib\ArrayUtils;

/**
 * Translator.
 *
 * @category   Zend
 * @package    Zend_I18n
 * @subpackage Translator
 */
class Translator extends \Zend\I18n\Translator\Translator
{
    /**
     * Add a translation file.
     *
     * @param  string $type
     * @param  string $filename
     * @param  string $textDomain
     * @param  string $locale
     * @return Translator
     */
    public function addTranslationFile(
        $type,
        $filename,
        $textDomain = 'default',
        $locale = null
    ) {
        $locale = $locale ?: '*';

        if (!isset($this->files[$textDomain])) {
            $this->files[$textDomain] = array();
        }

        if (!isset($this->files[$textDomain][$locale])) {
            $this->files[$textDomain][$locale] = array();
        }

        $this->files[$textDomain][$locale][] = array(
            'type'     => $type,
            'filename' => $filename,
        );

        return $this;
    }

    /**
     * Load messages for a given language and domain.
     *
     * @param  string $textDomain
     * @param  string $locale
     * @return void
     */
    protected function loadMessages($textDomain, $locale)
    {
        if (!isset($this->messages[$textDomain])) {
            $this->messages[$textDomain] = array();
        }

        if (null !== ($cache = $this->getCache())) {
            $cacheId = 'Zend_I18n_Translator_Messages_' . md5($textDomain . $locale);

            if (null !== ($result = $cache->getItem($cacheId))) {
                $this->messages[$textDomain][$locale] = $result;
                return;
            }
        }

        // Try to load from pattern
        if (isset($this->patterns[$textDomain])) {
            foreach ($this->patterns[$textDomain] as $pattern) {
                $filename = $pattern['baseDir']
                          . '/' . sprintf($pattern['pattern'], $locale);
                if (is_file($filename)) {
                    $this->messages[$textDomain][$locale] = $this->getPluginManager()
                         ->get($pattern['type'])
                         ->load($filename, $locale);
                }
            }
        }

        // Load concrete files, may override those loaded from patterns
        foreach (array($locale, '*') as $currentLocale) {
            if (!isset($this->files[$textDomain][$currentLocale])) {
                continue;
            }
            $messages = array();

            foreach($this->files[$textDomain][$currentLocale] as $file) {
                $messages = array_merge(
                    $messages,
                    $this->getPluginManager()
                         ->get($file['type'])
                         ->load($file['filename'], $locale)->getArrayCopy()
                );
            }
            $this->messages[$textDomain][$locale] = new TextDomain($messages);
            unset($this->files[$textDomain][$currentLocale]);
        }

        // Cache the loaded text domain
        if ($cache !== null) {
            $cache->setItem($cacheId, $this->messages[$textDomain][$locale]);
        }
    }
}
