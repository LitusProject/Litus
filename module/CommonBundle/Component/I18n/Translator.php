<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\I18n;

use Locale,
    Traversable,
    Zend\Cache,
    Zend\Cache\Storage\StorageInterface as CacheStorage,
    Zend\I18n\Exception,
    Zend\I18n\Translator\Loader\FileLoaderInterface,
    Zend\I18n\Translator\TextDomain,
    Zend\Stdlib\ArrayUtils;

/**
 * Translator
 *
 * @author Kristof Mariën <kristof.mariën@litus.cc>
 */
class Translator extends \Zend\I18n\Translator\Translator
    implements \Zend\Validator\Translator\TranslatorInterface
{
    /**
     * Add a translation file.
     *
     * @param string $type The file's type
     * @param string $filename The file's name
     * @param string $textDomain The domain
     * @param string $locale The locale
     * @return \CommonBundle\Component\I18n\Translator
     */
    public function addTranslationFile($type, $filename, $textDomain = 'default', $locale = null) {
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
     * @param string $textDomain The domain
     * @param string $locale The locale
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
        if (isset($this->remote[$textDomain])) {
            foreach ($this->remote[$textDomain] as $loaderType) {
                $loader = $this->getPluginManager()->get($loaderType);

                if (!$loader instanceof RemoteLoaderInterface) {
                    throw new Exception\RuntimeException('Specified loader is not a remote loader');
                }

                $this->messages[$textDomain][$locale] = $loader->load($locale, $textDomain);
            }
        }

        // Load files, which may override those loaded from patterns
        foreach (array($locale, '*') as $currentLocale) {
            if (!isset($this->files[$textDomain][$currentLocale])) {
                continue;
            }

            $messages = array();

            foreach($this->files[$textDomain][$currentLocale] as $file) {
                $loader = $this->getPluginManager()->get($file['type']);

                if (!$loader instanceof FileLoaderInterface) {
                    throw new Exception\RuntimeException('Specified loader is not a file loader');
                }

                $messages = array_merge(
                    $messages,
                    $loader->load($locale, $file['filename'])->getArrayCopy()
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
