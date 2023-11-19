<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class TestController extends AbstractController
{
    #[Route('/test', name:'test_upload')]
    public function testUpload(Request $request) : Response
    {
        dd($request->files->get('image'));
    }
}