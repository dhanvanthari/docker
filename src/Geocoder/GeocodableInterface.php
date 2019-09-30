<?php

namespace AppBundle\Geocoder;

interface GeocodableInterface
{
    /**
     * Returns the geocodable address as a string.
     */
    public function getGeocodableAddress(): string;
}
