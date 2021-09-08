<?php
namespace  App\Controller;




use App\Entity\Category;
use App\Entity\EventSearch;
use App\Entity\UserSearch;
use App\Entity\Zone;
use App\Form\CategoryType;
use App\Form\EventSearchType;
use App\Form\UserSearchType;
use App\Form\ZoneType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;


class AdminController extends  AbstractController
{

    /**
     * @var Environment
     */
    private $render;



    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em,UserRepository $userRepository,Environment $render)
    {
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->render = $render;
    }

    public function index() {
        return $this->render("pages/admin/admin.home.html.twig");
    }

    public function showCompanies(Request $request,PaginatorInterface $paginator) {

        $search = new UserSearch();
        $form_search = $this->createForm(UserSearchType::class,$search);
        $form_search->handleRequest($request);

        $page = $request->get('page',1);
        $events = $paginator->paginate($this->userRepository->findCompaniesBySearch($search),$page,10);

        return $this->render("pages/admin/admin.companies.show.html.twig",[
            'events' => $events,
            'search_form' => $form_search->createView()
        ]);
    }

    public function addZone(Request $request) {

        $zone = new Zone();
        $form = $this->createForm(ZoneType::class,$zone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($zone);
            $this->em->flush();
            $this->addFlash('success',"Zone/Wilaya crée avec succès");

            return $this->redirectToRoute('admin.home');
        }

        return $this->render("pages/admin/zone/admin.zone.create.html.twig");
    }


    public function editZone(Zone $zone,Request $request) {

        $form = $this->createForm(ZoneType::class,$zone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success',"Zone/Wilaya edité avec succès");

            return $this->redirectToRoute('admin.home');
        }

        return $this->render("pages/admin/zone/admin.zone.edit.html.twig");
    }

    public function removeZone(Zone $zone,Request $request) {

            if ($this->isCsrfTokenValid('remove' . $zone->getId(),$request->get("_token"))) {

                $this->em->remove($zone);
                $this->em->flush();
                $this->addFlash('success',"Zone Supprimée Avec Succès");
                return $this->redirectToRoute('admin.home');
            }

            return $this->redirectToRoute('admin.home');
        }

    public function removeCategory(Category $category,Request $request) {

        if ($this->isCsrfTokenValid('remove' . $category->getId(),$request->get("_token"))) {

            $this->em->remove($category);
            $this->em->flush();
            $this->addFlash('success',"Catégorie Supprimée Avec Succès");
            return $this->redirectToRoute('admin.home');
        }

        return $this->redirectToRoute('admin.home');
    }

    public function editCategory (Category $category,Request $request) {

        $form = $this->createForm(ZoneType::class,$category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success',"Catégorie edité avec succès");

            return $this->redirectToRoute('admin.home');
        }

        return $this->render("pages/admin/zone/admin.zone.edit.html.twig");
    }

    public function addCategory(Request $request) {

        $category = new Category();
        $form = $this->createForm(CategoryType::class,$category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($category);
            $this->em->flush();
            $this->addFlash('success',"Catégorie crée avec succès");

            return $this->redirectToRoute('admin.home');
        }

        return $this->render("pages/admin/category/admin.category.create.html.twig");
    }
}





?>