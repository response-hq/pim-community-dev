<?php

declare(strict_types=1);

namespace Akeneo\Pim\Enrichment\Component\Product\Updater\Converter;

use Akeneo\Pim\Enrichment\Component\Product\Model\ValueInterface;
use Akeneo\Pim\Structure\Component\AttributeTypes;
use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Webmozart\Assert\Assert;

/**
 * @copyright 2020 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
final class StringToStringDataConverter implements ValueDataConverter
{
    /** @var array */
    private $supportedAttributeTypes = [
        AttributeTypes::IDENTIFIER => [
            AttributeTypes::TEXT => true,
            AttributeTypes::TEXTAREA => true,
        ],
        AttributeTypes::TEXT => [
            AttributeTypes::TEXTAREA => true,
            AttributeTypes::OPTION_SIMPLE_SELECT => true,
            AttributeTypes::REFERENCE_ENTITY_SIMPLE_SELECT => true,
        ],
        AttributeTypes::OPTION_SIMPLE_SELECT => [
            AttributeTypes::TEXT => true,
            AttributeTypes::TEXTAREA => true,
            AttributeTypes::REFERENCE_ENTITY_SIMPLE_SELECT => true,
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function supportsAttributes(AttributeInterface $sourceAttribute, AttributeInterface $targetAttribute): bool
    {
        return isset($this->supportedAttributeTypes[$sourceAttribute->getType()][$targetAttribute->getType()]);
    }

    /**
     * {@inheritdoc}
     */
    public function convert(ValueInterface $sourceValue, AttributeInterface $targetAttribute)
    {
        Assert::string($sourceValue->getData());

        return $sourceValue->getData();
    }
}
