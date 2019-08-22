<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Job;
use App\Form\JobType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class JobController extends AbstractController
{
    /**
     *
     * @Route("/", name="job.list", methods="GET")
     *
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function list(EntityManagerInterface $em) : Response
    {
        $categories = $em->getRepository(Category::class)->findWithActiveJobs();
        return $this->render('job/list.html.twig', [
            'categories' => $categories,
        ]);
    }
    /**
     *
     * @Route("job/{id}", name="job.show", methods="GET", requirements={"id" = "\d+"})
     *
     * @Entity("job", expr="repository.findActiveJob(id)")
     *
     * @param Job $job
     *
     * @return Response
     */
    public function show(Job $job) : Response
    {
        $deleteForm = $this->createDeleteForm($job);
        return $this->render('job/show.html.twig', [
            'job' => $job,
            'deleteForm' => $deleteForm->createView(),
        ]);
    }
    /**
     *
     * @Route("/job/create", name="job.create", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     *
     * @return RedirectResponse|Response
     */
    public function create(Request $request, EntityManagerInterface $em) : Response
    {
        $job = new Job();
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($job);
            $em->flush();
            return $this->redirectToRoute(
                'job.preview',
                ['token' => $job->getToken()]
            );
        }
        return $this->render('job/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     *
     * @Route("/job/{token}/edit", name="job.edit", methods={"GET", "POST"}, requirements={"token" = "\w+"})
     *
     * @param Request $request
     * @param Job $job
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function edit(Request $request, Job $job, EntityManagerInterface $em) : Response
    {
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute(
                'job.preview',
                ['token' => $job->getToken()]
            );
        }
        return $this->render('job/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     *
     * @Route("job/{token}", name="job.preview", methods="GET", requirements={"token" = "\w+"})
     *
     * @param Job $job
     *
     * @return Response
     */
    public function preview(Job $job) : Response
    {
       
        $deleteForm = $this->createDeleteForm($job);
            return $this->render('job/show.html.twig', [
                'job' => $job,
                'hasControlAccess' => true,
                'deleteForm' => $deleteForm->createView(),
            ]);
        
    }
    /**
     *
     * @Route("job/{token}/delete", name="job.delete", methods="DELETE", requirements={"token" = "\w+"})
     *
     * @param Request $request
     * @param Job $job
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function delete(Request $request, Job $job, EntityManagerInterface $em) : Response
    {
        $form = $this->createDeleteForm($job);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($job);
            $em->flush();
        }
        return $this->redirectToRoute('job.list');
    }
    
    /**
     *
     * @param Job $job
     *
     * @return FormInterface
     */
    private function createDeleteForm(Job $job) : FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('job.delete', ['token' => $job->getToken()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}