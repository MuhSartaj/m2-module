<?php
declare(strict_types=1);

namespace Sartaj\OrderInstruction\Observer\Frontend\Checkout;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface;

class CartProductAddAfter implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestInterface $request,
        LoggerInterface $logger
    ) {
        $this->request = $request;
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
        if(null != $this->request->getParam('instruction')){
            try{
                $_item = $observer->getQuoteItem();
                $additionalOptions[] = array(
                        'label' => "Instruction",
                        'value' => $this->request->getParam('instruction'),
                );

                $addOption = array(
                    "code" => 'additional_options',
                    "product_id" => $_item->getProductId(),
                    "value" => serialize($additionalOptions)
                );
                $_item->addOption($addOption);
            }
            catch(Exception $ex){
                $this->logger->critical($ex->getMessage());
            }
        }
    }
}
