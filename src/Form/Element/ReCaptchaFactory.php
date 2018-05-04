<?php
/**
 * @link      http://github.com/zetta-code/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2018 Zetta Code
 */

namespace Zetta\ZendBootstrap\Form\Element;

use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Form\Element\Captcha;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zetta\ZendAuthentication\Permissions\Acl\Acl;
use Zend\Captcha\ReCaptcha as ZendReCaptcha;

class ReCaptchaFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $config = isset($config['zend_boostrap']) && isset($config['zend_boostrap']['recaptcha'])
            ? $config['zend_boostrap']['recaptcha']
            : [];

        $reCaptcha = new ZendReCaptcha();
        $reCaptcha->setSiteKey($config['site_key']);
        $reCaptcha->setSecretKey($config['secret_key']);

        $captcha = new Captcha();
        $captcha->setCaptcha($reCaptcha);

        return $captcha;
    }
}
