<?php

namespace Dywee\CMSBundle\Controller;

use Dywee\CMSBundle\Entity\Page;
use Dywee\CMSBundle\Entity\PageElement;
use Dywee\CMSBundle\Entity\PageStat;
use Dywee\CMSBundle\Entity\PageTextElement;
use Dywee\CMSBundle\Form\PageType;
use Dywee\ModuleBundle\Entity\FormResponseContainer;
use Dywee\NotificationBundle\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class PageController extends Controller
{
    public function indexAction(Request $request)
    {
        $pr = $this->getDoctrine()->getManager()->getRepository('DyweeCMSBundle:Page');
        $page = $pr->findHomePage();

        if($page == null)
            return $this->redirect($this->generateUrl('page_install'));

        return $this->viewAction($page->getId(), $request);
    }

    public function inMenuSwitchAction(Page $page)
    {
        $em = $this->getDoctrine()->getManager();

        $page->setInMenu(!$page->getInMenu());
        $em->persist($page);
        $em->flush();

        return $this->redirect($this->generateUrl('page_table'));
    }

    public function renderHomeAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $pageStat = new PageStat();
        $pageStat->setPage($page);

        $em->persist($pageStat);
        $em->flush();

        $data = array('page' => $page);

        return $this->render('DyweeCMSBundle:Page:view.html.twig', $data);
    }

    public function adminViewAction(Page $page)
    {
        $em = $this->getDoctrine()->getManager();

        $psr = $em->getRepository('DyweeCMSBundle:PageStat');
        $pss = $psr->findLastStatsForPage($page);
        return $this->render('DyweeCMSBundle:Admin:details.html.twig', array('page' => $page, 'stats' => $pss));
    }

    public function tableAction()
    {
        $pr = $this->getDoctrine()->getManager()->getRepository('DyweeCMSBundle:Page');
        $ps = $pr->findAll();

        return $this->render('DyweeCMSBundle:Page:table.html.twig', array('pageList' => $ps));
    }

    public function viewAction($data, Request $request)
    {
        /*
         * Le problème a étudier est celui des pages comprenant un ou plusieurs formulaire(s)
         * Un render depuis une vue twig rend correctement le formulaire mais ne permet pas de le valider
         * Le formulaire soumis est considéré comme vide
         *
         * Sur les forums on ne trouve pas grand chose vu que c'est pas un use case habituel
         *
         * Piste:
         * Possible que dans l'ancien système et tous les forward etc... de controlleur, on en passait pas les Request à tous
         * et que ça foutait le bordel parce qu'il recevait un mauvais Request?
         *
         * Pourquoi ne pas traiter les formulaires en ajax?
         */

        $pageRepository = $this->getDoctrine()->getManager()->getRepository('DyweeCMSBundle:Page');

        if(is_numeric($data))
            $page = $pageRepository->findById($data);
        else $page = $pageRepository->findBySeoUrl($data);

        switch($page->getType())
        {
            case 3: return $this->redirect($this->generateUrl('message_new'));
            /*case 5: return $this->forward('DyweeModuleBundle:Event:page', array('page' => $page));
            case 6: return $this->forward('DyweeEshopBundle:Eshop:pageHandler', array('page' => $page));
            case 7: return $this->render('DyweeBlogBundle:Blog:page.html.twig', array('page' => $page));
            case 8: return $this->forward('DyweeModuleBundle:Form:page', array('page' => $page));
            case 9: return $this->forward('DyweeFaqBundle:Faq:page', array('page' => $page));
            case 10: return $this->forward('DyweeFaqBundle:PictureGallery:page', array('page' => $page));
            case 11: return $this->forward('DyweeFaqBundle:VideoGallery:page', array('page' => $page));
            case 12: return $this->forward('DyweeModuleBundle:MusicGallery:page', array('page' => $page));*/
        }

        $data = array('page' => $page);

        /*if($page->hasForm())
        {
            $formsId = $page->getForms();

            $em = $this->getDoctrine()->getManager();
            $formRepository = $em->getRepository('DyweeModuleBundle:DyweeForm');

            $formBuilderService = $this->get('form.builder');

            $forms = array();
            foreach($formsId as $formId)
            {
                $customForm = $formRepository->findOneBy(array('id' => $formId, 'website' => $this->container->getParameter('website.id')));

                if($customForm)
                {
                    //Le form n'est pas un form à proprement parler mais un objet de type DyweeForm
                    $formBuilder = $formBuilderService->buildFormBuilder($customForm);

                    //Génération du formulaire
                    $form = $formBuilder->getForm();

                    //Traitement des formulaires
                    if($form->handleRequest($request)->isValid())
                    {
                        $websiteRepository = $em->getRepository('DyweeWebsiteBundle:Website');
                        $website = $websiteRepository->findOneById($this->container->getParameter('website.id'));

                        $response = new FormResponseContainer();
                        $response->setFromForm($customForm, $form->getData());

                        $notification = new Notification();
                        $notification->setContent('Une nouvelle réponse a été validée pour le formulaire');
                        $notification->setBundle('module');
                        $notification->setType('form.response.new');
                        $notification->setRoutingPath('customFormResponse_view');
                        $notification->setWebsite($website);

                        $em->persist($response);
                        $em->flush();

                        $notification->setRoutingArguments(json_encode(array('id' => $response->getId())));

                        $em->persist($notification);
                        $em->flush();
                    }

                    //Rendu des formulaires
                    $forms['form_' . $formId] = $form->createView();
                }
            }

            //On passe les formulaires à la vue
            $data['forms'] = $forms;
        }*/
        return $this->render('DyweeCMSBundle:Page:view.html.twig', $data);
    }

    public function addAction(Request $request)
    {
        $page = new Page();

        $em = $this->getDoctrine()->getManager();

        $form = $this->get('form.factory')->create(PageType::class, $page);

        if($form->handleRequest($request)->isValid())
        {
            $page->setUpdatedBy($this->getUser());

            $em->persist($page);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Page bien créée');

            return $this->redirect($this->generateUrl('page_table'));
        }
        return $this->render('DyweeCMSBundle:Page:add.html.twig', array('form' => $form->createView()));
    }

    public function updateAction(Page $page, Request $request)
    {
        $langToUse = null;
        if($request->get('lang'))
        {
            $pageRepository = $this->getDoctrine()->getManager()->getRepository('DyweeCMSBundle:Page');
            $langToUse = $request->get('lang');
        }

        $form = $this->get('form.factory')->create(PageType::class, $page);

        if($form->handleRequest($request)->isValid())
        {
            $page->setUpdatedBy($this->get('security.token_storage')->getToken()->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($page);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Page bien modifiée');

            return $this->redirect($this->generateUrl('page_table'));
        }
        return $this->render('DyweeCMSBundle:Page:edit.html.twig', array('page' => $page, 'form' => $form->createView()));
    }

    public function deleteAction(Page $page)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($page);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Page bien supprimée');

        return $this->redirect($this->generateUrl('page_table'));
    }
}
