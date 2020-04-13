<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\QuoteDownloadableLinks\Model\Cart\BuyRequest;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Cart\BuyRequest\BuyRequestDataProviderInterface;
use Magento\Quote\Model\Cart\Data\CartItem;

/**
 * DataProvider for building downloadable product links in buy requests
 */
class DownloadableLinkDataProvider implements BuyRequestDataProviderInterface
{
    private const OPTION_TYPE = 'downloadable';

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function execute(CartItem $cartItem): array
    {
        $linksData = [];

        foreach ($cartItem->getSelectedOptions() as $optionData) {
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $optionData = \explode('/', base64_decode($optionData->getId()));

            if ($this->isProviderApplicable($optionData) === false) {
                continue;
            }
            $this->validateInput($optionData);

            [, $linkId] = $optionData;
            $linksData[] = $linkId;
        }

        return ['links' => $linksData];
    }

    /**
     * Checks whether this provider is applicable for the current option
     *
     * @param array $optionData
     * @return bool
     */
    private function isProviderApplicable(array $optionData): bool
    {
        if ($optionData[0] !== self::OPTION_TYPE) {
            return false;
        }

        return true;
    }

    /**
     * Validates the provided options structure
     *
     * @param array $optionData
     * @throws LocalizedException
     */
    private function validateInput(array $optionData): void
    {
        if (count($optionData) !== 2) {
            throw new LocalizedException(
                __('Wrong format of the entered option data. Must be "downloadable/link_id"')
            );
        }
    }
}
