<?php

namespace MediaVorus;

use \Doctrine\Common\Collections\ArrayCollection;

class MediaCollection extends ArrayCollection
{

    /**
     * Filters a MediaCollection with Filters
     *
     * @param Filter\Filter $filter
     * @param type $invert_match
     * @return type
     */
    public function match(Filter\Filter $filter, $invert_match = false)
    {
        if ($invert_match)
        {
            $partitions = $this->partition($filter->apply());

            return array_pop($partitions);
        }
        else
        {
            return $this->filter($filter->apply());
        }
    }

}
