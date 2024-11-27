<?php
namespace Omekalia\Service\Stdlib;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Omekalia\Stdlib\Status;

class StatusFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedNamed, array $options = null)
    {
        $filepath = dirname(__DIR__, 3) . '/data/omekalia.json';
        $json = file_get_contents($filepath);
        $omekaliaData = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $status = new Status($omekaliaData);

        return $status;
    }
}
