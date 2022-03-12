<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Service\MovieService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\Mapping\OrderBy;
use Exception;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\VarDumper;

#[Route('/movie')]
class MovieController extends AbstractController
{
    #[Route('/', name: 'movie_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $movies = $entityManager
            ->getRepository(Movie::class)
            ->findBy([],['score'=>'DESC','name'=>'ASC']);
        



        return $this->render('movie/index.html.twig', [
            'movies' => $movies,
        ]);
    }

    #[Route('/new', name: 'movie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, MovieService $movieService): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {   

            $service=$movieService->getApi();

            try
            {
                $movie=$movieService->setMovieInfo($movie, $service);
                $entityManager->persist($movie);
                $entityManager->flush();
                return $this->redirectToRoute('movie_index', [], Response::HTTP_SEE_OTHER);

            }
            catch(Exception $e)
            {
                echo "Film non existant";
            }

        }

        return $this->renderForm('movie/new.html.twig', [
            'movie' => $movie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'movie_show', methods: ['GET'])]
    public function show(Movie $movie): Response
    {
        return $this->render('movie/show.html.twig', [
            'movie' => $movie,
        ]);
    }

    #[Route('/{id}/edit', name: 'movie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Movie $movie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('movie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('movie/edit.html.twig', [
            'movie' => $movie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'movie_delete', methods: ['POST'])]
    public function delete(Request $request, Movie $movie, EntityManagerInterface $entityManager): Response
    {
        $code = $this->getParameter('app.admin_code');
      
        if ($this->isCsrfTokenValid('delete'.$movie->getId(), $request->request->get('_token'))&&$_POST["code"]==$code)
        {
            $entityManager->remove($movie);
            $entityManager->flush();

        }
        else
        {
            echo "Code administrateur invalide";
        }
        return $this->redirectToRoute('movie_index', [], Response::HTTP_SEE_OTHER);
    }
}
