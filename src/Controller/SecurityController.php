<?php
namespace  App\Controller;




use App\Entity\Inventories;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;


class SecurityController extends  AbstractController
{

    private $encoder;
    private $em;

    public function __construct(Environment $render,UserPasswordEncoderInterface $encoder,EntityManagerInterface $em)
    {
        $this->encoder = $encoder;
        $this->em = $em;
    }


    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('pages/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    public function createChoice(Request $request) {
        return $this->render('pages/admin/create_user_choice.html.twig');
    }

    public function createCompany(Request $request) {

        $company = new User();
        $form = $this->createForm(UserType::class,$company);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid() ) {
            $company->setLatitude(0);
            $company->setLatitude(0);
            $company->setRoles(["ROLE_COMPANY"]);


            $inventory = new Inventories();
            $inventory->setCompanyName($company);
            $inventory->setContent([]);
            $this->em->persist($inventory);
            $this->em->flush();

            $company->setInventory($inventory);

            $company
                ->setPassword($this->encoder->encodePassword($company,$company->getPassword()));

        }
        return $this->render('pages/admin/create_company.html.twig');
    }
    public function  logout() {
        throw new \Exception('this should not be reached!');
    }



}





?>