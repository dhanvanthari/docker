<?php

namespace AppBundle\Serializer;

use AppBundle\Entity\Mooc\Mooc;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class MoocSerializerSubscriber implements EventSubscriberInterface
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ['event' => Events::POST_SERIALIZE, 'method' => 'onPostSerialize', 'class' => Mooc::class],
        ];
    }

    public function onPostSerialize(ObjectEvent $event): void
    {
        if (\in_array('mooc_list', $event->getContext()->attributes->get('groups')->get())) {
            /** @var Mooc $mooc */
            $mooc = $event->getObject();

            $event->getVisitor()->addData(
                'image',
                $mooc->getImageName()
                    ? $this->router->generate('asset_url', ['path' => $mooc->getImagePath()], UrlGeneratorInterface::ABSOLUTE_URL)
                    : $mooc->getYoutubeThumbnail()
            );
        }
    }
}
