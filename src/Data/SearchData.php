<?php


namespace App\Data;


use App\Entity\Participant;
use App\Entity\Site;

class SearchData
{
    /**
     * @var int
     */
    public $page = 1;
    /**
     * @var Site
     */
    public $site;
    /**
     * @var string
     */
    public $pattern = '';
    /**
     * @var string
     */
    public $dateStart;
    /**
     * @var string
     */
    public $dateEnd;
    /**
     * @var boolean
     */
    public $organizer = false;
    /**
     * @var boolean
     */
    public $registered = false;
    /**
     * @var boolean
     */
    public $unregistered = false;
    /**
     * @var boolean
     */
    public $finished = false;
}