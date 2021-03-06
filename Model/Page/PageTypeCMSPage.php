<?php
/**
 * This file is part of the Presta Bundle project.
 *
 * @author Nicolas Bastien <nbastien@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Presta\CMSCoreBundle\Model\Page;

/**
 * Base page type for CMS
 *
 * @author     Nicolas Bastien <nbastien@prestaconcept.net>
 */
class PageTypeCMSPage implements PageTypeInterface
{
    const TAB_CONTENT = 'content';

    /**
     * @var Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * @var Presta\CMSCoreBundle\Model\WebsiteManager
     */
    protected $websiteManager;

    /**
     * @var Presta\CMSCoreBundle\Model\ThemeManager
     */
    protected $themeManager;

    public function __construct($container, $websiteManager, $themeManager)
    {
        $this->container = $container;
        $this->websiteManager = $websiteManager;
        $this->themeManager   = $themeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'cms_page';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultSettings()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getEditTabs()
    {
        return array (
            'cms_page' => 'content'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getEditTabData($tab, $page)
    {
        switch ($tab) {
            case self::TAB_CONTENT:
                $draft = $page;

                if (count($page->getZones()) == 0) {
                    // Todo améliorer ça !
                    // + prendre en compte le changement de template!
                    $repository = $this->container->get('doctrine_phpcr')->getManager()
                        ->getRepository('Presta\CMSCoreBundle\Document\Page');

                    //If there is no corresponding data, initialisation with default configuration
                    $repository->initializeForTemplate($draft, $this->themeManager->getPageTemplateConfiguration($draft->getTemplate()));
                    $this->container->get('doctrine_phpcr')->getManager()->clear();
                    $draft = $this->container->get('doctrine_phpcr')->getManager()->findTranslation(
                        'Presta\CMSCoreBundle\Document\Page',
                        $page->getId(),
                        $page->getLocale()
                    );
                }
                $website = $this->websiteManager->getCurrentWebsite();

                return array(
                    'page' 	   => $draft,
                    'locale'   => $page->getLocale(),
                    'website'  => $website,
                    'websiteId' => ($website) ? $website->getId() : null,
                    'template' => $this->themeManager->getPageTemplate($draft->getTemplate(), $draft)
                );
                break;
            default:
                break;
        }

        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getEditTabTemplate($tab)
    {
        return 'PrestaCMSCoreBundle:Admin/Page/CMSPage:tab_content.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getData($page)
    {
        return array(
            'template' => $this->themeManager->getPageTemplateFile($page->getTemplate())
        );
    }
}
