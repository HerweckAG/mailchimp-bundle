<?php

namespace Welp\MailchimpBundle\Provider;

use Welp\MailchimpBundle\Provider\ListProviderInterface;
use Welp\MailchimpBundle\Subscriber\SubscriberList;

class ConfigListProvider implements ListProviderInterface
{
    private $providerFactory;
    private $lists;

    public function __construct(ProviderFactory $providerFactory, $lists)
    {
       $this->lists = $lists;
       $this->providerFactory = $providerFactory;
    }

    /**
     * Default list provider, retrieve the lists from the config file.
     * {@inheritdoc}
     */
    public function getLists()
    {
        if (sizeof($this->lists) == 0) {
            throw new \RuntimeException("No Mailchimp list has been defined. Check the your config.yml file based on MailchimpBundle's README.md");
        }
        $lists = array();
        foreach ($this->lists as $listId => $listParameters) {
            $providerServiceKey = $listParameters['subscriber_provider'];

            $provider = $this->providerFactory->create($providerServiceKey);
            $lists[] = new SubscriberList($listId, $provider);           
        }

        return $lists;
    }
}
