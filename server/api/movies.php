<?php

class movies {

    public function get() {
        $start = ($_POST['page'] - 1) * $_POST['onPage'];
        $end = $_POST['onPage'];
        // get and count movies

            $result = ($_POST['havePic']) ?
                $this->db->query("SELECT SQL_CALC_FOUND_ROWS t1.*, t2.image FROM movies t1 LEFT JOIN pictures t2 ON t1.movie_id = t2.movie_id WHERE t2.image is NOT NULL AND t2.image != '' LIMIT {$start}, {$end}", true)
                :
                $this->db->query("SELECT SQL_CALC_FOUND_ROWS t1.*, t2.image FROM movies t1 LEFT JOIN pictures t2 ON t1.movie_id = t2.movie_id WHERE t2.image is NULL OR t2.image = '' LIMIT {$start}, {$end}", true);

        return [
            'list' => $result->rows,
            'total' => $result->count
        ];;

    }
    public function parse() {
        $start = ($_POST['page'] - 1) * $_POST['onPage'];
        $end = $_POST['onPage'];
        $movies = [
            'total' => 0,
            'list' => []
        ];

        if ($_POST['reload']) {

            $html = file_get_html('https://ru.kinorium.com/');

            foreach ($html->find('body li.item[data-id] div[data-moviename]') as $el) {
                $element = $el->parent()->parent()->parent();

                $item['title'] = $element->find('a h3', 0)->plaintext;
                $item['title_original'] = $element->find('a h4', 0)->plaintext;
                if (preg_match("#url\((.+?(?=\)))#", $element->find('img', 0)->outertext, $img)) {
                    $item['image'] = $img[1];
                } else {
                    $item['image'] = false;
                }

                $item['movie_id'] = $element->find('div[data-movieid]', 0)->attr['data-movieid'];
                if(!isset($movies['list'][$item['movie_id']])) {
                    $movies['list'][$item['movie_id']] = $item;
                }

                //$movies['total']++;
            }
            $movies['list'] = array_values($movies['list']);
            // set to session
            $_SESSION['movies'] = $movies;

        } else {
            // get from session
            $movies = $_SESSION['movies'];
        }

        $list = [];
        if($_POST['havePic']){

            foreach ($movies['list'] as $key => $movie) {
                if ($movie['image'] AND $_POST['havePic']) {
                    $list[] = $movie;
                    $movies['total']++;
                }
            }
            }else{

            foreach ($movies['list'] as $key => $movie) {
                if (!$movie['image'] AND !$_POST['havePic']) {
                    $list[] = $movie;
                    $movies['total']++;
                }
            }
        }
            $movies['list'] = $list;


        // limit on page
        $movies['list'] = array_slice($movies['list'], $start, $end);
        //die(print_r($movies['list']));
        return $movies;
    }

    public function import() {
        $movies = $_SESSION['movies'];
        if ($movies['total']) {
            $this->db->query("TRUNCATE TABLE movies");
            $this->db->query("TRUNCATE TABLE pictures");
            $insertMovies =  ArrSqlInsertInto($movies['list'],['image'], $this->db->connection);
            $insertPictures = ArrSqlInsertInto($movies['list'],['title', 'title_original'], $this->db->connection);

            // insert into movies
            $this->db->query("INSERT INTO movies({$insertMovies['columns']}) VALUES{$insertMovies['values']}");
            // insert into pictures
            $this->db->query("INSERT INTO pictures({$insertPictures['columns']}) VALUES{$insertPictures['values']}");
            $this->result->status = 1;
        } else {
            $this->result->status = 0;
        }

        return $this->result;
    }
}
