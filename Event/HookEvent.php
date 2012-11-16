<?php

/*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Avro\StripeBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class DataEvent extends Event
{
    private $data;

    /**
     * Constructs an event.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Returns the data for this event.
     *
     * @return $data
     */
    public function getData()
    {
        return $this->data;
    }
}
