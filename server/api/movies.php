<?php

class movies {

    public function get() {
        $start = ($_POST['page'] - 1) * $_POST['onPage'];
        $end = $_POST['onPage'];
        // get and count movies
        $req = $this->db->query("select SQL_CALC_FOUND_ROWS * from movies limit {$start}, {$end}", true);

        return [
            'total' => $req->count,
            'list' => $req->rows
        ];

    }

    public function parse() {
        $start = ($_POST['page'] - 1) * $_POST['onPage'];
        $end = $_POST['onPage'];
        $movies = [
            'total' => 0,
            'list' => []
        ];
        session_start();

        if($_POST['reload']) {

            $html = file_get_html('https://ru.kinorium.com/');

            foreach($html->find('body li.item') as $element) {

                $item['title'] = $element->find('a h3', 0)->plaintext;
                $item['title_original'] = $element->find('a h4', 0)->plaintext;
                if(preg_match("#url\((.+?(?=\)))#", $element->find('img', 0)->outertext, $img)) {
                    $item['img'] = $img[1];
                }else{
                    $item['img'] = false;
                }

                $movies['list'][] = $item;

                $movies['total']++;
            }
            // set to session
            $_SESSION['movies'] = $movies;

        }else{
            // get from session
            $movies = $_SESSION['movies'];
        }
        // limit on page
        $movies['list'] = array_slice($movies['list'], $start, $end);
        return $movies;
    }

    public function import() {

    }
}
