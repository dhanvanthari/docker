<?php

namespace AppBundle\Search;

class CitizenActionSearchResultsProvider extends EventSearchResultsProvider
{
    public function getSupportedTypeOfSearch(): string
    {
        return SearchParametersFilter::TYPE_CITIZEN_ACTIONS;
    }
}
