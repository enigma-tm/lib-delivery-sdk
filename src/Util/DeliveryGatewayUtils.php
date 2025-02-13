<?php

declare(strict_types=1);

namespace Paysera\DeliverySdk\Util;

use Paysera\DeliveryApi\MerchantClient\Entity\Order;
use Paysera\DeliverySdk\Entity\PayseraDeliveryGatewaySettingsInterface;
use Paysera\DeliverySdk\Entity\PayseraDeliverySettingsInterface;
use Paysera\DeliverySdk\Exception\UndefinedDeliveryGatewayException;

class DeliveryGatewayUtils
{
    public function resolveDeliveryGatewayCode(string $deliveryGatewayCode): string
    {
        $lastDelimPosition = strripos($deliveryGatewayCode, ':');

        return str_replace(
            [
                '_' . PayseraDeliverySettingsInterface::TYPE_COURIER,
                '_' . PayseraDeliverySettingsInterface::TYPE_PARCEL_MACHINE,
                '_' . PayseraDeliverySettingsInterface::TYPE_TERMINALS,
                PayseraDeliverySettingsInterface::DELIVERY_GATEWAY_PREFIX,
            ],
            '',
            $lastDelimPosition
                ? substr($deliveryGatewayCode, 0, $lastDelimPosition)
                : $deliveryGatewayCode
        );
    }

    public function getShipmentMethodCode(
        PayseraDeliveryGatewaySettingsInterface $deliveryGatewaySettings
    ): string {
        return sprintf(
            '%s2%s',
            $deliveryGatewaySettings->getSenderType(),
            $deliveryGatewaySettings->getReceiverType()
        );
    }

    public function getGatewayCodeFromDeliveryOrder(Order $order): ?string
    {
        $shippingMethod = $order->getShipmentMethod();
        $shippingGateway = $order->getShipmentGateway();

        if ($shippingMethod === null || $shippingGateway === null) {
            return null;
        }

        $receiverCode = $shippingMethod->getReceiverCode();
        $shipmentMethodCode = PayseraDeliverySettingsInterface::TYPE_COURIER;

        if ($receiverCode === PayseraDeliverySettingsInterface::TYPE_PARCEL_MACHINE) {
            $shipmentMethodCode = PayseraDeliverySettingsInterface::TYPE_TERMINALS;
        }

        return sprintf(
            '%s_%s',
            $shippingGateway->getCode(),
            $shipmentMethodCode
        );
    }
}
