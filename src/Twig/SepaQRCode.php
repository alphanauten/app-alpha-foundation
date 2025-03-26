<?php declare(strict_types=1);

namespace HuntacTheme\Twig;

use SepaQr\Data;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class SepaQRCode extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('sepaQRCode', [$this, 'createSepaQRCode']),
        ];
    }

    public function createSepaQRCode(float $amount, string $ordernumber = '')
    {
        $paymentData = Data::create()
            ->setName('HunTac GmbH & Co. KG')
            ->setIban('DE76265400700550019400')
            ->setBic('COBADEFFxxx')
            ->setInformation($ordernumber)
            ->setAmount($amount); // The amount in Euro

        $qrOptions = new QROptions([
            'eccLevel' => QRCode::ECC_M // required by EPC standard
        ]);

        return (new QRCode($qrOptions))->render($paymentData->__toString());
    }
}