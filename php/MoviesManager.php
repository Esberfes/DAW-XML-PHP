<?php

class MoviesManager {

    private $xml_path;
    private $xml_object;

    function __construct($xml_path) {
        $this->xml_path = $xml_path;
        $this->exist_or_create();
        $this->process_request();
    }

    private function exist_or_create() {
        if(!file_exists($this->xml_path)) {
            $this->xml_object = new SimpleXMLElement('<movies></movies>');
            $this->save();
            return false;
        }
        $this->xml_object = simplexml_load_file($this->xml_path);
        return true;
    }

    public function add_movie($title, $sinopsis, $year, $duration, $image) {
        $id = $this->get_next_id();

        $movie = $this->xml_object->addChild('movie');

        $movie->addChild('id' , $id);
        $movie->addChild('title' , $title);
        $movie->addChild('sinopsis' , $sinopsis);
        $movie->addChild('year' , $year);
        $movie->addChild('duration' , $duration);
        $movie->addChild('image' , $image);

        $this->save();
    }

    private function save() {
        $this->xml_object->asXml($this->xml_path);
    }

    private function get_next_id(){
        $last_id = 0;

        if(count($this->xml_object->movie) == 0) return 0;

        foreach($this->xml_object->movie as $movie){
            $id = htmlentities((int)$movie->id);
            if($id > $last_id) $last_id = $id;
        }

        return $last_id + 1;
    }

    private function get_movie_by_id($id){
        foreach($this->xml_object->movie as $movie){
            if($movie->id == $id) return $movie;
        }
        return false;
    }

    public function delete_by_id($id) {
        $movie = $this->get_movie_by_id($id);

        if(!$movie) return;

        $dom = dom_import_simplexml($movie);
        $dom->parentNode->removeChild($dom);

        $this->save();
    }

    public function render() {
        $html = "<table class='table'>
                    <thead>
                        <tr>
                            <th scope='col'>#</th>
                            <th scope='col'>TITLE</th>
                            <th scope='col'>SINOPSIS</th>
                            <th scope='col'>YEAR</th>
                            <th scope='col'>DURATION</th>
                            <th scope='col'>IMAGE</th>
                            <th scope='col'>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <form action='{$_SERVER['PHP_SELF']}' method='POST'>
                            <td scope='row'>#</td>
                            <td><input type='text' name='title'></td>
                            <td><input type='text' name='sinopsis'></td>
                            <td><input type='number' name='year'></td>
                            <td><input type='number' name='duration'></td>
                            <td><input type='text' name='image'></td>
                            <td><button name='new_movie' class='btn btn-outline-primary'><i class='fas fa-save'></i></button></td>
                        </form>
                    </tr>
                    ";
            foreach($this->xml_object->movie as $movie){

                $html .= "<tr>";
                $html .= "<form action='{$_SERVER['PHP_SELF']}' method='POST'>";
                $html .= "<td scope='row'>{$movie->id}</td>";
                $html .= "<td scope='row'><input type='text' name='title' value='{$movie->title}'></td>";
                $html .= "<td scope='row'><input type='text' name='sinopsis' value='{$movie->sinopsis}'></td>";
                $html .= "<td scope='row'><input type='number' name='year' value='{$movie->year}'></td>";
                $html .= "<td scope='row'><input type='number' name='duration' value='{$movie->duration}'></td>";
                $html .= "<td scope='row'><input type='text' name='image' value='{$movie->image}'></td>";
                $html .= "<td scope='row'>
                    <button name='edit_movie' class='btn btn-outline-primary'><i class='fas fa-save'></i></button>
                    <button name='delete_movie' class='btn btn-outline-primary'><i class='fas fa-trash-alt'></i></button>
                </td>";
                $html .= "<input type='hidden' name='id' value='{$movie->id}'>";
                $html .= "</form>";
                $html .= "</tr>";
            }

            $html .= "</tbody></table>";

            echo $html;
    }

    public function render_items() {
        $html = "<div class='row justify-content-around'>";
        foreach($this->xml_object->movie as $movie) {
            $html .= "<div class='col-lg-4 col-12 d-flex align-items-stretch mb-3'>";
            $html .= "
            <div class='card'>
                <img class='card-img-top' src='{$movie->image}' alt='{$movie->title}'>
                <div class='card-body'>
                    <h5 class='card-title'>{$movie->title}</h5>
                    <p class='card-text'>{$movie->sinopsis}</p>
                </div>
                <ul class='list-group list-group-flush'>
                        <li class='list-group-item'>Año: <b> {$movie->year}</b></li>
                        <li class='list-group-item'>Duración: <b> {$movie->duration} min</b></li>
                    </ul>
            </div>";
            $html .= "</div>";
        }
        $html .= "</div>";
        echo $html;
    }

    private function process_request() {

        if(isset($_POST['new_movie'])) {
            $title = $_POST['title'];
            $sinopsis = $_POST['sinopsis'];
            $year = $_POST['year'];
            $duration = $_POST['duration'];
            $image = $_POST['image'];

            $this->add_movie($title, $sinopsis, $year, $duration, $image);
        }

        if(isset($_POST['edit_movie'])) {
            $id = $_POST['id'];
            $title = $_POST['title'];
            $sinopsis = $_POST['sinopsis'];
            $year = $_POST['year'];
            $duration = $_POST['duration'];
            $image = $_POST['image'];

            $movie = $this->get_movie_by_id($id);
            if(!$movie) return;

            $movie->title = $title;
            $movie->sinopsis = $sinopsis;
            $movie->duration = $duration;
            $movie->year = $year;
            $movie->image = $image;

            $this->save();
        }

        if(isset($_POST['delete_movie'])) {
            $id = $_POST['id'];
            
            $this->delete_by_id($id);
        }
    }
}