<?php

namespace TH\TranslationLogBundle;



/**
 * User: tarjei
 * Date: 3/13/13 / 1:54 PM
 */ 
use Symfony\Component\Config\Resource\ResourceInterface;
use Symfony\Component\Serializer\Exception\UnsupportedException;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\MetadataAwareInterface;

class LoggingMessageCatalogue implements MessageCatalogueInterface, MetadataAwareInterface {

    protected $resource, $locale ;

    public $parent;

    protected $messages = array();

    private $metadata = array();

    /**
     * @var MessageCatalogue
     */
    protected $fallbackCatalogue;


    function __construct($locale, $logger)
    {
        $this->locale = $locale;
        $this->logger = $logger;
    }

    /**
     * Gets the catalogue locale.
     *
     * @return string The locale
     *
     * @api
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Gets the domains.
     *
     * @return array An array of domains
     *
     * @api
     */
    public function getDomains()
    {
        return array();
    }

    /**
     * Gets the messages within a given domain.
     *
     * If $domain is null, it returns all messages.
     *
     * @param string $domain The domain name
     *
     * @return array An array of messages
     *
     * @api
     */
    public function all($domain = null)
    {
        return array();
    }

    /**
     * Sets a message translation.
     *
     * @param string $id          The message id
     * @param string $translation The messages translation
     * @param string $domain      The domain name
     *
     * @api
     */
    public function set($id, $translation, $domain = 'messages')
    {
        throw new UnsupportedException("Set not supported");
    }

    /**
     * Checks if a message has a translation.
     *
     * @param string $id     The message id
     * @param string $domain The domain name
     *
     * @return Boolean true if the message has a translation, false otherwise
     *
     * @api
     */
    public function has($id, $domain = 'messages')
    {
        //$this->logger->debug("has: $id -> $domain");
        if (null !== $this->fallbackCatalogue) {
            return $this->fallbackCatalogue->has($id, $domain);
        }

        return false;

    }

    /**
     * Checks if a message has a translation (it does not take into account the fallback mechanism).
     *
     * @param string $id     The message id
     * @param string $domain The domain name
     *
     * @return Boolean true if the message has a translation, false otherwise
     *
     * @api
     */
    public function defines($id, $domain = 'messages')
    {
        $this->logger->debug("defines: $id -> $domain");
        return false;
    }

    /**
     * Gets a message translation.
     *
     * @param string $id     The message id
     * @param string $domain The domain name
     *
     * @return string The message translation
     *
     * @api
     */
    public function get($id, $domain = 'messages')
    {
        $this->logger->info(sprintf('"%s",%s,%s', $id, $domain, $this->parent->getLocale()));
        //$this->logger->info("get: $id -> $domain");
        if (null !== $this->fallbackCatalogue) {
            return $this->fallbackCatalogue->get($id, $domain);
        }
        return $id;
    }

    /**
     * Sets translations for a given domain.
     *
     * @param string $messages An array of translations
     * @param string $domain   The domain name
     *
     * @api
     */
    public function replace($messages, $domain = 'messages')
    {
        throw new \Exception("Not supported!");
    }

    /**
     * Adds translations for a given domain.
     *
     * @param string $messages An array of translations
     * @param string $domain   The domain name
     *
     * @api
     */
    public function add($messages, $domain = 'messages')
    {
        throw new \Exception("Not supported!");

    }

    /**
     * Merges translations from the given Catalogue into the current one.
     *
     * The two catalogues must have the same locale.
     *
     * @param MessageCatalogueInterface $catalogue A MessageCatalogueInterface instance
     *
     * @api
     */
    public function addCatalogue(MessageCatalogueInterface $catalogue)
    {

    }


    /**
     * Returns an array of resources loaded to build this collection.
     *
     * @return ResourceInterface[] An array of resources
     *
     * @api
     */
    public function getResources()
    {
        return array();
    }

    /**
     * Adds a resource for this collection.
     *
     * @param ResourceInterface $resource A resource instance
     *
     * @api
     */
    public function addResource(ResourceInterface $resource)
    {

    }


    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function addFallbackCatalogue(MessageCatalogueInterface $catalogue)
    {
        // detect circular references
        $c = $this;
        do {
            if ($c->getLocale() === $catalogue->getLocale()) {
                throw new \LogicException(sprintf('Circular reference detected when adding a fallback catalogue for locale "%s".', $catalogue->getLocale()));
            }
        } while ($c = $c->parent);

        $catalogue->parent = $this;
        $this->fallbackCatalogue = $catalogue;

        foreach ($catalogue->getResources() as $resource) {
            $this->addResource($resource);
        }
    }

    /**
     * Gets the fallback catalogue.
     *
     * @return MessageCatalogueInterface A MessageCatalogueInterface instance
     *
     * @api
     */
    public function getFallbackCatalogue()
    {
        return $this->fallbackCatalogue;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = '', $domain = 'messages')
    {
        if (empty($domain)) {
            return $this->metadata;
        }

        if (isset($this->metadata[$domain])) {
            if (!empty($key)) {
                if (isset($this->metadata[$domain][$key])) {
                    return $this->metadata[$domain][$key];
                }
            } else {
                return $this->metadata[$domain];
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setMetadata($key, $value, $domain = 'messages')
    {
        $this->metadata[$domain][$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMetadata($key = '', $domain = 'messages')
    {
        if (empty($domain)) {
            $this->metadata = array();
        }

        if (empty($key)) {
            unset($this->metadata[$domain]);
        }

        unset($this->metadata[$domain][$key]);
    }

    /**
     * Adds current values with the new values.
     *
     * @param array $values Values to add
     */
    private function addMetadata(array $values)
    {
        foreach ($values as $domain => $keys) {
            foreach ($keys as $key => $value) {
                $this->setMetadata($key, $value, $domain);
            }
        }
    }
}
