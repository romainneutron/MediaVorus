<?php

namespace MediaVorus\Media;

class Audio extends DefaultMedia
{

    public function getDuration()
    {
        $value = null;
        if ($this->getMetadatas()->containsKey('Composite:Duration'))
        {
            $value = $this->getMetadatas()->get('Composite:Duration')->getValue();
        }
        
        if ($value)
        {
            preg_match('/([0-9\.]+) s/', $value, $matches);

            if (count($matches) > 0)
            {
                return (float) $matches[1];
            }

            preg_match('/[0-9]+:[0-9]+:[0-9\.]+/', $value, $matches);

            if (count($matches) > 0)
            {
                $data = explode(':', $matches[0]);

                $duration = 0;
                $factor   = 1;
                while ($segment  = array_pop($data))
                {
                    $duration += $segment * $factor;
                    $factor *=60;
                }

                return (float) $duration;
            }
        }

        return null;
    }

}
