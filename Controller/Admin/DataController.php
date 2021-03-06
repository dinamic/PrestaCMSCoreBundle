<?php
/**
 * This file is part of the Presta Bundle project.
 *
 * @author Nicolas Bastien <nbastien@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Presta\CMSCoreBundle\Controller\Admin;

use Presta\CMSCoreBundle\Controller\Admin\BaseController as AdminController;

use Application\Presta\CMSCoreBundle\Entity\Page;

/**
 * Data import / export controller
 *
 * @package    PrestaCMS
 * @subpackage CoreBundle
 * @author     Nicolas Bastien <nbastien@prestaconcept.net>
 */
class DataController extends AdminController
{
    //todo nbn refactor tous ces get dans cms base controller !

    /**
     * Return Website manager
     *
     * @return Presta\CMSCoreBundle\Model\WebsiteManager
     */
    public function getWebsiteManager()
    {
        return $this->get('presta_cms.website_manager');
    }

    /**
     * Return Theme manager
     *
     * @return Presta\CMSCoreBundle\Model\ThemeManager
     */
    public function getThemeManager()
    {
        return $this->get('presta_cms.theme_manager');
    }

    /**
     * Return Page manager
     *
     * @return Presta\CMSCoreBundle\Model\PageManager
     */
    public function getPageManager()
    {
        return $this->get('presta_cms.page_manager');
    }

    /**
     * Default page
     */
    public function indexAction($website_id, $locale)
    {
        $viewParams = array('website_id' => $website_id, 'locale' => $locale);

        //todo refactor chargement des pages
        $website = $this->getWebsiteManager()->getWebsite($website_id, $locale);
        if ($website != null) {
            $theme = $this->getThemeManager()->getTheme($website->getTheme());

            $navigations = array();
            foreach ($theme->getNavigations() as $navigation) {
                $navigations[$navigation] = $this->getPageManager()->getNavigationTree($website, $navigation, true);
            }
            $navigations['single_pages'] = $this->getPageManager()->getSinglePagesTree($website, true);
            $viewParams['theme'] = $theme;
            $viewParams['navigations'] = $navigations;
        }

        return $this->render('PrestaCMSCoreBundle:Admin/Data:index.html.twig', $viewParams);
    }
}
