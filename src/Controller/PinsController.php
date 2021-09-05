<?php

namespace App\Controller;


use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Pin;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\PinType;



class PinsController extends AbstractController
{
     /**
     * @Route("/", name="app_home",methods="GET")
     */
    public function index(EntityManagerInterface $em): Response
    {
        

        $repo=$em->getRepository(Pin::class);

        $pins=$repo->findBy([],['createdAt'=>'DESC']);



        return $this->render('pins/index.html.twig',['pins'=> $pins]);
    }



    /**
     * @Route("/pins/create",name="app_pins_create",methods={"GET","POST"})
     */
    public function create(Request $request,EntityManagerInterface $em)
    {

        $pin= new Pin;


       $form=$this->createForm(PinType::class,$pin);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $em->persist($pin);

            $em->flush();
            $this->addFlash('success','Pin Successfully created');

            return $this->redirectToRoute('app_pins_show',['id' => $pin->getId()]);
        }

        return $this->render('pins/create.html.twig',[
                                'monForm' => $form->createView()
                            ]);
    }
      /**
    *@Route("/pins/{id<[0-9]+>},name=app_pins_show")
    */
    public function show(PinRepository $repo,int $id): Response
    {
        /*id est un parametre de route*/

        $pin = $repo->find($id);

        if (!$pin) {
            throw $this->createNotFoundException('Pin # '.$id.' not found');
            
        }

        return $this->render('pins/show.html.twig',compact('pin'));
    }

      /**
    *@Route("/pins/{id<[0-9]+>}/edit", name="app_pins_edit",methods={"GET","POST","PUT"})
    */
    public function edit(Request $request,Pin $pin,EntityManagerInterface $em):Response
    {
        $form=$this->createForm(PinType::class,$pin,[
            'method'=> 'PUT'
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

                        $this->addFlash('success','Pin Successfully updated');


            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/edit.html.twig',[
                                'pin' => $pin,  
                                'monForm' => $form->createView()
                            ]);
    }


}
