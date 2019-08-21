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

class JobController extends Controller
{
    /**
     * @Route("/job/create",name="job.create",methods="GET|POST")
     * 
     * @param Request $request
     * @param EntityManagerInterface $em
     * 
     * @return RedirectResponse|Response
     */
    public function create(Request $request,EntityManagerInterface $em,FileUploader $fileUploader):Response{
        $job=new Job();
        $form=$this->createForm(JobType::class,$job);
        $form -> handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile|null $logoFile */
            $logoFile = $form->get('logo')->getData();

            if ($logoFile instanceof UploadedFile) {
                $fileName = \bin2hex(\random_bytes(10)) . '.' . $logoFile->guessExtension();
                $logoFile->move(
                    $this->getParameter('jobs_directory'),
                    $fileName
                );

                $job->setLogo($fileName);
            }

            $em->persist($job);
            $em->flush();

            return $this->redirectToRoute('job.list');
        }
        return $this->render('job/create.html.twig',[
            'form'=>$form->createView()
        ]);
    }
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
        return $this->render('job/show.html.twig', [
            'job' => $job,
        ]);
    }
}
