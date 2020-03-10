<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\MerchantDataImport\Business\Model;

use Orm\Zed\Merchant\Persistence\SpyMerchantQuery;
use Spryker\Zed\DataImport\Business\Exception\InvalidDataException;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;
use Spryker\Zed\MerchantDataImport\Business\Model\DataSet\MerchantDataSetInterface;

class MerchantWriterStep implements DataImportStepInterface
{
    protected const REQUIRED_DATA_SET_KEYS = [
        MerchantDataSetInterface::MERCHANT_KEY,
        MerchantDataSetInterface::MERCHANT_REFERENCE,
        MerchantDataSetInterface::NAME,
        MerchantDataSetInterface::REGISTRATION_NUMBER,
        MerchantDataSetInterface::STATUS,
        MerchantDataSetInterface::EMAIL,
    ];

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return void
     */
    public function execute(DataSetInterface $dataSet): void
    {
        $this->validateDataSet($dataSet);

        $merchantEntity = SpyMerchantQuery::create()
            ->filterByMerchantKey($dataSet[MerchantDataSetInterface::MERCHANT_KEY])
            ->findOneOrCreate();

        $merchantEntity
            ->setName($dataSet[MerchantDataSetInterface::NAME])
            ->setRegistrationNumber($dataSet[MerchantDataSetInterface::REGISTRATION_NUMBER])
            ->setStatus($dataSet[MerchantDataSetInterface::STATUS])
            ->setEmail($dataSet[MerchantDataSetInterface::EMAIL])
            ->setMerchantReference($dataSet[MerchantDataSetInterface::MERCHANT_REFERENCE])
            ->setIsActive($dataSet[MerchantDataSetInterface::IS_ACTIVE])
            ->save();
    }

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return void
     */
    protected function validateDataSet(DataSetInterface $dataSet): void
    {
        foreach (static::REQUIRED_DATA_SET_KEYS as $requiredDataSetKey) {
            $this->validateRequireDataSetByKey($dataSet, $requiredDataSetKey);
        }
    }

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     * @param string $requiredDataSetKey
     *
     * @throws \Spryker\Zed\DataImport\Business\Exception\InvalidDataException
     *
     * @return void
     */
    protected function validateRequireDataSetByKey(DataSetInterface $dataSet, string $requiredDataSetKey): void
    {
        if (!$dataSet[$requiredDataSetKey]) {
            throw new InvalidDataException('"' . $requiredDataSetKey . '" is required.');
        }
    }
}
