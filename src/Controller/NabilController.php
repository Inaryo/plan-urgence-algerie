<?php
namespace  App\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;



/// DO NOT REMOVE , IT IS FOR NABIL TO TEST HIS TEMPLATES
/// DO NOT REMOVE , IT IS FOR NABIL TO TEST HIS TEMPLATES
/// DO NOT REMOVE , IT IS FOR NABIL TO TEST HIS TEMPLATES
/// DO NOT REMOVE , IT IS FOR NABIL TO TEST HIS TEMPLATES
/// DO NOT REMOVE , IT IS FOR NABIL TO TEST HIS TEMPLATES
/// DO NOT REMOVE , IT IS FOR NABIL TO TEST HIS TEMPLATES
/// DO NOT REMOVE , IT IS FOR NABIL TO TEST HIS TEMPLATES

class NabilController extends  AbstractController
{


    public function __construct()
    {}

    /**
     * @Route("/nabil/test", name="nabil.test")
     */
    public function index()
    {
        return $this->render("pages/home.html.twig");
    }
}





?>