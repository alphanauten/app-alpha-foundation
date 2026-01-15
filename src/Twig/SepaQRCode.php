<?php declare(strict_types=1);

namespace AlphaFoundation\Twig;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use SepaQr\Data;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SepaQRCode extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('sepaQRCode', $this->createSepaQRCode(...)),
        ];
    }

    public function createSepaQRCode(float $amount, string $ordernumber = '')
    {
        $paymentData = Data::create()
            ->setName('NAME')
            ->setIban('IBAN')
            ->setBic('BIC')
            ->setInformation($ordernumber)
            ->setAmount($amount); // The amount in Euro

        $qrOptions = new QROptions([
            'eccLevel' => QRCode::ECC_M // required by EPC standard
        ]);

        return (new QRCode($qrOptions))->render($paymentData->__toString());
    }
}