<?php

namespace Tests\Provider;

use PHPUnit\Framework\TestCase;
use Welp\MailchimpBundle\Provider\ConfigListProvider;
use Welp\MailchimpBundle\Subscriber\SubscriberList;

class ConfigListProviderTest extends TestCase
{
    
    protected $providerFactory = null;
    protected $listConfig;

    public function setUp()
    {
        $this->providerFactory = $this->createMock(\Welp\MailchimpBundle\Provider\ProviderFactory::class);   
          
        $this->providerFactory           
            ->method('create')
            ->willReturn(self::returnValue($this->createMock(\Welp\MailchimpBundle\Provider\FosSubscriberProvider::class)))
        ;

        $this->listConfig = array(
            'sampleid' => array(
                'subscriber_provider' => 'sample.provider',
                'webhook_url' => 'url',
                'webhook_secret' => 'secret',
                'merge_fields' => array(
                    array(
                        'tag' => 'FNAME',
                        'name' => 'First Name',
                        'type'=> 'text',
                        'public' => true
                    ),
                    array(
                        'tag' => 'LNAME',
                        'name' => 'Last Name',
                        'type'=> 'text',
                        'public' => true
                    )
                )
            ),
            'sampleid2' => array(
                'subscriber_provider' => 'sample.provider',
                'webhook_url' => 'url',
                'webhook_secret' => 'secret',
                'merge_fields' => array(
                    array(
                        'tag' => 'FNAME2',
                        'name' => 'First Name',
                        'type'=> 'text',
                        'public' => true
                    ),
                    array(
                        'tag' => 'LNAME2',
                        'name' => 'Last Name',
                        'type'=> 'text',
                        'public' => true
                    )
                )
            )
        );
    }

    public function testGetListsEmpty()
    {
        //test with empty lists
        $configListProvider = new ConfigListProvider($this->providerFactory, array());
        $this->expectException(\RuntimeException::class);
        $configListProvider->getLists();
    }

    public function testGetLists()
    {
               
        $configListProvider = new ConfigListProvider($this->providerFactory, $this->listConfig);      
        $lists = $configListProvider->getLists();

        $this->assertCount(2, count($lists));
        $this->assertInstanceOf(SubscriberList::class, $lists[0]);
        $this->assertEquals("FNAME", $lists[0]->getMergeFields()[0]['tag']);
    }

    public function testGetList()
    {
               
        $configListProvider = new ConfigListProvider($this->providerFactory, $this->listConfig);      
        $list = $configListProvider->getList("sampleid2");

        $this->assertInstanceOf(SubscriberList::class, $list);
        $this->assertEquals("sampleid2", $list->getListId());
        $this->assertEquals("FNAME2", $list->getMergeFields()[0]['tag']);
        $this->assertEquals("LNAME2", $list->getMergeFields()[01]['tag']);
        $this->assertEquals("url", $list->getWebhookUrl());
        $this->assertEquals("secret", $list->getWebhookSecret());
    }

   
}
