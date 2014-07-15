<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 13/07/14
 * Time: 11:33 PM
 */

namespace Authentication\View\Helper;

use Authentication\Service\AuthenticationService;
use Zend\View\Helper\AbstractHelper;
class Authentication extends AbstractHelper
{
    private $service;

    public function __construct(AuthenticationService $service)
    {
        $this->service = $service;
    }

    public function __invoke()
    {
        return $this->service;
    }
} 