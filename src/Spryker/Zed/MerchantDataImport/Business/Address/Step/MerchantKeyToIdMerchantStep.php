<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\MerchantDataImport\Business\Address\Step;

use Orm\Zed\Merchant\Persistence\Map\SpyMerchantTableMap;
use Orm\Zed\Merchant\Persistence\SpyMerchantQuery;
use Spryker\Zed\DataImport\Business\Exception\EntityNotFoundException;
use Spryker\Zed\DataImport\Business\Exception\InvalidDataException;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;
use Spryker\Zed\MerchantDataImport\Business\Address\DataSet\MerchantAddressDataSetInterface;

class MerchantKeyToIdMerchantStep implements DataImportStepInterface
{
    /**
     * @var array
     */
    protected $idMerchantCache = [];

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @throws \Spryker\Zed\DataImport\Business\Exception\InvalidDataException
     *
     * @return void
     */
    public function execute(DataSetInterface $dataSet): void
    {
        $merchantKey = $dataSet[MerchantAddressDataSetInterface::MERCHANT_KEY];

        if (!$merchantKey) {
            throw new InvalidDataException('"' . MerchantAddressDataSetInterface::MERCHANT_KEY . '" is required.');
        }

        if (!isset($this->idMerchantCache[$merchantKey])) {
            $this->idMerchantCache[$merchantKey] = $this->getIdMerchant($merchantKey);
        }

        $dataSet[MerchantAddressDataSetInterface::ID_MERCHANT] = $this->idMerchantCache[$merchantKey];
    }

    /**
     * @param string $merchantKey
     *
     * @throws \Spryker\Zed\DataImport\Business\Exception\EntityNotFoundException
     *
     * @return int
     */
    protected function getIdMerchant(string $merchantKey): int
    {
        /** @var \Orm\Zed\Merchant\Persistence\SpyMerchantQuery $merchantQuery */
        $merchantQuery = SpyMerchantQuery::create()
            ->select(SpyMerchantTableMap::COL_ID_MERCHANT);
        $merchantEntity = $merchantQuery->findOneByMerchantKey($merchantKey);

        if (!$merchantEntity) {
            throw new EntityNotFoundException(sprintf('Could not find Merchant by key "%s"', $merchantKey));
        }

        return $merchantEntity->getIdMerchant();
    }
}
