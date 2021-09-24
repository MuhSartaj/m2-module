<?php
declare(strict_types=1);

namespace Sartaj\OrderInstruction\Plugin\Frontend\Magento\Quote\Model\Quote\Item;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface;

use Magento\Quote\Model\Quote\Item\ToOrderItem as QuoteToOrderItem;

class ToOrderItem
{

    /**
     * @param RequestInterface $request
     * @param LayoutInterface $layout
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        LayoutInterface $layout,
        RequestInterface $request,
        LoggerInterface $logger
    )
    {
        $this->_layout = $layout;
        $this->_storeManager = $storeManager;
        $this->_request = $request;
        $this->logger = $logger;
    }
    /**
     * aroundConvert
     *
     * @param QuoteToOrderItem $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param array $data
     *
     * @return \Magento\Sales\Model\Order\Item
     */
    public function aroundConvert(
        QuoteToOrderItem $subject,
        \Closure $proceed,
        $item,
        $data = []
    ) {
        // Get Order Item
        $orderItem = $proceed($item, $data);
        // Get Quote Item's additional Options
        $additionalOptions = $item->getOptionByCode('additional_options');
        // Check if there is any additional options in Quote Item
        if ($additionalOptions) {
            // Get Order Item's other options
            $options = $orderItem->getProductOptions();
            // Set additional options to Order Item
            $options['additional_options'] = unserialize($additionalOptions->getValue());
            $orderItem->setProductOptions($options);
        }
        return $orderItem;
    }
}
