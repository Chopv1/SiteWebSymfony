<?php
namespace App\Service;
use aharen\OMDbAPI;
use App\Entity\Movie;
use stdClass;

class MovieService
{
    
    public function getApi()
    {
        $omdb = new OMDbAPI('57b1c861');
        $resultat = $omdb->fetch('t', $_POST["movie"]["name"]);
    
        return $resultat;
    }
    public function setMovieInfo(Movie $movie, stdClass $service)
    {
        $movie->setName($service->data->Title);
        $movie->setDescription($service->data->Plot);
        $movie->setScore($service->data->imdbRating);
        $movie->setVotersnumber($service->data->imdbVotes);
        
        return $movie;
    }
}