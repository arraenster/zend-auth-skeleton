<?php
/**
 *
 * @author: Vladyslav Semerenko <vladyslav.semerenko@bigbank.ee>
 */
namespace Auth\Interfaces;

interface AuthServiceAwareInterface
{

    public function setAuthService($authService);
}
