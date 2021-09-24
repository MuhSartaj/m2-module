<?php
namespace Sartaj\OrderInstruction\Serialize\Serializer;

class Json extends \Magento\Framework\Serialize\Serializer\Json
{


    /**
     * {@inheritDoc}
     * @since 100.2.0
     */
    public function unserialize($string)
    {
        $result = json_decode($string, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            if(false !== @unserialize($string)){
                return unserialize($string);
            }
            throw new \InvalidArgumentException('Unable to unserialize value.');
        }
        return $result;
    }
}
