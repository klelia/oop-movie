<?php
include __DIR__ . '/Genre.php';
include __DIR__ . '/Product.php';
class Movie extends Product
{
    private int $id;
    private string $title;
    private string $overview;
    private float $vote_average;
    private string $poster_path;

    private string $original_language;
    private array $genres; //array di oggetti di tipo Genre


    function __construct($id, $title, $overview, $vote, $language, $image, $genres, $quantity, $price)
    {
        parent::__construct($price, $quantity);

        $this->id = $id;
        $this->title = $title;
        $this->overview = $overview;
        $this->vote_average = $vote;
        $this->poster_path = $image;
        $this->original_language = $language;
        $this->genres = $genres;
    }

    private function getVote()
    {
        $vote = ceil($this->vote_average / 2);
        $template = "<p>";
        for ($n = 1; $n <= 5; $n++) {
            $template .= $n <= $vote ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
        }
        $template .= "</p>";
        return $template;
    }
    private function formatGenres()
    {

        $template = "<p>";
        for ($n = 0; $n < count($this->genres); $n++) {
            $template .= $this->genres[$n]->drawGenre();
        }
        $template .= "</p>";
        return $template;
    }
    public function printCard()
    {
        $error = '';
        if (ceil($this->vote_average) < 7) {
            try {
                $this->setDiscount(10);
            } catch (Exception $e) {
                $error = 'Eccezione: ' . $e->getMessage();
            }

        }
        $sconto = $this->getDiscount();
        $image = $this->poster_path;
        $title = strlen($this->title) > 28 ? substr($this->title, 0, 28) . '...' : $this->title;
        $content = substr($this->overview, 0, 100) . '...';
        $custom = $this->getVote();
        $genre = $this->formatGenres();
        $price = $this->price;
        $quantity = $this->quantity;
        include __DIR__ . '/../Views/card.php';
    }
    public static function fetchAll()
    {

        //leggo i dati dal file come stringa json
        $movieString = file_get_contents(__DIR__ . '/movie_db.json');

        //converto la stringa json in un array php
        $movieList = json_decode($movieString, true);

        //preparo una variabile che conterrà le istanze (oggetti) della classe Movie
        $movies = [];
        //prendo la lista dei generi
        $genres = Genre::fetchAll();
        foreach ($movieList as $item) {
            //creo array vuoto per i generi da passare al movie
            $moviegenres = [];
            //ciclo sull'array dei generi disponibili nei dati
            while (count($moviegenres) < count($item['genre_ids'])) {
                //per ogni genere id creo randomicamente un indice
                $index = rand(0, count($genres) - 1);
                //prendo dall'array dei generi quello con quell'indice
                $rand_genre = $genres[$index];
                //lo pusho nell'array di generi da passare al movie
                if (!in_array($rand_genre, $moviegenres)) {
                    $moviegenres[] = $rand_genre;
                }

            }
            $quantity = rand(0, 100);
            $price = rand(5, 200);
            //creo l'istanza del movie
            $movies[] = new Movie($item['id'], $item['title'], $item['overview'], $item['vote_average'], $item['original_language'], $item['poster_path'], $moviegenres, $quantity, $price);
        }
        //ritorno la lista di oggetti movie (istanze della classe Movie)
        return $movies;
    }

}


?>