<?php

namespace GhoSter\MultipleSalesRecipient\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use GhoSter\MultipleSalesRecipient\Model\Config;

class UpdateAttribute extends AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()//phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    {
        // TODO: Implement _construct() method.
    }

    /**
     * Save comment flag
     *
     * @param int $numberOfLimit
     * @return $this
     */
    public function saveCommentFlag(int $numberOfLimit): UpdateAttribute
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('eav_attribute')
        )->where(
            'attribute_code = ?',
            Config::ATTRIBUTE_CODE
        );

        $row = $connection->fetchRow($select);

        if ($row) {
            $whereCondition = [
                'attribute_id = ?' => $row['attribute_id']
            ];

            $row['note'] = sprintf('Comma separated, limit at %s emails', $numberOfLimit);
            $connection->update($this->getTable('eav_attribute'), $row, $whereCondition);
        }

        return $this;
    }
}
