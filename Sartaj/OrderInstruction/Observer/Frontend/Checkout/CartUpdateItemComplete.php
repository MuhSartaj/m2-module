<?php

declare(strict_types=1);

namespace Sartaj\OrderInstruction\Observer\Frontend\Checkout;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Quote\Model\QuoteFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Psr\Log\LoggerInterface;

class CartUpdateItemComplete implements \Magento\Framework\Event\ObserverInterface
{

    const QUOTE_ITEM_OPTION = 'quote_item_option';


    /**
     * @param RequestInterface $request
     * @param ResourceConnection $resourceConnection
     * @param QuoteFactory $quoteFactory
     * @param CheckoutSession $checkoutSession
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestInterface $request,
        ResourceConnection $resourceConnection,
        QuoteFactory $quoteFactory,
        CheckoutSession $checkoutSession,
        LoggerInterface $logger
    ) {    
        $this->request = $request;
        $this->resourceConnection = $resourceConnection;
        $this->quoteFactory = $quoteFactory;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $quote_item = $this->quoteFactory->create()->load($this->checkoutSession->getQuote()->getId());
        foreach ($quote_item->getAllItems() as $_item) {
            if(null != $this->request->getParam('instruction')){
                try{
                    $additionalOptions[] = [
                        'label' => "Instruction",
                        'value' => $this->request->getParam('instruction')
                    ];
                    $data = [
                        'item_id'       => $_item->getId(), 
                        'product_id'    => $this->request->getParam('product'),
                        'code'          => 'additional_options',
                        'value'         => serialize($additionalOptions)
                    ];
                    $conn = $this->resourceConnection->getConnection();
                    $tbl = $conn->getTableName(self::QUOTE_ITEM_OPTION);
                    $conn->insert($tbl, $data);
                }
                catch(Exception $ex){
                    $this->logger->critical($ex->getMessage());
                }
            }
        }
    }
}