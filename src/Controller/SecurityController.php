<?php
namespace  App\Controller;




use App\Entity\Inventories;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\Zone;
use App\Form\ProductType;
use App\Form\UserType;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
        return $this->render('pages/admin/user/create_user_choice.html.twig');
    }

    public function createCompany(Request $request) {

        $company = new User();
        $form = $this->createForm(UserType::class,$company);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid() ) {

            $logoImage = $form->get('logoName')->getData();
            $logoImage = $this->moveUploadedImages([$logoImage],$company);
            $company->setLogoName($logoImage[0]) ;

            //TODO GeoLocalisation
            $company->setLatitude(0);
            $company->setLongitude(0);

            $company->setRoles(["ROLE_COMPANY"]);

            $inventory = new Inventories();
            $inventory->setCompanyName($company);
            $inventory->setContent([]);
            $this->em->persist($inventory);


            $company->setInventory($inventory);

            $company
                ->setPassword($this->encoder->encodePassword($company,$company->getPassword()));

            $this->em->persist($company);
            $this->em->flush();
            $this->addFlash('success',"Compte Entreprise crée avec succès");

            return $this->redirectToRoute('home');

        }
        return $this->render('pages/admin/user/create_company.html.twig',[
            'form' => $form->createView()
        ]);
    }

    public function editCompany(User $company,Request $request) {

        $form = $this->createForm(UserType::class,$company,["validation_groups" => "edit"]);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid()) {

            $logoImage = $form->get('logoName')->getData();
            if($logoImage) {
                $logoImage = $this->moveUploadedImages([$logoImage],$company);
                $company->setLogoName($logoImage[0]) ;
            }
            $this->em->flush();
            $this->addFlash('success',"Produit Edité avec succees");
            return $this->redirectToRoute('admin.companies.show');
        }

        return $this->render('pages/admin/user/admin.product.edit.html.twig',[
            'form' => $form->createView()
        ]);
    }

    public function removeCompany(User $company,Request $request) {

        if ($this->isCsrfTokenValid('remove' . $company->getId(),$request->get("_token"))) {

            $this->em->remove($company->getCategory());
            $this->em->remove($company);
            $this->em->flush();
            $this->addFlash('success',"Entreprise Supprimée Avec Succès");
            return $this->redirectToRoute('admin.home');
        }

        return $this->redirectToRoute('admin.companies.show');
    }

    public function  logout() {
        throw new \Exception('this should not be reached!');
    }

    private function moveUploadedImages(Array $array,User $user): array
    {
        $slugger = new Slugify();
        $return_array = [];

        foreach ($array as $imageData) {

            $safeFilename = $slugger->slugify($user->getUsername());
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageData->guessExtension();


            try {
                $imageData->move(
                    $this->getParameter('products_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                throw  new ErrorException("Error Uploading file");
            }

            array_push($return_array,$newFilename);
            //   }
        }
        return $return_array;

    }



}





?>