<?php
namespace App\Controller;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Uid\Uuid;


class TestController extends AbstractController
{
    #[Route('/', name:'test_form')]
    public function formTest() : Response
    {
        return $this->render('form/test_form.html.twig');
    }

    #[Route('/test', name:'test_upload')]
    public function testUpload(Request $request) : Response
    {
       

         return new Response();
    }
}